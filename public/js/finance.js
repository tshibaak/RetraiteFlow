// Finance page - Gestion financière

document.addEventListener('DOMContentLoaded', function () {
    // Ajouter les événements aux formulaires
    document.getElementById('inputsForm')?.addEventListener('submit', addInput);
    document.getElementById('forecastForm')?.addEventListener('submit', addForecast);
    document.getElementById('actualForm')?.addEventListener('submit', addActual);

    // Export buttons
    document.getElementById('exportInputsBtn')?.addEventListener('click', exportInputs);
    document.getElementById('exportForecastBtn')?.addEventListener('click', exportForecasts);
    document.getElementById('exportActualBtn')?.addEventListener('click', exportActuals);
    document.getElementById('exportRemainingBtn')?.addEventListener('click', exportRemaining);
    document.getElementById('exportParticipantsBtn')?.addEventListener('click', exportFinanceParticipants);
    document.getElementById('exportLogsBtn')?.addEventListener('click', exportFinanceLogs);
    document.querySelectorAll('.finance-status-btn').forEach((button) => {
        button.addEventListener('click', () => setParticipantStatus(button.dataset.id, button.dataset.status));
    });

    // Mettre à jour le tableau des restants
    updateRemainingTable();
    renderFinanceCharts();
});

function setParticipantStatus(id, status) {
    const label = status === 'confirme' ? 'confirmer' : 'déconfirmer';
    if (!confirm(`Voulez-vous ${label} ce participant ?`)) return;

    fetch('/api/finance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=set_participant_status&id_participant=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.status) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(e => alert('Erreur: ' + e));
}

function addInput(e) {
    e.preventDefault();
    const form = e.target;
    const source = document.getElementById('inputSource').value;
    const amount = document.getElementById('inputAmount').value;

    fetch('/api/finance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_input&source=${encodeURIComponent(source)}&amount=${amount}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.status) {
            alert('Entrée ajoutée avec succès');
            form.reset();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(e => alert('Erreur: ' + e));
}

function deleteInput(id) {
    if(confirm('Confirmer la suppression?')) {
        fetch('/api/finance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_input&id_input=${id}`
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

function addForecast(e) {
    e.preventDefault();
    const form = e.target;
    const commission = document.getElementById('forecastCommission').value;
    const budget = document.getElementById('forecastAmount').value;

    fetch('/api/finance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_forecast&commission=${encodeURIComponent(commission)}&budget=${budget}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.status) {
            alert('Prévision ajoutée avec succès');
            form.reset();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(e => alert('Erreur: ' + e));
}

function deleteForecast(id) {
    if(confirm('Confirmer la suppression?')) {
        fetch('/api/finance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_forecast&id_forecast=${id}`
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

function addActual(e) {
    e.preventDefault();
    const form = e.target;
    const commission = document.getElementById('actualCommission').value;
    const amount = document.getElementById('actualAmount').value;

    fetch('/api/finance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_actual&commission=${encodeURIComponent(commission)}&amount=${amount}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.status) {
            alert('Dépense ajoutée avec succès');
            form.reset();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(e => alert('Erreur: ' + e));
}

function deleteActual(id) {
    if(confirm('Confirmer la suppression?')) {
        fetch('/api/finance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_actual&id_actual=${id}`
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

function updateRemainingTable() {
    const table = document.getElementById('remainingTableBody');
    if(!table) return;

    const forecastRows = document.querySelectorAll('#forecastTableBody tr');
    const actualRows = document.querySelectorAll('#actualTableBody tr');

    const forecasts = {};
    const actuals = {};

    forecastRows.forEach(row => {
        const commission = row.cells[0]?.textContent.trim();
        const budget = parseMoney(row.cells[1]?.textContent);
        if(commission && commission !== 'Commission') {
            forecasts[commission] = budget;
        }
    });

    actualRows.forEach(row => {
        const commission = row.cells[0]?.textContent.trim();
        const amount = parseMoney(row.cells[1]?.textContent);
        if(commission && commission !== 'Commission') {
            actuals[commission] = (actuals[commission] || 0) + amount;
        }
    });

    const commissions = new Set([
        ...Object.keys(forecasts),
        ...Object.keys(actuals)
    ]);

    const html = Array.from(commissions).sort().map(commission => {
        const budget = forecasts[commission];
        const actual = actuals[commission] || 0;
        const remaining = (budget || 0) - actual;
        return `
            <tr>
                <td data-label="Commission">${escapeHtml(commission)}</td>
                <td data-label="Budget">$${(budget || 0).toFixed(2)}</td>
                <td data-label="Dépenses réelles">$${actual.toFixed(2)}</td>
                <td data-label="Reste">$${remaining.toFixed(2)}</td>
            </tr>
        `;
    }).join('');

    table.innerHTML = html || '<tr><td colspan="4" style="text-align:center;">Aucun budget prévisionnel</td></tr>';
}

function exportCsv(filename, rows) {
    if (!rows.length) {
        alert("Aucune donnée à exporter.");
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

function exportInputs() {
    const rows = [];
    document.querySelectorAll('#inputsTableBody tr').forEach(row => {
        rows.push({
            Source: row.cells[0]?.textContent.trim(),
            Montant: row.cells[1]?.textContent.trim()
        });
    });
    exportCsv('entrees_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function exportForecasts() {
    const rows = [];
    document.querySelectorAll('#forecastTableBody tr').forEach(row => {
        rows.push({
            Commission: row.cells[0]?.textContent.trim(),
            Budget: row.cells[1]?.textContent.trim()
        });
    });
    exportCsv('previsions_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function exportActuals() {
    const rows = [];
    document.querySelectorAll('#actualTableBody tr').forEach(row => {
        rows.push({
            Commission: row.cells[0]?.textContent.trim(),
            Dépense: row.cells[1]?.textContent.trim()
        });
    });
    exportCsv('depenses_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function exportRemaining() {
    const rows = [];
    document.querySelectorAll('#remainingTableBody tr').forEach(row => {
        rows.push({
            Commission: row.cells[0]?.textContent.trim(),
            Budget: row.cells[1]?.textContent.trim(),
            'Dépenses réelles': row.cells[2]?.textContent.trim(),
            Reste: row.cells[3]?.textContent.trim()
        });
    });
    exportCsv('budget_restant_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function exportFinanceParticipants() {
    const rows = [];
    document.querySelectorAll('#financeParticipantsBody tr').forEach(row => {
        if (row.cells.length < 7) return;
        rows.push({
            Participant: row.cells[0]?.textContent.trim(),
            Encadreur: row.cells[1]?.textContent.trim(),
            Groupe: row.cells[2]?.textContent.trim(),
            Commission: row.cells[3]?.textContent.trim(),
            Montant: row.cells[4]?.textContent.trim(),
            Statut: row.cells[5]?.textContent.trim()
        });
    });
    exportCsv('participants_finance_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function exportFinanceLogs() {
    const rows = [];
    document.querySelectorAll('#financeLogsBody tr').forEach(row => {
        if (row.cells.length < 5) return;
        rows.push({
            Date: row.cells[0]?.textContent.trim(),
            Encadreur: row.cells[1]?.textContent.trim(),
            Module: row.cells[2]?.textContent.trim(),
            Action: row.cells[3]?.textContent.trim(),
            Detail: row.cells[4]?.textContent.trim()
        });
    });
    exportCsv('logs_encadreurs_' + new Date().toISOString().slice(0, 10) + '.csv', rows);
}

function renderFinanceCharts() {
    renderBarChart(document.getElementById('financeStatusChart'), [
        { label: 'Confirmés', value: Number(document.getElementById('financeStatusChart')?.dataset.confirmed || 0), color: '#10b981' },
        { label: 'Attente', value: Number(document.getElementById('financeStatusChart')?.dataset.pending || 0), color: '#f59e0b' },
        { label: 'Déconfirmés', value: Number(document.getElementById('financeStatusChart')?.dataset.rejected || 0), color: '#ef4444' }
    ]);

    renderBarChart(document.getElementById('financeBudgetChart'), [
        { label: 'Entrées', value: Number(document.getElementById('financeBudgetChart')?.dataset.inputs || 0), color: '#2563eb' },
        { label: 'Réelles', value: Number(document.getElementById('financeBudgetChart')?.dataset.actuals || 0), color: '#ef4444' },
        { label: 'Prévisions', value: Number(document.getElementById('financeBudgetChart')?.dataset.forecasts || 0), color: '#8b5cf6' }
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
    ctx.font = '12px Inter, Arial, sans-serif';
    ctx.fillStyle = '#64748b';

    data.forEach((item, index) => {
        const x = padding + index * (barWidth + 18);
        const barHeight = ((height - 82) * item.value) / max;
        const y = height - 44 - barHeight;

        ctx.fillStyle = item.color;
        ctx.fillRect(x, y, barWidth, barHeight);
        ctx.fillStyle = '#1e293b';
        ctx.textAlign = 'center';
        ctx.fillText(String(item.value.toFixed(item.value % 1 ? 2 : 0)), x + barWidth / 2, y - 8);
        ctx.fillStyle = '#64748b';
        ctx.fillText(item.label, x + barWidth / 2, height - 18);
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
    return String(text ?? '').replace(/[&<>"']/g, m => map[m]);
}

function parseMoney(text) {
    const normalized = String(text ?? '')
        .replace(/\s/g, '')
        .replace(/\$/g, '');

    if (normalized.includes(',') && normalized.includes('.')) {
        return parseFloat(normalized.replace(/,/g, '')) || 0;
    }

    return parseFloat(normalized.replace(',', '.')) || 0;
}
