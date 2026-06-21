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
    if(!isset($_SESSION['id_enc']) || ($_SESSION['role'] ?? '') !== 'finance'){
        f_erreur_400("authentification");
    }

    $id_enc = $_SESSION['id_enc'];

    function require_positive_float($value, string $field): void {
        if($value === false || $value === null || $value <= 0){
            f_erreur_400($field);
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            switch($action){
                case 'add_input':
                    $source = filter_input(INPUT_POST, 'source', FILTER_SANITIZE_SPECIAL_CHARS);
                    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                    verification_parametre($source, 'source');
                    require_positive_float($amount, 'amount');

                    $stmt_insert = $db->prepare("
                        INSERT INTO finance_inputs (id_encadreur, source_input, amount_input)
                        VALUES (:id_enc, :source, :amount)
                    ");
                    $stmt_insert->execute([":id_enc" => $id_enc, ":source" => $source, ":amount" => $amount]);
                    app_log($db, 'finance', 'enregistrement', "Ajout d'une entrée complémentaire: {$source} ({$amount} $)", 'finance_inputs', (int)$db->lastInsertId());

                    echo json_encode([
                        "status" => true,
                        "message" => "Entrée ajoutée avec succès"
                    ]);
                    break;

                case 'delete_input':
                    $id_input = filter_input(INPUT_POST, 'id_input', FILTER_VALIDATE_INT);
                    verification_parametre($id_input, 'id_input');

                    $stmt_check = $db->prepare("SELECT id_input FROM finance_inputs WHERE id_input = :id_input AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_input" => $id_input, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("input_not_found");
                    }

                    $stmt_delete = $db->prepare("DELETE FROM finance_inputs WHERE id_input = :id_input");
                    $stmt_delete->execute([":id_input" => $id_input]);
                    app_log($db, 'finance', 'suppression', "Suppression d'une entrée complémentaire", 'finance_inputs', $id_input);

                    echo json_encode([
                        "status" => true,
                        "message" => "Entrée supprimée avec succès"
                    ]);
                    break;

                case 'add_forecast':
                    $commission = filter_input(INPUT_POST, 'commission', FILTER_SANITIZE_SPECIAL_CHARS);
                    $budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);
                    verification_parametre($commission, 'commission');
                    require_positive_float($budget, 'budget');

                    $stmt_insert = $db->prepare("
                        INSERT INTO finance_forecasts (id_encadreur, commission_forecast, budget_forecast)
                        VALUES (:id_enc, :commission, :budget)
                    ");
                    $stmt_insert->execute([":id_enc" => $id_enc, ":commission" => $commission, ":budget" => $budget]);
                    $forecast_id = (int)$db->lastInsertId();

                    $stmt_prevision = $db->prepare("
                        INSERT INTO prevision_depense (id_financier, commission, budget)
                        VALUES (:id_enc, :commission, :budget)
                    ");
                    $stmt_prevision->execute([":id_enc" => $id_enc, ":commission" => $commission, ":budget" => $budget]);
                    app_log($db, 'finance', 'enregistrement', "Ajout d'une prévision: {$commission} ({$budget} $)", 'finance_forecasts', $forecast_id);

                    echo json_encode([
                        "status" => true,
                        "message" => "Prévision ajoutée avec succès"
                    ]);
                    break;

                case 'delete_forecast':
                    $id_forecast = filter_input(INPUT_POST, 'id_forecast', FILTER_VALIDATE_INT);
                    verification_parametre($id_forecast, 'id_forecast');

                    $stmt_check = $db->prepare("SELECT id_forecast FROM finance_forecasts WHERE id_forecast = :id_forecast AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_forecast" => $id_forecast, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("forecast_not_found");
                    }

                    $stmt_delete = $db->prepare("DELETE FROM finance_forecasts WHERE id_forecast = :id_forecast");
                    $stmt_delete->execute([":id_forecast" => $id_forecast]);
                    app_log($db, 'finance', 'suppression', "Suppression d'une prévision de dépense", 'finance_forecasts', $id_forecast);

                    echo json_encode([
                        "status" => true,
                        "message" => "Prévision supprimée avec succès"
                    ]);
                    break;

                case 'add_actual':
                    $commission = filter_input(INPUT_POST, 'commission', FILTER_SANITIZE_SPECIAL_CHARS);
                    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                    verification_parametre($commission, 'commission');
                    require_positive_float($amount, 'amount');

                    $stmt_insert = $db->prepare("
                        INSERT INTO finance_actuals (id_encadreur, commission_actual, amount_actual)
                        VALUES (:id_enc, :commission, :amount)
                    ");
                    $stmt_insert->execute([":id_enc" => $id_enc, ":commission" => $commission, ":amount" => $amount]);
                    $actual_id = (int)$db->lastInsertId();

                    $stmt_depense = $db->prepare("
                        INSERT INTO depense_reelles (id_financier, commission_depense_relle, budget_depense_rel)
                        VALUES (:id_enc, :commission, :amount)
                    ");
                    $stmt_depense->execute([":id_enc" => $id_enc, ":commission" => $commission, ":amount" => $amount]);
                    app_log($db, 'finance', 'achat', "Ajout d'une dépense réelle: {$commission} ({$amount} $)", 'finance_actuals', $actual_id);

                    echo json_encode([
                        "status" => true,
                        "message" => "Dépense ajoutée avec succès"
                    ]);
                    break;

                case 'delete_actual':
                    $id_actual = filter_input(INPUT_POST, 'id_actual', FILTER_VALIDATE_INT);
                    verification_parametre($id_actual, 'id_actual');

                    $stmt_check = $db->prepare("SELECT id_actual FROM finance_actuals WHERE id_actual = :id_actual AND id_encadreur = :id_enc");
                    $stmt_check->execute([":id_actual" => $id_actual, ":id_enc" => $id_enc]);
                    if(!$stmt_check->fetch()){
                        f_erreur_400("actual_not_found");
                    }

                    $stmt_delete = $db->prepare("DELETE FROM finance_actuals WHERE id_actual = :id_actual");
                    $stmt_delete->execute([":id_actual" => $id_actual]);
                    app_log($db, 'finance', 'suppression', "Suppression d'une dépense réelle", 'finance_actuals', $id_actual);

                    echo json_encode([
                        "status" => true,
                        "message" => "Dépense supprimée avec succès"
                    ]);
                    break;

                case 'set_participant_status':
                    $id_participant = filter_input(INPUT_POST, 'id_participant', FILTER_VALIDATE_INT);
                    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
                    verification_parametre($id_participant, 'id_participant');
                    verification_parametre($status, 'status');

                    if(!in_array($status, ['confirme', 'deconfirme'], true)){
                        f_erreur_400('status');
                    }

                    $stmt_part = $db->prepare("
                        SELECT p.nom_part, p.montant_part, CONCAT(e.nom_enc, ' ', e.prenom_enc) AS encadreur_name
                        FROM participants p
                        JOIN table_encadreur e ON e.id_enc = p.id_encadreur
                        WHERE p.id_part = :id_part
                    ");
                    $stmt_part->execute([":id_part" => $id_participant]);
                    $participant = $stmt_part->fetch(PDO::FETCH_ASSOC);
                    if(!$participant){
                        f_erreur_400("participant_not_found");
                    }

                    $stmt_update = $db->prepare("
                        UPDATE participants
                        SET finance_status = :status,
                            finance_validated_by = :id_enc,
                            finance_validated_at = CURRENT_TIMESTAMP
                        WHERE id_part = :id_part
                    ");
                    $stmt_update->execute([
                        ":status" => $status,
                        ":id_enc" => $id_enc,
                        ":id_part" => $id_participant
                    ]);

                    $label = $status === 'confirme' ? 'Confirmation' : 'Déconfirmation';
                    app_log(
                        $db,
                        'finance',
                        $status,
                        "{$label} du participant {$participant['nom_part']} inscrit par {$participant['encadreur_name']} ({$participant['montant_part']} $)",
                        'participants',
                        $id_participant
                    );

                    echo json_encode([
                        "status" => true,
                        "message" => "Statut financier mis à jour"
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
