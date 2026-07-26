<?php

namespace App;

class View
{
    public static function view(string $template, array $data = []): void
    {
        $template = str_replace('.', DIRECTORY_SEPARATOR, $template);
        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $template . '.php';
    }
}
