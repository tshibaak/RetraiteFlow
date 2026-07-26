USE retraiteflow;

INSERT INTO commissions(`name`) VALUES
    ('discipline'),
    ('finance'),
    ('logistique'),
    ('nettoyage'),
    ('restauration'),
    ('santé')
ON DUPLICATE KEY UPDATE name = VALUES(name);
