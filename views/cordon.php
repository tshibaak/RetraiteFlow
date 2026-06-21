<?php

use Router\Router;

    require_once '../src/config/database.php';
    require_once '../src/lib/funcstd.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier l'accès (super-admin)
    if(!isset($_SESSION['id_enc']) || !in_array($_SESSION['role'], ['coordination', 'cordon'], true)){
        die("Accès refusé");
    }

    $id_enc = $_SESSION['id_enc'];
    $nom_enc = $_SESSION['nom_enc'] . ' ' . $_SESSION['prenom_enc'];

    // Récupérer les KPIs globaux (tous les encadreurs)
    $stmt_total_participants = $db->prepare("SELECT COUNT(*) as count FROM participants");
    $stmt_total_participants->execute();
    $kpi_participants = $stmt_total_participants->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_total_dortoirs = $db->prepare("SELECT COUNT(*) as count FROM logistique_dortoirs");
    $stmt_total_dortoirs->execute();
    $kpi_dortoirs = $stmt_total_dortoirs->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_total_ateliers = $db->prepare("SELECT COUNT(*) as count FROM logistique_ateliers");
    $stmt_total_ateliers->execute();
    $kpi_ateliers = $stmt_total_ateliers->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_total_inputs = $db->prepare("SELECT SUM(montant_part) as total FROM participants WHERE finance_status = 'confirme'");
    $stmt_total_inputs->execute();
    $kpi_finance = $stmt_total_inputs->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt_actuals = $db->prepare("SELECT SUM(amount_actual) as total FROM finance_actuals");
    $stmt_actuals->execute();
    $total_actuals = $stmt_actuals->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt_forecasts = $db->prepare("SELECT SUM(budget_forecast) as total FROM finance_forecasts");
    $stmt_forecasts->execute();
    $total_forecasts = $stmt_forecasts->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt_loges = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE dortoir_id IS NOT NULL");
    $stmt_loges->execute();
    $participants_loges = $stmt_loges->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_atelier_participants = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE atelier_id IS NOT NULL");
    $stmt_atelier_participants->execute();
    $participants_atelier = $stmt_atelier_participants->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Récupérer les résumés par groupe
    $stmt_solvables = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE groupe_part = 'solvable'");
    $stmt_solvables->execute();
    $count_solvables = $stmt_solvables->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_accredites = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE groupe_part = 'accrédité'");
    $stmt_accredites->execute();
    $count_accredites = $stmt_accredites->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_sociaux = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE groupe_part IN ('cas_social', 'cas social')");
    $stmt_sociaux->execute();
    $count_sociaux = $stmt_sociaux->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_confirmes = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE finance_status = 'confirme'");
    $stmt_confirmes->execute();
    $count_confirmes = $stmt_confirmes->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_attente = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE finance_status = 'en_attente'");
    $stmt_attente->execute();
    $count_attente = $stmt_attente->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_deconfirmes = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE finance_status = 'deconfirme'");
    $stmt_deconfirmes->execute();
    $count_deconfirmes = $stmt_deconfirmes->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $activity_logs = fetch_activity_logs($db, 'all', null, 100);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Cordon / Super-admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style_encadreur.css">
</head>

