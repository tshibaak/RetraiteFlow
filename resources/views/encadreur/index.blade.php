<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Encadreur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/encadreur.css">
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
                            <div class="user-info-role" id="userInfoRole">Encadreur</div>
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
        <!-- Header avec image de fond -->
        <header class="header">
            <h1>RetraiteFlow</h1>
            <p>Bienvenue, Encadreur • Inscrivez efficacement les participants à la retraite spirituelle</p>
        </header>
        <!-- Statistiques -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalParticipants">0</div>
                    <div class="stat-label">Solvables</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="accredites">0</div>
                    <div class="stat-label">Accrédités</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-pink">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="casSociaux">0</div>
                    <div class="stat-label">Cas Sociaux</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalRevenue">0 $</div>
                    <div class="stat-label">Revenus totaux</div>
                </div>
            </div>
        </div>


        <!-- Actions Bar -->
        <div class="actions-bar">
            <button class="btn-primary" id="openFormBtn">
                <i class="fas fa-plus"></i>
                Inscrire un Participant
            </button>
            <div class="search-bar-small">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un participant...">
                <button class="search-clear" id="searchClear" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Participants Table -->
        <section class="participants-section">
            <div class="section-header">
                <h2 class="section-title">Liste des Participants</h2>
                <div class="table-actions">
                    <button class="btn-secondary" id="exportBtn" onclick="exportToExcel()">
                        <i class="fas fa-download"></i>
                        Exporter
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
                            <th>Contact</th>
                            <th>Paiement</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="participantsTable">
                        <!-- Les participants seront ajoutés ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal Formulaire -->
    <div id="modal" class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-user-plus"></i>
                    Nouveau Participant
                </h3>
                <button type="button" class="close-modal" id="closeModal" title="Fermer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="inscriptionForm">
                    <div class="form-grid">
                        <!-- Ligne 1: Nom complet -->
                        <div class="form-group full-width">
                            <label for="nom">
                                <i class="fas fa-user"></i>
                                Nom complet
                            </label>
                            <input id="nom" name="nom" type="text" placeholder="Ex: KANDOLO Nicole" required />
                        </div>

                        <!-- Ligne 2: Sexe et Âge -->
                        <div class="form-group">
                            <label for="sexe">
                                <i class="fas fa-venus-mars"></i>
                                Sexe
                            </label>
                            <select id="sexe" name="sexe" required>
                                <option value="Masculin">Masculin</option>
                                <option value="Féminin">Féminin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="age">
                                <i class="fas fa-birthday-cake"></i>
                                Âge
                            </label>
                            <input id="age" name="age" type="number" min="15" max="50" placeholder="Âge" required />
                        </div>

                        <!-- Ligne 3: Groupe et Commission -->
                        <div class="form-group">
                            <label for="groupe">
                                <i class="fas fa-users"></i>
                                Groupe
                            </label>
                            <select id="groupe" name="groupe" required>
                                <option value="">Choisir un groupe</option>
                                <option value="Solvable">Solvable</option>
                                <option value="Accrédité">Accrédité</option>
                                <option value="Cas Social">Cas Social</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="commission">
                                <i class="fas fa-tasks"></i>
                                Commission
                            </label>
                            <select id="commission" name="commission" required>
                                <option value="">Choisir une commission</option>
                                <option value="Rien">Sans commission</option>
                                <option value="Discipline">Discipline</option>
                                <option value="Finance">Finance</option>
                                <option value="Logistique">Logistique</option>
                                <option value="Nettoyage">Nettoyage</option>
                                <option value="Restauration">Restauration</option>
                                <option value="Santé">Santé</option>
                            </select>
                        </div>

                        <!-- Ligne 4: Téléphone -->
                        <div class="form-group full-width">
                            <label for="telephone">
                                <i class="fas fa-phone"></i>
                                Téléphone/WhatsApp
                            </label>
                            <div class="phone-input-container">
                                <span class="phone-prefix">+243</span>
                                <input type="tel" id="telephone" name="telephone" placeholder="XXXXXXXXX" maxlength="9"
                                    required>
                            </div>
                        </div>

                        <!-- Ligne 5: Paiement et Durée -->
                        <div class="form-group">
                            <label for="montant">
                                <i class="fas fa-money-bill-wave"></i>
                                Paiement ($)
                            </label>
                            <input id="montant" name="montant" type="number" min="0" max="25" step="0.5"
                                placeholder="0.00" required />
                            <small id="montantHelp"
                                style="display: none; color: var(--muted); font-size: 12px; margin-top: 4px;">
                                <i class="fas fa-info-circle"></i> Le montant est automatiquement à 0 pour les cas
                                sociaux
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="jours">
                                <i class="fas fa-calendar-day"></i>
                                Durée (jours)
                            </label>
                            <input id="jours" name="jours" type="number" min="0" max="7" placeholder="0" required />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelBtn">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <button class="btn-primary" type="submit" form="inscriptionForm">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </div>
    </div>

    <!-- Help Widget -->
    <div class="help-widget"
        onclick="alert('Bienvenue sur RetraiteFlow !\n\nUtilisez le bouton + pour inscrire un nouveau participant.\nLes statistiques se mettent à jour automatiquement.\n\nVous pouvez exporter la liste complète au format Excel.')">
        <i class="fas fa-question"></i>
    </div>
    <!-- lien vers le fichier JS   -->
    <script src="../../script/shared/store.js"></script>
    <script src="../../script/Page1/encadreur.js"></script>
</body>

</html>