-- Structure de la table des catégories
CREATE TABLE IF NOT EXISTS link_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-folder',
    ordre INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table des liens
CREATE TABLE IF NOT EXISTS links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(2048) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-link',
    tags VARCHAR(255),
    ordre INT DEFAULT 0,
    clicks INT DEFAULT 0,
    last_check TIMESTAMP NULL,
    status VARCHAR(20) DEFAULT 'active',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES link_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de quelques catégories par défaut
INSERT INTO link_categories (name, description, icon, ordre) VALUES
('Développement Web', 'Ressources pour le développement web', 'fas fa-code', 1),
('Design', 'Outils et ressources de design', 'fas fa-paint-brush', 2),
('Productivité', 'Applications et outils de productivité', 'fas fa-tasks', 3),
('Marketing', 'Ressources marketing et SEO', 'fas fa-bullhorn', 4),
('Apprentissage', 'Plateformes et ressources d\'apprentissage', 'fas fa-graduation-cap', 5);
