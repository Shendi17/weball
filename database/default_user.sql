-- Création d'un utilisateur par défaut (mot de passe : admin123)
INSERT INTO users (username, password, email, full_name, permissions, is_active)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin@weballone.local',
    'Administrateur',
    '["annuaire","magasin","formations","admin"]',
    1
);
