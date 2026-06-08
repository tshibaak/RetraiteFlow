// Page Cordon / Super-admin - tableau de bord global (même design que Encadreur/Logistique)

document.addEventListener('DOMContentLoaded', function () {
    const store = window.RetraiteFlowStore;

    // --- Top bar utilisateur (même logique que les autres pages) ---
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");
    const accountSettings = document.getElementById("accountSettings");

    function getUserInfo() {
        return store?.getUserInfo?.() || {
            username: localStorage.getItem('username') || 'Cordon',
            role: localStorage.getItem('role') || 'Cordon / Super-admin'
        };
    }

    function initUserInfo() {
        const { username, role } = getUserInfo();
        const initials = String(username || 'C')
            .split(' ')
            .map((n) => n[0] || '')
            .slice(0, 2)
            .join('')
            .toUpperCase() || 'C';

        setText('userName', username);
        setText('userInfoName', username);
        setText('userInfoRole', role || 'Cordon / Super-admin');
        setText('userInitials', initials);
        setText('userInfoInitials', initials);
    }

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
            if (confirm('Se déconnecter de RetraiteFlow ?')) {
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

    // --- KPIs & tableaux (IDs compatibles avec cordon.html) ---
    const kpiParticipants = document.getElementById('kpiParticipants');
    const kpiLogistique = document.getElementById('kpiLogistique');
    const kpiFinance = document.getElementById('kpiFinance');

    const tableParticipantsSummary = document.getElementById('tableParticipantsSummary');
    const tableLogistiqueSummary = document.getElementById('tableLogistiqueSummary');
    const tableFinanceSummary = document.getElementById('tableFinanceSummary');

    function refreshData() {
        const participants = store?.getParticipants?.() || [];
        const dortoirs = store?.getDortoirs?.() || [];
        const ateliers = store?.getAteliers?.() || [];

        // Participants
        const totalP = participants.length;
        const solvables = participants.filter((p) => p.groupe === 'Solvable').length;
        const accredites = participants.filter((p) => p.groupe === 'Accrédité').length;
        const casSociaux = participants.filter((p) => p.groupe === 'Cas Social').length;
        const hommes = participants.filter((p) => p.sexe === 'Masculin').length;
        const femmes = participants.filter((p) => p.sexe === 'Féminin').length;

        if (kpiParticipants) kpiParticipants.textContent = String(totalP);

        if (tableParticipantsSummary) {
            tableParticipantsSummary.innerHTML = `
                <tr>
                    <td>Solvables</td>
                    <td>${solvables}</td>
                </tr>
                <tr>
                    <td>Accrédités</td>
                    <td>${accredites}</td>
                </tr>
                <tr>
                    <td>Cas sociaux</td>
                    <td>${casSociaux}</td>
                </tr>
                <tr>
                    <td>Hommes</td>
                    <td>${hommes}</td>
                </tr>
                <tr>
                    <td>Femmes</td>
                    <td>${femmes}</td>
                </tr>
            `;
        }

        // Logistique
        const totalDortoirs = dortoirs.length;
        const totalAteliers = ateliers.length;
        if (kpiLogistique) kpiLogistique.textContent = `${totalDortoirs} / ${totalAteliers}`;

        if (tableLogistiqueSummary) {
            const dortoirsFemmes = dortoirs.filter((d) => d.sexe === 'Féminin').length;
            const dortoirsHommes = dortoirs.filter((d) => d.sexe === 'Masculin').length;

            tableLogistiqueSummary.innerHTML = `
                <tr>
                    <td>Total dortoirs</td>
                    <td>${totalDortoirs}</td>
                </tr>
                <tr>
                    <td>Dortoirs Hommes</td>
                    <td>${dortoirsHommes}</td>
                </tr>
                <tr>
                    <td>Dortoirs Femmes</td>
                    <td>${dortoirsFemmes}</td>
                </tr>
                <tr>
                    <td>Total ateliers</td>
                    <td>${totalAteliers}</td>
                </tr>
            `;
        }

        // Finance (estimation)
        const montantTotal = participants
            .filter((p) => p.groupe !== 'Cas Social')
            .reduce((acc, p) => acc + (parseFloat(p.montant || '0') || 0), 0);

        if (kpiFinance) kpiFinance.textContent = `$${montantTotal.toFixed(2)}`;

        if (tableFinanceSummary) {
            tableFinanceSummary.innerHTML = `
                <tr>
                    <td>Total entrées (participants)</td>
                    <td>$${montantTotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <td>Cas sociaux (non payants)</td>
                    <td>${casSociaux}</td>
                </tr>
            `;
        }
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '';
    }

    initUserInfo();
    refreshData();
});

