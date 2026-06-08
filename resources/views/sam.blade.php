<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Cordon / Super-admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- On garde le même design que Encadreur / Logistique -->
    <link rel="stylesheet" href="../../style/page1/encadreur.css">
</head>

<body>
    <!-- Barre supérieure -->
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
        <!-- En-tête -->
        <header class="header">
            <h1>Vue d'ensemble</h1>
            <p>Accès complet à toutes les données : participants, finances, chambres, ateliers.</p>
        </header>

        <!-- Ligne 1 : KPI principaux (comme ta capture) -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiParticipants">142</div>
                    <div class="stat-label">Total Participants</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                        Solvables 98 • Accrédités 32 • Cas Sociaux 12
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="kpiFinance">$8,450</div>
                    <div class="stat-label">Revenus Totaux</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                        Budget cible $10,000 • Solde restant $1,550
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">86%</div>
                    <div class="stat-label">Taux d'occupation</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                        Chambres occupées 43/50 • Places restantes 28
                    </div>
                </div>
            </div>
        </div>

        <!-- Ligne 2 : Répartition par groupe (emplacement du donut) -->
        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Répartition par groupe</h2>
            </div>
            <div class="table-container">
                <!-- Ici plus tard tu peux mettre un vrai graphe (canvas, chart.js, etc.) -->
                <div style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                ">
                    <div style="
                        width: 220px;
                        height: 220px;
                        border-radius: 50%;
                        background: conic-gradient(
                            #22c55e 0 65%,
                            #3b82f6 65% 90%,
                            #f59e0b 90% 100%
                        );
                        position: relative;
                    ">
                        <div style="
                            position: absolute;
                            inset: 32px;
                            border-radius: 50%;
                            background: white;
                        "></div>
                    </div>
                    <div style="font-size: 14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                            <span style="width:12px;height:12px;border-radius:4px;background:#22c55e;"></span>
                            <span>Solvables</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                            <span style="width:12px;height:12px;border-radius:4px;background:#3b82f6;"></span>
                            <span>Accrédités</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:12px;height:12px;border-radius:4px;background:#f59e0b;"></span>
                            <span>Cas Sociaux</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ligne 3 : Actions rapides -->
        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Actions rapides</h2>
            </div>
            <div class="stats-container">
                <button class="btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Ajouter participant
                </button>
                <button class="btn-secondary">
                    <i class="fas fa-file-excel"></i>
                    Exporter Excel
                </button>
                <button class="btn-secondary">
                    <i class="fas fa-print"></i>
                    Imprimer badges
                </button>
                <button class="btn-secondary">
                    <i class="fas fa-sms"></i>
                    Envoyer SMS
                </button>
            </div>
        </section>

        <!-- Ligne 4 : Participants récents -->
        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Participants récents</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Groupe</th>
                            <th>Commission</th>
                            <th>Chambre</th>
                            <th>Paiement</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableRecentParticipants">
                        <!-- Tu pourras remplir dynamiquement depuis le JS ou laisser statique -->
                        <tr>
                            <td>Exemple Participant</td>
                            <td>Solvable</td>
                            <td>Discipline</td>
                            <td>Dortoir A1</td>
                            <td>$25</td>
                            <td>
                                <div class="actions">
                                    <div class="btn-action btn-edit">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../../script/shared/store.js"></script>
    <script src="../../script/Page1/cordon.js"></script>
</body>

</html>