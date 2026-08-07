<?php

use Router\Router;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow - Mot de passe oublié</title>
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container">
            <form action="<?= Router::route('/forgot-password') ?>" method="POST" class="login-form">
                <div class="brand-block">
                    <div class="brand-badge">RF</div>
                    <h1>Mot de passe oublié</h1>
                    <p class="login-subtitle">Entrez votre adresse email pour recevoir les instructions.</p>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="flash-message flash-error">
                        <?php echo h($_SESSION['message']); unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="input-icon aesthetic">
                    <span class="icon-user">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 21C20 18.8783 19.1571 16.8434 17.6569 15.3431C16.1566 13.8429 14.1217 13 12 13C9.87827 13 7.84344 13.8429 6.34315 15.3431C4.84285 16.8434 4 18.8783 4 21" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="email" name="email" placeholder="Adresse email" required autocomplete="email">
                </div>

                <button type="submit" class="btn-pill aesthetic">Envoyer la demande</button>
                <a href="<?= Router::route('/') ?>" class="btn-google" style="margin-top: 4px;">Retour à la connexion</a>
            </form>
        </div>
    </div>
</body>
</html>
