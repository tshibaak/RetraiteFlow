<?php
namespace App\Controllers;

use Router\Router;

class Controller
{
    public static function sanitize(string $input): string
    {
        return trim(strip_tags($input));
    }

    public static function status(int $status): self
    {
        http_response_code($status);
        return new self();
    }

    public static function json(array $array): void
    {
        header('Content-Type: application/json');
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function message(string $message): void
    {
        $_SESSION['message'] = $message;
    }

    protected function success(string $message): void
    {
        $_SESSION['confirmation_ok'] = $message;
    }

    protected function redirect(string $route): void
    {
        header('Location: ' . Router::route($route));
        exit;
    }

    protected function requireAuth(array $roles = []): object
    {
        $user = current_user();
        if (!$user) {
            $this->redirect('/');
        }

        if ($roles !== [] && !in_array($user->role_name ?? '', $roles, true)) {
            $this->redirect('/');
        }

        return $user;
    }

    protected function requireAuthJson(array $roles = []): object
    {
        $user = current_user();
        if (!$user) {
            self::status(401)->json(['status' => false, 'message' => 'Non authentifié']);
        }

        if ($roles !== [] && !in_array($user->role_name ?? '', $roles, true)) {
            self::status(403)->json(['status' => false, 'message' => 'Accès refusé']);
        }

        return $user;
    }

    protected function clientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
