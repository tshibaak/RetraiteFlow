// Encadreur - gestion des participants (persistance + auto-attribution logistique)

let participants = [];
let editingIndex = -1;
let filteredParticipants = [];

document.addEventListener('DOMContentLoaded', function () {
    // Références DOM (après chargement de la page)
    const modal = document.getElementById("modal");
    const openFormBtn = document.getElementById("openFormBtn");
    const closeModalBtn = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("inscriptionForm");
    const participantsTable = document.getElementById("participantsTable");
    const totalParticipantsEl = document.getElementById("totalParticipants");
    const accreditesEl = document.getElementById("accredites");
    const casSociauxEl = document.getElementById("casSociaux");
    const totalRevenueEl = document.getElementById("totalRevenue");
    const groupeSelect = document.getElementById("groupe");
    const montantInput = document.getElementById("montant");
    const montantHelp = document.getElementById("montantHelp");
    const searchInput = document.getElementById("searchInput");
    const searchClear = document.getElementById("searchClear");
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");
    const accountSettings = document.getElementById("accountSettings");

function getUser() {
    return (window.RetraiteFlowStore?.getUserInfo?.() || { username: 'Utilisateur', role: 'encadreur' });
}

function initUserInfo() {
    const { username, role } = getUser();
    const initials = username.split(' ').map(n => n[0] || '').slice(0, 2).join('').toUpperCase() || 'U';

    const elUserName = document.getElementById('userName');
    const elUserInfoName = document.getElementById('userInfoName');
    const elUserInfoRole = document.getElementById('userInfoRole');
    const elUserInitials = document.getElementById('userInitials');
    const elUserInfoInitials = document.getElementById('userInfoInitials');

    if (elUserName) elUserName.textContent = username;
    if (elUserInfoName) elUserInfoName.textContent = username;
    if (elUserInfoRole) elUserInfoRole.textContent = role || 'Encadreur';
    if (elUserInitials) elUserInitials.textContent = initials;
    if (elUserInfoInitials) elUserInfoInitials.textContent = initials;
}

// Menu utilisateur
if (userMenuBtn && userMenuDropdown) {
    userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userMenuDropdown.classList.toggle('show');
        userMenuBtn.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
            userMenuDropdown.classList.remove('show');
            userMenuBtn.classList.remove('active');
        }
    });
}

if (logoutBtn) {
    logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
            localStorage.removeItem('username');
            localStorage.removeItem('role');
            sessionStorage.removeItem('username');
            sessionStorage.removeItem('role');
            window.location.href = '../../index.html';
        }
    });
}

if (accountSettings) {
    accountSettings.addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = '../../html/login.html';
    });
}

// Ouvrir le modal (nouveau participant)
if (openFormBtn && modal) {
    openFormBtn.addEventListener("click", () => {
        editingIndex = -1;
        const modalTitle = document.querySelector('.modal-title');
        if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-user-plus"></i> Nouveau Participant';
        if (form) form.reset();
        resetMontantState();
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
    });
}

function resetMontantState() {
    if (!montantInput) return;
    montantInput.disabled = false;
    montantInput.setAttribute("required", "required");
    montantInput.style.backgroundColor = "white";
    montantInput.style.cursor = "text";
    if (montantHelp) montantHelp.style.display = "none";
}

// Fermer le modal
function closeModal() {
    if (modal) modal.style.display = "none";
    document.body.style.overflow = "auto";
    if (form) form.reset();
    editingIndex = -1;
    resetMontantState();
}

if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
if (cancelBtn) cancelBtn.addEventListener("click", closeModal);
if (modal) modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
});

// Mettre à jour les statistiques
function updateStats() {
    const solvables = participants.filter(p => p.groupe === "Solvable").length;
    const accredites = participants.filter(p => p.groupe === "Accrédité").length;
    const casSociaux = participants.filter(p => p.groupe === "Cas Social").length;
    const revenue = participants
        .filter(p => p.groupe !== "Cas Social")
        .reduce((sum, p) => sum + parseFloat(p.montant || 0), 0);

    if (totalParticipantsEl) totalParticipantsEl.textContent = solvables;
    if (accreditesEl) accreditesEl.textContent = accredites;
    if (casSociauxEl) casSociauxEl.textContent = casSociaux;
    if (totalRevenueEl) totalRevenueEl.textContent = "$" + revenue.toFixed(2);
}

