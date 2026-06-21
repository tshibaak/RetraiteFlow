// Logistique page - Gestion des dortoirs et ateliers

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');

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

    // Recherche
    if(searchInput) {
        searchInput.addEventListener('input', filterDortoirs);
    }

    if(searchClear) {
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            searchClear.style.display = 'none';
            filterDortoirs();
        });
    }

    // Formulaires
    document.getElementById('dortoirForm')?.addEventListener('submit', saveDortoir);
    document.getElementById('atelierForm')?.addEventListener('submit', saveAtelier);
    document.getElementById('exportLogistiqueBtn')?.addEventListener('click', exportLogistique);
});

function openDortoirModal() {
    const modal = document.getElementById('dortoirModal');
    if(modal) {
        modal.style.display = 'flex';
        document.getElementById('dortoirForm')?.reset();
    }
}

function closeDortoirModal() {
    const modal = document.getElementById('dortoirModal');
    if(modal) {
        modal.style.display = 'none';
    }
}

function openAtelierModal() {
    const modal = document.getElementById('atelierModal');
    if(modal) {
        modal.style.display = 'flex';
        document.getElementById('atelierForm')?.reset();
    }
}

function closeAtelierModal() {
    const modal = document.getElementById('atelierModal');
    if(modal) {
        modal.style.display = 'none';
    }
}

function saveDortoir(e) {
    e.preventDefault();
    const nom = document.getElementById('dortoirNom').value;
    const sexe = document.getElementById('dortoirSexe').value;
    const ageMin = document.getElementById('dortoirAgeMin').value;
    const ageMax = document.getElementById('dortoirAgeMax').value;
    const capacite = document.getElementById('dortoirCapacite').value;

    fetch('/api/logistique', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_dortoir&nom=${encodeURIComponent(nom)}&sexe=${encodeURIComponent(sexe)}&age_min=${ageMin}&age_max=${ageMax}&capacite=${capacite}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.status) {
            alert('Dortoir créé avec succès');
            closeDortoirModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(e => alert('Erreur: ' + e));
}

function deleteDortoir(id) {
    if(confirm('Confirmer la suppression de ce dortoir?')) {
        fetch('/api/logistique', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_dortoir&id_dortoir=${id}`
        })
        .then(r => r.json())
        .then(data => {
            if(data.status) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
}

function saveAtelier(e) {
    e.preventDefault();
    const nom = document.getElementById('atelierNom').value;
    const ageMin = document.getElementById('atelierAgeMin').value;
    const ageMax = document.getElementById('atelierAgeMax').value;
    const capacite = document.getElementById('atelierCapacite').value;

    fetch('/api/logistique', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_atelier&nom=${encodeURIComponent(nom)}&age_min=${ageMin}&age_max=${ageMax}&capacite=${capacite}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.status) {
            alert('Atelier créé avec succès');
            closeAtelierModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(e => alert('Erreur: ' + e));
}

function deleteAtelier(id) {
    if(confirm('Confirmer la suppression de cet atelier?')) {
        fetch('/api/logistique', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_atelier&id_atelier=${id}`
        })
        .then(r => r.json())
        .then(data => {
            if(data.status) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
}

function filterDortoirs() {
    const term = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const searchClear = document.getElementById('searchClear');
    const rows = document.querySelectorAll('#dortoirsTableBody tr');

    if(term) {
        searchClear.style.display = 'flex';
        rows.forEach(row => {
            const nom = row.cells[0]?.textContent.toLowerCase() || '';
            row.style.display = nom.includes(term) ? '' : 'none';
        });
    } else {
        searchClear.style.display = 'none';
        rows.forEach(row => row.style.display = '');
    }
}

// Fermer les modals en cliquant sur le fond
document.addEventListener('click', function(e) {
    const dortoirModal = document.getElementById('dortoirModal');
    const atelierModal = document.getElementById('atelierModal');

    if(e.target === dortoirModal) {
        closeDortoirModal();
    }
    if(e.target === atelierModal) {
        closeAtelierModal();
    }
});

function exportLogistique() {
    const rows = [];
    document.querySelectorAll('#dortoirsTableBody tr').forEach(row => {
        if (row.cells.length < 6) return;
        rows.push({
            Type: 'Dortoir',
            Nom: row.cells[0]?.textContent.trim(),
            Detail1: row.cells[1]?.textContent.trim(),
            Detail2: row.cells[2]?.textContent.trim(),
            Capacite: row.cells[3]?.textContent.trim(),
            Occupants: row.cells[4]?.textContent.trim(),
            Statut: row.cells[5]?.textContent.trim()
        });
    });
    document.querySelectorAll('#atelierCards .rf-card').forEach(card => {
        rows.push({
            Type: 'Atelier',
            Nom: card.querySelector('h3')?.textContent.trim(),
            Detail1: card.querySelector('.text-small')?.textContent.trim(),
            Detail2: '',
            Capacite: card.querySelector('.card-stat .badge')?.textContent.trim(),
            Occupants: '',
            Statut: card.querySelectorAll('.card-stat .badge')[1]?.textContent.trim() || ''
        });
    });
    exportCsv('logistique_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function exportCsv(filename, rows) {
    if (!rows.length) {
        alert('Aucune donnée à exporter.');
        return;
    }
    const headers = Object.keys(rows[0]);
    const csv = [
        headers.join(','),
        ...rows.map(r => headers.map(h => `"${String(r[h] ?? '').replace(/"/g, '""')}"`).join(','))
    ].join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
