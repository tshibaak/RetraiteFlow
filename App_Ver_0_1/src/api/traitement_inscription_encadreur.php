<?php
    session_start();
    //header("Content-Type: application/json; charset=UTF-8");
    //header("Access-Control-Allow-Origin: *");

    // --- permet de capturer et afficher les erreurs php invisibles --- //
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once '../config/database.php';
    require '../lib/funcstd.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $message_niv = [
            "nom","prenom","email","mdp","tel","jour","moi","annee","DN","adresse","sexe","role"
        ];


        // -- recuperation des données des différents paramettre --- 

        $nom_enc = filter_input(INPUT_POST,'nom',FILTER_SANITIZE_SPECIAL_CHARS);
        $prenom_enc = filter_input(INPUT_POST,'prenom',FILTER_SANITIZE_SPECIAL_CHARS);
        $email_enc = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
        $tel_enc = filter_input(INPUT_POST,'telephone',FILTER_SANITIZE_SPECIAL_CHARS);
        $date_jour_enc =  filter_input(INPUT_POST,'jour',FILTER_VALIDATE_INT);
        $date_mois_enc = filter_input(INPUT_POST,'mois',FILTER_VALIDATE_INT);
        $date_annee_enc = filter_input(INPUT_POST,'annee',FILTER_VALIDATE_INT);
        $adresse_enc = filter_input(INPUT_POST,'adresse',FILTER_SANITIZE_SPECIAL_CHARS);
        $mdp_enc = $_POST['mdp'];

        verification_parametre($nom_enc , $message_niv[0]);
        verification_parametre($prenom_enc, $message_niv[1]);
        verification_parametre($email_enc, $message_niv[2]);
        verification_parametre($mdp_enc, $message_niv[3]);
        
        verification_parametre($tel_enc, $message_niv[4]);
        if(!preg_match('/^[0-9+\-\s]+$/', $tel_enc)){
            f_erreur_400($message_niv[4]);
        }
        
        verification_parametre($date_jour_enc, $message_niv[5]);
        verification_parametre($date_mois_enc, $message_niv[6]);
        verification_parametre($date_annee_enc, $message_niv[7]);
        if(!checkdate($date_mois_enc,$date_jour_enc,$date_annee_enc)){
            f_erreur_400($message_niv[8]);
        }
        $date_naissance = (string)"$date_annee_enc-$date_mois_enc-$date_jour_enc";
        verification_parametre($date_naissance, $message_niv[8]);

        verification_parametre($adresse_enc, $message_niv[9]);

        if(isset($_POST['sexe']) && !empty($_POST['sexe'])){
            if( mb_strtoupper(trim($_POST['sexe'])) == 'HOMME'){
                $sexe_enc = 'M';
                //$sexe_enc = filter_input(INPUT_POST,'sexe',FILTER_SANITIZE_SPECIAL_CHARS);
            }else if(mb_strtoupper(trim($_POST['sexe'])) == 'FEMME'){
                $sexe_enc = 'F';
            }else{
                f_erreur_400($message_niv[10]);
            }
        }else{
            f_erreur_400($message_niv[10]);
        }


        if(isset($_POST['equipe']) && !empty($_POST['equipe'])){
            if(mb_strtoupper(trim($_POST['equipe'])) == 'COORDINATION'){
                $role_enc = 'coordination';
                //$role_enc = filter_input(INPUT_POST,'equipe',FILTER_SANITIZE_SPECIAL_CHARS);
            }
            else if(mb_strtoupper(trim($_POST['equipe'])) == 'ENCADREMENT'){
                $role_enc = 'encadreur';
            }
            else if(mb_strtoupper(trim($_POST['equipe'])) == 'FINANCE'){
                $role_enc = 'finance';
            }
            else if(mb_strtoupper(trim($_POST['equipe'])) == 'LOGISTIQUE'){
                $role_enc = 'logistique';
            }
            else{
                f_erreur_400($message_niv[11]);
            }

        }else{
            f_erreur_400($message_niv[11]);
        }
            

        // --- envoi des données dans la BDD --- //

        try{
            $requette_insertion = "insert into table_encadreur(nom_enc,prenom_enc,mdp_enc,mail_enc,date_naissance_enc,sex_enc,role,adresse,tel_enc) 
                                    values(:nom_enc,:prenom_enc,:mdp_enc,:mail_enc,:date_naissance_enc,:sex_enc,:role,:adresse,:tel_enc)";
            $execution_req = $db->prepare($requette_insertion);
            $execution_req->execute([
                ":nom_enc" => $nom_enc,
                ":prenom_enc"=> $prenom_enc,
                ":mdp_enc"=> $mdp_enc,
                ":mail_enc"=> $email_enc,
                ":date_naissance_enc"=>$date_naissance,
                ":sex_enc"=> $sexe_enc,
                ":role"=> $role_enc,
                ":adresse" => $adresse_enc,
                ":tel_enc" => $tel_enc
            ]);

            $requette_verification = "select count(*) from table_encadreur where mail_enc = :mail_enc ";
            $execution_req_verif = $db->prepare($requette_verification);
            $execution_req_verif->execute([
                ":mail_enc"=> $email_enc
            ]);

            $count_verif = $execution_req_verif->fetchColumn();
            if( $count_verif > 0 ){
                $_SESSION['message_inscripttion'] = "inscription reussi !";
                header("Location: ../../public/assets/pages/login.php");
                exit();
            }else{
                $_SESSION['message_inscripttion'] = "inscription échouer !";
                header("Location: ../../public/assets/pages/login.php");
                exit();
            }

        }catch(PDOException $e){
            die("Erreur lors de l'insertion des donnés : " .$e->getMessage());
        }

    }else{
        f_erreur_405();
    }
 