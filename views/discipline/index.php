<?php

use Router\Router;

$nom_enc = $nom_enc ?? current_user_name();
$count_entries = (int) ($count_entries ?? 0);
$count_exits = (int) ($count_exits ?? 0);
$participants = $participants ?? [];
$today = date('Y-m-d');

$groupeLabels = [
    'solvable' => 'Solvable',
    'accredited' => 'Accrédité',
    'social_case' => 'Cas Social',
];

?>

<?php
$page_title = 'RetraiteFlow — Discipline';
require dirname(__DIR__, 1) . '/layouts/head.php';
?>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = 'Discipline';
    $nav_home_url = Router::route('/discipline');
    $nav_extra_links = [];
    require dirname(__DIR__, 1) . '/layouts/top-bar.php';
    ?>

    <main class="main-content">
        <!-- En-tête -->
        <header class="header">
            <h1>RetraiteFlow</h1>
            <p>Bienvenue, Discipline • Suivi des entrées / sorties et présence des participants</p>
        </header>

        <!-- Statistiques rapides -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statEntreesJour"><?php echo $count_entries; ?></div>
                    <div class="stat-label">Entrées du jour</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-door-closed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statSortiesJour"><?php echo $count_exits; ?></div>
                    <div class="stat-label">Sorties du jour</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statPresents">0</div>
                    <div class="stat-label">Participants présents</div>
                </div>
            </div>
        </div>

        <!-- Zone de recherche + liste participants -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Gestion des présences</h2>
                <div class="search-bar-small">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un participant...">
                    <button class="search-clear" id="searchClear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Groupe</th>
                            <th>Commission</th>
                            <th>Dernière action</th>
                            <th>Dernière heure</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="disciplineTable">
                        <?php if (count($participants) === 0): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">
                                <i class="fas fa-users" style="font-size:40px;margin-bottom:10px;opacity:.5;display:block;"></i>
                                Aucun participant trouvé.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($participants as $p): ?>
                            <?php
                                $groupeName = $p['groupe_name'] ?? '';
                                $groupeDisplay = $groupeLabels[$groupeName] ?? $groupeName;
                                $participantName = $p['name'] ?? '';
                            ?>
                            <tr>
                                <td><?php echo h($participantName); ?></td>
                                <td><?php echo h($groupeDisplay); ?></td>
                                <td><?php echo h($p['commission_name'] ?? ''); ?></td>
                                <td class="lastActionType">—</td>
                                <td class="lastActionTime">—</td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-secondary" type="button" onclick="markEntry(<?= json_encode($participantName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)">
                                            <i class="fas fa-door-open"></i>
                                        </button>
                                        <button class="btn-secondary" type="button" onclick="markExit(<?= json_encode($participantName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)">
                                            <i class="fas fa-door-closed"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Rapports quotidiens -->
        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Rapports quotidiens</h2>
                <div style="display:flex;align-items:center;gap:8px;">
                    <label for="rapportDate" style="font-size:14px;color:var(--muted);">Date :</label>
                    <input type="date" id="rapportDate" value="<?php echo h($today); ?>"
                        style="padding:8px 10px;border-radius:6px;border:1px solid #e2e8f0;">
                </div>
            </div>
            <div class="table-container">
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button class="btn-secondary" id="exportEntreesBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter Entrées du jour
                    </button>
                    <button class="btn-secondary" id="exportSortiesBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter Sorties du jour
                    </button>
                    <button class="btn-primary" id="exportEntreesSortiesBtn">
                        <i class="fas fa-file-csv"></i>
                        Enregistrer Entrées + Sorties du jour
                    </button>
                    <button class="btn-secondary" id="exportPresentsBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter Présents actuels
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script src="/js/discipline.js"></script>
    <script src="/js/script_encadreur.js" defer></script>
</body>

</html>
