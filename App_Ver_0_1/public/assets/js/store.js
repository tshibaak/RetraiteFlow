/**
 * RetraiteFlow - couche de stockage partagée (temporaire)
 * Aujourd'hui: localStorage
 * Demain: backend (API) -> on remplacera ces fonctions sans casser le reste du code.
 */

(function () {
    const KEYS = {
        participants: 'rf_participants_v1',
        dortoirs: 'rf_dortoirs_v1',
        ateliers: 'rf_ateliers_v1',
    };

    function safeJsonParse(value, fallback) {
        try {
            if (!value) return fallback;
            return JSON.parse(value);
        } catch {
            return fallback;
        }
    }

    function uid(prefix = 'id') {
        return `${prefix}_${Date.now()}_${Math.random().toString(16).slice(2)}`;
    }

    function toInt(value, fallback = 0) {
        const n = parseInt(String(value ?? ''), 10);
        return Number.isFinite(n) ? n : fallback;
    }

    function normalizeSexe(sexe) {
        if (sexe === 'Masculin' || sexe === 'Homme') return 'Masculin';
        if (sexe === 'Féminin' || sexe === 'Femme') return 'Féminin';
        return String(sexe || '').trim() || 'Masculin';
    }

    function normalizeParticipant(p) {
        const nom = String(p?.nom || '').trim();
        return {
            id: p?.id || uid('p'),
            nom,
            sexe: normalizeSexe(p?.sexe),
            age: toInt(p?.age, 0),
            groupe: String(p?.groupe || '').trim(),
            commission: String(p?.commission || '').trim(),
            telephone: String(p?.telephone || '').trim(),
            montant: String(p?.montant || '').trim(),
            jours: toInt(p?.jours, 0),
            createdAt: p?.createdAt || new Date().toISOString(),
            createdBy: String(p?.createdBy || '').trim(),
            dortoirId: p?.dortoirId ?? null,
            atelierId: p?.atelierId ?? null,
        };
    }

    function normalizeDortoir(d) {
        return {
            id: d?.id || uid('d'),
            nom: String(d?.nom || '').trim(),
            sexe: normalizeSexe(d?.sexe),
            ageMin: toInt(d?.ageMin, 0),
            ageMax: toInt(d?.ageMax, 0),
            capacite: toInt(d?.capacite, 0),
            createdAt: d?.createdAt || new Date().toISOString(),
        };
    }

    function normalizeAtelier(a) {
        return {
            id: a?.id || uid('a'),
            nom: String(a?.nom || '').trim(),
            ageMin: toInt(a?.ageMin, 0),
            ageMax: toInt(a?.ageMax, 0),
            capacite: toInt(a?.capacite, 0),
            createdAt: a?.createdAt || new Date().toISOString(),
        };
    }

    function getUserInfo() {
        const username = localStorage.getItem('username') || sessionStorage.getItem('username') || 'Utilisateur';
        const role = localStorage.getItem('role') || sessionStorage.getItem('role') || '';
        return { username, role };
    }

    function getParticipants() {
        const raw = safeJsonParse(localStorage.getItem(KEYS.participants), []);
        return Array.isArray(raw) ? raw.map(normalizeParticipant) : [];
    }

    function setParticipants(participants) {
        localStorage.setItem(KEYS.participants, JSON.stringify(participants.map(normalizeParticipant)));
    }

    function getDortoirs() {
        const raw = safeJsonParse(localStorage.getItem(KEYS.dortoirs), []);
        return Array.isArray(raw) ? raw.map(normalizeDortoir) : [];
    }

    function setDortoirs(dortoirs) {
        localStorage.setItem(KEYS.dortoirs, JSON.stringify(dortoirs.map(normalizeDortoir)));
    }

    function getAteliers() {
        const raw = safeJsonParse(localStorage.getItem(KEYS.ateliers), []);
        return Array.isArray(raw) ? raw.map(normalizeAtelier) : [];
    }

    function setAteliers(ateliers) {
        localStorage.setItem(KEYS.ateliers, JSON.stringify(ateliers.map(normalizeAtelier)));
    }

    function autoAssign({ participants, dortoirs, ateliers }) {
        const ps = (participants || []).map(normalizeParticipant);
        const ds = (dortoirs || []).map(normalizeDortoir);
        const as = (ateliers || []).map(normalizeAtelier);

        // Réinitialiser les affectations
        for (const p of ps) {
            p.dortoirId = null;
            p.atelierId = null;
        }

        // Affectation Dortoirs (sexe + tranche d'âge + capacité)
        const dortoirUsed = new Map(); // dortoirId -> count
        const sortedDortoirs = [...ds].sort((a, b) => String(a.createdAt).localeCompare(String(b.createdAt)));

        for (const p of ps) {
            const candidates = sortedDortoirs.filter(d =>
                d.sexe === p.sexe &&
                p.age >= d.ageMin &&
                p.age <= d.ageMax
            );

            for (const d of candidates) {
                const used = dortoirUsed.get(d.id) || 0;
                if (used < d.capacite) {
                    p.dortoirId = d.id;
                    dortoirUsed.set(d.id, used + 1);
                    break;
                }
            }
        }

        // Affectation Ateliers (tranche d'âge + capacité)
        const atelierUsed = new Map(); // atelierId -> count
        const sortedAteliers = [...as].sort((a, b) => String(a.createdAt).localeCompare(String(b.createdAt)));

        for (const p of ps) {
            const candidates = sortedAteliers.filter(a =>
                p.age >= a.ageMin &&
                p.age <= a.ageMax
            );

            for (const a of candidates) {
                const used = atelierUsed.get(a.id) || 0;
                if (used < a.capacite) {
                    p.atelierId = a.id;
                    atelierUsed.set(a.id, used + 1);
                    break;
                }
            }
        }

        return ps;
    }

    function saveParticipantsWithAutoAssign(participants) {
        const ds = getDortoirs();
        const as = getAteliers();
        const assigned = autoAssign({ participants, dortoirs: ds, ateliers: as });
        setParticipants(assigned);
        return assigned;
    }

    window.RetraiteFlowStore = {
        KEYS,
        uid,
        getUserInfo,
        getParticipants,
        setParticipants,
        saveParticipantsWithAutoAssign,
        getDortoirs,
        setDortoirs,
        getAteliers,
        setAteliers,
        autoAssign,
    };
})();

