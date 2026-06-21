// Discipline page - Suivi des entrées/sorties

let participants = [];
let filteredParticipants = [];
let events = [];

document.addEventListener('DOMContentLoaded', function () {
    const disciplineTable = document.getElementById('disciplineTable');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');
    const rapportDateInput = document.getElementById('rapportDate');

    // Récupérer les lignes du tableau
    const rows = document.querySelectorAll('#disciplineTable tr');
    rows.forEach(row => {
        const nom = row.cells[0]?.textContent.trim();
        const groupe = row.cells[1]?.textContent.trim();
        const commission = row.cells[2]?.textContent.trim();
        const cells = row.querySelectorAll('td');
        if(cells.length > 0 && cells[0].textContent.trim() !== ''){
            participants.push({
                nom,
                groupe,
                commission
            });
        }
    });

    // Recherche
    if(searchInput) {
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase().trim();
            if(term) {
                searchClear.style.display = 'flex';
                filterParticipants(term);
            } else {
                searchClear.style.display = 'none';
                renderParticipants(participants);
            }
        });
    }

    if(searchClear) {
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            searchClear.style.display = 'none';
            renderParticipants(participants);
        });
    }

    // Export CSV
    document.getElementById('exportEntreesBtn')?.addEventListener('click', exportEntrees);
    document.getElementById('exportSortiesBtn')?.addEventListener('click', exportSorties);
    document.getElementById('exportEntreesSortiesBtn')?.addEventListener('click', exportEntreesSorties);
    document.getElementById('exportPresentsBtn')?.addEventListener('click', exportPresents);
});

function filterParticipants(term) {
    filteredParticipants = participants.filter(p =>
        p.nom.toLowerCase().includes(term) ||
        p.groupe.toLowerCase().includes(term) ||
        p.commission.toLowerCase().includes(term)
    );
    renderParticipants(filteredParticipants);
}

function renderParticipants(list) {
    const table = document.getElementById('disciplineTable');
    table.innerHTML = list.map(p => `
        <tr>
            <td>${escapeHtml(p.nom)}</td>
            <td>${escapeHtml(p.groupe)}</td>
            <td>${escapeHtml(p.commission)}</td>
            <td class="lastActionType">—</td>
            <td class="lastActionTime">—</td>
            <td>
                <div class="actions">
                    <button class="btn-secondary" type="button" onclick="markEntry('${escapeAttr(p.nom)}')">
                        <i class="fas fa-door-open"></i>
                    </button>
                    <button class="btn-secondary" type="button" onclick="markExit('${escapeAttr(p.nom)}')">
                        <i class="fas fa-door-closed"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function markEntry(participantName) {
    // Récupérer l'ID du participant depuis la base de données
    fetchParticipantAndMark(participantName, 'entree');
}

function markExit(participantName) {
    fetchParticipantAndMark(participantName, 'sortie');
}

function fetchParticipantAndMark(participantName, type) {
    // En pratique, il faudrait faire un appel pour obtenir l'ID
    // Pour la démo, on simule avec un message
    alert(`${type === 'entree' ? 'Entrée' : 'Sortie'} enregistrée pour ${participantName}`);
    
    // Mettre à jour les stats
    updateStats();
}

function updateStats() {
    const today = document.getElementById('rapportDate')?.value || new Date().toISOString().slice(0, 10);
    
    // Appel au serveur pour obtenir les stats du jour
    fetch('/api/discipline', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_logs&date=${today}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.status && data.logs) {
            const entries = data.logs.filter(l => l.type_log === 'entree').length;
            const exits = data.logs.filter(l => l.type_log === 'sortie').length;
            
            document.getElementById('statEntreesJour').textContent = entries;
            document.getElementById('statSortiesJour').textContent = exits;
        }
    })
    .catch(e => console.error('Erreur:', e));
}

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
    const day = document.getElementById('rapportDate')?.value || new Date().toISOString().slice(0, 10);
    // Récupérer les logs du serveur
    fetch('/api/discipline', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_logs&date=${day}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.logs) {
            const rows = data.logs
                .filter(l => l.type_log === 'entree')
                .map(l => ({
                    Date: day,
                    Heure: new Date(l.logged_at).toLocaleTimeString('fr-FR'),
                    Nom: l.nom_part,
                    Groupe: l.groupe_part,
                    Commission: l.commission_part,
                    Sexe: l.sexe_part
                }));
            exportCsv(`entrees_${day}.csv`, rows);
        }
    });
}

function exportSorties() {
    const day = document.getElementById('rapportDate')?.value || new Date().toISOString().slice(0, 10);
    fetch('/api/discipline', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_logs&date=${day}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.logs) {
            const rows = data.logs
                .filter(l => l.type_log === 'sortie')
                .map(l => ({
                    Date: day,
                    Heure: new Date(l.logged_at).toLocaleTimeString('fr-FR'),
                    Nom: l.nom_part,
                    Groupe: l.groupe_part,
                    Commission: l.commission_part,
                    Sexe: l.sexe_part
                }));
            exportCsv(`sorties_${day}.csv`, rows);
        }
    });
}

function exportEntreesSorties() {
    const day = document.getElementById('rapportDate')?.value || new Date().toISOString().slice(0, 10);
    fetch('/api/discipline', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_logs&date=${day}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.logs) {
            const rows = data.logs
                .map(l => ({
                    Date: day,
                    Heure: new Date(l.logged_at).toLocaleTimeString('fr-FR'),
                    Type: l.type_log === 'entree' ? 'Entrée' : 'Sortie',
                    Nom: l.nom_part,
                    Groupe: l.groupe_part,
                    Commission: l.commission_part,
                    Sexe: l.sexe_part
                }));
            exportCsv(`entrees_sorties_${day}.csv`, rows);
        }
    });
}

function exportPresents() {
    const day = document.getElementById('rapportDate')?.value || new Date().toISOString().slice(0, 10);
    fetch('/api/discipline', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_logs&date=${day}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.logs) {
            const lastLogMap = new Map();
            data.logs.forEach(l => {
                const key = l.id_participant;
                if(!lastLogMap.get(key) || new Date(l.logged_at) > new Date(lastLogMap.get(key).logged_at)) {
                    lastLogMap.set(key, l);
                }
            });
            
            const rows = Array.from(lastLogMap.values())
                .filter(l => l.type_log === 'entree')
                .map(l => ({
                    Nom: l.nom_part,
                    Groupe: l.groupe_part,
                    Commission: l.commission_part,
                    Sexe: l.sexe_part
                }));
            exportCsv(`presents_${day}.csv`, rows);
        }
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function escapeAttr(text) {
    return text.replace(/'/g, '\\\'').replace(/"/g, '&quot;');
}
