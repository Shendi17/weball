-- Table des catégories de formation
CREATE TABLE IF NOT EXISTS formations_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#3498db',
    icon VARCHAR(50) DEFAULT 'fas fa-graduation-cap',
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des formateurs
CREATE TABLE IF NOT EXISTS formations_formateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    telephone VARCHAR(20),
    specialites TEXT,
    cv_filename VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des formations
CREATE TABLE IF NOT EXISTS formations_formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    objectifs TEXT,
    prerequis TEXT,
    programme TEXT,
    duree INT NOT NULL COMMENT 'Durée en minutes',
    max_participants INT DEFAULT 10,
    lieu VARCHAR(255),
    status ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    formateur_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES formations_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (formateur_id) REFERENCES formations_formateurs(id) ON DELETE SET NULL
);

-- Table des participants
CREATE TABLE IF NOT EXISTS formations_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    telephone VARCHAR(20),
    service VARCHAR(100),
    status ENUM('inscrit', 'present', 'absent', 'annule') DEFAULT 'inscrit',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (formation_id) REFERENCES formations_formations(id) ON DELETE CASCADE
);

-- Table des documents de formation
CREATE TABLE IF NOT EXISTS formations_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    filename VARCHAR(255) NOT NULL,
    type ENUM('support', 'exercice', 'correction', 'autre') DEFAULT 'autre',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations_formations(id) ON DELETE CASCADE
);

-- Table des évaluations
CREATE TABLE IF NOT EXISTS formations_evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    participant_id INT NOT NULL,
    contenu_note INT CHECK (contenu_note BETWEEN 1 AND 5),
    formateur_note INT CHECK (formateur_note BETWEEN 1 AND 5),
    organisation_note INT CHECK (organisation_note BETWEEN 1 AND 5),
    commentaires TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations_formations(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES formations_participants(id) ON DELETE CASCADE
);

-- Insertion des catégories par défaut
INSERT INTO formations_categories (name, description, color, icon, ordre) VALUES
('Informatique', 'Formations aux outils informatiques', '#3498db', 'fas fa-laptop', 1),
('Management', 'Formations au management et leadership', '#2ecc71', 'fas fa-users', 2),
('Sécurité', 'Formations à la sécurité et prévention', '#e74c3c', 'fas fa-shield-alt', 3),
('Qualité', 'Formations aux normes et procédures qualité', '#f1c40f', 'fas fa-check-circle', 4),
('Communication', 'Formations à la communication', '#9b59b6', 'fas fa-comments', 5);
