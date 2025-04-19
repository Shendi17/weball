-- Table des articles
CREATE TABLE IF NOT EXISTS articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    auteur_id INT,
    categorie_id INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('brouillon', 'publie', 'archive') DEFAULT 'brouillon',
    tags TEXT,
    vues INT DEFAULT 0,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des catégories d'articles
CREATE TABLE IF NOT EXISTS categories_articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    icone VARCHAR(50),
    couleur VARCHAR(20),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des documents
CREATE TABLE IF NOT EXISTS documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    fichier_path VARCHAR(255),
    type VARCHAR(50),
    categorie_id INT,
    auteur_id INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('brouillon', 'actif', 'archive') DEFAULT 'brouillon',
    tags TEXT,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des événements
CREATE TABLE IF NOT EXISTS evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    lieu VARCHAR(255),
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    organisateur_id INT,
    statut ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
    type VARCHAR(50),
    participants_max INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organisateur_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de démonstration pour les catégories d'articles
INSERT INTO categories_articles (nom, description, icone, couleur) VALUES
('Actualités', 'Nouvelles et mises à jour', 'fas fa-newspaper', '#4299e1'),
('Événements', 'Événements à venir et passés', 'fas fa-calendar-alt', '#48bb78'),
('Tutoriels', 'Guides et instructions', 'fas fa-graduation-cap', '#ed8936'),
('Communiqués', 'Annonces officielles', 'fas fa-bullhorn', '#9f7aea');

-- Données de démonstration pour les articles
INSERT INTO articles (titre, contenu, statut, date_creation, tags) VALUES
('Lancement de la nouvelle plateforme', 'Nous sommes ravis de vous annoncer le lancement de notre nouvelle plateforme collaborative...', 'publie', NOW(), 'Plateforme,Innovation,Digital'),
('Séminaire sur l\'innovation - Mars 2024', 'Rejoignez-nous pour une journée exceptionnelle dédiée à l\'innovation et aux nouvelles technologies...', 'publie', NOW(), 'Séminaire,Innovation,Technologie'),
('Guide : Optimiser son espace de travail', 'Découvrez nos conseils pratiques pour organiser efficacement votre espace de travail...', 'publie', NOW(), 'Guide,Productivité,Organisation'),
('Résultats du premier trimestre 2024', 'Nous sommes heureux de partager avec vous les excellents résultats du premier trimestre...', 'brouillon', NOW(), 'Résultats,Finance,Croissance');

-- Données de démonstration pour les documents
INSERT INTO documents (titre, description, type, statut, date_creation) VALUES
('Rapport annuel 2023', 'Rapport détaillé des activités et résultats de l\'année 2023', 'rapport', 'actif', NOW()),
('Guide utilisateur v1.0', 'Documentation complète pour les utilisateurs de la plateforme', 'guide', 'actif', NOW()),
('Procédure de sécurité', 'Protocoles et procédures de sécurité à suivre', 'procedure', 'actif', NOW());

-- Données de démonstration pour les événements
INSERT INTO evenements (titre, description, date_debut, date_fin, statut, type) VALUES
('Formation Excel Avancé', 'Formation approfondie sur les fonctionnalités avancées d\'Excel', '2024-02-15 09:00:00', '2024-02-15 17:00:00', 'planifie', 'formation'),
('Réunion d\'équipe mensuelle', 'Bilan mensuel et planification des objectifs', '2024-02-01 14:00:00', '2024-02-01 16:00:00', 'planifie', 'reunion'),
('Workshop Design Thinking', 'Atelier pratique sur les méthodes de design thinking', '2024-02-20 09:00:00', '2024-02-21 17:00:00', 'planifie', 'workshop');
