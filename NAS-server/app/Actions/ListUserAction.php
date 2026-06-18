<?php

namespace App\Actions;

use phpseclib3\Net\SSH2;
use Exception;

class ListUserAction
{
    public function execute(): array //array to create a list
    {
        $host = env('NAS_SSH_HOST'); // Register Host
        $user = env('NAS_SSH_USER'); // Register User
        $pass = env('NAS_SSH_PASS'); // Register Password

        // SSH connection
        $ssh = new SSH2($host);
        if (!$ssh->login($user, $pass)) {
            throw new Exception("Connexion SSH échouée. Vérifiez les identifiants dans le .env.");
        }
        $adminMembers = $this->Group($ssh);
        $cmdlist = "sudo pdbedit -L";

        $output = $ssh->exec($cmdlist);

        
        $ssh->disconnect();
        $lines = explode("\n", trim($output));
        $usersList = [];

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                $parts = explode(':', $line);
                $username = $parts[0];

                $usersList[] = [
                    'username' => $username,
                    'is_admin' => in_array($username, $adminMembers)
                ];
            }
        }

        return $usersList;
            }
    private function Group(SSH2 $ssh): array
    {
        $cmdgetent = "getent group Administrateur";
        $groupOutput = $ssh->exec($cmdgetent);
        
        if ($groupOutput !== false && !empty(trim($groupOutput))) {
            $groupParts = explode(':', trim($groupOutput));
            
            // Les membres du groupe sont à l'index 3 (séparés par des virgules)
            if (isset($groupParts[3]) && !empty(trim($groupParts[3]))) {
                return explode(',', trim($groupParts[3]));
            }
        }

        return []; // Retourne un tableau vide si le groupe n'existe pas ou est vide
    }
}