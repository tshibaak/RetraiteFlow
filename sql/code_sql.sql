-- =====================================================
-- CREATION DE LA BASE DE DONNEES
-- Parce que visiblement les humains adorent empiler
-- des tables comme des boîtes de sardines.
-- =====================================================

CREATE DATABASE IF NOT EXISTS retraiteflow;
USE retraiteflow;

-- =====================================================
-- TABLE : table_encadreur
-- =====================================================

CREATE TABLE IF NOT EXISTS table_encadreur (
    
    id_enc INT AUTO_INCREMENT PRIMARY KEY,

    nom_enc VARCHAR(100) NOT NULL,
    prenom_enc VARCHAR(100) NOT NULL,

    mdp_enc VARCHAR(255) NOT NULL,
    mail_enc VARCHAR(150) UNIQUE NOT NULL,
    tel_enc VARCHAR(20),

    date_naissance_enc DATE NOT NULL,

    sex_enc ENUM('M', 'F') NOT NULL,

    role ENUM(
        'encadreur',
        'coordination',
        'cordon',
        'discipline',
        'finance',
        'logistique'
    ) NOT NULL,

    adresse TEXT NOT NULL

);

-- =====================================================
-- TABLE : gestionnaire_encadreur
-- =====================================================

CREATE TABLE IF NOT EXISTS gestionnaire_encadreur (

    id INT AUTO_INCREMENT PRIMARY KEY,

    id_encadreur INT NOT NULL,

    nom_part VARCHAR(100) NOT NULL,
    sex_part ENUM('M', 'F') NOT NULL,
    Age_part INT NOT NULL,

    groupe_part ENUM(
        'solvable',
        'cas social',
        'accrédité'
    ) NOT NULL,

    commission_part VARCHAR(100) NOT NULL,
    tel_part VARCHAR(20) NOT NULL,
    paiment_part DECIMAL(10,2) DEFAULT 0.00,
    duree_part INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

-- =====================================================
-- EXEMPLE D'INSERTION
-- Histoire de nourrir la base avec autre chose
-- que le vide cosmique.
-- =====================================================

-- =====================================================
-- TABLE : participants (Gestion des participants par encadreur)
-- =====================================================

CREATE TABLE IF NOT EXISTS participants (

    id_part INT AUTO_INCREMENT PRIMARY KEY,

    id_encadreur INT NOT NULL,

    nom_part VARCHAR(100) NOT NULL,
    sexe_part ENUM('Masculin', 'Féminin') NOT NULL,
    age_part INT NOT NULL,
    
    groupe_part ENUM(
        'solvable',
        'cas_social',
        'cas social',
        'accrédité'
    ) NOT NULL,

    commission_part VARCHAR(100),

    telephone_part VARCHAR(20),

    montant_part DECIMAL(10,2) DEFAULT 0.00,

    jours_part INT DEFAULT 0,

    finance_status ENUM('en_attente', 'confirme', 'deconfirme') DEFAULT 'en_attente',
    finance_validated_by INT,
    finance_validated_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    dortoir_id INT,
    atelier_id INT,

    INDEX idx_participants_encadreur (id_encadreur),
    INDEX idx_participants_dortoir (dortoir_id),
    INDEX idx_participants_atelier (atelier_id),
    INDEX idx_participants_groupe (groupe_part),
    INDEX idx_participants_finance_status (finance_status),

    CONSTRAINT fk_participants_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_participants_finance_validator
        FOREIGN KEY (finance_validated_by)
        REFERENCES table_encadreur(id_enc)
        ON DELETE SET NULL
        ON UPDATE CASCADE

);

-- =====================================================
-- TABLE : app_logs (Historique des actions)
-- =====================================================

CREATE TABLE IF NOT EXISTS app_logs (

    id_log INT AUTO_INCREMENT PRIMARY KEY,

    actor_id INT NOT NULL,
    actor_role VARCHAR(50) NOT NULL,
    actor_name VARCHAR(220) NOT NULL,

    module VARCHAR(60) NOT NULL,
    action_type VARCHAR(60) NOT NULL,
    description TEXT NOT NULL,

    target_table VARCHAR(80),
    target_id INT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_app_logs_actor (actor_id, created_at),
    INDEX idx_app_logs_role (actor_role, created_at),
    INDEX idx_app_logs_module (module, created_at),

    CONSTRAINT fk_app_logs_actor
        FOREIGN KEY (actor_id)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

-- =====================================================
-- TABLE : discipline_logs (Suivi entrées/sorties)
-- =====================================================

CREATE TABLE IF NOT EXISTS discipline_logs (

    id_log INT AUTO_INCREMENT PRIMARY KEY,

    id_participant INT NOT NULL,

    type_log ENUM('entree', 'sortie') NOT NULL,

    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    logged_by INT,

    CONSTRAINT fk_discipline_logs_participant
        FOREIGN KEY (id_participant)
        REFERENCES participants(id_part)
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

    id_revenue INT AUTO_INCREMENT PRIMARY KEY,

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

-- =====================================================
-- TABLE : finance_forecasts (Prévisions de dépenses)
-- =====================================================

CREATE TABLE IF NOT EXISTS finance_forecasts (

    id_forecast INT AUTO_INCREMENT PRIMARY KEY,

    id_encadreur INT NOT NULL,

    commission_forecast VARCHAR(150) NOT NULL,

    budget_forecast DECIMAL(10,2) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_finance_forecasts_encadreur_created (id_encadreur, created_at),
    INDEX idx_finance_forecasts_commission (commission_forecast),

    CONSTRAINT chk_finance_forecasts_budget
        CHECK (budget_forecast > 0),

    CONSTRAINT fk_finance_forecasts_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

-- Même besoin métier, avec les noms de tables demandés côté BDD.
CREATE TABLE IF NOT EXISTS prevision_depense (

    id_prevision INT AUTO_INCREMENT PRIMARY KEY,

    id_financier INT NOT NULL,
    commission VARCHAR(150) NOT NULL,
    budget DECIMAL(10,2) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_prevision_depense_financier_created (id_financier, created_at),

    CONSTRAINT chk_prevision_depense_budget
        CHECK (budget > 0),

    CONSTRAINT fk_prevision_depense_financier
        FOREIGN KEY (id_financier)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

-- =====================================================
-- TABLE : finance_actuals (Dépenses réelles)
-- =====================================================

CREATE TABLE IF NOT EXISTS finance_actuals (

    id_actual INT AUTO_INCREMENT PRIMARY KEY,

    id_encadreur INT NOT NULL,

    commission_actual VARCHAR(150) NOT NULL,

    amount_actual DECIMAL(10,2) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_finance_actuals_encadreur_created (id_encadreur, created_at),
    INDEX idx_finance_actuals_commission (commission_actual),

    CONSTRAINT chk_finance_actuals_amount
        CHECK (amount_actual > 0),

    CONSTRAINT fk_finance_actuals_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

CREATE TABLE IF NOT EXISTS depense_reelles (

    id_depense_reelle INT AUTO_INCREMENT PRIMARY KEY,

    id_financier INT NOT NULL,
    commission_depense_relle VARCHAR(150) NOT NULL,
    budget_depense_rel DECIMAL(10,2) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_depense_reelles_financier_created (id_financier, created_at),

    CONSTRAINT chk_depense_reelles_budget
        CHECK (budget_depense_rel > 0),

    CONSTRAINT fk_depense_reelles_financier
        FOREIGN KEY (id_financier)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

-- =====================================================
-- MIGRATION SI LES TABLES EXISTENT DEJA
-- =====================================================

ALTER TABLE participants
    ADD COLUMN finance_status ENUM('en_attente', 'confirme', 'deconfirme') DEFAULT 'en_attente';
ALTER TABLE participants
    ADD COLUMN finance_validated_by INT;
ALTER TABLE participants
    ADD COLUMN finance_validated_at TIMESTAMP NULL;

-- =====================================================
-- TABLE : logistique_dortoirs (Gestion des dortoirs)
-- =====================================================

CREATE TABLE IF NOT EXISTS logistique_dortoirs (

    id_dortoir INT AUTO_INCREMENT PRIMARY KEY,

    id_encadreur INT NOT NULL,

    nom_dortoir VARCHAR(100) NOT NULL,

    sexe_dortoir ENUM('Masculin', 'Féminin') NOT NULL,

    age_min_dortoir INT NOT NULL,

    age_max_dortoir INT NOT NULL,

    capacite_dortoir INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_dortoirs_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);

-- =====================================================
-- TABLE : logistique_ateliers (Gestion des ateliers)
-- =====================================================

CREATE TABLE IF NOT EXISTS logistique_ateliers (

    id_atelier INT AUTO_INCREMENT PRIMARY KEY,

    id_encadreur INT NOT NULL,

    nom_atelier VARCHAR(100) NOT NULL,

    age_min_atelier INT NOT NULL,

    age_max_atelier INT NOT NULL,

    capacite_atelier INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ateliers_encadreur
        FOREIGN KEY (id_encadreur)
        REFERENCES table_encadreur(id_enc)
        ON DELETE CASCADE
        ON UPDATE CASCADE

);
