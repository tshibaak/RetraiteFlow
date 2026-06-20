<?php
 namespace Router;

 use App\App;
use App\controllers\Controller;

   class Router
   {

      private static string $route_name = "";
      private static string $uri = '';
      private static $instance ;

      public function __construct()
      {
         self::$uri = $_ENV['APP_URL'] ?? '';
      }

      public static function Instance():self
      {
         if(self::$instance == null)
         {
            self::$instance = new Router();
         }
         return self::$instance;
        
      }

      public static function name(string $name)
      {
        self::$route_name = $name;
      }

      public static function route(string $route)
      {
          return self::$uri . $route ;
      }
       public static function get(string $route,mixed $target)
       {
          App::getInstanceRouter()->map('GET',$route,$target,self::$route_name);
       }

       public static function post(string $route, mixed $target)
       {
        App::getInstanceRouter()->map('POST',$route,$target,self::$route_name);
       }

       public static function delete(string $route, mixed $target, string $name = '')
       {
        App::getInstanceRouter()->map('DELETE',$route,$target,self::$route_name);
       }

       public static function put(string $route,mixed $target)
       {
        App::getInstanceRouter()->map('PUT',$route,$target,self::$route_name);
       }
       
       public static function origin($path)
       {
        App::getInstanceRouter()->setBasePath($path);
       }

       public static function matcher()
       {
          $match =App::getInstanceRouter()->match();
   
          if($match && is_callable($match['target']))
            {
            call_user_func($match['target'],$match['params']);
          }

          elseif(is_array($match) && is_array($match['target']))
          {
            $controller = $match['target'][0];
            $method = $match['target'][1];
            $controller = new $controller();
            $controller->$method($match['params']);
          }

          else
          {
             self::respondNotFound();
          }
       }

       private static function respondNotFound()
       {
         if(self::apiRequest()){
              Controller::status(404)->json([
                'status' => 404,
                'message' => 'api not found '
              ]);
              return;
           }

          self::respondWithError(404, 'La page que vous recherchez est introuvable.');
         return;
       }

       private static function apiRequest():bool
       {
           $url = $_SERVER['REQUEST_URI'] ?? '';
           $header = $_SERVER['HTTP_ACCEPT'] ?? '';
           return str_starts_with($url, '/api') || str_contains($header, 'application/json');
       }

       public static function respondWithError(int $status, string $message = '')
       {
           Controller::status($status);
           $statusText = $message ?: self::getStatusText($status);
        
           echo "<h1>$status</h1><p>$statusText</p>";
       }

       private static function getStatusText(int $statusCode): string
       {
           return match ($statusCode) {
               400 => 'Requête incorrecte.',
               401 => 'Accès non autorisé.',
               403 => 'Accès refusé.',
               404 => 'Page introuvable.',
               405 => 'Méthode non autorisée.',
               409 => 'Conflit de ressources.',
               410 => 'Ressource supprimée.',
               415 => 'Type de média non supporté.',
               419 => 'Token CSRF expiré.',
               422 => 'Entité non traitable.',
               500 => 'Erreur interne du serveur.',
               502 => 'Mauvaise passerelle.',
               503 => 'Service indisponible.',
               504 => 'Délai d’attente dépassé.',
               default => 'Une erreur est survenue.'
           };
       }

   }
?>