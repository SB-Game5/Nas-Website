<?php

namespace App\Actions;

use phpseclib3\Net\SSH2;
use Exception;

class FileTreeAction
{
    public function execute(string $targetDirectory): string
    {
        $host = env('NAS_SSH_HOST');
        $user = env('NAS_SSH_USER');
        $pass = env('NAS_SSH_PASS');

        // 1. Connexion SSH au NAS
        $ssh = new SSH2($host);
        if (!$ssh->login($user, $pass)) {
            throw new Exception("Connexion SSH échouée pour le listing des fichiers.");
        }

        // 2. Exécution de la commande ls -R (avec -p pour ajouter un / aux dossiers)
        $output = $ssh->exec("ls -Rp " . escapeshellarg($targetDirectory));
        $ssh->disconnect();

        if (empty(trim($output))) {
            return "<p class='text-gray-500 italic p-4'>Aucun fichier ou dossier trouvé.</p>";
        }

        return $this->parseLsToHtml($output, $targetDirectory);
    }

    /**
     * Analyse la sortie d'un ls -R pour en faire un arbre HTML imbriqué propre
     */
    private function parseLsToHtml(string $output, string $baseDir): string
    {
        $sections = explode("\n\n", trim($output));
        $tree = [];

        foreach ($sections as $section) {
            $lines = explode("\n", trim($section));
            if (empty($lines)) continue;

            // La première ligne d'une section contient le chemin (ex: /ServeurSB/Dossier1:)
            $currentPath = rtrim(array_shift($lines), ':');
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $isDir = str_ends_with($line, '/');
                $name = $isDir ? rtrim($line, '/') : $line;
                $fullPath = $currentPath . '/' . $name;

                // Construction d'une structure plate indexée par le chemin complet
                $tree[$fullPath] = [
                    'name' => $name,
                    'is_dir' => $isDir,
                    'parent' => $currentPath
                ];
            }
        }

        // Fonction récursive interne pour générer le HTML final à partir de la structure
        $buildHtml = function ($parentPath) use (&$tree, &$buildHtml, $baseDir) {
            $html = "";
            $items = [];

            foreach ($tree as $path => $info) {
                if ($info['parent'] === $parentPath) {
                    $items[$path] = $info;
                }
            }

            if (!empty($items)) {
                // Si ce n'est pas le dossier racine, on cache le sous-menu par défaut
                $hiddenClass = ($parentPath === $baseDir) ? "" : "hidden";
                $html .= "<ul class='pl-4 space-y-1 {$hiddenClass}'>";
                
                foreach ($items as $path => $info) {
                    if ($info['is_dir']) {
                        $html .= "<li class='pft-directory' data-path='" . htmlspecialchars($path) . "'>";
                        $html .= "<span class='cursor-pointer text-yellow-500 font-medium hover:text-yellow-400 select-none'>📁 " . htmlspecialchars($info['name']) . "</span>";
                        $html .= $buildHtml($path); // Appel récursif pour le contenu du dossier
                        $html .= "</li>";
                    } else {
                        $ext = strtolower(pathinfo($info['name'], PATHINFO_EXTENSION));
                        $html .= "<li class='pft-file pl-4 text-gray-300 hover:text-blue-400 cursor-pointer select-none' data-path='" . htmlspecialchars($path) . "' data-ext='{$ext}'>";
                        $html .= "📄 " . htmlspecialchars($info['name']);
                        $html .= "</li>";
                    }
                }
                $html .= "</ul>";
            }
            return $html;
        };

        return $buildHtml($baseDir);
    }
}