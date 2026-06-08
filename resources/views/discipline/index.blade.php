<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Discipline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/encadreur.css">
</head>

<body>
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="user-menu-container">
                <button class="user-menu-btn" id="userMenuBtn">
                    <div class="user-avatar">
                        <span id="userInitials">D</span>
                    </div>
                    <span class="user-name" id="userName">Discipline</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-info">
                        <div class="user-info-avatar">
                            <span id="userInfoInitials">D</span>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name" id="userInfoName">Discipline</div>
                            <div class="user-info-role" id="userInfoRole">Commission Discipline</div>
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
                    <div class="stat-value" id="statEntreesJour">0</div>
                    <div class="stat-label">Entrées du jour</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-door-closed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statSortiesJour">0</div>
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
                        <!-- rempli par discipline.js -->
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
                    <input type="date" id="rapportDate"
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

    <!-- Scripts -->
    <script src="../js/store.js"></script>
    <script src="../js/discipline.js"></script>
</body>

</html>