<body>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = 'Cordon / Super-admin';
    $nav_home_url = Router::route('/cordon');
    $nav_extra_links = [
        ['url' => Router::route('/coordon/register'), 'icon' => 'fas fa-user-plus', 'label' => 'Ajouter un membre'],
    ];
    require dirname(__DIR__) . '/partials/top-bar.php';
    ?>

    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <h1>RetraiteFlow</h1>
            <p>Bienvenue, Cordon • Vue globale des participants, de la logistique et des finances</p>
        </header>

        <?php if (isset($_SESSION['message_inscripttion'])): ?>
            <div class="flash-message flash-success">
                <?php
                echo h($_SESSION['message_inscripttion']);
                unset($_SESSION['message_inscripttion']);
                ?>
            </div>
        <?php endif; ?>

        <div class="actions-bar">
            <a href="<?= Router::route('/coordon/register') ?>" class="btn-primary">
                <i class="fas fa-user-plus"></i>
                Ajouter un membre
            </a>
            <button class="btn-secondary" type="button" id="exportCordonBtn">
                <i class="fas fa-download"></i>
                Exporter les synthèses
            </button>
        </div>

        <!-- Cartes de statistiques globales -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiParticipants"><?php echo $kpi_participants; ?></div>
                    <div class="stat-label">Participants totaux</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiLogistique"><?php echo $kpi_dortoirs; ?> / <?php echo $kpi_ateliers; ?></div>
                    <div class="stat-label">Dortoirs & ateliers</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiFinance">$<?php echo number_format($kpi_finance, 2); ?></div>
                    <div class="stat-label">Montant total estimé</div>
                </div>
            </div>
        </div>

        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Graphiques de pilotage</h2>
            </div>
            <div class="chart-grid">
                <div class="chart-panel">
                    <h3>Répartition participants</h3>
                    <canvas id="cordonParticipantsChart" width="420" height="220"
                        data-solvables="<?php echo $count_solvables; ?>"
                        data-accredites="<?php echo $count_accredites; ?>"
                        data-sociaux="<?php echo $count_sociaux; ?>"></canvas>
                </div>
                <div class="chart-panel">
                    <h3>Validation finance</h3>
                    <canvas id="cordonFinanceChart" width="420" height="220"
                        data-confirmed="<?php echo $count_confirmes; ?>"
                        data-pending="<?php echo $count_attente; ?>"
                        data-rejected="<?php echo $count_deconfirmes; ?>"></canvas>
                </div>
            </div>
        </section>

        <!-- Résumé participants -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Répartition des participants</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Nombre</th>
                        </tr>
                    </thead>
                    <tbody id="tableParticipantsSummary">
                        <tr>
                            <td>Solvables</td>
                            <td><?php echo $count_solvables; ?></td>
                        </tr>
                        <tr>
                            <td>Accrédités</td>
                            <td><?php echo $count_accredites; ?></td>
                        </tr>
                        <tr>
                            <td>Cas Sociaux</td>
                            <td><?php echo $count_sociaux; ?></td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                            <td>Total</td>
                            <td><?php echo $kpi_participants; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Résumé logistique -->
        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Résumé logistique</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Élément</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody id="tableLogistiqueSummary">
                        <tr>
                            <td>Dortoirs créés</td>
                            <td><?php echo $kpi_dortoirs; ?></td>
                        </tr>
                        <tr>
                            <td>Ateliers créés</td>
                            <td><?php echo $kpi_ateliers; ?></td>
                        </tr>
                        <tr>
                            <td>Participants logés</td>
                            <td id="participantsLogis"><?php echo $participants_loges; ?></td>
                        </tr>
                        <tr>
                            <td>Participants en atelier</td>
                            <td id="participantsAtelier"><?php echo $participants_atelier; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Résumé financier -->
        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Résumé financier</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Montant estimé</th>
                        </tr>
                    </thead>
                    <tbody id="tableFinanceSummary">
                        <tr>
                            <td>Entrées totales</td>
                            <td id="totalInputs">$<?php echo number_format($kpi_finance, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Dépenses réelles</td>
                            <td id="totalActuals">$<?php echo number_format($total_actuals, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Prévisions budgétaires</td>
                            <td id="totalForecasts">$<?php echo number_format($total_forecasts, 2); ?></td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                            <td>Solde</td>
                            <td id="balance">$<?php echo number_format($kpi_finance - $total_actuals, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Historique global des actions</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Compte</th>
                            <th>Rôle</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($activity_logs) === 0): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 24px; color: var(--muted);">Aucun historique disponible</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($activity_logs as $log): ?>
                            <tr>
                                <td><?php echo h($log['created_at']); ?></td>
                                <td><?php echo h($log['actor_name']); ?></td>
                                <td><?php echo h($log['actor_role']); ?></td>
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

    <script src="/js/cordon.js"></script>
    <script src="/js/script_encadreur.js" defer></script>
</body>

</html>
