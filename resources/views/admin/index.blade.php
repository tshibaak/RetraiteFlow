<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Cordon / Super-admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/encadreur.css">
</head>

<body>
    <!-- Barre supérieure avec menu utilisateur (même design que les autres pages) -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="user-menu-container">
                <button class="user-menu-btn" id="userMenuBtn">
                    <div class="user-avatar">
                        <span id="userInitials">C</span>
                    </div>
                    <span class="user-name" id="userName">Cordon</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-info">
                        <div class="user-info-avatar">
                            <span id="userInfoInitials">C</span>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name" id="userInfoName">Cordon</div>
                            <div class="user-info-role" id="userInfoRole">Cordon / Super-admin</div>
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
        <!-- Header avec même style que Encadreur/Logistique -->
        <header class="header">
            <h1>RetraiteFlow</h1>
            <p>Bienvenue, Cordon • Vue globale des participants, de la logistique et des finances</p>
        </header>

        <!-- Cartes de statistiques globales (même design que les autres stats) -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiParticipants">0</div>
                    <div class="stat-label">Participants totaux</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiLogistique">0 / 0</div>
                    <div class="stat-label">Dortoirs & ateliers</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiFinance">$0.00</div>
                    <div class="stat-label">Montant total estimé</div>
                </div>
            </div>
        </div>

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
                        <!-- rempli par cordon.js -->
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
                        <!-- rempli par cordon.js -->
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
                        <!-- rempli par cordon.js -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../js/store.js"></script>
    <script src="../js/cordon.js"></script>
</body>

</html>