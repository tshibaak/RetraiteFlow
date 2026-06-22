<?php
use Router\Router;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_enc']) || !in_array($_SESSION['role'] ?? '', ['coordination', 'cordon'], true)) {
    header('Location: ' . Router::route('/'));
    exit();
}

require_once dirname(__DIR__, 2) . '/src/lib/funcstd.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Ajouter un membre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/encadreur.css">
    <link rel="stylesheet" href="/css/inscription_encadreur.css">
    <script src="/js/phone-mask.js" defer></script>
</head>

<body class="register-page">
    <?php
    $nav_user_name = current_user_name();
    $nav_role_label = 'Cordon / Super-admin';
    $nav_home_url = Router::route('/cordon');
    $nav_extra_links = [
        ['url' => Router::route('/cordon'), 'icon' => 'fas fa-chart-pie', 'label' => 'Tableau de bord'],
    ];
    require dirname(__DIR__) . '/partials/top-bar.php';
    ?>

    <main class="main-content">
        <header class="header header-compact">
            <h1>Ajouter un membre</h1>
            <p>Créez un compte pour un nouvel encadreur ou membre d'équipe</p>
        </header>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="flash-message flash-error">
                <?php
                echo h($_SESSION['message']);
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="form-container register-form-card">
            <form action="<?= Router::route('/coordon/register') ?>" method="POST">
                <ol class="form-list">
                    <li class="form-group name-group">
                        <label>Nom complet</label>
                        <div class="name-inputs">
                            <div class="input-field">
                                <input type="text" id="nom" name="nom" placeholder="Nom" required>
                            </div>
                            <div class="input-field">
                                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                            </div>
                        </div>
                    </li>

                    <li class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="email@exemple.com" required>
                    </li>

                    <li class="form-group">
                        <label for="mdp">Mot de passe</label>
                        <input type="password" id="mdp" name="mdp" placeholder="Mot de passe temporaire" required>
                    </li>

                    <li class="form-group">
                        <label for="telephone">Téléphone / WhatsApp</label>
                        <div class="phone-input-container">
                            <span class="phone-prefix">+243</span>
                            <input type="tel" id="telephone" name="telephone" placeholder="XXXXXXXXX" maxlength="9" required>
                        </div>
                    </li>

                    <li class="form-group">
                        <label>Date de naissance</label>
                        <div class="date-inputs">
                            <div class="input-field">
                                <input type="number" id="jour" name="jour" placeholder="JJ" min="1" max="31" required>
                            </div>
                            <div class="input-field">
                                <input type="number" id="mois" name="mois" placeholder="MM" min="1" max="12" required>
                            </div>
                            <div class="input-field">
                                <input type="number" id="annee" name="annee" placeholder="AAAA" min="1900" max="2026" required>
                            </div>
                        </div>
                    </li>

                    <li class="form-group">
                        <label for="sexe">Sexe</label>
                        <select id="sexe" name="sexe" required>
                            <option value="">Choisissez le sexe</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </li>

                    <li class="form-group">
                        <label for="equipe">Équipe / Rôle</label>
                        <select id="equipe" name="equipe" required>
                            <option value="">Choisissez l'équipe</option>
                            <option value="encadrement">Encadrement</option>
                            <option value="finance">Finance</option>
                            <option value="logistique">Logistique</option>
                            <option value="discipline">Discipline</option>
                            <option value="coordination">Coordination</option>
                        </select>
                    </li>

                    <li class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea id="adresse" name="adresse" placeholder="Adresse complète" required></textarea>
                    </li>
                </ol>

                <div class="form-group submit-group form-actions-row">
                    <a href="<?= Router::route('/cordon') ?>" class="btn-secondary">Annuler</a>
                    <button type="submit" name="envoyer" class="btn-primary">Ajouter le membre</button>
                </div>
            </form>
        </div>
    </main>
    <script src="/js/script_encadreur.js" defer></script>
</body>

</html>
