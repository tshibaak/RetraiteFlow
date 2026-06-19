// Encadreur - gestion participants avec formulaire serveur.

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal');
    const openFormBtn = document.getElementById('openFormBtn');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('inscriptionForm');
    const modalTitle = document.querySelector('.modal-title');
    const groupeSelect = document.getElementById('groupe');
    const montantInput = document.getElementById('montant');
    const montantHelp = document.getElementById('montantHelp');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDropdown = document.getElementById('userMenuDropdown');

    const message = document.getElementById('confirmation');
    if (message) {
        setTimeout(function () {
            message.style.transition = 'opacity 1s';
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 1000);
        }, 5000);
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

    function resetMontantState() {
        if (!montantInput) return;
        montantInput.disabled = false;
        montantInput.setAttribute('required', 'required');
        montantInput.style.backgroundColor = 'white';
        montantInput.style.cursor = 'text';
        if (montantHelp) montantHelp.style.display = 'none';
    }

    function handleGroupeChange() {
        if (!groupeSelect || !montantInput) return;
        if (groupeSelect.value === 'Cas Social') {
            montantInput.disabled = true;
            montantInput.value = '0';
            montantInput.removeAttribute('required');
            montantInput.style.backgroundColor = '#f1f5f9';
            montantInput.style.cursor = 'not-allowed';
            if (montantHelp) montantHelp.style.display = 'block';
        } else {
            resetMontantState();
        }
    }

    function openModalForCreate() {
        if (!modal || !form) return;
        form.reset();
        document.getElementById('formAction').value = 'save';
        document.getElementById('participantId').value = '';
        if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-user-plus"></i> Nouveau Participant';
        resetMontantState();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!modal || !form) return;
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        form.reset();
        document.getElementById('formAction').value = 'save';
        document.getElementById('participantId').value = '';
        resetMontantState();
    }

    function normalizeGroupe(value) {
        const clean = String(value || '').toLowerCase();
        if (clean.includes('cas')) return 'Cas Social';
        if (clean.includes('accr')) return 'Accrédité';
        return 'Solvable';
    }

    function setSelectByText(select, value) {
        if (!select) return;
        const wanted = String(value || '').toLowerCase();
        const option = Array.from(select.options).find((opt) => {
            const optValue = String(opt.value || '').toLowerCase();
            const optText = String(opt.textContent || '').toLowerCase();
            return optValue === wanted || optText === wanted;
        });
        if (option) select.value = option.value;
    }

    function openModalForEdit(button) {
        if (!modal || !form) return;
        form.reset();

        document.getElementById('formAction').value = 'save';
        document.getElementById('participantId').value = button.dataset.id || '';
        document.getElementById('nom').value = button.dataset.nom || '';
        document.getElementById('age').value = button.dataset.age || '';
        document.getElementById('telephone').value = String(button.dataset.telephone || '').replace(/^\+?243\s*/, '');
        document.getElementById('montant').value = button.dataset.montant || '0';
        document.getElementById('jours').value = button.dataset.jours || '0';

        setSelectByText(document.getElementById('sexe'), button.dataset.sexe);
        setSelectByText(document.getElementById('groupe'), normalizeGroupe(button.dataset.groupe));
        setSelectByText(document.getElementById('commission'), button.dataset.commission);

        handleGroupeChange();
        if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-user-edit"></i> Modifier Participant';
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function filterRows() {
        const term = String(searchInput?.value || '').toLowerCase().trim();
        document.querySelectorAll('#participantsTable tr').forEach((row) => {
            row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
        if (searchClear) searchClear.style.display = term ? 'flex' : 'none';
    }

    if (openFormBtn) openFormBtn.addEventListener('click', openModalForCreate);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (modal) modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    if (groupeSelect) groupeSelect.addEventListener('change', handleGroupeChange);
    if (searchInput) searchInput.addEventListener('input', filterRows);
    if (searchClear) searchClear.addEventListener('click', () => {
        searchInput.value = '';
        filterRows();
    });

    document.querySelectorAll('.edit-participant').forEach((button) => {
        button.addEventListener('click', () => openModalForEdit(button));
    });
});

function exportToExcel() {
    const visibleRows = Array.from(document.querySelectorAll('#participantsTable tr'))
        .filter((row) => row.style.display !== 'none');

    if (!visibleRows.length) {
        alert('Aucun participant à exporter');
        return;
    }

    const headers = ['Participant', 'Groupe', 'Commission', 'Contact', 'Paiement'];
    const rows = visibleRows.map((row) => Array.from(row.cells).slice(0, 5).map((cell) => cell.textContent.trim()));
    const csvContent = [headers, ...rows]
        .map((row) => row.map((cell) => `"${String(cell ?? '').replaceAll('"', '""')}"`).join(','))
        .join('\n');

    const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `participants_retraite_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
