<?php
   use Router\Router;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetreatFlow</title>
    <link rel="stylesheet" href="../css/style_inscription_encadreur.css">
    <script src="../js/phone-mask.js" defer></script>
</head>

<body>
    <div class="form-container">
        <h1>Bienvenue sur RetreatFlow</h1>
        <h2>Créez votre profil</h2>

        <form action="<?= Router::route('/register') ?>" method="POST">
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
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                </li>

                <li class="form-group">
                    <label for="mdp">Mot de passe :</label>
                    <input type="text" name="mdp" placeholder="Entrez votre mot de passe" required>
                </li>

                <li class="form-group">
                    <label for="telephone">Téléphone/WhatsApp :</label>
                    <div class="phone-input-container">
                        <span class="phone-prefix">+243</span>
                        <input type="tel" id="telephone" name="telephone" placeholder="XXXXXXXXX" maxlength="9"
                            required>
                    </div>
                </li>

                <li class="form-group">
                    <label>Date de naissance :</label>
                    <div class="date-inputs">
                        <div class="input-field">
                            <input type="number" id="jour" name="jour" placeholder="JJ" min="1" max="31" required>
                        </div>
                        <div class="input-field">
                            <input type="number" id="mois" name="mois" placeholder="MM" min="1" max="12" required>
                        </div>
                        <div class="input-field">
                            <input type="number" id="annee" name="annee" placeholder="AAAA" min="1900" max="2025" required>
                        </div>
                    </div>
                </li>

                <li class="form-group">
                    <label for="sexe">Sexe :</label>
                    <select id="sexe" name="sexe" required>
                        <option value="">Choisissez votre sexe</option>
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                    </select>
                </li>

                <li class="form-group">
                    <label for="equipe">Équipe :</label>
                    <select id="equipe" name="equipe" required>
                        <option value="">Choisissez votre équipe</option>
                        <option value="coordination">Coordination</option>
                        <option value="encadrement">Encadrement</option>
                        <option value="finance">Finance</option>
                        <option value="logistique">Logistique</option>
                    </select>
                </li>

                <li class="form-group">
                    <label for="adresse">Adresse :</label>
                    <textarea id="adresse" name="adresse" placeholder="Entrez votre adresse complète"
                        required></textarea>
                </li>
            </ol>

            <div class="form-group submit-group">
                <button type="submit" name="envoyer">Envoyer la demande</button>
            </div>
        </form>
    </div>
</body>

</html>