<?php

use Router\Router;

$nom_enc = $nom_enc ?? current_user_name();
$total_inputs = (float) ($total_inputs ?? 0);
$total_actuals = (float) ($total_actuals ?? 0);
$total_forecasts = (float) ($total_forecasts ?? 0);
$solde = (float) ($solde ?? ($total_inputs - $total_actuals));
$remaining_budget = (float) ($remaining_budget ?? ($total_forecasts - $total_actuals));
$total_participants = (int) ($total_participants ?? 0);
$confirmed_participants = (int) ($confirmed_participants ?? 0);
$pending_participants = (int) ($pending_participants ?? 0);
$rejected_participants = (int) ($rejected_participants ?? 0);
$all_forecasts = $all_forecasts ?? [];
$all_actuals = $all_actuals ?? [];
$finance_participants = $finance_participants ?? [];
$activity_logs = $activity_logs ?? [];

$groupeLabels = [
    'solvable' => 'Solvable',
    'accredited' => 'Accrédité',
    'social_case' => 'Cas Social',
];

$statusMap = [
    'pending' => ['key' => 'en_attente', 'label' => 'En attente', 'class' => 'badge-warning'],
    'en_attente' => ['key' => 'en_attente', 'label' => 'En attente', 'class' => 'badge-warning'],
    'confirmed' => ['key' => 'confirme', 'label' => 'Confirmé', 'class' => 'badge-success'],
    'confirme' => ['key' => 'confirme', 'label' => 'Confirmé', 'class' => 'badge-success'],
    'rejected' => ['key' => 'deconfirme', 'label' => 'Déconfirmé', 'class' => 'badge-danger'],
    'deconfirme' => ['key' => 'deconfirme', 'label' => 'Déconfirmé', 'class' => 'badge-danger'],
];

?>

<?php
$page_title = 'RetraiteFlow — Finance';
require dirname(__DIR__, 1) . '/layouts/head.php';
?>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = 'Finance';
    $nav_home_url = Router::route('/finance');
    $nav_extra_links = [];
    require dirname(__DIR__, 1) . '/layouts/top-bar.php';
    ?>

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
                        <?php if (count($finance_participants) === 0): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:24px; color:var(--muted);">Aucun participant enregistré</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($finance_participants as $p): ?>
                            <?php
                                $rawStatus = $p['paiement_statut'] ?? 'pending';
                                $statusInfo = $statusMap[$rawStatus] ?? $statusMap['pending'];
                                $groupeName = $p['groupe_name'] ?? '';
                                $groupeDisplay = $groupeLabels[$groupeName] ?? $groupeName;
                            ?>
                            <tr>
                                <td data-label="Participant">
                                    <div style="font-weight:700;"><?php echo h($p['name'] ?? ''); ?></div>
                                    <div style="font-size:13px;color:var(--muted);"><?php echo h($p['age'] ?? ''); ?> ans • <?php echo h($p['sexe'] ?? ''); ?></div>
                                </td>
                                <td data-label="Encadreur"><?php echo h($p['encadreur_name'] ?? ''); ?></td>
                                <td data-label="Groupe"><?php echo h($groupeDisplay); ?></td>
                                <td data-label="Commission"><?php echo h($p['commission_name'] ?? ''); ?></td>
                                <td data-label="Montant"><?php echo number_format((float) ($p['amount'] ?? 0), 2); ?> $</td>
                                <td data-label="Statut">
                                    <span class="badge <?php echo h($statusInfo['class']); ?>"><?php echo h($statusInfo['label']); ?></span>
                                    <?php if (!empty($p['validator_name'])): ?>
                                        <div style="font-size:12px;color:var(--muted);"><?php echo h($p['validator_name']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Actions">
                                    <div class="actions actions-cell">
                                        <button class="btn-secondary finance-status-btn" data-id="<?php echo (int) ($p['id'] ?? 0); ?>" data-status="confirme">
                                            <i class="fas fa-check"></i>
                                            Confirmer
                                        </button>
                                        <button class="btn-secondary finance-status-btn" data-id="<?php echo (int) ($p['id'] ?? 0); ?>" data-status="deconfirme">
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

        <!-- Entrées (paiements confirmés) -->
        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Entrées</h2>
            </div>
            <div class="table-container">
                <p style="padding: 12px 4px; color: var(--muted); margin: 0 0 12px;">
                    Les revenus proviennent des paiements confirmés des participants (plus d'entrées complémentaires manuelles).
                </p>
                <table>
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody id="inputsTableBody">
                        <tr>
                            <td data-label="Source">
                                <strong>Participants confirmés</strong>
                                <div style="font-size: 12px; color: var(--muted);">Calculé depuis les confirmations du financier</div>
                            </td>
                            <td data-label="Montant"><?php echo number_format($total_inputs, 2); ?> $</td>
                        </tr>
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
                        <?php foreach ($all_forecasts as $forecast): ?>
                        <tr>
                            <td data-label="Commission"><?php echo h($forecast['commission_name'] ?? ''); ?></td>
                            <td data-label="Budget"><?php echo number_format((float) ($forecast['budget'] ?? 0), 2); ?> $</td>
                            <td data-label="Actions">
                                <button class="btn-secondary" onclick="deleteForecast(<?php echo (int) ($forecast['id'] ?? 0); ?>)">
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
                        <?php foreach ($all_actuals as $actual): ?>
                        <tr>
                            <td data-label="Commission"><?php echo h($actual['commission_name'] ?? ''); ?></td>
                            <td data-label="Dépense"><?php echo number_format((float) ($actual['budget_depense_rel'] ?? 0), 2); ?> $</td>
                            <td data-label="Actions">
                                <button class="btn-secondary" onclick="deleteActual(<?php echo (int) ($actual['id'] ?? 0); ?>)">
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
                            <th>Action</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody id="financeLogsBody">
                        <?php if (count($activity_logs) === 0): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:var(--muted);">Aucun log encadreur disponible</td>
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

    <script src="/js/finance.js"></script>
    <script src="/js/script_encadreur.js" defer></script>
</body>

</html>
