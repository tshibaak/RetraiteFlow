<?php
    session_start();
    require_once '../config/database.php';
    require '../lib/funcstd.php';

    header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier l'authentification
    if(!isset($_SESSION['id_enc'])){
        f_erreur_400("authentification");
    }

    $id_enc = $_SESSION['id_enc'];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            switch($action){
                case 'add_dortoir':
                    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
                    $sexe = filter_input(INPUT_POST, 'sexe', FILTER_SANITIZE_SPECIAL_CHARS);
                    $age_min = filter_input(INPUT_POST, 'age_min', FILTER_VALIDATE_INT);
                    $age_max = filter_input(INPUT_POST, 'age_max', FILTER_VALIDATE_INT);
                    $capacite = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);

                    verification_parametre($nom, 'nom');
                    verification_parametre($sexe, 'sexe');
                    verification_parametre($age_min, 'age_min');
                    verification_parametre($age_max, 'age_max');
                    verification_parametre($capacite, 'capacite');

                    $stmt_insert = $db->prepare("
                        INSERT INTO logistique_dortoirs 
                        (id_encadreur, nom_dortoir, sexe_dortoir, age_min_dortoir, age_max_dortoir, capacite_dortoir)
                        VALUES (:id_enc, :nom, :sexe, :age_min, :age_max, :capacite)
                    ");
                    $stmt_insert->execute([
                        ":id_enc" => $id_enc,
                        ":nom" => $nom,
                        ":sexe" => $sexe,
                        ":age_min" => $age_min,
                        ":age_max" => $age_max,
                        ":capacite" => $capacite
                    ]);
                    $new_id = (int)$db->lastInsertId();
                    auto_assign_logistique($db);
                    app_log($db, 'logistique', 'enregistrement', "Création du dortoir {$nom}", 'logistique_dortoirs', $new_id);

                    echo json_encode([
                        "status" => true,
                        "message" => "Dortoir créé avec succès"
                    ]);
                    break;

                case 'delete_dortoir':
                    $id_dortoir = filter_input(INPUT_POST, 'id_dortoir', FILTER_VALIDATE_INT);
                    verification_parametre($id_dortoir, 'id_dortoir');

                    $stmt_check = $db->prepare("SELECT id_dortoir FROM logistique_dortoirs WHERE id_dortoir = :id_dortoir AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_dortoir" => $id_dortoir, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("dortoir_not_found");
                    }

                    $stmt_delete = $db->prepare("DELETE FROM logistique_dortoirs WHERE id_dortoir = :id_dortoir");
                    $stmt_delete->execute([":id_dortoir" => $id_dortoir]);
                    auto_assign_logistique($db);
                    app_log($db, 'logistique', 'suppression', "Suppression d'un dortoir", 'logistique_dortoirs', $id_dortoir);

                    echo json_encode([
                        "status" => true,
                        "message" => "Dortoir supprimé avec succès"
                    ]);
                    break;

                case 'add_atelier':
                    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
                    $age_min = filter_input(INPUT_POST, 'age_min', FILTER_VALIDATE_INT);
                    $age_max = filter_input(INPUT_POST, 'age_max', FILTER_VALIDATE_INT);
                    $capacite = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);

                    verification_parametre($nom, 'nom');
                    verification_parametre($age_min, 'age_min');
                    verification_parametre($age_max, 'age_max');
                    verification_parametre($capacite, 'capacite');

                    $stmt_insert = $db->prepare("
                        INSERT INTO logistique_ateliers 
                        (id_encadreur, nom_atelier, age_min_atelier, age_max_atelier, capacite_atelier)
                        VALUES (:id_enc, :nom, :age_min, :age_max, :capacite)
                    ");
                    $stmt_insert->execute([
                        ":id_enc" => $id_enc,
                        ":nom" => $nom,
                        ":age_min" => $age_min,
                        ":age_max" => $age_max,
                        ":capacite" => $capacite
                    ]);
                    $new_id = (int)$db->lastInsertId();
                    auto_assign_logistique($db);
                    app_log($db, 'logistique', 'enregistrement', "Création de l'atelier {$nom}", 'logistique_ateliers', $new_id);

                    echo json_encode([
                        "status" => true,
                        "message" => "Atelier créé avec succès"
                    ]);
                    break;

                case 'delete_atelier':
                    $id_atelier = filter_input(INPUT_POST, 'id_atelier', FILTER_VALIDATE_INT);
                    verification_parametre($id_atelier, 'id_atelier');

                    $stmt_check = $db->prepare("SELECT id_atelier FROM logistique_ateliers WHERE id_atelier = :id_atelier AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_atelier" => $id_atelier, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("atelier_not_found");
                    }

                    $stmt_delete = $db->prepare("DELETE FROM logistique_ateliers WHERE id_atelier = :id_atelier");
                    $stmt_delete->execute([":id_atelier" => $id_atelier]);
                    auto_assign_logistique($db);
                    app_log($db, 'logistique', 'suppression', "Suppression d'un atelier", 'logistique_ateliers', $id_atelier);

                    echo json_encode([
                        "status" => true,
                        "message" => "Atelier supprimé avec succès"
                    ]);
                    break;

                default:
                    f_erreur_400("action_invalide");
            }
        } catch(PDOException $e){
            echo json_encode([
                "status" => false,
                "message" => "Erreur base de données: " . $e->getMessage()
            ]);
        }
    } else {
        f_erreur_405();
    }
?>
