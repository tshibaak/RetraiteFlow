// Finance - Entrées / Prévisions / Dépenses réelles + calcul automatique (frontend only)

document.addEventListener('DOMContentLoaded', function () {
    const store = window.RetraiteFlowStore;

    // ---- LocalStorage keys (manual parts) ----
    const KEY_INPUTS = 'rf_finance_inputs_manual_v1';
    const KEY_FORECAST = 'rf_finance_forecast_v1';
    const KEY_ACTUAL = 'rf_finance_actual_v1';

    const PRESENCE = {
        // Entrées automatiques basées sur les participants (paiements)
        participantsPayments: 0
    };

    // ---- DOM ----
    const inputsForm = document.getElementById('inputsForm');
    const inputSource = document.getElementById('inputSource');
    const inputAmount = document.getElementById('inputAmount');
    const inputsTableBody = document.getElementById('inputsTableBody');

    const forecastForm = document.getElementById('forecastForm');
    const forecastCommission = document.getElementById('forecastCommission');
    const forecastAmount = document.getElementById('forecastAmount');
    const forecastTableBody = document.getElementById('forecastTableBody');

    const actualForm = document.getElementById('actualForm');
    const actualCommission = document.getElementById('actualCommission');
    const actualAmount = document.getElementById('actualAmount');
    const actualTableBody = document.getElementById('actualTableBody');

    const remainingTableBody = document.getElementById('remainingTableBody');

    const statTotalInputs = document.getElementById('statTotalInputs');
    const statTotalActualExpenses = document.getElementById('statTotalActualExpenses');
    const statSolde = document.getElementById('statSolde');
    const statRemainingBudget = document.getElementById('statRemainingBudget');

    const exportInputsBtn = document.getElementById('exportInputsBtn');
    const exportForecastBtn = document.getElementById('exportForecastBtn');
    const exportActualBtn = document.getElementById('exportActualBtn');
    const exportRemainingBtn = document.getElementById('exportRemainingBtn');

    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");
    const accountSettings = document.getElementById("accountSettings");

    // ---- Auth header (same pattern as other pages) ----
    function initUserInfo() {
        const username = localStorage.getItem('username') || sessionStorage.getItem('username') || 'Utilisateur';
        const role = localStorage.getItem('role') || sessionStorage.getItem('role') || 'Finance';
        const initials = String(username || 'U').split(' ').map(n => n[0] || '').slice(0, 2).join('').toUpperCase() || 'U';

        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val ?? '';
        };

        setText('userName', username);
        setText('userInfoName', username);
        setText('userInfoRole', role);
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

    initUserInfo();

    // ---- Helpers ----
    function safeJsonParse(value, fallback) {
        try {
            if (!value) return fallback;
            const parsed = JSON.parse(value);
            return Array.isArray(parsed) ? parsed : fallback;
        } catch {
            return fallback;
        }
    }

    function uid(prefix = 'id') {
        return `${prefix}_${Date.now()}_${Math.random().toString(16).slice(2)}`;
    }

    function toMoney(value) {
        const n = parseFloat(String(value ?? '').replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    }

    function moneyFormat(n) {
        const num = toMoney(n);
        return `$${num.toFixed(2)}`;
    }

    function load(key) {
        const raw = localStorage.getItem(key);
        return safeJsonParse(raw, []);
    }

    function save(key, arr) {
        localStorage.setItem(key, JSON.stringify(arr));
    }

    function exportCsv(filename, rows) {
        if (!rows || !rows.length) {
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

    // ---- Data models (manual) ----
    let inputs = load(KEY_INPUTS); // {id, source, amount}
    let forecasts = load(KEY_FORECAST); // {id, commission, budget}
    let actuals = load(KEY_ACTUAL); // {id, commission, expense}

    // ---- Derived inputs: sum of participants payments ----
    function computeParticipantsPayments() {
        const participants = store?.getParticipants?.() || [];
        let sum = 0;
        for (const p of participants) {
            // Cas Social -> montant forcé à 0 côté Encadreur
            sum += toMoney(p.montant);
        }
        return sum;
    }

    // ---- Calculs ----
    function computeTotals() {
        const paymentsParticipants = computeParticipantsPayments();
        PRESENCE.participantsPayments = paymentsParticipants;

        const totalInputsManual = inputs.reduce((acc, i) => acc + toMoney(i.amount), 0);
        const totalInputs = paymentsParticipants + totalInputsManual;

        const totalForecast = forecasts.reduce((acc, f) => acc + toMoney(f.budget), 0);
        const totalActual = actuals.reduce((acc, a) => acc + toMoney(a.expense), 0);

        const solde = totalInputs - totalActual;
        const remainingBudget = totalForecast - totalActual;

        return { paymentsParticipants, totalInputs, totalInputsManual, totalForecast, totalActual, solde, remainingBudget };
    }

    function groupActualByCommission() {
        const map = new Map(); // commission -> sum
        for (const a of actuals) {
            const key = String(a.commission || '').trim();
            map.set(key, (map.get(key) || 0) + toMoney(a.expense));
        }
        return map;
    }

    function computeRemainingByCommission() {
        const actualMap = groupActualByCommission();
        const rows = [];
        for (const f of forecasts) {
            const commission = String(f.commission || '').trim();
            const budget = toMoney(f.budget);
            const actual = actualMap.get(commission) || 0;
            const remaining = budget - actual;
            rows.push({ commission, budget, actual, remaining });
        }

        // Commissions qui n'ont que de l'actuel (facultatif)
        for (const [commission, actual] of actualMap.entries()) {
            const hasForecast = forecasts.some(f => String(f.commission || '').trim() === commission);
            if (!hasForecast) {
                rows.push({ commission, budget: 0, actual, remaining: -actual });
            }
        }

        // Trier pour affichage
        rows.sort((a, b) => a.commission.localeCompare(b.commission));
        return rows;
    }

    // ---- Render ----
    function renderAll() {
        const totals = computeTotals();

        if (statTotalInputs) statTotalInputs.textContent = moneyFormat(totals.totalInputs);
        if (statTotalActualExpenses) statTotalActualExpenses.textContent = moneyFormat(totals.totalActual);
        if (statSolde) {
            statSolde.textContent = moneyFormat(totals.solde);
            statSolde.style.color = totals.solde < 0 ? 'var(--danger)' : 'var(--dark)';
        }
        if (statRemainingBudget) {
            statRemainingBudget.textContent = moneyFormat(totals.remainingBudget);
            statRemainingBudget.style.color = totals.remainingBudget < 0 ? 'var(--danger)' : 'var(--dark)';
        }

        renderInputs();
        renderForecasts();
        renderActuals();
        renderRemaining();
    }

    function renderInputs() {
        if (!inputsTableBody) return;

        const paymentsParticipants = PRESENCE.participantsPayments;
        const manualRows = inputs.map(i => ({
            id: i.id,
            source: String(i.source || '').trim(),
            amount: toMoney(i.amount)
        }));

        const autoRow = `
            <tr>
                <td><strong>Paiements participants</strong></td>
                <td>${moneyFormat(paymentsParticipants)}</td>
                <td>
                    <span style="font-size:12px;color:var(--muted);">auto</span>
                </td>
            </tr>`;

        const manual = manualRows.map(r => `
            <tr>
                <td>${escapeHtml(r.source || '—')}</td>
                <td>${moneyFormat(r.amount)}</td>
                <td>
                    <div class="actions">
                        <div class="btn-action btn-delete" title="Supprimer" onclick="FinancePage.removeInput('${escapeAttr(r.id)}')">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');

        inputsTableBody.innerHTML = autoRow + manual;
    }

    function renderForecasts() {
        if (!forecastTableBody) return;
        const rows = forecasts.map(f => ({
            id: f.id,
            commission: String(f.commission || '').trim(),
            budget: toMoney(f.budget)
        }));

        forecastTableBody.innerHTML = rows.length ? rows.map(r => `
            <tr>
                <td>${escapeHtml(r.commission || '—')}</td>
                <td>${moneyFormat(r.budget)}</td>
                <td>
                    <div class="actions">
                        <div class="btn-action btn-delete" title="Supprimer" onclick="FinancePage.removeForecast('${escapeAttr(r.id)}')">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="3" style="text-align:center;padding:24px;color:var(--muted);">Aucune prévision</td></tr>`;
    }

    function renderActuals() {
        if (!actualTableBody) return;
        const rows = actuals.map(a => ({
            id: a.id,
            commission: String(a.commission || '').trim(),
            expense: toMoney(a.expense)
        }));

        actualTableBody.innerHTML = rows.length ? rows.map(r => `
            <tr>
                <td>${escapeHtml(r.commission || '—')}</td>
                <td>${moneyFormat(r.expense)}</td>
                <td>
                    <div class="actions">
                        <div class="btn-action btn-delete" title="Supprimer" onclick="FinancePage.removeActual('${escapeAttr(r.id)}')">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="3" style="text-align:center;padding:24px;color:var(--muted);">Aucune dépense réelle</td></tr>`;
    }

    function renderRemaining() {
        if (!remainingTableBody) return;
        const rows = computeRemainingByCommission();

        if (!rows.length) {
            remainingTableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:24px;color:var(--muted);">Ajoute des prévisions pour voir le reste à payer.</td></tr>`;
            return;
        }

        remainingTableBody.innerHTML = rows.map(r => {
            const badgeColor = r.remaining < 0 ? 'var(--danger)' : 'var(--success)';
            return `
                <tr>
                    <td>${escapeHtml(r.commission)}</td>
                    <td>${moneyFormat(r.budget)}</td>
                    <td>${moneyFormat(r.actual)}</td>
                    <td style="color:${badgeColor};font-weight:700;">${moneyFormat(r.remaining)}</td>
                </tr>
            `;
        }).join('');
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[m]));
    }

    function escapeAttr(str) {
        return String(str ?? '').replace(/["']/g, '');
    }

    // ---- Actions (public) ----
    window.FinancePage = {
        removeInput(id) {
            inputs = inputs.filter(x => x.id !== id);
            save(KEY_INPUTS, inputs);
            renderAll();
        },
        removeForecast(id) {
            forecasts = forecasts.filter(x => x.id !== id);
            save(KEY_FORECAST, forecasts);
            renderAll();
        },
        removeActual(id) {
            actuals = actuals.filter(x => x.id !== id);
            save(KEY_ACTUAL, actuals);
            renderAll();
        }
    };

    // ---- Form submissions ----
    if (inputsForm) {
        inputsForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const source = String(inputSource?.value || '').trim();
            const amount = toMoney(inputAmount?.value);
            if (!source) return alert('Veuillez saisir la source.');
            if (amount <= 0) return alert('Veuillez saisir un montant valide.');

            inputs.unshift({ id: uid('in'), source, amount });
            save(KEY_INPUTS, inputs);
            inputsForm.reset();
            renderAll();
        });
    }

    if (forecastForm) {
        forecastForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const commission = String(forecastCommission?.value || '').trim();
            const budget = toMoney(forecastAmount?.value);
            if (!commission) return alert('Veuillez saisir la commission/budget.');
            if (budget <= 0) return alert('Veuillez saisir un budget valide.');

            forecasts.unshift({ id: uid('fc'), commission, budget });
            save(KEY_FORECAST, forecasts);
            forecastForm.reset();
            renderAll();
        });
    }

    if (actualForm) {
        actualForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const commission = String(actualCommission?.value || '').trim();
            const expense = toMoney(actualAmount?.value);
            if (!commission) return alert('Veuillez saisir la commission.');
            if (expense <= 0) return alert('Veuillez saisir une dépense valide.');

            actuals.unshift({ id: uid('ac'), commission, expense });
            save(KEY_ACTUAL, actuals);
            actualForm.reset();
            renderAll();
        });
    }

    // ---- Exports ----
    if (exportInputsBtn) {
        exportInputsBtn.addEventListener('click', () => {
            const totals = computeTotals();
            const rows = [
                { Source: 'Paiements participants', Montant: totals.paymentsParticipants },
                ...inputs.map(i => ({ Source: i.source, Montant: toMoney(i.amount) }))
            ];
            exportCsv(`entrees_${new Date().toISOString().slice(0, 10)}.csv`, rows);
        });
    }

    if (exportForecastBtn) {
        exportForecastBtn.addEventListener('click', () => {
            const rows = forecasts.map(f => ({ Commission: f.commission, Budget: toMoney(f.budget) }));
            exportCsv(`previsions_${new Date().toISOString().slice(0, 10)}.csv`, rows);
        });
    }

    if (exportActualBtn) {
        exportActualBtn.addEventListener('click', () => {
            const rows = actuals.map(a => ({ Commission: a.commission, Depense: toMoney(a.expense) }));
            exportCsv(`depenses_reelles_${new Date().toISOString().slice(0, 10)}.csv`, rows);
        });
    }

    if (exportRemainingBtn) {
        exportRemainingBtn.addEventListener('click', () => {
            const rows = computeRemainingByCommission().map(r => ({
                Commission: r.commission,
                Budget: r.budget,
                DepensesReelles: r.actual,
                Reste: r.remaining
            }));
            exportCsv(`reste_par_commission_${new Date().toISOString().slice(0, 10)}.csv`, rows);
        });
    }

    // ---- Init ----
    renderAll();
});

