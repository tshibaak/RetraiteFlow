<?php
   use Router\Router;
    require_once '../src/config/database.php';
    require_once '../src/lib/funcstd.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier l'accès
    if(!isset($_SESSION['id_enc']) || $_SESSION['role'] !== 'logistique'){
        die("Accès refusé");
    }

    $id_enc = $_SESSION['id_enc'];
    $nom_enc = $_SESSION['nom_enc'] . ' ' . $_SESSION['prenom_enc'];

    // Récupérer les statistiques
    $stmt_participants = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE id_encadreur = :id_enc");
    $stmt_participants->execute([":id_enc" => $id_enc]);
    $count_participants = $stmt_participants->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_dortoirs = $db->prepare("SELECT COUNT(*) as count FROM logistique_dortoirs WHERE id_encadreur = :id_enc");
    $stmt_dortoirs->execute([":id_enc" => $id_enc]);
    $count_dortoirs = $stmt_dortoirs->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $stmt_ateliers = $db->prepare("SELECT COUNT(*) as count FROM logistique_ateliers WHERE id_encadreur = :id_enc");
    $stmt_ateliers->execute([":id_enc" => $id_enc]);
    $count_ateliers = $stmt_ateliers->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Récupérer les dortoirs et ateliers
    $stmt_all_dortoirs = $db->prepare("SELECT * FROM logistique_dortoirs WHERE id_encadreur = :id_enc ORDER BY nom_dortoir ASC");
    $stmt_all_dortoirs->execute([":id_enc" => $id_enc]);
    $all_dortoirs = $stmt_all_dortoirs->fetchAll(PDO::FETCH_ASSOC);

    $stmt_all_ateliers = $db->prepare("SELECT * FROM logistique_ateliers WHERE id_encadreur = :id_enc ORDER BY nom_atelier ASC");
    $stmt_all_ateliers->execute([":id_enc" => $id_enc]);
    $all_ateliers = $stmt_all_ateliers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Logistique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style_encadreur.css">
    <link rel="stylesheet" href="/css/style_logistique.css">
</head>

