<?php

use Router\Router;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow - Créer un compte</title>
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <form action="<?= Router::route('/register') ?>" method="POST" class="login-form">
                <div class="brand-block">
                    <div class="brand-badge">RF</div>
                    <h1>Créer un compte</h1>
                    <p class="login-subtitle">Renseignez vos informations pour rejoindre RetraiteFlow.</p>
                </div>

                <div class="input-icon aesthetic">
                    <span class="icon-user"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 21C20 18.8783 19.1571 16.8434 17.6569 15.3431C16.1566 13.8429 14.1217 13 12 13C9.87827 13 7.84344 13.8429 6.34315 15.3431C4.84285 16.8434 4 18.8783 4 21" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <input type="text" name="nom" placeholder="Nom complet" required>
                </div>

                <div class="input-icon aesthetic">
                    <span class="icon-user"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 21C20 18.8783 19.1571 16.8434 17.6569 15.3431C16.1566 13.8429 14.1217 13 12 13C9.87827 13 7.84344 13.8429 6.34315 15.3431C4.84285 16.8434 4 18.8783 4 21" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <input type="email" name="email" placeholder="Adresse email" required>
                </div>

                <div class="input-icon aesthetic">
                    <span class="icon-lock"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 20.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <input type="password" name="mdp" placeholder="Mot de passe" required>
                </div>

                <button type="submit" class="btn-pill aesthetic">Créer mon compte</button>
                <a href="<?= Router::route('/') ?>" class="forgot-link-inline">Retour à la connexion</a>
            </form>
        </div>
    </div>
</body>
</html>
