<?php

use Router\Router;

require_once dirname(__DIR__, 2) . '/src/lib/funcstd.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow - Connexion</title>
    <link rel="stylesheet" href="/css/style_login.css">
    <script src="/js/script_login.js" defer></script>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container">

            <?php if (isset($_SESSION['message_inscripttion'])): ?>
                <div class="flash-message flash-success">
                    <?php
                    echo h($_SESSION['message_inscripttion']);
                    unset($_SESSION['message_inscripttion']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="flash-message flash-error">
                    <?php
                    echo h($_SESSION['message']);
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?= Router::route('/login') ?>" method="POST" class="login-form" name="loginForm">
                <h1>RetraiteFlow</h1>
                <p class="login-subtitle">Connectez-vous à votre espace</p>
                <div class="input-icon aesthetic">
                    <span class="icon-user">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 21C20 18.8783 19.1571 16.8434 17.6569 15.3431C16.1566 13.8429 14.1217 13 12 13C9.87827 13 7.84344 13.8429 6.34315 15.3431C4.84285 16.8434 4 18.8783 4 21" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="email" id="username" name="username" placeholder="Adresse email" required autocomplete="email">
                </div>
                <div class="input-icon aesthetic">
                    <span class="icon-lock">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
                </div>
                <div class="input-icon aesthetic select-wrapper">
                    <span class="icon-role">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M2 17L12 22L22 17" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M2 12L12 17L22 12" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <select id="role" name="role" required>
                        <option value="">Sélectionnez votre rôle</option>
                        <option value="encadreur">Encadreur</option>
                        <option value="finance">Finance</option>
                        <option value="logistique">Logistique</option>
                        <option value="discipline">Discipline</option>
                        <option value="cordon">Cordon / Super-admin</option>
                    </select>
                </div>
                <div class="error-messages" id="error-messages"></div>
                <input type="submit" id="login" name="login" value="Se connecter" class="btn-pill aesthetic">
            </form>
        </div>
    </div>
</body>

</html>
