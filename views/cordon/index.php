<?php

use Router\Router;

$nom_enc = $nom_enc ?? current_user_name();
$kpi_participants = (int) ($kpi_participants ?? 0);
$kpi_dortoirs = (int) ($kpi_dortoirs ?? 0);
$kpi_ateliers = (int) ($kpi_ateliers ?? 0);
$kpi_finance = (float) ($kpi_finance ?? 0);
$total_actuals = (float) ($total_actuals ?? 0);
$total_forecasts = (float) ($total_forecasts ?? 0);
$participants_loges = (int) ($participants_loges ?? 0);
$participants_atelier = (int) ($participants_atelier ?? 0);
$count_solvables = (int) ($count_solvables ?? 0);
$count_accredites = (int) ($count_accredites ?? 0);
$count_sociaux = (int) ($count_sociaux ?? 0);
$count_confirmes = (int) ($count_confirmes ?? 0);
$count_attente = (int) ($count_attente ?? 0);
$count_deconfirmes = (int) ($count_deconfirmes ?? 0);
$activity_logs = $activity_logs ?? [];

?>

<?php
$page_title = 'RetraiteFlow — Cordon / Super-admin';
require dirname(__DIR__, 1) . '/layouts/head.php';
?>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = 'Cordon / Super-admin';
    $nav_home_url = Router::route('/cordon');
    $nav_extra_links = [
        ['url' => Router::route('/coordon/register'), 'icon' => 'fas fa-user-plus', 'label' => 'Ajouter un membre'],
    ];
    require dirname(__DIR__, 1) . '/layouts/top-bar.php';
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
                            <th>Action</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($activity_logs) === 0): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 24px; color: var(--muted);">Aucun historique disponible</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($activity_logs as $log): ?>
                            <tr>
                                <td><?php echo h($log['created_at'] ?? ''); ?></td>
                                <td><?php echo h($log['user_name'] ?? ''); ?></td>
                                <td><?php echo h($log['action'] ?? ''); ?></td>
                                <td><?php echo h($log['detail'] ?? ''); ?></td>
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
