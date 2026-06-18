<?php

namespace App\Actions;

use App\DTOs\UserDTO;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AddUserAction
{
    public function execute(UserDTO $dto): bool
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
        $password = $dto->password;
        $cmdCreate = "sudo useradd -M -s /sbin/nologin --badname " .escapeshellarg($dto->username);
        $cmdPassword = "printf " . escapeshellarg($dto->password . "\n" . $dto->password . "\n") . " | sudo smbpasswd -s -a {$username}";
//         $cmdPassword = "sudo smbpasswd -s -a {$username} <<EOF
// {$dto->password}
// {$dto->password}
// EOF";
// Exécution sur le NAS
        
        \Illuminate\Support\Facades\Log::info("Commande Samba générée : " . $cmdPassword); // Inspecter la commande exacte générée dans storage/logs/laravel.log
        $ssh->exec($cmdCreate);
        $ssh->exec($cmdPassword);

        
        $ssh->disconnect();

        return true;
    }
}