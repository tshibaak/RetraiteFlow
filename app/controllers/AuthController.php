<?php
namespace App\Controllers;

use App\Models\User;
use App\View;
use Router\Router;

class AuthController extends Controller
{
    protected User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        View::view('auth.login');
    }

  public function login()
  {
      $datas = $_POST;
      $email = $this->sanitaze($datas['email'] ?? '');
      $password = $this->sanitaze($datas['password'] ?? '');

      if ($email === "" || $password === "") {
         $this->message('Veuillez remplir tous les champs correctement.');
         $this->redirect('/');
         return;
      }

      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $this->message('Email incorrect');
          $this->redirect('/');
          return;
      }
  
      if (empty($password) || strlen($password) < 8) {
        $this->message('Mot de passe trop court ou vide');
        $this->redirect('/');
          return;
      }
   
      $user = $this->user->findByEmail($email,\PDO::FETCH_OBJ);
  
      if ($user && password_verify($password, $user->password)) {
          // Protection contre les attaques timing (double vérification)
          if (!hash_equals($user->password, crypt($password, $user->password))) {
              $this->redirect('/');
              return;
          }

          // Stocker l'utilisateur en session
          $_SESSION['user'] = $user;
  
          // Redirection selon le rôle
          $this->redirect(Router::route('/'.$user->role_name));
          return;
      }
  
      // Si échec
      $this->redirect('/');
      return;
  }


    // Création de compte
    public function register(string $name, string $email, string $password)
    {
        // Vérifier si l’email existe déjà
        $existing = $this->user->findByEmail($email);
        if ($existing) {
            return false; // email déjà utilisé
        }

        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insérer dans la base
        return $this->user->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function logout()
    {
        // Détruire la session
        $_SESSION = array();
    
        // Détruire le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    
        session_destroy();
        $this->redirect('/');
    }
}
