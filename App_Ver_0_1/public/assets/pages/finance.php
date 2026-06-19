<?php
    session_start();
    require_once '../../../src/config/database.php';
    require_once '../../../src/lib/funcstd.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier l'accès
    if(!isset($_SESSION['id_enc']) || $_SESSION['role'] !== 'finance'){
        die("Accès refusé");
    }

    $id_enc = $_SESSION['id_enc'];
    $nom_enc = $_SESSION['nom_enc'] . ' ' . $_SESSION['prenom_enc'];

    function fetch_sum(PDO $db, string $sql, array $params = []): float {
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return (float) ($stmt->fetchColumn() ?: 0);
        } catch (PDOException $e) {
            return 0.0;
        }
    }

    // Récupérer les statistiques financières
    $total_inputs = fetch_sum($db, "SELECT SUM(montant_part) FROM participants WHERE finance_status = 'confirme'");
    $total_actuals = fetch_sum($db, "SELECT SUM(amount_actual) FROM finance_actuals WHERE id_encadreur = :id_enc", [":id_enc" => $id_enc]);
    $total_forecasts = fetch_sum($db, "SELECT SUM(budget_forecast) FROM finance_forecasts WHERE id_encadreur = :id_enc", [":id_enc" => $id_enc]);
    $total_participants = (int) fetch_sum($db, "SELECT COUNT(*) FROM participants");
    $confirmed_participants = (int) fetch_sum($db, "SELECT COUNT(*) FROM participants WHERE finance_status = 'confirme'");
    $pending_participants = (int) fetch_sum($db, "SELECT COUNT(*) FROM participants WHERE finance_status = 'en_attente'");
    $rejected_participants = (int) fetch_sum($db, "SELECT COUNT(*) FROM participants WHERE finance_status = 'deconfirme'");

    $solde = $total_inputs - $total_actuals;
    $remaining_budget = $total_forecasts - $total_actuals;

    // Récupérer les données
    $stmt_all_inputs = $db->prepare("SELECT * FROM finance_inputs WHERE id_encadreur = :id_enc ORDER BY created_at DESC");
    $stmt_all_inputs->execute([":id_enc" => $id_enc]);
    $all_inputs = $stmt_all_inputs->fetchAll(PDO::FETCH_ASSOC);

    $stmt_all_forecasts = $db->prepare("SELECT * FROM finance_forecasts WHERE id_encadreur = :id_enc ORDER BY created_at DESC");
    $stmt_all_forecasts->execute([":id_enc" => $id_enc]);
    $all_forecasts = $stmt_all_forecasts->fetchAll(PDO::FETCH_ASSOC);

    $stmt_all_actuals = $db->prepare("SELECT * FROM finance_actuals WHERE id_encadreur = :id_enc ORDER BY created_at DESC");
    $stmt_all_actuals->execute([":id_enc" => $id_enc]);
    $all_actuals = $stmt_all_actuals->fetchAll(PDO::FETCH_ASSOC);

    $stmt_participants = $db->prepare("
        SELECT
            p.*,
            CONCAT(e.nom_enc, ' ', e.prenom_enc) AS encadreur_name,
            v.nom_enc AS validator_nom,
            v.prenom_enc AS validator_prenom
        FROM participants p
        JOIN table_encadreur e ON e.id_enc = p.id_encadreur
        LEFT JOIN table_encadreur v ON v.id_enc = p.finance_validated_by
        ORDER BY p.created_at DESC, p.nom_part ASC
    ");
    $stmt_participants->execute();
    $finance_participants = $stmt_participants->fetchAll(PDO::FETCH_ASSOC);

    $activity_logs = fetch_activity_logs($db, 'encadreurs', null, 80);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Finance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style_encadreur.css">
</head>

<body>
    <!-- Barre supérieure -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="user-menu-container">
                <button class="user-menu-btn" id="userMenuBtn">
                    <div class="user-avatar">
                        <span id="userInitials">F</span>
                    </div>
                    <span class="user-name" id="userName"><?php echo htmlspecialchars($nom_enc); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-info">
                        <div class="user-info-avatar">
                            <span id="userInfoInitials">F</span>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name" id="userInfoName"><?php echo htmlspecialchars($nom_enc); ?></div>
                            <div class="user-info-role" id="userInfoRole">Finance</div>
                        </div>
                    </div>
                    <div class="user-menu-divider"></div>
                    <a href="#" class="user-menu-item" id="accountSettings">
                        <i class="fas fa-user-cog"></i>
                        <span>Compte et paramètres</span>
                    </a>
                    <a href="/files/RetreatFlow/App_Ver_0_1/src/api/traitement_logout.php" class="user-menu-item" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="main-content">
        <header class="header">
            <h1>RetraiteFlow</h1>
            <p>Bienvenue, Finance • Entrées, prévisions, dépenses réelles et solde</p>
        </header>

        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statTotalInputs"><?php echo number_format($total_inputs, 2); ?> $</div>
                    <div class="stat-label">Entrées réelles confirmées</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-pink">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statTotalActualExpenses"><?php echo number_format($total_actuals, 2); ?> $</div>
                    <div class="stat-label">Dépenses réelles</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statSolde"><?php echo number_format($solde, 2); ?> $</div>
                    <div class="stat-label">Solde (confirmés - dépenses)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statRemainingBudget"><?php echo number_format($remaining_budget, 2); ?> $</div>
                    <div class="stat-label">Reste sur budget prévisionnel</div>
                </div>
            </div>
        </div>

        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Dashboard des participants</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportParticipantsBtn">
                        <i class="fas fa-download"></i>
                        Exporter
                    </button>
                </div>
            </div>

            <div class="stats-container compact-stats">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $total_participants; ?></div>
                        <div class="stat-label">Participants enregistrés</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-check"></i></div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $confirmed_participants; ?></div>
                        <div class="stat-label">Confirmés</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-yellow"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $pending_participants; ?></div>
                        <div class="stat-label">En attente</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-pink"><i class="fas fa-times"></i></div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $rejected_participants; ?></div>
                        <div class="stat-label">Déconfirmés</div>
                    </div>
                </div>
            </div>

            <div class="chart-grid">
                <div class="chart-panel">
                    <h3>Statut financier</h3>
                    <canvas id="financeStatusChart" width="420" height="220"
                        data-confirmed="<?php echo $confirmed_participants; ?>"
                        data-pending="<?php echo $pending_participants; ?>"
                        data-rejected="<?php echo $rejected_participants; ?>"></canvas>
                </div>
                <div class="chart-panel">
                    <h3>Budget</h3>
                    <canvas id="financeBudgetChart" width="420" height="220"
                        data-inputs="<?php echo $total_inputs; ?>"
                        data-actuals="<?php echo $total_actuals; ?>"
                        data-forecasts="<?php echo $total_forecasts; ?>"></canvas>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Encadreur</th>
                            <th>Groupe</th>
                            <th>Commission</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="financeParticipantsBody">
                        <?php if(count($finance_participants) === 0): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:24px; color:var(--muted);">Aucun participant enregistré</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($finance_participants as $p): ?>
                            <?php
                                $status = $p['finance_status'] ?? 'en_attente';
                                $status_label = $status === 'confirme' ? 'Confirmé' : ($status === 'deconfirme' ? 'Déconfirmé' : 'En attente');
                                $status_class = $status === 'confirme' ? 'badge-success' : ($status === 'deconfirme' ? 'badge-danger' : 'badge-warning');
                            ?>
                            <tr>
                                <td data-label="Participant">
                                    <div style="font-weight:700;"><?php echo h($p['nom_part']); ?></div>
                                    <div style="font-size:13px;color:var(--muted);"><?php echo h($p['age_part']); ?> ans • <?php echo h($p['sexe_part']); ?></div>
                                </td>
                                <td data-label="Encadreur"><?php echo h($p['encadreur_name']); ?></td>
                                <td data-label="Groupe"><?php echo h($p['groupe_part']); ?></td>
                                <td data-label="Commission"><?php echo h($p['commission_part']); ?></td>
                                <td data-label="Montant"><?php echo number_format((float)$p['montant_part'], 2); ?> $</td>
                                <td data-label="Statut">
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                                    <?php if(!empty($p['finance_validated_at'])): ?>
                                        <div style="font-size:12px;color:var(--muted);"><?php echo h($p['finance_validated_at']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Actions">
                                    <div class="actions actions-cell">
                                        <button class="btn-secondary finance-status-btn" data-id="<?php echo (int)$p['id_part']; ?>" data-status="confirme">
                                            <i class="fas fa-check"></i>
                                            Confirmer
                                        </button>
                                        <button class="btn-secondary finance-status-btn" data-id="<?php echo (int)$p['id_part']; ?>" data-status="deconfirme">
                                            <i class="fas fa-times"></i>
                                            Déconfirmer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Entrées -->
        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Entrées complémentaires</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportInputsBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter
                    </button>
                </div>
            </div>

            <div class="table-container" style="margin-bottom: 18px;">
                <form id="inputsForm" class="form-grid">
                    <div class="form-group">
                        <label for="inputSource"><i class="fas fa-tag"></i> Source</label>
                        <input id="inputSource" name="inputSource" type="text" placeholder="Ex: Paiements participants" required>
                    </div>
                    <div class="form-group">
                        <label for="inputAmount"><i class="fas fa-money-bill-wave"></i> Montant ($)</label>
                        <input id="inputAmount" name="inputAmount" type="number" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Ajouter une entrée
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Montant</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inputsTableBody">
                        <tr>
                            <td data-label="Source">
                                <strong>Participants confirmés</strong>
                                <div style="font-size: 12px; color: var(--muted);">Calculé depuis les confirmations du financier</div>
                            </td>
                            <td data-label="Montant"><?php echo number_format($total_inputs, 2); ?> $</td>
                            <td data-label="Actions">
                                <span style="font-size: 12px; color: var(--muted); font-weight: 600;">Auto</span>
                            </td>
                        </tr>
                        <?php foreach($all_inputs as $input): ?>
                        <tr>
                            <td data-label="Source"><?php echo htmlspecialchars($input['source_input']); ?></td>
                            <td data-label="Montant"><?php echo number_format($input['amount_input'], 2); ?> $</td>
                            <td data-label="Actions">
                                <button class="btn-secondary" onclick="deleteInput(<?php echo $input['id_input']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Prévisions dépenses -->
        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Prévisions de dépenses</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportForecastBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter
                    </button>
                </div>
            </div>

            <div class="table-container" style="margin-bottom: 18px;">
                <form id="forecastForm" class="form-grid">
                    <div class="form-group">
                        <label for="forecastCommission"><i class="fas fa-building"></i> Commission / Budget</label>
                        <input id="forecastCommission" name="forecastCommission" type="text" placeholder="Ex: Location lieu, Logistique, Restauration" required>
                    </div>
                    <div class="form-group">
                        <label for="forecastAmount"><i class="fas fa-coins"></i> Budget ($)</label>
                        <input id="forecastAmount" name="forecastAmount" type="number" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Ajouter une prévision
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Commission</th>
                            <th>Budget</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="forecastTableBody">
                        <?php foreach($all_forecasts as $forecast): ?>
                        <tr>
                            <td data-label="Commission"><?php echo htmlspecialchars($forecast['commission_forecast']); ?></td>
                            <td data-label="Budget"><?php echo number_format($forecast['budget_forecast'], 2); ?> $</td>
                            <td data-label="Actions">
                                <button class="btn-secondary" onclick="deleteForecast(<?php echo $forecast['id_forecast']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Dépenses réelles -->
        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Dépenses réelles</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportActualBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter
                    </button>
                </div>
            </div>

            <div class="table-container" style="margin-bottom: 18px;">
                <form id="actualForm" class="form-grid">
                    <div class="form-group">
                        <label for="actualCommission"><i class="fas fa-building"></i> Commission</label>
                        <input id="actualCommission" name="actualCommission" type="text" placeholder="Ex: Logistique, Lieu, Restauration" required>
                    </div>
                    <div class="form-group">
                        <label for="actualAmount"><i class="fas fa-receipt"></i> Dépense ($)</label>
                        <input id="actualAmount" name="actualAmount" type="number" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Ajouter une dépense
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Commission</th>
                            <th>Dépense</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="actualTableBody">
                        <?php foreach($all_actuals as $actual): ?>
                        <tr>
                            <td data-label="Commission"><?php echo htmlspecialchars($actual['commission_actual']); ?></td>
                            <td data-label="Dépense"><?php echo number_format($actual['amount_actual'], 2); ?> $</td>
                            <td data-label="Actions">
                                <button class="btn-secondary" onclick="deleteActual(<?php echo $actual['id_actual']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Reste par commission -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Quelles dépenses peuvent encore être faites ?</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportRemainingBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Commission</th>
                            <th>Budget</th>
                            <th>Dépenses réelles</th>
                            <th>Reste</th>
                        </tr>
                    </thead>
                    <tbody id="remainingTableBody">
                        <!-- Rempli par JavaScript -->
                    </tbody>
                </table>
            </div>
        </section>

        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Logs des encadreurs</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportLogsBtn">
                        <i class="fas fa-download"></i>
                        Exporter
                    </button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Encadreur</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody id="financeLogsBody">
                        <?php if(count($activity_logs) === 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:24px; color:var(--muted);">Aucun log encadreur disponible</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($activity_logs as $log): ?>
                            <tr>
                                <td><?php echo h($log['created_at']); ?></td>
                                <td><?php echo h($log['actor_name']); ?></td>
                                <td><?php echo h($log['module']); ?></td>
                                <td><?php echo h($log['action_type']); ?></td>
                                <td><?php echo h($log['description']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../js/finance.js"></script>
</body>

</html>
