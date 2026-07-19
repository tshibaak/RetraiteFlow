DROP IF DATABASE EXISTS retraiteflow;
CREATE DATABASE IF NOT EXISTS retraiteflow;
USE retraiteflow;
CREATE TABLE IF NOT EXISTS roles (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS categories(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT NOW()
);

CREATE TABLE  IF NOT EXISTS users(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT ,
    `name` VARCHAR(255),
    `email` VARCHAR(255) UNIQUE,
    `password` VARCHAR(255),
    `phone` VARCHAR(70),
    `address` TEXT DEFAULT NULL,
    `sexe` ENUM('M', 'F') NOT NULL,
);

CREATE Table IF NOT EXISTS commissions(
    `id`  INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE
);

CREATE TABLE IF NOT EXISTS groupes(
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `name` VARCHAR(30) UNIQUE  NOT NULL,
);

CREATE TABLE IF NOT EXISTS participants (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `sexe` ENUM('Masculin', 'Féminin') NOT NULL,
    `age` INT NOT NULL,
    `phone` VARCHAR(50),
    `groupe_id` INT NOT NULL,
    `commission_id` INT NOT NULL,
    `atelier_id` INT,
    `dortoir_id` INT, 
    `days` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user
        FOREIGN KEY (user_id)
        REFERENCES users(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_participants_finance_validator
        FOREIGN KEY (finance_validated_by)
        REFERENCES table_encadreur(id_enc)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_commission FOREIGN KEY (commission_id)
     REFERENCES commissions(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_atelier FOREIGN KEY (atelier_id)
     REFERENCES locaux(`id`)
             ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_dortoir FOREIGN KEY (dortoir_id)
     REFERENCES locaux(`id`)
             ON DELETE SET NULL
        ON UPDATE CASCADE,
);

CREATE TABLE IF NOT EXISTS paiements(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `participant_id` INT NOT NULL,
    `amount` DECIMAL(10,2) DEFAULT 0.00,
    `mode` TEXT,
    `statut`  ENUM('pending', 'confirmed', 'rejected') DEFAULT 'pending',
    `validator_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_participant FOREIGN KEY (participant_id)
     REFERENCES participants(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
);

CREATE TABLE IF NOT EXISTS `logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `action` VARCHAR(60) NOT NULL,
    `detail` TEXT NOT NULL,
    `ip` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- =====================================================
-- TABLE : finance_inputs (Entrées d'argent)
-- =====================================================

CREATE TABLE IF NOT EXISTS finance_inputs (
    id_input INT AUTO_INCREMENT PRIMARY KEY,
    id_encadreur INT NOT NULL,
    source_input VARCHAR(150) NOT NULL,
    amount_input DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_finance_inputs_encadreur_created (id_encadreur, created_at),

    CONSTRAINT chk_finance_inputs_amount
        CHECK (amount_input > 0),

    CONSTRAINT fk_finance_inputs_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =====================================================
-- TABLE : finance_revenues (Revenus remis par les encadreurs)
-- =====================================================

CREATE TABLE IF NOT EXISTS finance_revenues (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    id_financier INT NOT NULL,
    id_encadreur_source INT NOT NULL,
    amount_revenue DECIMAL(10,2) NOT NULL,
    note_revenue VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_finance_revenues_financier_created (id_financier, created_at),
    INDEX idx_finance_revenues_encadreur_created (id_encadreur_source, created_at),

    CONSTRAINT chk_finance_revenues_amount
        CHECK (amount_revenue > 0),

    CONSTRAINT fk_finance_revenues_financier
        FOREIGN KEY (id_financier)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_finance_revenues_encadreur
        FOREIGN KEY (id_encadreur_source)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Même besoin métier, avec les noms de tables demandés côté BDD.
CREATE TABLE IF NOT EXISTS prevision_depense (
   `id` INT AUTO_INCREMENT PRIMARY KEY,
    id_financier INT NOT NULL,
    commission_id INT NOT NULL,
    budget DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_prevision_depense_financier_created (id_financier, created_at),

    CONSTRAINT chk_prevision_depense_budget
        CHECK (budget > 0),

    CONSTRAINT fk_prevision_depense_financier
        FOREIGN KEY (id_financier)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_commission FOREIGN KEY (commission_id)
     REFERENCES commissions(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
);

CREATE TABLE IF NOT EXISTS depense_reelles (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `commission_id` INT NOT NULL,
    budget_depense_rel DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_depense_reelles_financier_created (id_financier, created_at),

    CONSTRAINT chk_depense_reelles_budget
        CHECK (budget_depense_rel > 0),

    CONSTRAINT fk_depense_reelles_financier
        FOREIGN KEY (user_id)
        REFERENCES users(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_commission FOREIGN KEY (commission_id)
     REFERENCES commissions(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
);

CREATE TABLE IF NOT EXISTS locaux(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `sexe` ENUM('Masculin', 'Féminin','Mixte') NOT NULL
    `age_min` INT NOT NULL,
    `age_max` INT NOT NULL,
    `capacity` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user
        FOREIGN KEY (user_id)
        REFERENCES users(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_category
       FOREIGN KEY (category_id)
       REFERENCES categories(id)
       ON DELETE CASCADE
       ON UPDATE CASCADE
);