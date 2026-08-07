<?php

use Router\Router;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow - Connexion</title>
    <link rel="stylesheet" href="/css/login.css">
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
                <div class="brand-block">
                    <div class="brand-badge">RF</div>
                    <h1>RetraiteFlow</h1>
                    <p class="login-subtitle">Connectez-vous à votre espace</p>
                </div>

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
                            <path d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 20.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
                </div>

                <div class="divider">
                    <span>ou</span>
                </div>

                <a class="btn-google" href="https://accounts.google.com/o/oauth2/v2/auth?scope=email&access_type=online&response_type=code&redirect_uri=<?= urlencode(Router::route('/google-auth')) ?>&client_id=<?= $_ENV['GOOGLE_ID'] ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M21.6 12.23c0-.79-.07-1.54-.2-2.27H12v4.3h5.38a4.6 4.6 0 0 1-2 3.02v2.5h3.24c1.9-1.75 2.98-4.33 2.98-7.55Z"/>
                        <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.43l-3.24-2.5c-.9.6-2.05.96-3.38.96-2.6 0-4.8-1.76-5.59-4.12H3.07v2.58A10 10 0 0 0 12 22Z"/>
                        <path fill="#FBBC05" d="M6.41 13.91a6.02 6.02 0 0 1 0-3.82V7.51H3.07a10 10 0 0 0 0 12.78l3.34-2.58Z"/>
                        <path fill="#EA4335" d="M12 6.08c1.47 0 2.79.5 3.83 1.48l2.87-2.87A9.96 9.96 0 0 0 12 2a10 10 0 0 0-8.93 5.51l3.34 2.58C7.2 7.84 9.4 6.08 12 6.08Z"/>
                    </svg>
                    <span>Connexion avec Google</span>
                </a>

                <a href="<?= Router::route('/forgot-password') ?>" class="forgot-link-inline">Mot de passe oublié ?</a>
                <a href="<?= Router::route('/create-account') ?>" class="forgot-link-inline" style="margin-top: 4px;">Créer un compte</a>
                <div class="error-messages" id="error-messages"></div>
                <input type="submit" id="login" name="login" value="Se connecter" class="btn-pill aesthetic">
            </form>
        </div>
    </div>
</body>

</html>
