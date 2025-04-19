SET FOREIGN_KEY_CHECKS=0;

-- Supprimer les tables si elles existent
DROP TABLE IF EXISTS `magasin_mouvements`;
DROP TABLE IF EXISTS `magasin_commandes_lignes`;
DROP TABLE IF EXISTS `magasin_commandes`;
DROP TABLE IF EXISTS `magasin_produits`;
DROP TABLE IF EXISTS `magasin_fournisseurs`;
DROP TABLE IF EXISTS `magasin_categories`;

SET FOREIGN_KEY_CHECKS=1;

-- Structure de la table magasin_categories
CREATE TABLE `magasin_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `color` varchar(7) DEFAULT '#000000',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table magasin_fournisseurs
CREATE TABLE `magasin_fournisseurs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255),
    `phone` varchar(20),
    `address` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table magasin_produits
CREATE TABLE `magasin_produits` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text,
    `reference` varchar(50),
    `category_id` int(11),
    `fournisseur_id` int(11),
    `quantite` int(11) DEFAULT 0,
    `unite` varchar(20) DEFAULT 'unité',
    `seuil_alerte` int(11) DEFAULT 10,
    `prix_achat` decimal(10,2),
    `prix_vente` decimal(10,2),
    PRIMARY KEY (`id`),
    KEY `category_id` (`category_id`),
    KEY `fournisseur_id` (`fournisseur_id`),
    CONSTRAINT `magasin_produits_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `magasin_categories` (`id`) ON DELETE SET NULL,
    CONSTRAINT `magasin_produits_ibfk_2` FOREIGN KEY (`fournisseur_id`) REFERENCES `magasin_fournisseurs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table magasin_mouvements
CREATE TABLE `magasin_mouvements` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `produit_id` int(11) NOT NULL,
    `type_mouvement` enum('entree','sortie') NOT NULL,
    `quantite` int(11) NOT NULL,
    `date_mouvement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` text,
    PRIMARY KEY (`id`),
    KEY `produit_id` (`produit_id`),
    CONSTRAINT `magasin_mouvements_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `magasin_produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de test pour les catégories
INSERT INTO `magasin_categories` (`name`, `color`) VALUES
('Fournitures de bureau', '#4287f5'),
('Informatique', '#f54242'),
('Mobilier', '#42f554');

-- Données de test pour les fournisseurs
INSERT INTO `magasin_fournisseurs` (`name`, `email`, `phone`) VALUES
('Bureau Plus', 'contact@bureauplus.fr', '0123456789'),
('Tech Solutions', 'sales@techsolutions.com', '0987654321'),
('Mobilier Pro', 'info@mobilierpro.fr', '0567891234');

-- Données de test pour les produits
INSERT INTO `magasin_produits` (`name`, `reference`, `category_id`, `fournisseur_id`, `quantite`, `unite`, `seuil_alerte`, `prix_achat`, `prix_vente`) VALUES
('Stylos Bic (lot de 50)', 'STY-001', 1, 1, 15, 'lot', 10, 12.50, 25.00),
('Ramette papier A4', 'PAP-001', 1, 1, 5, 'unité', 20, 3.50, 5.00),
('Souris sans fil', 'INF-001', 2, 2, 8, 'unité', 5, 15.00, 29.99),
('Chaise de bureau', 'MOB-001', 3, 3, 2, 'unité', 3, 89.00, 149.99);

-- Données de test pour les mouvements
INSERT INTO `magasin_mouvements` (`produit_id`, `type_mouvement`, `quantite`, `notes`) VALUES
(1, 'entree', 10, 'Réception commande initiale'),
(2, 'sortie', 5, 'Distribution service comptabilité'),
(3, 'entree', 15, 'Réception commande fournisseur');
