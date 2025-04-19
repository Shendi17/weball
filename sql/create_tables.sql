-- Création de la table articles
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    image_url VARCHAR(255),
    categorie VARCHAR(100),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    auteur_id INT,
    statut ENUM('brouillon', 'publie') DEFAULT 'brouillon'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formations
CREATE TABLE IF NOT EXISTS formations_formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    niveau ENUM('debutant', 'intermediaire', 'avance') DEFAULT 'debutant',
    duree VARCHAR(50),
    categorie ENUM('development', 'design', 'marketing') DEFAULT 'development',
    date_debut DATE,
    date_fin DATE,
    statut ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
    max_participants INT DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formations_participants
CREATE TABLE IF NOT EXISTS formations_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    telephone VARCHAR(20),
    service VARCHAR(100),
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations_formations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formations_formateurs
CREATE TABLE IF NOT EXISTS formations_formateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    telephone VARCHAR(20),
    specialite VARCHAR(255),
    bio TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formations_categories
CREATE TABLE IF NOT EXISTS formations_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formations_documents
CREATE TABLE IF NOT EXISTS formations_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    filename VARCHAR(255) NOT NULL,
    type ENUM('support', 'exercice', 'correction', 'autre') DEFAULT 'autre',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations_formations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table outils
CREATE TABLE IF NOT EXISTS outils (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table users si elle n'existe pas
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    permissions TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de l'utilisateur admin par défaut
INSERT INTO users (username, password, email, role, is_active)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'admin', 1);

-- Ajout des données de test dans la table articles
INSERT INTO articles (titre, contenu, statut) VALUES
('Guide débutant : Apprendre la programmation', '<p>Vous souhaitez vous lancer dans la programmation ? Ce guide vous donnera les bases essentielles pour bien démarrer.</p>', 'publie'),
('Sécurité informatique : les bonnes pratiques', '<p>La sécurité informatique est plus importante que jamais. Voici un guide des bonnes pratiques à suivre.</p>', 'publie'),
('Guide par Thomas', 'Un guide écrit par Thomas...', 'publie');

-- Ajout des données de test dans la table formations
INSERT INTO formations_formations (titre, description, niveau, duree, categorie, date_debut, date_fin, statut) VALUES
('Formation PHP Avancé', 'Apprenez les concepts avancés de PHP', 'avance', '3 semaines', 'development', '2025-02-01', '2025-02-15', 'planifiee'),
('Formation JavaScript', 'Les bases de JavaScript', 'debutant', '2 semaines', 'development', '2025-03-01', '2025-03-15', 'planifiee'),
('Formation SQL', 'Maîtrisez les bases de données', 'intermediaire', '4 semaines', 'development', '2025-04-01', '2025-04-15', 'planifiee'),
('Formation Développement Web Avancé', 'Maîtrisez les technologies web modernes : HTML5, CSS3, JavaScript et PHP', 'avance', '6 semaines', 'development', '2025-05-01', '2025-05-30', 'planifiee'),
('Formation Sécurité Web', 'Sécurisez vos applications web', 'intermediaire', '3 semaines', 'development', '2025-06-01', '2025-06-15', 'planifiee');

-- Ajout des données de test dans la table outils
INSERT INTO outils (nom, description) VALUES
('PHPUnit', 'Framework de test unitaire pour PHP'),
('Git', 'Système de contrôle de version'),
('Docker', 'Plateforme de conteneurisation');

-- Ajout des index FULLTEXT pour la recherche
ALTER TABLE articles ADD FULLTEXT INDEX ft_articles_search(titre, contenu);
ALTER TABLE formations_formations ADD FULLTEXT INDEX ft_formations_search(titre, description);
ALTER TABLE outils ADD FULLTEXT INDEX ft_outils_search(nom, description);
