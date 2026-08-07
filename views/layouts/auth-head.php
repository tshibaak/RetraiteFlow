<?php
/**
 * Partial head pour pages d'authentification
 * Variables : $page_title, $extra_js (optionnel)
 */
$page_title = $page_title ?? 'RetraiteFlow — Connexion';
$extra_js = $extra_js ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="RetraiteFlow — Connexion">
    <title><?= h($page_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/tokens.css">
    <link rel="stylesheet" href="/css/login.css">
    <script src="/js/app-shell.js" defer></script>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= h($js) ?>" defer></script>
    <?php endforeach; ?>
</head>

<body>
    <button class="theme-toggle theme-toggle-floating" type="button" data-theme-toggle aria-label="Activer le thème sombre">
        <i class="theme-toggle-icon fas fa-sun" aria-hidden="true"></i>
    </button>
