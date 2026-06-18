<?php

namespace App\Extensions;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use phpseclib3\Net\SSH2;
use App\Models\User;

class LoginUserProvider implements UserProvider
{
    // Étape 1 : Trouver l'utilisateur (ici on crée un objet utilisateur "virtuel")
    public function retrieveById($identifier)
    {
        return new User(['id' => $identifier, 'username' => $identifier]);
    }

    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}

    // Étape 2 : Récupérer l'utilisateur par ses identifiants de formulaire
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['username'])) {
            return null;
        }

        // On retourne un objet User temporaire avec le nom saisi
        return new User([
            'id' => $credentials['username'],
            'username' => $credentials['username']
        ]);
    }

    // Étape 3 : LA VÉRIFICATION MAGIQUE DU MOT DE PASSE VIA SAMBA
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        $host = env('NAS_SSH_HOST', '192.168.137.2');
        $sshUser = env('NAS_SSH_USER', 'swann');
        $sshPass = env('NAS_SSH_PASS');

        try {
            $ssh = new SSH2($host);
            if (!$ssh->login($sshUser, $sshPass)) {
                return false;
            }

            // Commande pour tester le mot de passe avec smbclient
            // On passe le mot de passe via l'entrée standard pour éviter qu'il demande une saisie interactive
            $cmd = sprintf(
                "echo %s | smbclient -L //localhost -U %s 2>&1",
                escapeshellarg($password),
                escapeshellarg($username)
            );

            $output = $ssh->exec($cmd);
            $ssh->disconnect();

            // Si le mot de passe est faux, smbclient renvoie généralement "NT_STATUS_LOGON_FAILURE"
            if (str_contains($output, 'NT_STATUS_LOGON_FAILURE') || str_contains($output, 'Access denied')) {
                return false;
            }

            // Si la commande s'est exécutée et ne contient pas d'échec de connexion, c'est bon !
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
    public function rehashPasswordIfRequired(\Illuminate\Contracts\Auth\Authenticatable $user, array $credentials, bool $force = false)
    {
        return null; //Samba doesn't need hashing
    }
}