<?php

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?object
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('current_user_name')) {
    function current_user_name(): string
    {
        $user = current_user();
        if ($user && !empty($user->name)) {
            return (string) $user->name;
        }
        return 'Utilisateur';
    }
}

if (!function_exists('auth_user_id')) {
    function auth_user_id(): ?int
    {
        $user = current_user();
        return $user ? (int) $user->id : null;
    }
}

if (!function_exists('auth_role')) {
    function auth_role(): ?string
    {
        $user = current_user();
        return $user->role_name ?? null;
    }
}
