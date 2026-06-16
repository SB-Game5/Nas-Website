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
        // 1. Récupération des accès SSH
        $host = env('NAS_SSH_HOST', '192.168.137.2');
        $user = env('NAS_SSH_USER', 'root');
        $pass = env('NAS_SSH_PASS');

        // 2. Connexion au NAS
        $ssh = new SSH2($host);
        
        if (!$ssh->login($user, $pass)) {
            throw new Exception("Connexion SSH échouée. Vérifiez les identifiants dans le .env.");
        }

        // 3. Commandes Linux pour créer l'utilisateur et son mot de passe
        // -m crée le home directory, -s définit le shell
        $username = escapeshellarg($dto->username);
        $password = $dto->password;
        $cmdCreate = "sudo useradd -M -s /sbin/nologin --badname " .escapeshellarg($dto->username);
$cmdPassword = "printf " . escapeshellarg($dto->password . "\n" . $dto->password . "\n") . " | sudo smbpasswd -s -a {$username}";
//         $cmdPassword = "sudo smbpasswd -s -a {$username} <<EOF
// {$dto->password}
// {$dto->password}
// EOF";
// Exécution sur le NAS
        // Inspecter la commande exacte générée dans storage/logs/laravel.log
        \Illuminate\Support\Facades\Log::info("Commande Samba générée : " . $cmdPassword);
        $ssh->exec($cmdCreate);
        $ssh->exec($cmdPassword);

        // 4. Déconnexion propre
        $ssh->disconnect();

        return true;
    }
}