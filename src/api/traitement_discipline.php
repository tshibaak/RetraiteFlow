<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'funcstd.php';

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
                case 'mark_entry':
                    $id_participant = filter_input(INPUT_POST, 'id_participant', FILTER_VALIDATE_INT);
                    verification_parametre($id_participant, 'id_participant');

                    // Vérifier que le participant appartient à cet encadreur
                    $stmt_check = $db->prepare("SELECT id_part FROM participants WHERE id_part = :id_part AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_part" => $id_participant, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("participant_not_found");
                    }

                    // Enregistrer l'entrée
                    $stmt_insert = $db->prepare("INSERT INTO discipline_logs (id_participant, type_log) VALUES (:id_part, 'entree')");
                    $stmt_insert->execute([":id_part" => $id_participant]);
                    app_log($db, 'discipline', 'enregistrement', "Entrée enregistrée pour le participant #{$id_participant}", 'discipline_logs', (int)$db->lastInsertId());

                    echo json_encode([
                        "status" => true,
                        "message" => "Entrée enregistrée avec succès"
                    ]);
                    break;

                case 'mark_exit':
                    $id_participant = filter_input(INPUT_POST, 'id_participant', FILTER_VALIDATE_INT);
                    verification_parametre($id_participant, 'id_participant');

                    // Vérifier que le participant appartient à cet encadreur
                    $stmt_check = $db->prepare("SELECT id_part FROM participants WHERE id_part = :id_part AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_part" => $id_participant, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("participant_not_found");
                    }

                    // Enregistrer la sortie
                    $stmt_insert = $db->prepare("INSERT INTO discipline_logs (id_participant, type_log) VALUES (:id_part, 'sortie')");
                    $stmt_insert->execute([":id_part" => $id_participant]);
                    app_log($db, 'discipline', 'enregistrement', "Sortie enregistrée pour le participant #{$id_participant}", 'discipline_logs', (int)$db->lastInsertId());

                    echo json_encode([
                        "status" => true,
                        "message" => "Sortie enregistrée avec succès"
                    ]);
                    break;

                case 'get_logs':
                    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
                    
                    $stmt_logs = $db->prepare("
                        SELECT dl.*, p.nom_part, p.groupe_part, p.commission_part, p.sexe_part
                        FROM discipline_logs dl
                        JOIN participants p ON dl.id_participant = p.id_part
                        WHERE p.id_encadreur = :id_enc
                        AND DATE(dl.logged_at) = :date
                        ORDER BY dl.logged_at DESC
                    ");
                    $stmt_logs->execute([":id_enc" => $id_enc, ":date" => $date]);
                    $logs = $stmt_logs->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode([
                        "status" => true,
                        "logs" => $logs
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
