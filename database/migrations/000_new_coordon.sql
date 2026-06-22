INSERT INTO table_encadreur (
    nom_enc,
    prenom_enc,
    mdp_enc,
    mail_enc,
    tel_enc,
    date_naissance_enc,
    sex_enc,
    role,
    adresse
) VALUES (
    'Jeremie Mbombo',
    'Admin',
    ' 30061960',
    'jeremiembombo10@gmail.com',
    '+243000000000',
    '2003-01-01',
    'M',
    'coordination',
    'Kinshasa'
);

-- =====================================================
-- 4 comptes supplementaires
-- Mot de passe en clair : password123
-- Hash genere avec password_hash('password123', PASSWORD_DEFAULT) (PHP / bcrypt)
-- =====================================================
INSERT INTO table_encadreur (
    nom_enc,
    prenom_enc,
    mdp_enc,
    mail_enc,
    tel_enc,
    date_naissance_enc,
    sex_enc,
    role,
    adresse
) VALUES
(
    'Jeremie',
    'Mbombo',
    '$2y$12$utC3K/wKMok5efHji.0clO2PWQ2WCNd4eJxf4XcQaROeOKtqXOuMa',
    'jeremie@retraiteflow.com',
    '+243000000001',
    '1995-02-10',
    'M',
    'finance',
    'Kinshasa'
),
(
    'Ilvit',
    'Tshibaak',
    '$2y$12$yo1L1Q0MgWcGAcnPT986Ve6wpd3/ev/CXYci.JP/aS/2szQQh2N7a',
    'ilvit@retraiteflow.com',
    '+243000000002',
    '1996-06-21',
    'F',
    'logistique',
    'Kinshasa'
),
(
    'Nicole',
    'Mukendi',
    '$2y$12$wg.bc7kGNxjgf4hRMqqyO.C0Jk3.KwS0gCpR0YJOmq9rPBzm6A3Sm',
    'nicole@retraiteflow.com',
    '+243000000003',
    '1998-11-05',
    'F',
    'discipline',
    'Kinshasa'
),
(
    'Henock',
    'Tumonakiese',
    '$2y$12$Jr6O2r8PW1/1GdPafdp7fOSP1BvoTp/FoUNLyjxXHj8C0lKqf4pDe',
    'henock@retraiteflow.com',
    '+243000000004',
    '1994-09-17',
    'M',
    'encadreur',
    'Kinshasa'
);
