<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'funcstd.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);      

    if($_SERVER['REQUEST_METHOD'] == "POST"){


        // --- récuperation et vérrification des données  --- //

        $adresse_email_login_enc = filter_input(INPUT_POST,'username',FILTER_VALIDATE_EMAIL);
        $mdp_login_enc = $_POST['password'];
        $role_login_enc = filter_input(INPUT_POST,'role',FILTER_SANITIZE_SPECIAL_CHARS);

        verification_parametre($adresse_email_login_enc );
        verification_parametre($mdp_login_enc);
        verification_parametre($role_login_enc);

        try{

            // --- verification que le compte existe pour se connecter --- //

            $requette_verification = " select count(*) from table_encadreur where mail_enc = :mail_enc and mdp_enc = :mdp_enc ";
            $execution_req_verification = $db->prepare($requette_verification);
            $execution_req_verification->execute([
                ":mail_enc" => $adresse_email_login_enc,
                ":mdp_enc" => $mdp_login_enc
            ]);

            $count_verif = $execution_req_verification->fetchColumn();
            if($count_verif > 0){

                // --- requette pour recupérer NOM , PRENOM , MAIL et ROLE --- //

                $requette_recup_1 = "
                    select id_enc , nom_enc , prenom_enc , mail_enc , role , mdp_enc
                    from table_encadreur
                    where mail_enc = :mail_enc
                    AND (
                        role = :role_login
                        OR (:role_login = 'cordon' AND role = 'coordination')
                    )
                ";
                $execution_req_recup_1 = $db->prepare($requette_recup_1);
                $execution_req_recup_1->execute([
                    ":mail_enc" => $adresse_email_login_enc,
                    ":role_login" => $role_login_enc
                ]);

                $encadreur = $execution_req_recup_1->fetch();

                if($encadreur){

                    if(strcmp($mdp_login_enc, $encadreur['mdp_enc']) == 0){

                        $id = $encadreur['id_enc'];
                        $nom = $encadreur['nom_enc'];
                        $prenom = $encadreur['prenom_enc'];
                        $mail = $encadreur['mail_enc'];
                        $role = $role_login_enc === 'cordon' ? 'cordon' : $encadreur['role'];

                        $_SESSION['id_enc'] = $id;
                        $_SESSION['nom_enc'] = $nom;
                        $_SESSION['prenom_enc'] = $prenom ;
                        $_SESSION['mail_enc'] = $mail;
                        $_SESSION['role'] = $role;

                        app_log($db, 'authentification', 'connexion', 'Connexion au compte ' . $role);

                        if($role == 'encadreur'){
                            header("Location: ../../public/assets/pages/encadreur.php");
                            exit();
                        }elseif($role == "finance"){
                            header("Location: ../../public/assets/pages/finance.php");
                            exit();
                        }elseif($role == "logistique"){
                            header("Location: ../../public/assets/pages/logistique.php");
                            exit();
                        }elseif($role == "discipline"){
                            header("Location: ../../public/assets/pages/discipline.php");
                            exit();
                        }elseif($role =="coordination" || $role == "cordon"){
                            header("Location: ../../public/assets/pages/cordon.php");
                            exit();
                        }else{
                            $_SESSION['message'] = "Rôle non reconnu";
                            header("Location: ../../public/assets/pages/login.php");
                            exit();}

                    }else{
                        $_SESSION['message'] = "Mot de passe incorrect";
                        header("Location: ../../public/assets/pages/login.php");
                        exit();
                    }

                }

            }else{
                $_SESSION['message'] = "utilisateur non éxistant";
                header("Location: ../../public/assets/pages/login.php");
                exit();                
            }

        }catch(PDOException $e){
            die("Erreur lors de la lecture des données : " .$e->getMessage());
        }



    }else{
        f_erreur_405();
    }
