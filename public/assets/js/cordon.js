// Cordon / Super-admin page - Vue globale

document.addEventListener('DOMContentLoaded', function () {
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");

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
            window.location.href = '/files/RetreatFlow/App_Ver_0_1/src/api/traitement_logout.php';
        });
    }

    document.getElementById('exportCordonBtn')?.addEventListener('click', exportCordonTables);
    renderCordonCharts();

    // Les synthèses sont calculées côté serveur.
});

function exportCordonTables() {
    const rows = [];
    document.querySelectorAll('.participants-section table').forEach((table) => {
        const title = table.closest('.participants-section')?.querySelector('.section-title')?.textContent.trim() || 'Section';
        table.querySelectorAll('tbody tr').forEach((row) => {
            rows.push({
                Section: title,
                Colonne1: row.cells[0]?.textContent.trim(),
                Colonne2: row.cells[1]?.textContent.trim(),
                Colonne3: row.cells[2]?.textContent.trim() || '',
                Colonne4: row.cells[3]?.textContent.trim() || '',
                Colonne5: row.cells[4]?.textContent.trim() || '',
                Colonne6: row.cells[5]?.textContent.trim() || ''
            });
        });
    });
    exportCsv('cordon_syntheses_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
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

function renderCordonCharts() {
    const participants = document.getElementById('cordonParticipantsChart');
    renderBarChart(participants, [
        { label: 'Solvables', value: Number(participants?.dataset.solvables || 0), color: '#2563eb' },
        { label: 'Accrédités', value: Number(participants?.dataset.accredites || 0), color: '#f59e0b' },
        { label: 'Sociaux', value: Number(participants?.dataset.sociaux || 0), color: '#ec4899' }
    ]);

    const finance = document.getElementById('cordonFinanceChart');
    renderBarChart(finance, [
        { label: 'Confirmés', value: Number(finance?.dataset.confirmed || 0), color: '#10b981' },
        { label: 'Attente', value: Number(finance?.dataset.pending || 0), color: '#f59e0b' },
        { label: 'Refusés', value: Number(finance?.dataset.rejected || 0), color: '#ef4444' }
    ]);
}

function renderBarChart(canvas, data) {
    if (!canvas || !canvas.getContext) return;
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const padding = 36;
    const max = Math.max(...data.map(d => d.value), 1);
    const barWidth = (width - padding * 2) / data.length - 18;

    ctx.clearRect(0, 0, width, height);
    data.forEach((item, index) => {
        const x = padding + index * (barWidth + 18);
        const barHeight = ((height - 82) * item.value) / max;
        const y = height - 44 - barHeight;

        ctx.fillStyle = item.color;
        ctx.fillRect(x, y, barWidth, barHeight);
        ctx.fillStyle = '#1e293b';
        ctx.font = '12px Inter, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(String(item.value), x + barWidth / 2, y - 8);
        ctx.fillStyle = '#64748b';
        ctx.fillText(item.label, x + barWidth / 2, height - 18);
    });
}

// Ajouter des styles CSS dynamiquement si nécessaire
const style = document.createElement('style');
style.textContent = `
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #e2e8f0;
        color: #1e293b;
    }

    .badge-success {
        background-color: #dcfce7;
        color: #10b981;
    }

    .badge-danger {
        background-color: #fce7f3;
        color: #ec4899;
    }

    .btn-secondary {
        background-color: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background-color: #e2e8f0;
    }

    .btn-primary {
        background-color: #2563eb;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
    }

    .actions {
        display: flex;
        gap: 6px;
    }

    .actions button {
        padding: 6px 10px;
        font-size: 12px;
    }
`;
document.head.appendChild(style);
