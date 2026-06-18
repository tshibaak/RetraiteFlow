<?php

    $host = 'localhost';
    $dbname = 'retraiteflow';
    $user = 'adminuser';
    $pass = 'Velonica9';

    try{
        $db = new PDO ("mysql:host=$host;dbname=$dbname;charset=utf8" , $user , $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        die("Erreur de connexion : " .$e->getMessage());
    }
