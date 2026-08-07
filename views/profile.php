<?php

use Router\Router;

$nom_enc = $nom_enc ?? current_user_name();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow - Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/encadreur.css">
</head>
<body>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = auth_role() ?? 'Utilisateur';
    $nav_home_url = Router::route('/');
    $nav_extra_links = [];
    require __DIR__ . '/layouts/top-bar.php';
    ?>

    <main class="main-content">
        <header class="header">
            <h1>Profil utilisateur</h1>
            <p>Gérez votre identité, vos informations personnelles et votre accès à l’application.</p>
        </header>

        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Informations personnelles</h2>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nom complet</label>
                    <input type="text" value="<?= h($nom_enc) ?>" disabled>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" value="<?= h(current_user()->email ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-id-badge"></i> Rôle</label>
                    <input type="text" value="<?= h(auth_role() ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Téléphone</label>
                    <input type="text" value="<?= h(current_user()->phone ?? '') ?>" disabled>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
