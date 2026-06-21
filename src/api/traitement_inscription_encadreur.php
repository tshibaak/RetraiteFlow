<?php

use Router\Router;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_enc']) || !in_array($_SESSION['role'] ?? '', ['coordination', 'cordon'], true)) {
    header('Location: ' . Router::route('/'));
    exit();
}

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'funcstd.php';

function register_redirect(string $message, bool $success = true): void
{
    $_SESSION[$success ? 'message_inscripttion' : 'message'] = $message;
    header('Location: ' . Router::route($success ? '/cordon' : '/coordon/register'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    f_erreur_405();
}

$nom_enc = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
$prenom_enc = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
$email_enc = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$tel_enc = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS);
$date_jour_enc = filter_input(INPUT_POST, 'jour', FILTER_VALIDATE_INT);
$date_mois_enc = filter_input(INPUT_POST, 'mois', FILTER_VALIDATE_INT);
$date_annee_enc = filter_input(INPUT_POST, 'annee', FILTER_VALIDATE_INT);
$adresse_enc = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_SPECIAL_CHARS);
$mdp_enc = $_POST['mdp'] ?? '';

if (!$nom_enc || !$prenom_enc || !$email_enc || $mdp_enc === '' || !$tel_enc
    || !$date_jour_enc || !$date_mois_enc || !$date_annee_enc || !$adresse_enc) {
    register_redirect('Certains champs sont invalides ou vides.', false);
}

if (!preg_match('/^[0-9+\-\s]+$/', $tel_enc)) {
    register_redirect('Numéro de téléphone invalide.', false);
}

if (!checkdate($date_mois_enc, $date_jour_enc, $date_annee_enc)) {
    register_redirect('Date de naissance invalide.', false);
}

$date_naissance = sprintf('%04d-%02d-%02d', $date_annee_enc, $date_mois_enc, $date_jour_enc);

$sexe_raw = mb_strtoupper(trim($_POST['sexe'] ?? ''));
if ($sexe_raw === 'HOMME') {
    $sexe_enc = 'M';
} elseif ($sexe_raw === 'FEMME') {
    $sexe_enc = 'F';
} else {
    register_redirect('Sexe invalide.', false);
}

$equipe_map = [
    'COORDINATION' => 'coordination',
    'ENCADREMENT' => 'encadreur',
    'FINANCE' => 'finance',
    'LOGISTIQUE' => 'logistique',
    'DISCIPLINE' => 'discipline',
];
$equipe_raw = mb_strtoupper(trim($_POST['equipe'] ?? ''));
if (!isset($equipe_map[$equipe_raw])) {
    register_redirect('Équipe invalide.', false);
}
$role_enc = $equipe_map[$equipe_raw];

try {
    $check = $db->prepare('SELECT COUNT(*) FROM table_encadreur WHERE mail_enc = :mail_enc');
    $check->execute([':mail_enc' => $email_enc]);
    if ((int) $check->fetchColumn() > 0) {
        register_redirect('Cet email est déjà utilisé.', false);
    }

    $requette_insertion = 'INSERT INTO table_encadreur(nom_enc, prenom_enc, mdp_enc, mail_enc, date_naissance_enc, sex_enc, role, adresse, tel_enc)
        VALUES(:nom_enc, :prenom_enc, :mdp_enc, :mail_enc, :date_naissance_enc, :sex_enc, :role, :adresse, :tel_enc)';
    $execution_req = $db->prepare($requette_insertion);
    $execution_req->execute([
        ':nom_enc' => $nom_enc,
        ':prenom_enc' => $prenom_enc,
        ':mdp_enc' => $mdp_enc,
        ':mail_enc' => $email_enc,
        ':date_naissance_enc' => $date_naissance,
        ':sex_enc' => $sexe_enc,
        ':role' => $role_enc,
        ':adresse' => $adresse_enc,
        ':tel_enc' => $tel_enc,
    ]);

    app_log($db, 'coordon', 'inscription', 'Ajout du membre ' . $nom_enc . ' ' . $prenom_enc . ' (' . $role_enc . ')');
    register_redirect('Membre ajouté avec succès !');
} catch (PDOException $e) {
    register_redirect("Erreur lors de l'ajout : " . $e->getMessage(), false);
}
