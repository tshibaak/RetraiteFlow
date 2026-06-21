<?php

use App\View;
use Router\Router;

Router::get('/', function () {
    View::view('auth.login');
});

Router::post('/login', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_login.php';
});

Router::get('/coordon/register', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['id_enc']) || !in_array($_SESSION['role'] ?? '', ['coordination', 'cordon'], true)) {
        header('Location: ' . Router::route('/'));
        exit();
    }
    View::view('auth.register');
});

Router::post('/coordon/register', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_inscription_encadreur.php';
});

Router::get('/logout', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_logout.php';
});

Router::get('/cordon', function () {
    View::view('cordon');
});

Router::get('/discipline', function () {
    View::view('discipline');
});

Router::get('/encadreur', function () {
    View::view('encadreur');
});

Router::post('/encadreur', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_gest_encadreur.php';
});

Router::get('/finance', function () {
    View::view('finance');
});

Router::get('/logistique', function () {
    View::view('logistique');
});

Router::post('/api/finance', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_finance.php';
});

Router::post('/api/discipline', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_discipline.php';
});

Router::post('/api/logistique', function () {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/api/traitement_logistique.php';
});

?>
