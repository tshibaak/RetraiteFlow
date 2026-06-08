// Discipline - suivi des entrées / sorties (frontend seulement, via localStorage)

document.addEventListener('DOMContentLoaded', function () {
    const store = window.RetraiteFlowStore;

    const PRESENCE_KEY = 'rf_discipline_presence_v1';

    let participants = store?.getParticipants?.() || [];
    let events = loadEvents();
    let filteredParticipants = [];

    // Références DOM
    const disciplineTable = document.getElementById('disciplineTable');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');

    const statEntreesJour = document.getElementById('statEntreesJour');
    const statSortiesJour = document.getElementById('statSortiesJour');
    const statPresents = document.getElementById('statPresents');

    const rapportDateInput = document.getElementById('rapportDate');
    const exportEntreesBtn = document.getElementById('exportEntreesBtn');
    const exportSortiesBtn = document.getElementById('exportSortiesBtn');
    const exportEntreesSortiesBtn = document.getElementById('exportEntreesSortiesBtn');
    const exportPresentsBtn = document.getElementById('exportPresentsBtn');

    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");
    const accountSettings = document.getElementById("accountSettings");

    // ----- User menu / infos -----
    function getUserInfo() {
        return store?.getUserInfo?.() || {
            username: localStorage.getItem('username') || 'Discipline',
            role: localStorage.getItem('role') || 'Discipline'
        };
    }

    function initUserInfo() {
        const { username, role } = getUserInfo();
        const initials = String(username || 'D').split(' ').map(n => n[0] || '').slice(0, 2).join('').toUpperCase() || 'D';

        setText('userName', username);
        setText('userInfoName', username);
        setText('userInfoRole', role || 'Discipline');
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

    // ====== Helpers stockage ======
    function loadEvents() {
        try {
            const raw = localStorage.getItem(PRESENCE_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }

    function saveEvents() {
        localStorage.setItem(PRESENCE_KEY, JSON.stringify(events));
    }

    function nowIso() {
        return new Date().toISOString();
    }

    function formatTime(iso) {
        if (!iso) return '—';
        const d = new Date(iso);
        return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    function formatDate(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return d.toISOString().slice(0, 10);
    }

    function sameDay(iso, ymd) {
        return formatDate(iso) === ymd;
    }

    function getLastEventByParticipant() {
        const map = new Map();
        for (const ev of events) {
            if (!map.get(ev.participantId) || new Date(ev.dateTime) > new Date(map.get(ev.participantId).dateTime)) {
                map.set(ev.participantId, ev);
            }
        }
        return map;
    }

    // ====== Actions Entrée / Sortie ======
    function addEvent(participantId, type) {
        const p = participants.find(pp => pp.id === participantId);
        if (!p) return;

        const ev = {
            id: `ev_${Date.now()}_${Math.random().toString(16).slice(2)}`,
            participantId,
            nom: p.nom,
            groupe: p.groupe,
            commission: p.commission,
            sexe: p.sexe,
            type,
            dateTime: nowIso()
        };
        events.push(ev);
        saveEvents();
        refresh();
    }

    // ====== UI : table participants ======
    function filterParticipants() {
        const term = (searchInput?.value || '').toLowerCase().trim();
        if (!term) {
            filteredParticipants = [...participants];
        } else {
            filteredParticipants = participants.filter(p =>
                (p.nom || '').toLowerCase().includes(term) ||
                (p.telephone || '').toLowerCase().includes(term) ||
                (p.groupe || '').toLowerCase().includes(term)
            );
        }

        if (searchClear) searchClear.style.display = term ? 'flex' : 'none';
        renderParticipants();
    }

    function renderParticipants() {
        if (!filteredParticipants.length) {
            disciplineTable.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">
                        <i class="fas fa-users" style="font-size:40px;margin-bottom:10px;opacity:.5;display:block;"></i>
                        Aucun participant trouvé.
                    </td>
                </tr>
            `;
            return;
        }

        const lastMap = getLastEventByParticipant();

        disciplineTable.innerHTML = filteredParticipants.map(p => {
            const last = lastMap.get(p.id);
            const lastType = last ? (last.type === 'entree' ? 'Entrée' : 'Sortie') : '—';
            const lastTime = last ? formatTime(last.dateTime) : '—';

            return `
                <tr>
                    <td>${escapeHtml(p.nom)}</td>
                    <td>${escapeHtml(p.groupe || '')}</td>
                    <td>${escapeHtml(p.commission || '')}</td>
                    <td>${lastType}</td>
                    <td>${lastTime}</td>
                    <td>
                        <div class="actions">
                            <button class="btn-secondary" type="button"
                                onclick="DisciplinePage.markEntry('${escapeAttr(p.id)}')">
                                <i class="fas fa-door-open"></i>
                            </button>
                            <button class="btn-secondary" type="button"
                                onclick="DisciplinePage.markExit('${escapeAttr(p.id)}')">
                                <i class="fas fa-door-closed"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // ====== Statistiques du haut ======
    function updateStats() {
        const today = rapportDateInput?.value || formatDate(nowIso());

        const entriesToday = events.filter(ev => ev.type === 'entree' && sameDay(ev.dateTime, today));
        const exitsToday = events.filter(ev => ev.type === 'sortie' && sameDay(ev.dateTime, today));

        const lastMap = getLastEventByParticipant();
        let presents = 0;
        for (const p of participants) {
            const last = lastMap.get(p.id);
            if (last && last.type === 'entree') presents++;
        }

        if (statEntreesJour) statEntreesJour.textContent = String(entriesToday.length);
        if (statSortiesJour) statSortiesJour.textContent = String(exitsToday.length);
        if (statPresents) statPresents.textContent = String(presents);
    }

    // ====== Rapports / export CSV ======
    function exportCsv(filename, rows) {
        if (!rows.length) {
            alert("Aucune donnée à exporter pour ce rapport.");
            return;
        }
        const headers = Object.keys(rows[0]);
        const csv = [
            headers.join(","),
            ...rows.map(r => headers.map(h => `"${String(r[h] ?? '').replace(/"/g, '""')}"`).join(","))
        ].join("\n");
        const blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8;" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportEntrees() {
        const day = rapportDateInput?.value || formatDate(nowIso());
        const rows = events
            .filter(ev => ev.type === 'entree' && sameDay(ev.dateTime, day))
            .map(ev => ({
                Date: formatDate(ev.dateTime),
                Heure: formatTime(ev.dateTime),
                Nom: ev.nom,
                Groupe: ev.groupe,
                Commission: ev.commission,
                Sexe: ev.sexe
            }));
        exportCsv(`entrees_${day}.csv`, rows);
    }

    function exportSorties() {
        const day = rapportDateInput?.value || formatDate(nowIso());
        const rows = events
            .filter(ev => ev.type === 'sortie' && sameDay(ev.dateTime, day))
            .map(ev => ({
                Date: formatDate(ev.dateTime),
                Heure: formatTime(ev.dateTime),
                Nom: ev.nom,
                Groupe: ev.groupe,
                Commission: ev.commission,
                Sexe: ev.sexe
            }));
        exportCsv(`sorties_${day}.csv`, rows);
    }

    function exportEntreesSorties() {
        const day = rapportDateInput?.value || formatDate(nowIso());
        const rows = events
            .filter(ev => (ev.type === 'entree' || ev.type === 'sortie') && sameDay(ev.dateTime, day))
            .sort((a, b) => new Date(a.dateTime) - new Date(b.dateTime))
            .map(ev => ({
                Date: formatDate(ev.dateTime),
                Heure: formatTime(ev.dateTime),
                Type: ev.type === 'entree' ? 'Entrée' : 'Sortie',
                Nom: ev.nom,
                Groupe: ev.groupe,
                Commission: ev.commission,
                Sexe: ev.sexe
            }));
        exportCsv(`entrees_sorties_${day}.csv`, rows);
    }

    function exportPresents() {
        const lastMap = getLastEventByParticipant();
        const rows = participants
            .filter(p => {
                const last = lastMap.get(p.id);
                return last && last.type === 'entree';
            })
            .map(p => ({
                Nom: p.nom,
                Groupe: p.groupe,
                Commission: p.commission,
                Sexe: p.sexe
            }));
        const day = formatDate(nowIso());
        exportCsv(`presents_${day}.csv`, rows);
    }

    // Rendre accessibles certaines fonctions depuis le HTML
    window.DisciplinePage = {
        markEntry(participantId) {
            addEvent(participantId, 'entree');
        },
        markExit(participantId) {
            addEvent(participantId, 'sortie');
        }
    };

    // Utils
    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '';
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[m]));
    }

    function escapeAttr(str) {
        return String(str || '').replace(/"/g, '').replace(/'/g, '');
    }

    function refresh() {
        filteredParticipants = [...participants];
        filterParticipants();
        updateStats();
    }

    // Initialisation
    if (rapportDateInput) {
        rapportDateInput.value = formatDate(nowIso());
    }

    if (searchInput) searchInput.addEventListener('input', filterParticipants);
    if (searchClear) {
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            filterParticipants();
        });
    }

    if (exportEntreesBtn) exportEntreesBtn.addEventListener('click', exportEntrees);
    if (exportSortiesBtn) exportSortiesBtn.addEventListener('click', exportSorties);
    if (exportEntreesSortiesBtn) exportEntreesSortiesBtn.addEventListener('click', exportEntreesSorties);
    if (exportPresentsBtn) exportPresentsBtn.addEventListener('click', exportPresents);

    initUserInfo();
    filteredParticipants = [...participants];
    renderParticipants();
    updateStats();
});

