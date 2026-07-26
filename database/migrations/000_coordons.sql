USE retraiteflow;

-- Mot de passe en clair pour tous : password123
-- Hash: password_hash('password123', PASSWORD_DEFAULT)

INSERT INTO users (role_id, name, email, password, phone, address, sexe)
SELECT r.id, 'Jeremie Mbombo', 'jeremie@retraiteflow.com',
       '$2y$12$utC3K/wKMok5efHji.0clO2PWQ2WCNd4eJxf4XcQaROeOKtqXOuMa',
       '+243000000001', 'Kinshasa', 'M'
FROM roles r WHERE r.name = 'finance'
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (role_id, name, email, password, phone, address, sexe)
SELECT r.id, 'Ilvit Tshibaak', 'ilvit@retraiteflow.com',
       '$2y$12$yo1L1Q0MgWcGAcnPT986Ve6wpd3/ev/CXYci.JP/aS/2szQQh2N7a',
       '+243000000002', 'Kinshasa', 'F'
FROM roles r WHERE r.name = 'logistique'
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (role_id, name, email, password, phone, address, sexe)
SELECT r.id, 'Nicole Mukendi', 'nicole@retraiteflow.com',
       '$2y$12$wg.bc7kGNxjgf4hRMqqyO.C0Jk3.KwS0gCpR0YJOmq9rPBzm6A3Sm',
       '+243000000003', 'Kinshasa', 'F'
FROM roles r WHERE r.name = 'discipline'
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (role_id, name, email, password, phone, address, sexe)
SELECT r.id, 'Henock Tumonakiese', 'henoctumonakiese@gmail.com',
       '$2y$12$Jr6O2r8PW1/1GdPafdp7fOSP1BvoTp/FoUNLyjxXHj8C0lKqf4pDe',
       '+243000000004', 'Kinshasa', 'M'
FROM roles r WHERE r.name = 'encadreur'
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (role_id, name, email, password, phone, address, sexe)
SELECT r.id, 'Admin Cordon', 'cordon@retraiteflow.com',
       '$2y$12$utC3K/wKMok5efHji.0clO2PWQ2WCNd4eJxf4XcQaROeOKtqXOuMa',
       '+243000000000', 'Kinshasa', 'M'
FROM roles r WHERE r.name = 'coordon'
ON DUPLICATE KEY UPDATE name = VALUES(name);
