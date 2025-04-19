-- Structure de la table services
CREATE TABLE IF NOT EXISTS `services` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text,
    `color` varchar(7) DEFAULT '#000000',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table users
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `full_name` varchar(255) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table user_services
CREATE TABLE IF NOT EXISTS `user_services` (
    `user_id` int(11) NOT NULL,
    `service_id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL,
    `phone` varchar(20),
    `mobile` varchar(20),
    `office` varchar(50),
    PRIMARY KEY (`user_id`, `service_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de démonstration pour les services
INSERT INTO `services` (`name`, `description`, `color`) VALUES
('Direction Générale', 'Direction et stratégie de l''entreprise', '#FF4B4B'),
('Ressources Humaines', 'Gestion du personnel et recrutement', '#4B7BFF'),
('Informatique', 'Support technique et développement', '#4BFF4B'),
('Commercial', 'Ventes et relation client', '#FFB74B'),
('Marketing', 'Communication et stratégie marketing', '#FF4B9D'),
('Comptabilité', 'Gestion financière et comptable', '#9D4BFF'),
('Production', 'Gestion de la production et qualité', '#4BFFB7');

-- Données de démonstration pour les roles
INSERT INTO `roles` (`name`, `description`) VALUES
('Administrateur', 'Accès complet à toutes les fonctionnalités'),
('Manager', 'Gestion d''équipe et accès aux rapports'),
('Employé', 'Accès standard aux fonctionnalités de base'),
('Stagiaire', 'Accès limité sous supervision'),
('Consultant', 'Accès temporaire aux projets spécifiques');

-- Données de démonstration pour les utilisateurs
INSERT INTO `users` (
    `username`,
    `password`,
    `email`,
    `full_name`,
    `is_active`
) VALUES
('jmartin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'j.martin@example.com', 'Jean Martin', 1),
('pdupont', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'p.dupont@example.com', 'Pierre Dupont', 1),
('smoreau', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 's.moreau@example.com', 'Sophie Moreau', 1),
('mrobert', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'm.robert@example.com', 'Marie Robert', 1),
('lbernard', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'l.bernard@example.com', 'Luc Bernard', 1),
('athomas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'a.thomas@example.com', 'Antoine Thomas', 1),
('cdurand', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'c.durand@example.com', 'Claire Durand', 1),
('rpetit', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'r.petit@example.com', 'Richard Petit', 1),
('vleroy', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'v.leroy@example.com', 'Valérie Leroy', 1),
('nroux', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'n.roux@example.com', 'Nicolas Roux', 1),
('mmichel', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'm.michel@example.com', 'Mathilde Michel', 1),
('fgirard', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'f.girard@example.com', 'François Girard', 1),
('dblanc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'd.blanc@example.com', 'David Blanc', 1),
('jfontaine', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'j.fontaine@example.com', 'Julie Fontaine', 1),
('pgarcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'p.garcia@example.com', 'Paul Garcia', 1),
('ecaron', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'e.caron@example.com', 'Emma Caron', 0);

-- Données de démonstration pour user_services
INSERT INTO `user_services` (`user_id`, `service_id`, `role_id`, `phone`, `mobile`, `office`) VALUES
(1, 1, 1, '0123456789', '0612345678', 'Bureau 101'),
(2, 1, 2, '0123456790', '0612345679', 'Bureau 102'),
(3, 2, 2, '0123456791', '0612345680', 'Bureau 201'),
(4, 2, 3, '0123456792', '0612345681', 'Bureau 202'),
(5, 3, 2, '0123456793', '0612345682', 'Bureau 301'),
(6, 3, 3, '0123456794', '0612345683', 'Bureau 302'),
(7, 3, 4, '0123456795', '0612345684', 'Bureau 303'),
(8, 4, 2, '0123456796', '0612345685', 'Bureau 401'),
(9, 4, 3, '0123456797', '0612345686', 'Bureau 402'),
(10, 5, 2, '0123456798', '0612345687', 'Bureau 501'),
(11, 5, 3, '0123456799', '0612345688', 'Bureau 502'),
(12, 6, 2, '0123456800', '0612345689', 'Bureau 601'),
(13, 6, 3, '0123456801', '0612345690', 'Bureau 602'),
(14, 7, 2, '0123456802', '0612345691', 'Bureau 701'),
(15, 7, 3, '0123456803', '0612345692', 'Bureau 702'),
(16, 7, 4, '0123456804', '0612345693', 'Bureau 703');
