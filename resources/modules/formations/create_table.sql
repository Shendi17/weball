CREATE TABLE IF NOT EXISTS formations_formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    categorie VARCHAR(50) DEFAULT 'development',
    niveau VARCHAR(50) DEFAULT 'intermediaire',
    duree VARCHAR(50) DEFAULT '40 heures',
    date_debut DATE,
    date_fin DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
