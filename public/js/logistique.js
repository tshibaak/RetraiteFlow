// Logistique - gestion des dortoirs/ateliers + affectation automatique des participants

document.addEventListener('DOMContentLoaded', () => {
    const store = window.RetraiteFlowStore;

    // --- User menu ---
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");
    const accountSettings = document.getElementById("accountSettings");

    initUserInfo();
    setupUserMenu();

    // --- UI refs ---
    const statParticipants = document.getElementById('statParticipants');
    const statDortoirsDispo = document.getElementById('statDortoirsDispo');
    const statAteliersActifs = document.getElementById('statAteliersActifs');
    const alertsContainer = document.getElementById('alertsContainer');

    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');

    const createDortoirBtn = document.getElementById('createDortoirBtn');
    const createAtelierBtn = document.getElementById('createAtelierBtn');

    const dortoirsTableBody = document.getElementById('dortoirsTableBody');
    const atelierCards = document.getElementById('atelierCards');

    // --- Modals ---
    const dortoirModal = document.getElementById('dortoirModal');
    const closeDortoirModal = document.getElementById('closeDortoirModal');
    const cancelDortoirBtn = document.getElementById('cancelDortoirBtn');
    const dortoirForm = document.getElementById('dortoirForm');

    const atelierModal = document.getElementById('atelierModal');
    const closeAtelierModal = document.getElementById('closeAtelierModal');
    const cancelAtelierBtn = document.getElementById('cancelAtelierBtn');
    const atelierForm = document.getElementById('atelierForm');

    // --- Data ---
    let participants = [];
    let dortoirs = [];
    let ateliers = [];
    let filteredDortoirs = [];
    let editingDortoirId = null;
    let editingAtelierId = null;

    // --- Actions ---
    createDortoirBtn.addEventListener('click', () => {
        editingDortoirId = null;
        setDortoirModalTitle('Nouveau dortoir');
        if (dortoirForm) dortoirForm.reset();
        openModal(dortoirModal, null);
    });
    createAtelierBtn.addEventListener('click', () => {
        editingAtelierId = null;
        setAtelierModalTitle('Nouvel atelier');
        if (atelierForm) atelierForm.reset();
        openModal(atelierModal, null);
    });

    closeDortoirModal.addEventListener('click', () => { editingDortoirId = null; closeModal(dortoirModal, dortoirForm); });
    cancelDortoirBtn.addEventListener('click', () => { editingDortoirId = null; closeModal(dortoirModal, dortoirForm); });
    dortoirModal.addEventListener('click', (e) => { if (e.target === dortoirModal) { editingDortoirId = null; closeModal(dortoirModal, dortoirForm); } });

    closeAtelierModal.addEventListener('click', () => { editingAtelierId = null; closeModal(atelierModal, atelierForm); });
    cancelAtelierBtn.addEventListener('click', () => { editingAtelierId = null; closeModal(atelierModal, atelierForm); });
    atelierModal.addEventListener('click', (e) => { if (e.target === atelierModal) { editingAtelierId = null; closeModal(atelierModal, atelierForm); } });

    dortoirForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const fd = new FormData(dortoirForm);

        const nom = String(fd.get('dortoirNom') || '').trim();
        const sexe = String(fd.get('dortoirSexe') || '').trim();
        const capacite = toInt(fd.get('dortoirCapacite'), 0);
        const ageMin = toInt(fd.get('dortoirAgeMin'), 0);
        const ageMax = toInt(fd.get('dortoirAgeMax'), 0);

        if (!nom) return alert('Nom du dortoir requis');
        if (ageMax < ageMin) return alert('Âge max doit être ≥ âge min');
        if (capacite <= 0) return alert('Capacité invalide');

        if (editingDortoirId) {
            const idx = dortoirs.findIndex(d => d.id === editingDortoirId);
            if (idx !== -1) {
                dortoirs[idx] = {
                    ...dortoirs[idx],
                    nom,
                    sexe,
                    capacite,
                    ageMin,
                    ageMax,
                };
            }
            editingDortoirId = null;
        } else {
            dortoirs.unshift({
                id: store?.uid?.('d') || 'd_' + Date.now(),
                nom,
                sexe,
                capacite,
                ageMin,
                ageMax,
                createdAt: new Date().toISOString(),
            });
        }
        store?.setDortoirs?.(dortoirs);
        participants = store?.saveParticipantsWithAutoAssign?.(participants) || participants;
        refresh();
        closeModal(dortoirModal, dortoirForm);
    });

    atelierForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const fd = new FormData(atelierForm);

        const nom = String(fd.get('atelierNom') || '').trim();
        const capacite = toInt(fd.get('atelierCapacite'), 0);
        const ageMin = toInt(fd.get('atelierAgeMin'), 0);
        const ageMax = toInt(fd.get('atelierAgeMax'), 0);

        if (!nom) return alert('Nom de l’atelier requis');
        if (ageMax < ageMin) return alert('Âge max doit être ≥ âge min');
        if (capacite <= 0) return alert('Capacité invalide');

        if (editingAtelierId) {
            const idx = ateliers.findIndex(a => a.id === editingAtelierId);
            if (idx !== -1) {
                ateliers[idx] = {
                    ...ateliers[idx],
                    nom,
                    capacite,
                    ageMin,
                    ageMax,
                };
            }
            editingAtelierId = null;
        } else {
            ateliers.unshift({
                id: store?.uid?.('a') || 'a_' + Date.now(),
                nom,
                capacite,
                ageMin,
                ageMax,
                createdAt: new Date().toISOString(),
            });
        }
        store?.setAteliers?.(ateliers);
        participants = store?.saveParticipantsWithAutoAssign?.(participants) || participants;
        refresh();
        closeModal(atelierModal, atelierForm);
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase().trim();
            filteredDortoirs = !term ? [...dortoirs] : dortoirs.filter(d => (d.nom || '').toLowerCase().includes(term));
            renderDortoirs();
            if (searchClear) searchClear.style.display = term ? 'flex' : 'none';
        });
    }

    if (searchClear) {
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            filteredDortoirs = [...dortoirs];
            renderDortoirs();
            searchClear.style.display = 'none';
        });
    }

    // --- Public actions (table / cartes) ---
    window.rfEditDortoir = function rfEditDortoir(id) {
        const d = dortoirs.find(x => x.id === id);
        if (!d) return;
        editingDortoirId = id;
        document.getElementById('dortoirNom').value = d.nom || '';
        document.getElementById('dortoirSexe').value = d.sexe || 'Masculin';
        document.getElementById('dortoirCapacite').value = d.capacite || '';
        document.getElementById('dortoirAgeMin').value = d.ageMin ?? '';
        document.getElementById('dortoirAgeMax').value = d.ageMax ?? '';
        setDortoirModalTitle('Modifier le dortoir');
        openModal(dortoirModal, null);
    };

    window.rfDeleteDortoir = function rfDeleteDortoir(id) {
        if (!confirm('Supprimer ce dortoir ?')) return;
        dortoirs = dortoirs.filter(d => d.id !== id);
        store?.setDortoirs?.(dortoirs);
        participants = store?.saveParticipantsWithAutoAssign?.(participants) || participants;
        refresh();
    };

    window.rfEditAtelier = function rfEditAtelier(id) {
        const a = ateliers.find(x => x.id === id);
        if (!a) return;
        editingAtelierId = id;
        document.getElementById('atelierNom').value = a.nom || '';
        document.getElementById('atelierCapacite').value = a.capacite || '';
        document.getElementById('atelierAgeMin').value = a.ageMin ?? '';
        document.getElementById('atelierAgeMax').value = a.ageMax ?? '';
        setAtelierModalTitle('Modifier l\'atelier');
        openModal(atelierModal, null);
    };

    window.rfDeleteAtelier = function rfDeleteAtelier(id) {
        if (!confirm('Supprimer cet atelier ?')) return;
        ateliers = ateliers.filter(a => a.id !== id);
        store?.setAteliers?.(ateliers);
        participants = store?.saveParticipantsWithAutoAssign?.(participants) || participants;
        refresh();
    };

    // --- Init ---
    bootstrap();

    function bootstrap() {
        participants = store?.getParticipants?.() || [];
        dortoirs = store?.getDortoirs?.() || [];
        ateliers = store?.getAteliers?.() || [];

        // recalcul affectations au chargement (si logistique change)
        participants = store?.saveParticipantsWithAutoAssign?.(participants) || participants;

        filteredDortoirs = [...dortoirs];
        refresh();
    }

    function refresh() {
        // recharger (sécurité)
        participants = store?.getParticipants?.() || participants;
        dortoirs = store?.getDortoirs?.() || dortoirs;
        ateliers = store?.getAteliers?.() || ateliers;
        filteredDortoirs = filteredDortoirs.length ? filteredDortoirs : [...dortoirs];

        renderStats();
        renderAlerts();
        renderDortoirs();
        renderAteliers();
    }

    function renderStats() {
        const totalP = participants.length;
        const dormDisponibles = dortoirs.filter(d => remainingInDortoir(d) > 0).length;
        const ateliersActifs = ateliers.length;

        if (statParticipants) statParticipants.textContent = String(totalP);
        if (statDortoirsDispo) statDortoirsDispo.textContent = String(dormDisponibles);
        if (statAteliersActifs) statAteliersActifs.textContent = String(ateliersActifs);
    }

    function renderAlerts() {
        const sansDortoir = participants.filter(p => !p.dortoirId).length;
        const sansAtelier = participants.filter(p => !p.atelierId).length;

        if (!alertsContainer) return;
        if (!sansDortoir && !sansAtelier) {
            alertsContainer.style.display = 'none';
            alertsContainer.innerHTML = '';
            return;
        }

        alertsContainer.style.display = 'block';
        alertsContainer.innerHTML = `
            <div>
                <strong>Attention</strong> :
                ${sansDortoir ? `${sansDortoir} participant(s) sans dortoir` : ''}
                ${sansDortoir && sansAtelier ? ' • ' : ''}
                ${sansAtelier ? `${sansAtelier} participant(s) sans atelier` : ''}
                <div style="margin-top:6px; color: var(--muted); font-size: 13px;">
                    Crée/ajuste des dortoirs (sexe + tranche d'âge + capacité) et des ateliers (tranche d'âge + capacité)
                    pour que l'affectation automatique puisse les placer.
                </div>
            </div>
        `;
    }

    function renderDortoirs() {
        const list = filteredDortoirs;
        if (!list.length) {
            dortoirsTableBody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align:center; padding: 28px; color: var(--muted);">
                        <i class="fas fa-bed" style="font-size: 42px; margin-bottom: 12px; display:block; opacity: .4;"></i>
                        Aucun dortoir. Cliquez sur <strong>Créer un dortoir</strong>.
                    </td>
                </tr>
            `;
            return;
        }

        dortoirsTableBody.innerHTML = list.map(d => {
            const occ = occupantsInDortoir(d);
            const rem = remainingInDortoir(d);
            const full = rem <= 0;
            const badge = full
                ? `<span class="rf-badge rf-badge-danger"><i class="fas fa-circle"></i> Complète</span>`
                : `<span class="rf-badge rf-badge-success"><i class="fas fa-circle"></i> Disponible</span>`;

            return `
                <tr>
                    <td style="font-weight: 700;">${escapeHtml(d.nom)}</td>
                    <td>${escapeHtml(d.sexe)}</td>
                    <td>${d.ageMin} - ${d.ageMax} ans</td>
                    <td>${d.capacite}</td>
                    <td>${occ}</td>
                    <td>${badge}</td>
                    <td>
                        <div class="actions">
                            <div class="btn-action btn-edit" onclick="rfEditDortoir('${escapeAttr(d.id)}')" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </div>
                            <div class="btn-action btn-delete" onclick="rfDeleteDortoir('${escapeAttr(d.id)}')" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderAteliers() {
        if (!ateliers.length) {
            atelierCards.innerHTML = `
                <div class="rf-card" style="grid-column: 1 / -1;">
                    <div class="rf-card-title">Aucun atelier</div>
                    <div class="rf-card-subtitle">Clique sur <strong>Créer un atelier</strong> pour démarrer.</div>
                </div>
            `;
            return;
        }

        atelierCards.innerHTML = ateliers.map(a => {
            const occ = occupantsInAtelier(a);
            const pct = a.capacite ? Math.min(100, Math.round((occ / a.capacite) * 100)) : 0;

            return `
                <div class="rf-card">
                    <div class="rf-card-title">${escapeHtml(a.nom)}</div>
                    <div class="rf-card-subtitle">Âges ${a.ageMin} - ${a.ageMax} • Capacité ${a.capacite}</div>
                    <div class="rf-progress" aria-label="progress">
                        <div style="width:${pct}%"></div>
                    </div>
                    <div class="rf-card-footer">
                        <span>${occ} / ${a.capacite}</span>
                        <div class="actions">
                            <div class="btn-action btn-edit" onclick="rfEditAtelier('${escapeAttr(a.id)}')" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </div>
                            <div class="btn-action btn-delete" onclick="rfDeleteAtelier('${escapeAttr(a.id)}')" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function occupantsInDortoir(d) {
        return participants.filter(p => p.dortoirId === d.id).length;
    }

    function remainingInDortoir(d) {
        return Math.max(0, toInt(d.capacite, 0) - occupantsInDortoir(d));
    }

    function occupantsInAtelier(a) {
        return participants.filter(p => p.atelierId === a.id).length;
    }

    function setDortoirModalTitle(title) {
        const el = dortoirModal?.querySelector('.modal-title');
        if (el) el.innerHTML = '<i class="fas fa-bed"></i> ' + title;
    }

    function setAtelierModalTitle(title) {
        const el = atelierModal?.querySelector('.modal-title');
        if (el) el.innerHTML = '<i class="fas fa-chalkboard-teacher"></i> ' + title;
    }

    function openModal(backdrop, formEl) {
        if (!backdrop) return;
        if (formEl) formEl.reset();
        backdrop.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(backdrop, formEl) {
        if (!backdrop) return;
        backdrop.style.display = 'none';
        document.body.style.overflow = 'auto';
        if (formEl) formEl.reset();
    }

    function initUserInfo() {
        const info = store?.getUserInfo?.() || { username: 'Utilisateur', role: 'Logistique' };
        const initials = String(info.username || 'U').split(' ').map(n => n[0] || '').slice(0, 2).join('').toUpperCase() || 'U';
        setText('userName', info.username);
        setText('userInfoName', info.username);
        setText('userInfoRole', info.role || 'Logistique');
        setText('userInitials', initials);
        setText('userInfoInitials', initials);
    }

    function setupUserMenu() {
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
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '';
    }

    function toInt(value, fallback = 0) {
        const n = parseInt(String(value ?? ''), 10);
        return Number.isFinite(n) ? n : fallback;
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, (m) => ({
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
});

