<?php
namespace App\Controllers;

use App\Models\Role;
use App\Models\User;
use App\View;

class AuthController extends Controller
{
    protected User $user;
    protected Role $role;
    protected LogController $logger;

    public function __construct()
    {
        $this->user = new User();
        $this->role = new Role();
        $this->logger = new LogController();
    }

    public function index(): void
    {
        if (current_user()) {
            $this->redirect('/' . current_user()->role_name);
        }
        View::view('auth.login');
    }

    public function login(): void
    {
        $email = self::sanitize($_POST['username'] ?? $_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleAsked = self::sanitize($_POST['role'] ?? '');

        if ($email === '' || $password === '') {
            $this->message('Veuillez remplir tous les champs correctement.');
            $this->redirect('/');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message('Email incorrect.');
            $this->redirect('/');
        }

        $user = $this->user->findByEmail($email, \PDO::FETCH_OBJ);

        if (!$user || !password_verify($password, $user->password)) {
            $this->message('Identifiants incorrects.');
            $this->redirect('/');
        }

        if ($roleAsked !== '' && $roleAsked !== ($user->role_name ?? '')) {
            $this->message('Le rôle sélectionné ne correspond pas à ce compte.');
            $this->redirect('/');
        }

        unset($user->password);
        $_SESSION['user'] = $user;

        $this->logger->store((int) $user->id, 'login', 'Connexion réussie');
        $this->redirect('/' . $user->role_name);
    }

    public function showRegister(): void
    {
        $this->requireAuth(['coordination', 'coordon']);
        View::view('auth.register');
    }

    public function register(): void
    {
        $actor = $this->requireAuth(['coordination', 'coordon']);

        $nom = self::sanitize($_POST['nom'] ?? '');
        $prenom = self::sanitize($_POST['prenom'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $tel = self::sanitize($_POST['telephone'] ?? '');
        $adresse = self::sanitize($_POST['adresse'] ?? '');
        $mdp = $_POST['mdp'] ?? '';
        $jour = filter_input(INPUT_POST, 'jour', FILTER_VALIDATE_INT);
        $mois = filter_input(INPUT_POST, 'mois', FILTER_VALIDATE_INT);
        $annee = filter_input(INPUT_POST, 'annee', FILTER_VALIDATE_INT);

        if (!$nom || !$prenom || !$email || $mdp === '' || !$tel || !$adresse || !$jour || !$mois || !$annee) {
            $this->message('Certains champs sont invalides ou vides.');
            $this->redirect('/coordon/register');
        }

        if (!preg_match('/^[0-9+\-\s]+$/', $tel)) {
            $this->message('Numéro de téléphone invalide.');
            $this->redirect('/coordon/register');
        }

        if (!checkdate($mois, $jour, $annee)) {
            $this->message('Date de naissance invalide.');
            $this->redirect('/coordon/register');
        }

        $sexeRaw = mb_strtoupper(trim($_POST['sexe'] ?? ''), 'UTF-8');
        $sexe = match ($sexeRaw) {
            'HOMME', 'M', 'MASCULIN' => 'M',
            'FEMME', 'F', 'FÉMININ', 'FEMININ' => 'F',
            default => null,
        };

        if (!$sexe) {
            $this->message('Sexe invalide.');
            $this->redirect('/coordon/register');
        }

        $equipeMap = [
            'COORDINATION' => 'coordination',
            'ENCADREMENT' => 'encadreur',
            'FINANCE' => 'finance',
            'LOGISTIQUE' => 'logistique',
            'DISCIPLINE' => 'discipline',
            'CORDON' => 'cordon',
        ];
        $equipeRaw = mb_strtoupper(trim($_POST['equipe'] ?? ''), 'UTF-8');
        if (!isset($equipeMap[$equipeRaw])) {
            $this->message('Équipe invalide.');
            $this->redirect('/coordon/register');
        }

        $role = $this->role->findByName($equipeMap[$equipeRaw]);
        if (!$role) {
            $this->message('Rôle introuvable en base.');
            $this->redirect('/coordon/register');
        }

        if ($this->user->findByEmail($email)) {
            $this->message('Cet email est déjà utilisé.');
            $this->redirect('/coordon/register');
        }

        $name = trim($nom . ' ' . $prenom);
        $this->user->insert([
            'role_id' => (int) $role['id'],
            'name' => $name,
            'email' => $email,
            'password' => password_hash($mdp, PASSWORD_BCRYPT),
            'phone' => $tel,
            'address' => $adresse,
            'sexe' => $sexe,
        ]);

        $this->logger->store((int) $actor->id, 'inscription', "Ajout du membre {$name} ({$role['name']})");
        $_SESSION['message_inscripttion'] = 'Membre ajouté avec succès !';
        $this->redirect('/cordon');
    }

    public function logout(): void
    {
        $user = current_user();
        if ($user) {
            $this->logger->store((int) $user->id, 'logout', 'Déconnexion');
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ' . \Router\Router::route('/'));
        exit;
    }
}
