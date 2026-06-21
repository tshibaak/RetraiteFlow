<?php

if (!isset($db)) {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'retraiteflow';
    $user = $_ENV['DB_USER'] ?? 'adminuser';
    $pass = $_ENV['DB_MDP'] ?? 'Velonica9';

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
}
