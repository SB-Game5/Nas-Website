<?php

namespace App\Actions;
use App\DTOs\ListInfoDTO;
use phpseclib3\Net\SSH2;
use Exception;

class ListInfoAction
{
    public function execute(ListInfoDTO $dto): array
    {
        $host = env('NAS_SSH_HOST'); // Register Host
        $user = env('NAS_SSH_USER'); // Register User
        $pass = env('NAS_SSH_PASS'); // Register Password

        // SSH connection
        $ssh = new SSH2($host);
        if (!$ssh->login($user, $pass)) {
            throw new Exception("Connexion SSH échouée. Vérifiez les identifiants dans le .env.");
        }
        $safePath = "'" . str_replace("'", "'\''", $dto->selectedPath) . "'";

        $cmdinfo = "stat $safePath";


        $infooutput = $ssh->exec($cmdinfo);

        $permissions = $this->Group($ssh, $dto);
        
        $ssh->disconnect();

        $usersList = [];

         return [
            'stat' => $infooutput,
            'can_read' => $permissions[0],
            'can_write' => $permissions[1],
            'can_execute' => $permissions[2],
        ];;
    }
    public function Group(SSH2 $ssh, ListInfoDTO $dto): array
    {
        $safeUser = escapeshellarg($dto->username);
        $safePath = "'" . str_replace("'", "'\''", $dto->selectedPath) . "'";
        $permoutputread    = trim($ssh->exec("sudo -u {$safeUser} test -r $safePath && echo Y"));
        $permoutputwrite   = trim($ssh->exec("sudo -u {$safeUser} test -w $safePath && echo Y"));
        $permoutputexecute = trim($ssh->exec("sudo -u {$safeUser} test -x $safePath && echo Y"));

        $canread    = ($permoutputread === "Y");
        $canwrite   = ($permoutputwrite === "Y");
        $canexecute = ($permoutputexecute === "Y");

        return [$canread, $canwrite, $canexecute];
    }
}