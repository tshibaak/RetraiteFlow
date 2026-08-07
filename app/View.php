<?php

namespace App;

class View
{
    public static function view(string $template, array $data = []): void
    {
        $template = str_replace('.', DIRECTORY_SEPARATOR, $template);
        extract($data, EXTR_SKIP);

        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views';
        $candidates = [
            $baseDir . DIRECTORY_SEPARATOR . $template . '.php',
            $baseDir . DIRECTORY_SEPARATOR . 'encadreur' . DIRECTORY_SEPARATOR . 'index.php',
            $baseDir . DIRECTORY_SEPARATOR . 'cordon' . DIRECTORY_SEPARATOR . 'index.php',
            $baseDir . DIRECTORY_SEPARATOR . 'finance' . DIRECTORY_SEPARATOR . 'index.php',
            $baseDir . DIRECTORY_SEPARATOR . 'logistique' . DIRECTORY_SEPARATOR . 'index.php',
            $baseDir . DIRECTORY_SEPARATOR . 'discipline' . DIRECTORY_SEPARATOR . 'index.php',
            $baseDir . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'login.php',
            $baseDir . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'register.php',
            $baseDir . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'create-account.php',
            $baseDir . DIRECTORY_SEPARATOR . 'profile.php',
            $baseDir . DIRECTORY_SEPARATOR . 'settings.php',
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                require $candidate;
                return;
            }
        }

        throw new \RuntimeException("Vue introuvable : {$template}");
    }
}
