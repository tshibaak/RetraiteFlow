<?php

use App\View;
use Router\Router;

Router::get('/',function(){
    View::view('auth.login');
});

Router::post('/login',function(){
  require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_login.php';
});

Router::get('/register',function(){
    View::view('auth.register');
});

Router::get('/logout',function(){
   require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_logout.php';
});

Router::get('/cordon',function(){
   View::view('cordon');
});

Router::get('/discipline',function(){
   View::view('discipline');
});

Router::get('/encadreur',function(){
   View::view('encadreur');
});

Router::get('/finance',function(){
   View::view('finance');
});

Router::get('/logistique',function(){
   View::view('logistique');
});

?>