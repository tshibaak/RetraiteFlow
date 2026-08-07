<?php

use Router\Router;

$nom_enc = $nom_enc ?? current_user_name();
$participants = $participants ?? [];
$activity_logs = $activity_logs ?? [];
$stats_sum_solvable = $stats_sum_solvable ?? 0;
$stats_sum_accredite = $stats_sum_accredite ?? 0;
$stats_sum_social = $stats_sum_social ?? 0;
$stats_total_paiment = $stats_total_paiment ?? 0;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow — Encadreur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/encadreur.css">
</head>

<body>
    <?php
    $nav_user_name = $nom_enc;
    $nav_role_label = 'Encadreur';
    $nav_home_url = Router::route('/encadreur');
    $nav_extra_links = [];
    require dirname(__DIR__, 1) . '/layouts/top-bar.php';
    ?>

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
                    <div class="stat-value" id="totalParticipants">
                        <?php echo (int) $stats_sum_solvable; ?>
                    </div>
                    <div class="stat-label">Solvables</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="accredites">
                        <?php echo (int) $stats_sum_accredite; ?>
                    </div>
                    <div class="stat-label">Accrédités</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-pink">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="casSociaux">
                        <?php echo (int) $stats_sum_social; ?>
                    </div>
                    <div class="stat-label">Cas Sociaux</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalRevenue">
                        <?php echo ((float) $stats_total_paiment) . ' $'; ?>
                    </div>
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
                        <?php foreach ($participants as $p): ?>
                            <?php
                            $groupeDisplay = $p['groupe_label'] ?? $p['groupe_name'] ?? '';
                            $commissionDisplay = $p['commission_name'] ?? 'Rien';
                            ?>
                            <tr>
                                <td data-label="Participant">
                                    <div style="font-weight: 600;"><?php echo h($p['name'] ?? ''); ?></div>
                                    <div style="font-size: 13px; color: var(--muted);">
                                        <?php echo h($p['age'] ?? ''); ?> ans • <?php echo h($p['sexe'] ?? ''); ?> • <?php echo h($p['days'] ?? ''); ?> jour(s)
                                    </div>
                                </td>
                                <td data-label="Groupe"><?php echo h($groupeDisplay); ?></td>
                                <td data-label="Commission"><?php echo h($commissionDisplay); ?></td>
                                <td data-label="Contact"><?php echo h($p['phone'] ?? ''); ?></td>
                                <td data-label="Paiement">
                                    <span class="status-badge">
                                        <?php echo number_format((float) ($p['amount'] ?? 0), 2); ?> $
                                    </span>
                                </td>
                                <td class="actions-cell" data-label="Actions">
                                    <div class="actions">
                                        <button
                                            type="button"
                                            class="btn-action btn-edit edit-participant"
                                            title="Modifier"
                                            data-id="<?php echo (int) ($p['id'] ?? 0); ?>"
                                            data-nom="<?php echo h($p['name'] ?? ''); ?>"
                                            data-sexe="<?php echo h($p['sexe'] ?? ''); ?>"
                                            data-age="<?php echo (int) ($p['age'] ?? 0); ?>"
                                            data-groupe="<?php echo h($groupeDisplay); ?>"
                                            data-commission="<?php echo h($commissionDisplay); ?>"
                                            data-telephone="<?php echo h($p['phone'] ?? ''); ?>"
                                            data-montant="<?php echo h($p['amount'] ?? 0); ?>"
                                            data-jours="<?php echo (int) ($p['days'] ?? 0); ?>"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= Router::route('/encadreur') ?>" method="post" onsubmit="return confirm('Supprimer ce participant ?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="participant_id" value="<?php echo (int) ($p['id'] ?? 0); ?>">
                                            <button type="submit" class="btn-action btn-delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="participants-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2 class="section-title">Mon historique</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($activity_logs) === 0): ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding: 24px; color: var(--muted);">Aucun historique disponible</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($activity_logs as $log): ?>
                            <tr>
                                <td><?php echo h($log['created_at'] ?? ''); ?></td>
                                <td><?php echo h($log['action'] ?? ''); ?></td>
                                <td><?php echo h($log['detail'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
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
                    <?php if (isset($_SESSION['confirmation_ok'])): ?>
                        <div id="confirmation" style="color: green; padding: 10px; border: 1px solid green;">
                            <?php
                                echo h($_SESSION['confirmation_ok']);
                                unset($_SESSION['confirmation_ok']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['confirmation_non'])): ?>
                        <div id="confirmation" style="color: red; padding: 10px; border: 1px solid red;">
                            <?php
                                echo h($_SESSION['confirmation_non']);
                                unset($_SESSION['confirmation_non']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?= Router::route('/encadreur') ?>" method="post" id="inscriptionForm">
                        <input type="hidden" name="action" id="formAction" value="save">
                        <input type="hidden" name="participant_id" id="participantId" value="">
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
                                    placeholder="0.00" required value="0"/>
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
                    <button class="btn-secondary" type="button" id="cancelBtn">
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
    <script src="/js/store.js"></script>
    <script src="/js/script_encadreur.js"></script>
</body>

</html>
