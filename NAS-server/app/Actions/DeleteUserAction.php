<?php

namespace App\Actions;

use App\DTOs\DeleteUserDTO;
use phpseclib3\Net\SSH2;
use Exception;

class DeleteUserAction
{
    public function execute(DeleteUserDTO $dto): bool
    {
        $host = env('NAS_SSH_HOST'); // Register Host
        $user = env('NAS_SSH_USER'); // Register User
        $pass = env('NAS_SSH_PASS'); // Register Password

        // SSH connection
        $ssh = new SSH2($host);
        if (!$ssh->login($user, $pass)) {
            throw new Exception("Connexion SSH échouée. Vérifiez les identifiants dans le .env.");
        }

        //assemble user and password into the linux command
        $username = escapeshellarg($dto->username);
        $protectedGroup = "Administrateur";
        $checkGroupCmd = "id -Gn " . $username;
        $userGroups = $ssh->exec($checkGroupCmd);
        if ($userGroups !== false && in_array($protectedGroup, explode(' ', trim($userGroups)))) {
            $ssh->disconnect();
            throw new Exception("Sécurité : L'utilisateur est Admin et ne peut pas être supprimé.");
        }
        $cmdDeleteLinux = "sudo deluser " . $username;
        $cmdDeleteSamba = "sudo smbpasswd -x " . $username;
        // Exécution sur le NAS
        $ssh->exec($cmdDeleteSamba);
        $ssh->exec($cmdDeleteLinux);
        

        
        $ssh->disconnect();

        return true;
    }
}