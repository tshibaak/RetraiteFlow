<?php

    // --- mes différentes fonctions --- //

    function f_erreur_400($level = ""){
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "message" => "Certains champs sont invalides ou vide",
            "niv" => $level
        ]);
        exit;
    }

    function f_erreur_405(){
        http_response_code(405);

        echo json_encode([
            "status" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    function verification_parametre($var , $level_param = ""){
        if( !isset($var) || empty($var) ){
            f_erreur_400($level_param);
        }
    }

    function h($value){
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    function current_user_name(){
        $nom = trim(($_SESSION['nom_enc'] ?? '') . ' ' . ($_SESSION['prenom_enc'] ?? ''));
        return $nom !== '' ? $nom : ($_SESSION['mail_enc'] ?? 'Utilisateur');
    }

    function app_log(PDO $db, string $module, string $action, string $description, ?string $target_table = null, ?int $target_id = null){
        if(!isset($_SESSION['id_enc'])){
            return;
        }

        try{
            $stmt = $db->prepare("
                INSERT INTO app_logs
                    (actor_id, actor_role, actor_name, module, action_type, description, target_table, target_id)
                VALUES
                    (:actor_id, :actor_role, :actor_name, :module, :action_type, :description, :target_table, :target_id)
            ");
            $stmt->execute([
                ':actor_id' => $_SESSION['id_enc'],
                ':actor_role' => $_SESSION['role'] ?? '',
                ':actor_name' => current_user_name(),
                ':module' => $module,
                ':action_type' => $action,
                ':description' => $description,
                ':target_table' => $target_table,
                ':target_id' => $target_id
            ]);
        }catch(PDOException $e){
            // L'historique ne doit jamais bloquer l'action principale.
        }
    }

    function fetch_activity_logs(PDO $db, string $scope, ?int $actor_id = null, int $limit = 80): array{
        try{
            if($scope === 'all'){
                $stmt = $db->prepare("
                    SELECT *
                    FROM app_logs
                    ORDER BY created_at DESC, id_log DESC
                    LIMIT :limit_rows
                ");
            }elseif($scope === 'encadreurs'){
                $stmt = $db->prepare("
                    SELECT *
                    FROM app_logs
                    WHERE actor_role = 'encadreur'
                    ORDER BY created_at DESC, id_log DESC
                    LIMIT :limit_rows
                ");
            }else{
                $stmt = $db->prepare("
                    SELECT *
                    FROM app_logs
                    WHERE actor_id = :actor_id
                    ORDER BY created_at DESC, id_log DESC
                    LIMIT :limit_rows
                ");
                $stmt->bindValue(':actor_id', $actor_id, PDO::PARAM_INT);
            }

            $stmt->bindValue(':limit_rows', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            return [];
        }
    }

    function auto_assign_logistique(PDO $db): void{
        try{
            $participants_stmt = $db->query("
                SELECT id_part, sexe_part, age_part
                FROM participants
                ORDER BY nom_part ASC, id_part ASC
            ");
            $participants = $participants_stmt->fetchAll(PDO::FETCH_ASSOC);

            $dortoirs_stmt = $db->query("
                SELECT id_dortoir, sexe_dortoir, age_min_dortoir, age_max_dortoir, capacite_dortoir
                FROM logistique_dortoirs
                ORDER BY nom_dortoir ASC, id_dortoir ASC
            ");
            $dortoirs = $dortoirs_stmt->fetchAll(PDO::FETCH_ASSOC);

            $ateliers_stmt = $db->query("
                SELECT id_atelier, age_min_atelier, age_max_atelier, capacite_atelier
                FROM logistique_ateliers
                ORDER BY nom_atelier ASC, id_atelier ASC
            ");
            $ateliers = $ateliers_stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->exec("UPDATE participants SET dortoir_id = NULL, atelier_id = NULL");

            $used_dortoirs = [];
            $used_ateliers = [];

            foreach($participants as $participant){
                foreach($dortoirs as $dortoir){
                    $id = (int)$dortoir['id_dortoir'];
                    $used = $used_dortoirs[$id] ?? 0;
                    if(
                        $used < (int)$dortoir['capacite_dortoir']
                        && $participant['sexe_part'] === $dortoir['sexe_dortoir']
                        && (int)$participant['age_part'] >= (int)$dortoir['age_min_dortoir']
                        && (int)$participant['age_part'] <= (int)$dortoir['age_max_dortoir']
                    ){
                        $stmt = $db->prepare("UPDATE participants SET dortoir_id = :dortoir_id WHERE id_part = :id_part");
                        $stmt->execute([':dortoir_id' => $id, ':id_part' => $participant['id_part']]);
                        $used_dortoirs[$id] = $used + 1;
                        break;
                    }
                }

                foreach($ateliers as $atelier){
                    $id = (int)$atelier['id_atelier'];
                    $used = $used_ateliers[$id] ?? 0;
                    if(
                        $used < (int)$atelier['capacite_atelier']
                        && (int)$participant['age_part'] >= (int)$atelier['age_min_atelier']
                        && (int)$participant['age_part'] <= (int)$atelier['age_max_atelier']
                    ){
                        $stmt = $db->prepare("UPDATE participants SET atelier_id = :atelier_id WHERE id_part = :id_part");
                        $stmt->execute([':atelier_id' => $id, ':id_part' => $participant['id_part']]);
                        $used_ateliers[$id] = $used + 1;
                        break;
                    }
                }
            }
        }catch(PDOException $e){
            // La répartition automatique ne doit pas bloquer l'enregistrement principal.
        }
    }

    function ensure_finance_status_table(PDO $db): void{
        try{
            $db->exec("
                CREATE TABLE IF NOT EXISTS finance_participant_status (
                    id_status INT AUTO_INCREMENT PRIMARY KEY,
                    participant_id INT NOT NULL,
                    finance_status ENUM('en_attente', 'confirme', 'deconfirme') DEFAULT 'en_attente',
                    validated_by INT DEFAULT NULL,
                    validated_at TIMESTAMP NULL DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_finance_participant_status_participant (participant_id),
                    INDEX idx_finance_participant_status_status (finance_status),
                    CONSTRAINT fk_finance_participant_status_participant
                        FOREIGN KEY (participant_id)
                        REFERENCES participants(id_part)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE,
                    CONSTRAINT fk_finance_participant_status_validator
                        FOREIGN KEY (validated_by)
                        REFERENCES table_encadreur(id_enc)
                        ON DELETE SET NULL
                        ON UPDATE CASCADE
                )
            ");
        }catch(PDOException $e){
            // Si la table existe déjà ou si le moteur refuse la DDL, on laisse l'appelant continuer.
        }
    }
