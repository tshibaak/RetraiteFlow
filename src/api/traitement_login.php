<?php

use App\controllers\Controller;
use Router\Router;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'funcstd.php';

function login_redirect_error(string $message): void
{
    $_SESSION['message'] = $message;
    header('Location: ' . Router::route('/'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    f_erreur_405();
}

$adresse_email_login_enc = filter_input(INPUT_POST, 'username', FILTER_VALIDATE_EMAIL);
$mdp_login_enc = $_POST['password'] ?? '';
$role_login_enc = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$adresse_email_login_enc || $mdp_login_enc === '' || !$role_login_enc) {
    login_redirect_error('Veuillez remplir tous les champs correctement.');
}

try {
    $stmt = $db->prepare('SELECT id_enc, nom_enc, prenom_enc, mail_enc, role, mdp_enc FROM table_encadreur WHERE mail_enc = :mail_enc LIMIT 1');
    $stmt->execute([':mail_enc' => $adresse_email_login_enc]);
    $encadreur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$encadreur) {
        login_redirect_error('Utilisateur inexistant.');
    }

    if ($mdp_login_enc !== $encadreur['mdp_enc']) {
        login_redirect_error('Mot de passe incorrect.');
    }

    $db_role = $encadreur['role'];
    $role_ok = ($db_role === $role_login_enc)
        || ($role_login_enc === 'cordon' && $db_role === 'coordination');

    if (!$role_ok) {
        login_redirect_error('Rôle incorrect pour ce compte.');
    }

    $role = $role_login_enc === 'cordon' ? 'cordon' : $db_role;

    $_SESSION['id_enc'] = $encadreur['id_enc'];
    $_SESSION['nom_enc'] = $encadreur['nom_enc'];
    $_SESSION['prenom_enc'] = $encadreur['prenom_enc'];
    $_SESSION['mail_enc'] = $encadreur['mail_enc'];
    $_SESSION['role'] = $role;

    app_log($db, 'authentification', 'connexion', 'Connexion au compte ' . $role);

    $redirects = [
        'encadreur' => '/encadreur',
        'finance' => '/finance',
        'logistique' => '/logistique',
        'discipline' => '/discipline',
        'coordination' => '/cordon',
        'cordon' => '/cordon',
    ];

    if (!isset($redirects[$role])) {
        login_redirect_error('Rôle non reconnu.');
    }

    header('Location: ' . Router::route($redirects[$role]));
    exit();
} catch (PDOException $e) {
    die('Erreur lors de la lecture des données : ' . $e->getMessage());
}