<body>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = 'Logistique';
    $nav_home_url = Router::route('/logistique');
    $nav_extra_links = [];
    require dirname(__DIR__) . '/partials/top-bar.php';
    ?>

    <main class="main-content">
        <header class="header">
            <h1>Commission Logistique</h1>
            <p>Gestion des dortoirs (hommes/femmes) et ateliers (selon l'âge), avec affectation automatique des participants</p>
        </header>

        <!-- Cartes stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statParticipants"><?php echo $count_participants; ?></div>
                    <div class="stat-label">Participants présents</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statDortoirsDispo"><?php echo $count_dortoirs; ?></div>
                    <div class="stat-label">Dortoirs disponibles</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statAteliersActifs"><?php echo $count_ateliers; ?></div>
                    <div class="stat-label">Ateliers actifs</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-bar">
            <button class="btn-primary" id="createDortoirBtn" onclick="openDortoirModal()">
                <i class="fas fa-plus"></i>
                Créer un dortoir
            </button>
            <button class="btn-secondary" id="createAtelierBtn" onclick="openAtelierModal()">
                <i class="fas fa-plus"></i>
                Créer un atelier
            </button>
            <button class="btn-secondary" id="exportLogistiqueBtn" type="button">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <div class="search-bar-small">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un dortoir...">
                <button class="search-clear" id="searchClear" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Alertes non attribués -->
        <div id="alertsContainer" class="rf-alerts" style="display:none;"></div>

        <!-- Gestion des dortoirs -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Gestion des dortoirs</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Dortoir</th>
                            <th>Sexe</th>
                            <th>Tranche d'âge</th>
                            <th>Capacité</th>
                            <th>Occupants</th>
                            <th>Statut</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dortoirsTableBody">
                        <?php foreach($all_dortoirs as $d): 
                            $stmt_count = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE dortoir_id = :id");
                            $stmt_count->execute([":id" => $d['id_dortoir']]);
                            $occupants = $stmt_count->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                            $capacity = $d['capacite_dortoir'];
                            $status = $occupants >= $capacity ? 'Plein' : 'Disponible';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['nom_dortoir']); ?></td>
                            <td><?php echo htmlspecialchars($d['sexe_dortoir']); ?></td>
                            <td><?php echo $d['age_min_dortoir'] . ' - ' . $d['age_max_dortoir']; ?> ans</td>
                            <td><?php echo $capacity; ?></td>
                            <td><?php echo $occupants; ?> / <?php echo $capacity; ?></td>
                            <td><span class="badge <?php echo $occupants >= $capacity ? 'badge-danger' : 'badge-success'; ?>"><?php echo $status; ?></span></td>
                            <td>
                                <button class="btn-secondary" onclick="deleteDortoir(<?php echo $d['id_dortoir']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div style="height: 24px;"></div>

        <!-- Répartition par atelier -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Répartition par atelier</h2>
            </div>
            <div id="atelierCards" class="rf-grid-cards">
                <?php foreach($all_ateliers as $a):
                    $stmt_count_a = $db->prepare("SELECT COUNT(*) as count FROM participants WHERE atelier_id = :id");
                    $stmt_count_a->execute([":id" => $a['id_atelier']]);
                    $occupants_a = $stmt_count_a->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                    $capacity_a = $a['capacite_atelier'];
                    $status_a = $occupants_a >= $capacity_a ? 'Plein' : 'Disponible';
                ?>
                <div class="rf-card">
                    <h3><?php echo htmlspecialchars($a['nom_atelier']); ?></h3>
                    <p class="text-small"><?php echo $a['age_min_atelier'] . ' - ' . $a['age_max_atelier']; ?> ans</p>
                    <div class="card-stat">
                        <span class="badge"><?php echo $occupants_a; ?> / <?php echo $capacity_a; ?></span>
                        <span class="badge <?php echo $occupants_a >= $capacity_a ? 'badge-danger' : 'badge-success'; ?>"><?php echo $status_a; ?></span>
                    </div>
                    <button class="btn-secondary" onclick="deleteAtelier(<?php echo $a['id_atelier']; ?>)">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- Modal Dortoir -->
    <div id="dortoirModal" class="modal-backdrop" style="display:none;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-bed"></i>
                    Nouveau dortoir
                </h3>
                <button class="close-modal" id="closeDortoirModal" onclick="closeDortoirModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="dortoirForm" onsubmit="saveDortoir(event)">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="dortoirNom"><i class="fas fa-tag"></i> Nom du dortoir</label>
                            <input id="dortoirNom" name="dortoirNom" type="text" placeholder="Ex: Dortoir Hommes A" required />
                        </div>

                        <div class="form-group">
                            <label for="dortoirSexe"><i class="fas fa-venus-mars"></i> Dortoir pour</label>
                            <select id="dortoirSexe" name="dortoirSexe" required>
                                <option value="Masculin">Hommes</option>
                                <option value="Féminin">Femmes</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dortoirCapacite"><i class="fas fa-users"></i> Capacité</label>
                            <input id="dortoirCapacite" name="dortoirCapacite" type="number" min="1" max="500" placeholder="Ex: 30" required />
                        </div>

                        <div class="form-group">
                            <label for="dortoirAgeMin"><i class="fas fa-child"></i> Âge min</label>
                            <input id="dortoirAgeMin" name="dortoirAgeMin" type="number" min="0" max="120" placeholder="Ex: 15" required />
                        </div>

                        <div class="form-group">
                            <label for="dortoirAgeMax"><i class="fas fa-user"></i> Âge max</label>
                            <input id="dortoirAgeMax" name="dortoirAgeMax" type="number" min="0" max="120" placeholder="Ex: 20" required />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelDortoirBtn" onclick="closeDortoirModal()">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <button class="btn-primary" type="submit" form="dortoirForm">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Atelier -->
    <div id="atelierModal" class="modal-backdrop" style="display:none;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Nouvel atelier
                </h3>
                <button class="close-modal" id="closeAtelierModal" onclick="closeAtelierModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="atelierForm" onsubmit="saveAtelier(event)">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="atelierNom"><i class="fas fa-tag"></i> Nom de l'atelier</label>
                            <input id="atelierNom" name="atelierNom" type="text" placeholder="Ex: Atelier Enseignement" required />
                        </div>

                        <div class="form-group">
                            <label for="atelierCapacite"><i class="fas fa-users"></i> Capacité</label>
                            <input id="atelierCapacite" name="atelierCapacite" type="number" min="1" max="500" placeholder="Ex: 25" required />
                        </div>

                        <div class="form-group">
                            <label for="atelierAgeMin"><i class="fas fa-child"></i> Âge min</label>
                            <input id="atelierAgeMin" name="atelierAgeMin" type="number" min="0" max="120" placeholder="Ex: 15" required />
                        </div>

                        <div class="form-group">
                            <label for="atelierAgeMax"><i class="fas fa-user"></i> Âge max</label>
                            <input id="atelierAgeMax" name="atelierAgeMax" type="number" min="0" max="120" placeholder="Ex: 20" required />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelAtelierBtn" onclick="closeAtelierModal()">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <button class="btn-primary" type="submit" form="atelierForm">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </div>
    </div>

    <script src="/js/logistique.js"></script>
    <script src="/js/script_encadreur.js" defer></script>
</body>

</html>
