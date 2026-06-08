<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Finance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/encadreur.css">
</head>

<body>
    <!-- Barre supérieure avec menu utilisateur (même style que l'encadreur) -->
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
                            <div class="user-info-role" id="userInfoRole">Finance</div>
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
                    <div class="stat-value" id="statTotalInputs">0 $</div>
                    <div class="stat-label">Entrées totales</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-pink">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statTotalActualExpenses">0 $</div>
                    <div class="stat-label">Dépenses réelles</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statSolde">0 $</div>
                    <div class="stat-label">Solde (Entrées - Réelles)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="statRemainingBudget">0 $</div>
                    <div class="stat-label">Reste sur budget prévisionnel</div>
                </div>
            </div>
        </div>

        <!-- Entrées -->
        <section class="participants-section" style="margin-bottom: 20px;">
            <div class="section-header">
                <h2 class="section-title">Entrées d'argent</h2>
                <div class="table-actions">
                    <button class="btn-secondary" type="button" id="exportInputsBtn">
                        <i class="fas fa-file-excel"></i>
                        Exporter
                    </button>
                </div>
            </div>

            <div class="table-container" style="margin-bottom: 18px;">
                <form id="inputsForm" class="form-grid">
                    <div class="form-group">
                        <label for="inputSource"><i class="fas fa-tag"></i> Source</label>
                        <input id="inputSource" name="inputSource" type="text" placeholder="Ex: Paiements participants" required>
                    </div>
                    <div class="form-group">
                        <label for="inputAmount"><i class="fas fa-money-bill-wave"></i> Montant ($)</label>
                        <input id="inputAmount" name="inputAmount" type="number" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Ajouter une entrée
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Montant</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inputsTableBody"></tbody>
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
                    <tbody id="forecastTableBody"></tbody>
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
                    <tbody id="actualTableBody"></tbody>
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
                    <tbody id="remainingTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../js/store.js"></script>
    <script src="../js/finance.js"></script>
</body>

</html>