// Cas sociaux -> montant bloqué à 0
function handleGroupeChange() {
    const groupeValue = groupeSelect.value;

    if (groupeValue === "Cas Social") {
        montantInput.disabled = true;
        montantInput.value = "0";
        montantInput.removeAttribute("required");
        montantInput.style.backgroundColor = "#f1f5f9";
        montantInput.style.cursor = "not-allowed";
        if (montantHelp) montantHelp.style.display = "block";
    } else {
        montantInput.disabled = false;
        montantInput.setAttribute("required", "required");
        montantInput.style.backgroundColor = "white";
        montantInput.style.cursor = "text";
        if (montantHelp) montantHelp.style.display = "none";
        if (montantInput.value === "0") montantInput.value = "";
    }
}

if (groupeSelect) groupeSelect.addEventListener("change", handleGroupeChange);

// Filtrer les participants selon la recherche
function filterParticipants() {
    const searchTerm = (searchInput?.value || '').toLowerCase().trim();

    if (searchTerm === '') {
        filteredParticipants = [...participants];
    } else {
        filteredParticipants = participants.filter(p => {
            const nom = (p.nom || '').toLowerCase();
            const telephone = (p.telephone || '').toLowerCase();
            const groupe = (p.groupe || '').toLowerCase();
            const commission = (p.commission || '').toLowerCase();
            return nom.includes(searchTerm) || telephone.includes(searchTerm) || groupe.includes(searchTerm) || commission.includes(searchTerm);
        });
    }

    renderParticipants();

    if (searchClear) searchClear.style.display = searchTerm ? 'flex' : 'none';
}

if (searchInput) searchInput.addEventListener('input', filterParticipants);
if (searchClear) {
    searchClear.addEventListener('click', () => {
        searchInput.value = '';
        filterParticipants();
    });
}

