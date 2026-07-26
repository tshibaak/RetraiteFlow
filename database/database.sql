DROP DATABASE IF EXISTS retraiteflow;
CREATE DATABASE IF NOT EXISTS retraiteflow;
USE retraiteflow;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(70),
    address TEXT DEFAULT NULL,
    sexe ENUM('M', 'F') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE commissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE
);

CREATE TABLE groupes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) UNIQUE NOT NULL
);

INSERT INTO roles(`name`) VALUES
    ('encadreur'),
    ('coordon'),
    ('cordon'),
    ('discipline'),
    ('finance'),
    ('logistique');

INSERT INTO categories(`name`) VALUES
    ('atelier'),
    ('dortoir');

INSERT INTO groupes(`name`) VALUES
    ('solvable'),
    ('social_case'),
    ('accredited');

INSERT INTO commissions(`name`) VALUES
    ('discipline'),
    ('finance'),
    ('logistique'),
    ('nettoyage'),
    ('restauration'),
    ('santé');

CREATE TABLE locaux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    sexe ENUM('Masculin', 'Féminin','Mixte') NOT NULL,
    age_min INT NOT NULL,
    age_max INT NOT NULL,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_locaux_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_locaux_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    commission_id INT,
    name VARCHAR(100) NOT NULL,
    sexe ENUM('Masculin', 'Féminin') NOT NULL,
    age INT NOT NULL,
    phone VARCHAR(50),
    groupe_id INT NOT NULL,
    atelier_id INT,
    dortoir_id INT,
    days INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_participant_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_participant_groupe FOREIGN KEY (groupe_id) REFERENCES groupes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_participant_commission FOREIGN KEY (commission_id) REFERENCES commissions(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_participant_atelier FOREIGN KEY (atelier_id) REFERENCES locaux(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_participant_dortoir FOREIGN KEY (dortoir_id) REFERENCES locaux(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL CHECK (amount >= 0),
    mode VARCHAR(50),
    statut ENUM('pending', 'confirmed', 'rejected') DEFAULT 'pending',
    validator_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_paiement_participant FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_paiement_validator FOREIGN KEY (validator_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(60) NOT NULL,
    detail TEXT NOT NULL,
    ip VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE depense_reelles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    commission_id INT,
    budget_depense_rel DECIMAL(10,2) NOT NULL CHECK (budget_depense_rel > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_depense_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_depense_commission FOREIGN KEY (commission_id) REFERENCES commissions(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE prevision_depense (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    commission_id INT,
    budget DECIMAL(10,2) NOT NULL CHECK (budget > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prevision_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_prevision_commission FOREIGN KEY (commission_id) REFERENCES commissions(id) ON DELETE SET NULL ON UPDATE CASCADE
);
