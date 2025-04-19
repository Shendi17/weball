-- Structure de la table des catégories de registre
CREATE TABLE IF NOT EXISTS register_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-folder',
    color VARCHAR(20) DEFAULT '#4a90e2',
    ordre INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table des entrées du registre
CREATE TABLE IF NOT EXISTS register_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'en_cours',
    priority VARCHAR(20) DEFAULT 'normale',
    date_event DATETIME NOT NULL,
    date_deadline DATETIME,
    assigned_to VARCHAR(100),
    tags VARCHAR(255),
    attachments TEXT,
    ordre INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES register_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table des commentaires
CREATE TABLE IF NOT EXISTS register_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_id INT NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entry_id) REFERENCES register_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des catégories par défaut
INSERT INTO register_categories (name, description, icon, color, ordre) VALUES
('Général', 'Entrées générales du registre', 'fas fa-book', '#4a90e2', 1),
('Maintenance', 'Tâches de maintenance et entretien', 'fas fa-tools', '#f39c12', 2),
('Incidents', 'Rapports d''incidents et problèmes', 'fas fa-exclamation-triangle', '#e74c3c', 3),
('Réunions', 'Comptes-rendus de réunions', 'fas fa-users', '#2ecc71', 4),
('Projets', 'Suivi des projets', 'fas fa-project-diagram', '#9b59b6', 5);
