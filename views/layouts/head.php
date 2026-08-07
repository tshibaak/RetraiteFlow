<?php
/**
 * Partial head commun — variables attendues :
 *   $page_title (string, requis)
 *   $extra_css  (array, optionnel) — chemins CSS additionnels ex. ['/css/logistique.css']
 *   $extra_js   (array, optionnel) — scripts defer additionnels
 *   $body_class (string, optionnel)
 */
$page_title = $page_title ?? 'RetraiteFlow';
$extra_css = $extra_css ?? [];
$extra_js = $extra_js ?? [];
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="RetraiteFlow — Gestion de retraite spirituelle">
    <title><?= h($page_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/tokens.css">
    <link rel="stylesheet" href="/css/encadreur.css">
    <script src="/js/app-shell.js" defer></script>
    <?php foreach ($extra_css as $css): ?>
        <link rel="stylesheet" href="<?= h($css) ?>">
    <?php endforeach; ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= h($js) ?>" defer></script>
    <?php endforeach; ?>
</head>

<body<?= $body_class !== '' ? ' class="' . h($body_class) . '"' : '' ?>>