// Afficher les participants dans le tableau
function renderParticipants() {
    const participantsToRender = filteredParticipants;

    if (!participantsToRender.length) {
        participantsTable.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">
                    <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.5;"></i>
                    ${(searchInput?.value || '').trim() ? 'Aucun participant trouvé pour cette recherche' : 'Aucun participant enregistré pour l\\'instant'}
                </td>
            </tr>
        `;
        return;
    }

    participantsTable.innerHTML = participantsToRender.map((participant) => {
        const realIndex = participants.indexOf(participant);
        return `
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="avatar">
                            ${(participant.nom || '').split(' ').map(n => n[0] || '').slice(0, 2).join('').toUpperCase()}
                        </div>
                        <div>
                            <div style="font-weight: 600;">${escapeHtml(participant.nom)}</div>
                            <div style="font-size: 13px; color: var(--muted);">
                                ${participant.age} ans • ${participant.sexe}
                            </div>
                        </div>
                    </div>
                </td>
                <td>${escapeHtml(participant.groupe)}</td>
                <td>${escapeHtml(participant.commission)}</td>
                <td>+243 ${escapeHtml(participant.telephone)}</td>
                <td>
                    <div style="font-weight: 600;">$${parseFloat(participant.montant || 0).toFixed(2)}</div>
                    <div style="font-size: 13px; color: var(--muted);">${participant.jours} jour(s)</div>
                </td>
                <td>
                    <div class="actions">
                        <div class="btn-action btn-edit" onclick="editParticipant(${realIndex})">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="btn-action btn-delete" onclick="deleteParticipant(${realIndex})">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Soumission du formulaire
form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const groupe = formData.get("groupe");
    const montant = groupe === "Cas Social" ? "0" : formData.get("montant");
    const { username } = getUser();

    const participant = {
        id: editingIndex >= 0 ? participants[editingIndex]?.id : undefined,
        nom: (formData.get("nom") || "").trim(),
        sexe: formData.get("sexe"),
        age: formData.get("age"),
        groupe: groupe,
        commission: formData.get("commission"),
        telephone: formData.get("telephone"),
        montant: montant,
        jours: formData.get("jours"),
        createdBy: username
    };

    const isEditing = editingIndex >= 0;

    if (isEditing) {
        participants[editingIndex] = participant;
    } else {
        participants.unshift(participant);
    }

    // Persister + auto-attribution (dortoir/atelier)
    if (window.RetraiteFlowStore?.saveParticipantsWithAutoAssign) {
        participants = window.RetraiteFlowStore.saveParticipantsWithAutoAssign(participants);
    } else {
        // fallback
        localStorage.setItem('rf_participants_v1', JSON.stringify(participants));
    }

    // Refresh filtre
    const currentSearch = (searchInput?.value || '').toLowerCase().trim();
    if (!currentSearch) filteredParticipants = [...participants];
    else filterParticipants();

    renderParticipants();
    updateStats();
    closeModal();

    showNotification(isEditing ? "Participant modifié avec succès !" : "Participant ajouté avec succès !", "success");
});

// Exporter (CSV)
function exportToExcel() {
    if (!participants.length) {
        alert("Aucun participant à exporter");
        return;
    }
    const headers = ["Nom", "Sexe", "Âge", "Groupe", "Commission", "Téléphone", "Paiement ($)", "Durée (jours)"];
    const rows = participants.map(p => [p.nom, p.sexe, p.age, p.groupe, p.commission, "+243 " + p.telephone, p.montant, p.jours]);
    const csvContent = [headers.join(","), ...rows.map(row => row.map(cell => `"${String(cell ?? '').replaceAll('"', '""')}"`).join(","))].join("\n");
    const BOM = "\uFEFF";
    const blob = new Blob([BOM + csvContent], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `participants_retraite_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Éditer un participant
window.editParticipant = function editParticipant(index) {
    const participant = participants[index];
    if (!participant) return;
    editingIndex = index;

    document.getElementById('nom').value = participant.nom;
    document.getElementById('sexe').value = participant.sexe;
    document.getElementById('age').value = participant.age;
    document.getElementById('groupe').value = participant.groupe;
    document.getElementById('commission').value = participant.commission;
    document.getElementById('telephone').value = participant.telephone;
    document.getElementById('montant').value = participant.montant;
    document.getElementById('jours').value = participant.jours;

    handleGroupeChange();

    const modalTitle = document.querySelector('.modal-title');
    if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-user-edit"></i> Modifier Participant';

    modal.style.display = "flex";
    document.body.style.overflow = "hidden";
};

// Supprimer un participant
window.deleteParticipant = function deleteParticipant(index) {
    if (!participants[index]) return;
    if (confirm("Êtes-vous sûr de vouloir supprimer ce participant ?")) {
        participants.splice(index, 1);
        if (window.RetraiteFlowStore?.saveParticipantsWithAutoAssign) {
            participants = window.RetraiteFlowStore.saveParticipantsWithAutoAssign(participants);
        } else {
            localStorage.setItem('rf_participants_v1', JSON.stringify(participants));
        }
        filterParticipants();
        updateStats();
        showNotification("Participant supprimé", "success");
    }
};

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[m];
    });
}

function showNotification(message, type = "success") {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${escapeHtml(message)}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function loadParticipants() {
    if (window.RetraiteFlowStore?.getParticipants) return window.RetraiteFlowStore.getParticipants();
    const raw = localStorage.getItem('rf_participants_v1');
    try {
        const parsed = raw ? JSON.parse(raw) : [];
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

    // Initialisation
    function init() {
        initUserInfo();
        participants = loadParticipants();
        // recalculer affectations au chargement (utile si dortoirs/ateliers changent)
        if (window.RetraiteFlowStore?.saveParticipantsWithAutoAssign) {
            participants = window.RetraiteFlowStore.saveParticipantsWithAutoAssign(participants);
        }
        filteredParticipants = [...participants];
        renderParticipants();
        updateStats();
        // Assurer la logique cas social
        if (groupeSelect) handleGroupeChange();
    }

    init();
}); // fin DOMContentLoaded