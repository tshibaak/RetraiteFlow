<?php

use Router\Router;

$nom_enc = $nom_enc ?? current_user_name();
?>

<?php
$page_title = 'RetraiteFlow — Paramètres';
require __DIR__ . '/layouts/head.php';
?>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = auth_role() ?? 'Utilisateur';
    $nav_home_url = Router::route('/');
    $nav_extra_links = [];
    require __DIR__ . '/layouts/top-bar.php';
    ?>

    <main class="main-content">
        <header class="header">
            <h1>Paramètres</h1>
            <p>Personnalisez l’expérience RetraiteFlow selon vos besoins.</p>
        </header>

        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Préférences du compte</h2>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-bell"></i> Notifications</label>
                    <select>
                        <option>Activées</option>
                        <option>Réduites</option>
                        <option>Désactivées</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-language"></i> Langue</label>
                    <select>
                        <option>Français</option>
                        <option>English</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label><i class="fas fa-lock"></i> Sécurité</label>
                    <a href="<?= Router::route('/forgot-password') ?>" class="btn-secondary" style="display:inline-flex; width:auto;">Réinitialiser le mot de passe</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
