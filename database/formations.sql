-- Table des formations
CREATE TABLE IF NOT EXISTS formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('Planifiée', 'En cours', 'Terminée', 'Annulée') NOT NULL DEFAULT 'Planifiée',
    formateur_id INT,
    capacite INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (formateur_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des évaluations
CREATE TABLE IF NOT EXISTS evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    user_id INT NOT NULL,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_evaluation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de test pour les formations
INSERT INTO formations (titre, description, date_debut, date_fin, statut) VALUES
('Formation SQL', 'Introduction aux bases de données SQL', '2025-01-10', '2025-01-12', 'Planifiée'),
('Formation PHP Avancé', 'Programmation PHP orientée objet', '2025-01-15', '2025-01-20', 'Planifiée'),
('Formation JavaScript', 'JavaScript moderne et frameworks', '2025-02-01', '2025-02-05', 'Planifiée');

-- Données de test pour les évaluations (à exécuter après avoir créé un utilisateur administrateur)
INSERT INTO evaluations (formation_id, user_id, note, commentaire, date_evaluation) VALUES
(1, 1, 5, 'Excellente formation', '2025-01-01 03:54:00');
