<?php

namespace App\Actions;

use phpseclib3\Net\SSH2;
use Exception;

class ListUserAction
{
    public function execute(): array //array to create a list
    {
        $host = env('NAS_SSH_HOST', '192.168.137.2'); // Register Host
        $user = env('NAS_SSH_USER', 'swann'); // Register User
        $pass = env('NAS_SSH_PASS'); // Register Password

        // SSH connection
        $ssh = new SSH2($host);
        if (!$ssh->login($user, $pass)) {
            throw new Exception("Connexion SSH échouée. Vérifiez les identifiants dans le .env.");
        }

        $cmdlist = "sudo pdbedit -L";


        $output = $ssh->exec($cmdlist);

        
        $ssh->disconnect();
        $lines = explode("\n", trim($output));
        $usernames = [];
        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                $parts = explode(':', $line);
                $usernames[] = $parts[0]; // On extrait uniquement le nom
            }
        }

        return $usernames;
            }
}