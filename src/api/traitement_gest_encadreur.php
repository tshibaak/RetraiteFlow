<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Router\Router;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'funcstd.php';

ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if(!isset($_SESSION['id_enc']) || ($_SESSION['role'] ?? '') !== 'encadreur'){
        die("Session invalide");
    }

    if($_SERVER['REQUEST_METHOD'] !== "POST"){
        f_erreur_405();
    }

    $id_enc = (int)$_SESSION['id_enc'];
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'save';

    function normalize_group_participant(string $groupe): string {
        $value = mb_strtoupper(trim($groupe), 'UTF-8');
        if($value === 'SOLVABLE'){
            return 'solvable';
        }
        if($value === 'ACCRÉDITÉ' || $value === 'ACCREDITE'){
            return 'accrédité';
        }
        if($value === 'CAS SOCIAL' || $value === 'CAS_SOCIAL'){
            return 'cas_social';
        }
        f_erreur_400('groupe');
    }

    function normalize_commission_participant(string $commission): string {
        $value = mb_strtoupper(trim($commission), 'UTF-8');
        $map = [
            'RIEN' => 'sans commission',
            'SANS COMMISSION' => 'sans commission',
            'DISCIPLINE' => 'discipline',
            'FINANCE' => 'finance',
            'LOGISTIQUE' => 'logistique',
            'NETTOYAGE' => 'nettoyage',
            'RESTAURATION' => 'restauration',
            'SANTÉ' => 'santé',
            'SANTE' => 'santé'
        ];

        if(isset($map[$value])){
            return $map[$value];
        }

        return trim($commission);
    }

    function normalize_sexe_participant(string $sexe): string {
        $value = mb_strtoupper(trim($sexe), 'UTF-8');
        if($value === 'MASCULIN' || $value === 'M'){
            return 'Masculin';
        }
        if($value === 'FÉMININ' || $value === 'FEMININ' || $value === 'F'){
            return 'Féminin';
        }
        f_erreur_400('sexe');
    }

    try{
        if($action === 'delete'){
            $participant_id = filter_input(INPUT_POST, 'participant_id', FILTER_VALIDATE_INT);
            verification_parametre($participant_id, 'participant_id');

            $stmt_check = $db->prepare("SELECT nom_part FROM participants WHERE id_part = :id_part AND id_encadreur = :id_enc");
            $stmt_check->execute([':id_part' => $participant_id, ':id_enc' => $id_enc]);
            $participant = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if(!$participant){
                die("Participant introuvable");
            }

            $stmt_delete = $db->prepare("DELETE FROM participants WHERE id_part = :id_part AND id_encadreur = :id_enc");
            $stmt_delete->execute([':id_part' => $participant_id, ':id_enc' => $id_enc]);
            auto_assign_logistique($db);
            app_log($db, 'encadreur', 'suppression', 'Suppression du participant ' . $participant['nom_part'], 'participants', $participant_id);

            $_SESSION['confirmation_ok'] = "participant supprimé !";
header("Location: " . Router::route('/encadreur'));
            exit();
        }

        $participant_id = filter_input(INPUT_POST, 'participant_id', FILTER_VALIDATE_INT);
        $noms_part = filter_input(INPUT_POST,'nom',FILTER_SANITIZE_SPECIAL_CHARS);
        $sexe_part = filter_input(INPUT_POST,'sexe',FILTER_SANITIZE_SPECIAL_CHARS);
        $age_part = filter_input(INPUT_POST,'age',FILTER_VALIDATE_INT);
        $groupe_post = filter_input(INPUT_POST,'groupe',FILTER_SANITIZE_SPECIAL_CHARS);
        $commission_post = filter_input(INPUT_POST,'commission',FILTER_SANITIZE_SPECIAL_CHARS);
        $tel_part = filter_input(INPUT_POST,'telephone',FILTER_SANITIZE_SPECIAL_CHARS);
        $paiement_part = filter_input(INPUT_POST,'montant',FILTER_VALIDATE_FLOAT);
        $duree_part = filter_input(INPUT_POST,'jours',FILTER_VALIDATE_INT);

        verification_parametre($noms_part, 'nom');
        verification_parametre($sexe_part, 'sexe');
        verification_parametre($age_part, 'age');
        verification_parametre($groupe_post, 'groupe');
        verification_parametre($commission_post, 'commission');
        verification_parametre($tel_part, 'telephone');
        verification_parametre($duree_part, 'jours');

        if(!preg_match('/^[0-9+\-\s]+$/', $tel_part)){
            f_erreur_400('telephone');
        }

        $groupe_part = normalize_group_participant($groupe_post);
        $sexe_part = normalize_sexe_participant($sexe_part);
        $commission_part = normalize_commission_participant($commission_post);

        if($groupe_part === 'cas_social'){
            $paiement_part = 0;
        }elseif($paiement_part === false || $paiement_part === null || $paiement_part < 0){
            f_erreur_400('montant');
        }

        if($participant_id){
            $stmt_check = $db->prepare("SELECT id_part FROM participants WHERE id_part = :id_part AND id_encadreur = :id_enc");
            $stmt_check->execute([':id_part' => $participant_id, ':id_enc' => $id_enc]);
            if(!$stmt_check->fetch()){
                die("Participant introuvable");
            }

            $stmt = $db->prepare("
                UPDATE participants
                SET nom_part = :nom_part,
                    sexe_part = :sexe_part,
                    age_part = :age_part,
                    groupe_part = :groupe_part,
                    commission_part = :commission_part,
                    telephone_part = :telephone_part,
                    montant_part = :montant_part,
                    jours_part = :jours_part
                WHERE id_part = :id_part
                AND id_encadreur = :id_enc
            ");
            $stmt->execute([
                ':nom_part' => $noms_part,
                ':sexe_part' => $sexe_part,
                ':age_part' => $age_part,
                ':groupe_part' => $groupe_part,
                ':commission_part' => $commission_part,
                ':telephone_part' => $tel_part,
                ':montant_part' => $paiement_part,
                ':jours_part' => $duree_part,
                ':id_part' => $participant_id,
                ':id_enc' => $id_enc
            ]);
            auto_assign_logistique($db);
            app_log($db, 'encadreur', 'modification', 'Modification du participant ' . $noms_part, 'participants', $participant_id);
            $_SESSION['confirmation_ok'] = "participant modifié !";
        }else{
            $stmt = $db->prepare("
                INSERT INTO participants
                    (id_encadreur, nom_part, sexe_part, age_part, groupe_part, commission_part, telephone_part, montant_part, jours_part)
                VALUES
                    (:id_encadreur, :nom_part, :sexe_part, :age_part, :groupe_part, :commission_part, :telephone_part, :montant_part, :jours_part)
            ");
            $stmt->execute([
                ':id_encadreur' => $id_enc,
                ':nom_part' => $noms_part,
                ':sexe_part' => $sexe_part,
                ':age_part' => $age_part,
                ':groupe_part' => $groupe_part,
                ':commission_part' => $commission_part,
                ':telephone_part' => $tel_part,
                ':montant_part' => $paiement_part,
                ':jours_part' => $duree_part
            ]);
            $new_id = (int)$db->lastInsertId();
            auto_assign_logistique($db);
            app_log($db, 'encadreur', 'enregistrement', 'Enregistrement du participant ' . $noms_part, 'participants', $new_id);
            $_SESSION['confirmation_ok'] = "inscription réussie !";
        }

        header("Location: " . Router::route('/encadreur'));
        exit();
    }catch(PDOException $e){
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
?>
