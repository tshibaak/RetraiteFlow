<?php
    use Router\Router;
    require_once '../src/config/database.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier que l'utilisateur est connecté et a le bon rôle
    if(!isset($_SESSION['id_enc']) || $_SESSION['role'] !== 'discipline'){
        die("Accès refusé");
    }

    $id_enc = $_SESSION['id_enc'];
    $nom_enc = $_SESSION['nom_enc'] . ' ' . $_SESSION['prenom_enc'];

    // Récupérer les statistiques pour le jour
    $today = date('Y-m-d');

    $stmt_entries = $db->prepare("
        SELECT COUNT(*) as count_entrees FROM discipline_logs
        WHERE id_participant IN (SELECT id_part FROM participants WHERE id_encadreur = :id_enc)
        AND type_log = 'entree'
        AND DATE(logged_at) = :today
    ");
    $stmt_entries->execute([":id_enc" => $id_enc, ":today" => $today]);
    $count_entries = $stmt_entries->fetch(PDO::FETCH_ASSOC)['count_entrees'] ?? 0;

    $stmt_exits = $db->prepare("
        SELECT COUNT(*) as count_sorties FROM discipline_logs
        WHERE id_participant IN (SELECT id_part FROM participants WHERE id_encadreur = :id_enc)
        AND type_log = 'sortie'
        AND DATE(logged_at) = :today
    ");
    $stmt_exits->execute([":id_enc" => $id_enc, ":today" => $today]);
    $count_exits = $stmt_exits->fetch(PDO::FETCH_ASSOC)['count_sorties'] ?? 0;

    // Récupérer tous les participants
    $stmt_participants = $db->prepare("
        SELECT * FROM participants
        WHERE id_encadreur = :id_enc
        ORDER BY nom_part ASC
    ");
    $stmt_participants->execute([":id_enc" => $id_enc]);
    $participants = $stmt_participants->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Discipline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style_encadreur.css">
</head>

<body>
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="user-menu-container">
                <button class="user-menu-btn" id="userMenuBtn">
                    <div class="user-avatar">
                        <span id="userInitials">D</span>
                    </div>
                    <span class="user-name" id="userName"><?php echo htmlspecialchars($nom_enc); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-info">
                        <div class="user-info-avatar">
                            <span id="userInfoInitials">D</span>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name" id="userInfoName"><?php echo htmlspecialchars($nom_enc); ?></div>
                            <div class="user-info-role" id="userInfoRole">Commission Discipline</div>
                        </div>
                    </div>
                    <div class="user-menu-divider"></div>
                    <a href="#" class="user-menu-item" id="accountSettings">
                        <i class="fas fa-user-cog"></i>
                        <span>Compte et paramètres</span>
                    </a>
                    <a href="<?= Router::route('/logout') ?>" class="user-menu-item" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                        <?php if(count($participants) === 0): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">
                                <i class="fas fa-users" style="font-size:40px;margin-bottom:10px;opacity:.5;display:block;"></i>
                                Aucun participant trouvé.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($participants as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nom_part']); ?></td>
                                <td><?php echo htmlspecialchars($p['groupe_part']); ?></td>
                                <td><?php echo htmlspecialchars($p['commission_part'] ?? ''); ?></td>
                                <td class="lastActionType">—</td>
                                <td class="lastActionTime">—</td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-secondary" type="button" onclick="markEntry(<?php echo $p['id_part']; ?>)">
                                            <i class="fas fa-door-open"></i>
                                        </button>
                                        <button class="btn-secondary" type="button" onclick="markExit(<?php echo $p['id_part']; ?>)">
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
                    <input type="date" id="rapportDate" value="<?php echo $today; ?>"
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

    <script src="../js/discipline.js"></script>
</body>

</html>
