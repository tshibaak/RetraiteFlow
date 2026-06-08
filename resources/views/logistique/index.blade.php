<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Logistique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/encadreur.css">
    <link rel="stylesheet" href="../css/logistique.css">
</head>

<body>
    <!-- Barre supérieure avec menu utilisateur -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="user-menu-container">
                <button class="user-menu-btn" id="userMenuBtn">
                    <div class="user-avatar">
                        <span id="userInitials">U</span>
                    </div>
                    <span class="user-name" id="userName">Utilisateur</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-info">
                        <div class="user-info-avatar">
                            <span id="userInfoInitials">U</span>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name" id="userInfoName">Utilisateur</div>
                            <div class="user-info-role" id="userInfoRole">Logistique</div>
                        </div>
                    </div>
                    <div class="user-menu-divider"></div>
                    <a href="#" class="user-menu-item" id="accountSettings">
                        <i class="fas fa-user-cog"></i>
                        <span>Compte et paramètres</span>
                    </a>
                    <a href="#" class="user-menu-item" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="main-content">
        <header class="header">
            <h1>Commission Logistique</h1>
            <p>Gestion des dortoirs (hommes/femmes) et ateliers (selon l’âge), avec affectation automatique des
                participants</p>
        </header>

        <!-- Cartes stats (comme sur ta capture) -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statParticipants">0</div>
                    <div class="stat-label">Participants présents</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statDortoirsDispo">0</div>
                    <div class="stat-label">Dortoirs disponibles</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statAteliersActifs">0</div>
                    <div class="stat-label">Ateliers actifs</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-bar">
            <button class="btn-primary" id="createDortoirBtn">
                <i class="fas fa-plus"></i>
                Créer un dortoir
            </button>
            <button class="btn-secondary" id="createAtelierBtn">
                <i class="fas fa-plus"></i>
                Créer un atelier
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
                    <tbody id="dortoirsTableBody"></tbody>
                </table>
            </div>
        </section>

        <div style="height: 24px;"></div>

        <!-- Répartition par atelier -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Répartition par atelier</h2>
            </div>
            <div id="atelierCards" class="rf-grid-cards"></div>
        </section>
    </main>

    <!-- Modal Dortoir -->
    <div id="dortoirModal" class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-bed"></i>
                    Nouveau dortoir
                </h3>
                <button class="close-modal" id="closeDortoirModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="dortoirForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="dortoirNom"><i class="fas fa-tag"></i> Nom du dortoir</label>
                            <input id="dortoirNom" name="dortoirNom" type="text" placeholder="Ex: Dortoir Hommes A"
                                required />
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
                            <input id="dortoirCapacite" name="dortoirCapacite" type="number" min="1" max="500"
                                placeholder="Ex: 30" required />
                        </div>

                        <div class="form-group">
                            <label for="dortoirAgeMin"><i class="fas fa-child"></i> Âge min</label>
                            <input id="dortoirAgeMin" name="dortoirAgeMin" type="number" min="0" max="120"
                                placeholder="Ex: 15" required />
                        </div>

                        <div class="form-group">
                            <label for="dortoirAgeMax"><i class="fas fa-user"></i> Âge max</label>
                            <input id="dortoirAgeMax" name="dortoirAgeMax" type="number" min="0" max="120"
                                placeholder="Ex: 20" required />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelDortoirBtn">
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
    <div id="atelierModal" class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Nouvel atelier
                </h3>
                <button class="close-modal" id="closeAtelierModal" type="button" title="Fermer la fenêtre">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="atelierForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="atelierNom"><i class="fas fa-tag"></i> Nom de l'atelier</label>
                            <input id="atelierNom" name="atelierNom" type="text" placeholder="Ex: Atelier Enseignement"
                                required />
                        </div>

                        <div class="form-group">
                            <label for="atelierCapacite"><i class="fas fa-users"></i> Capacité</label>
                            <input id="atelierCapacite" name="atelierCapacite" type="number" min="1" max="500"
                                placeholder="Ex: 25" required />
                        </div>

                        <div class="form-group">
                            <label for="atelierAgeMin"><i class="fas fa-child"></i> Âge min</label>
                            <input id="atelierAgeMin" name="atelierAgeMin" type="number" min="0" max="120"
                                placeholder="Ex: 15" required />
                        </div>

                        <div class="form-group">
                            <label for="atelierAgeMax"><i class="fas fa-user"></i> Âge max</label>
                            <input id="atelierAgeMax" name="atelierAgeMax" type="number" min="0" max="120"
                                placeholder="Ex: 20" required />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelAtelierBtn">
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

    <div class="help-widget"
        onclick="alert('Commission Logistique\\n\\n- Créez des dortoirs (sexe + tranche d\'âge + capacité)\\n- Créez des ateliers (tranche d\'âge + capacité)\\n- Les participants inscrits côté Encadreur sont affectés automatiquement.')">
        <i class="fas fa-question"></i>
    </div>

    <script src="../js/store.js"></script>
    <script src="../js/logistique.js"></script>
</body>

</html>