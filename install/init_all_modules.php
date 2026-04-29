<?php
/**
 * Script d'initialisation de tous les modules WebAll
 * Crée les tables et insère les données de démo
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getPDO();
    
    echo "=== Initialisation de tous les modules WebAll ===\n\n";
    
    // 1. OUTILS - Cadran
    echo "Création de la table 'cadran'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS cadran (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        statut VARCHAR(50) DEFAULT 'actif'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO cadran (titre, description, type, user_id) VALUES
        ('Tableau de bord principal', 'Vue d\'ensemble des activités', 'dashboard', 1),
        ('Statistiques mensuelles', 'Rapport des performances du mois', 'stats', 1),
        ('Alertes système', 'Notifications importantes', 'alerts', 1)");
    
    // 2. OUTILS - Journal
    echo "Création de la table 'journal'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS journal (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        contenu TEXT,
        categorie VARCHAR(100),
        date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
        auteur_id INT,
        statut VARCHAR(50) DEFAULT 'publié'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO journal (titre, contenu, categorie, auteur_id) VALUES
        ('Lancement de WebAll', 'Aujourd\'hui marque le début d\'une nouvelle ère pour notre plateforme', 'annonce', 1),
        ('Mise à jour système', 'Nouvelles fonctionnalités disponibles', 'technique', 1),
        ('Événement communautaire', 'Rejoignez-nous pour notre prochain webinaire', 'événement', 1),
        ('Rapport mensuel', 'Bilan des activités du mois écoulé', 'rapport', 1)");
    
    // 3. OUTILS - Ecran
    echo "Création de la table 'ecran'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS ecran (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        resolution VARCHAR(50),
        date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO ecran (nom, description, type, resolution, user_id) VALUES
        ('Écran principal', 'Affichage principal de la salle de conférence', 'LED', '1920x1080', 1),
        ('Écran secondaire', 'Écran de présentation mobile', 'LCD', '1366x768', 1),
        ('Écran tactile', 'Interface interactive pour les visiteurs', 'Tactile', '1920x1080', 1)");
    
    // 4. CLASSEUR - Personnalité
    echo "Création de la table 'personnalite'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS personnalite (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        titre VARCHAR(255),
        biographie TEXT,
        photo VARCHAR(255),
        date_naissance DATE,
        email VARCHAR(255),
        telephone VARCHAR(50),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO personnalite (nom, prenom, titre, biographie, email, telephone, user_id) VALUES
        ('Dupont', 'Jean', 'Directeur Général', 'Expert en management avec 20 ans d\'expérience', 'jean.dupont@example.com', '+33 1 23 45 67 89', 1),
        ('Martin', 'Claire', 'Responsable Marketing', 'Spécialiste en stratégie digitale', 'claire.martin@example.com', '+33 1 23 45 67 90', 1),
        ('Durand', 'Paul', 'Chef de Projet', 'Gestion de projets innovants', 'paul.durand@example.com', '+33 1 23 45 67 91', 1),
        ('Bernard', 'Sophie', 'Directrice Financière', 'Expert-comptable certifiée', 'sophie.bernard@example.com', '+33 1 23 45 67 92', 1)");
    
    // 5. CLASSEUR - Autorité
    echo "Création de la table 'autorite'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS autorite (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        domaine VARCHAR(255),
        pays VARCHAR(100),
        site_web VARCHAR(255),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO autorite (nom, type, description, domaine, pays, site_web, user_id) VALUES
        ('Autorité de Régulation des Télécoms', 'Régulateur', 'Organisme de régulation des télécommunications', 'Télécommunications', 'France', 'https://arcep.fr', 1),
        ('Commission Nationale Informatique', 'Commission', 'Protection des données personnelles', 'Informatique', 'France', 'https://cnil.fr', 1),
        ('Autorité de la Concurrence', 'Régulateur', 'Surveillance des pratiques commerciales', 'Commerce', 'France', 'https://autoritedelaconcurrence.fr', 1)");
    
    // 6. CLASSEUR - Entité
    echo "Création de la table 'entite'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS entite (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        secteur VARCHAR(255),
        adresse TEXT,
        ville VARCHAR(100),
        code_postal VARCHAR(20),
        pays VARCHAR(100),
        telephone VARCHAR(50),
        email VARCHAR(255),
        site_web VARCHAR(255),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO entite (nom, type, description, secteur, adresse, ville, code_postal, pays, telephone, email, site_web, user_id) VALUES
        ('TechCorp Solutions', 'Entreprise', 'Société de développement logiciel', 'Technologie', '123 Rue de la Tech', 'Paris', '75001', 'France', '+33 1 23 45 67 89', 'contact@techcorp.fr', 'https://techcorp.fr', 1),
        ('Innovation Lab', 'Laboratoire', 'Centre de recherche et innovation', 'R&D', '456 Avenue des Sciences', 'Lyon', '69001', 'France', '+33 4 12 34 56 78', 'info@innovlab.fr', 'https://innovlab.fr', 1),
        ('Digital Agency', 'Agence', 'Agence de communication digitale', 'Marketing', '789 Boulevard du Web', 'Marseille', '13001', 'France', '+33 4 91 23 45 67', 'hello@digitalagency.fr', 'https://digitalagency.fr', 1)");
    
    // 7. CLASSEUR - Localité
    echo "Création de la table 'localite'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS localite (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        region VARCHAR(255),
        pays VARCHAR(100),
        code_postal VARCHAR(20),
        population INT,
        superficie DECIMAL(10,2),
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO localite (nom, type, description, region, pays, code_postal, population, superficie, user_id) VALUES
        ('Paris', 'Ville', 'Capitale de la France', 'Île-de-France', 'France', '75000', 2165423, 105.40, 1),
        ('Lyon', 'Ville', 'Troisième ville de France', 'Auvergne-Rhône-Alpes', 'France', '69000', 516092, 47.87, 1),
        ('Marseille', 'Ville', 'Deuxième ville de France', 'Provence-Alpes-Côte d\'Azur', 'France', '13000', 869815, 240.62, 1),
        ('Toulouse', 'Ville', 'Ville rose du sud-ouest', 'Occitanie', 'France', '31000', 471941, 118.30, 1)");
    
    // 8. COLLECTION - Instrument
    echo "Création de la table 'instrument'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS instrument (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        categorie VARCHAR(100),
        fabricant VARCHAR(255),
        modele VARCHAR(255),
        annee_fabrication INT,
        etat VARCHAR(50),
        date_acquisition DATETIME,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO instrument (nom, type, description, categorie, fabricant, modele, annee_fabrication, etat, user_id) VALUES
        ('Piano à queue Steinway', 'Instrument de musique', 'Piano de concert professionnel', 'Clavier', 'Steinway & Sons', 'Model D', 2015, 'Excellent', 1),
        ('Violon Stradivarius (copie)', 'Instrument à cordes', 'Réplique de haute qualité', 'Cordes', 'Luthier Artisan', 'Strad Copy', 2018, 'Très bon', 1),
        ('Guitare électrique Fender', 'Instrument électrique', 'Guitare stratocaster classique', 'Cordes', 'Fender', 'Stratocaster', 2020, 'Neuf', 1),
        ('Saxophone alto Yamaha', 'Instrument à vent', 'Saxophone professionnel', 'Vent', 'Yamaha', 'YAS-62', 2019, 'Excellent', 1)");
    
    // 9. COLLECTION - Office
    echo "Création de la table 'office'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS office (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        adresse TEXT,
        ville VARCHAR(100),
        code_postal VARCHAR(20),
        pays VARCHAR(100),
        capacite INT,
        equipements TEXT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO office (nom, type, description, adresse, ville, code_postal, pays, capacite, equipements, user_id) VALUES
        ('Bureau Principal Paris', 'Siège social', 'Bureau principal de l\'entreprise', '100 Avenue des Champs-Élysées', 'Paris', '75008', 'France', 150, 'WiFi, Climatisation, Salle de réunion, Cafétéria', 1),
        ('Espace Coworking Lyon', 'Coworking', 'Espace de travail partagé moderne', '25 Rue de la République', 'Lyon', '69002', 'France', 50, 'WiFi, Imprimante, Cuisine, Terrasse', 1),
        ('Bureau Satellite Marseille', 'Bureau régional', 'Antenne régionale', '15 Cours Julien', 'Marseille', '13006', 'France', 30, 'WiFi, Salle de réunion, Parking', 1)");
    
    // 10. PLATEFORME - Banque
    echo "Création de la table 'banque'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS banque (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        code_swift VARCHAR(50),
        code_bic VARCHAR(50),
        pays VARCHAR(100),
        adresse TEXT,
        telephone VARCHAR(50),
        email VARCHAR(255),
        site_web VARCHAR(255),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO banque (nom, type, description, code_swift, code_bic, pays, adresse, telephone, email, site_web, user_id) VALUES
        ('Banque Nationale de France', 'Banque centrale', 'Institution financière principale', 'BNFRFRPP', 'BNFRFRPPXXX', 'France', '1 Rue de la Banque, Paris', '+33 1 42 92 42 92', 'contact@bnf.fr', 'https://bnf.fr', 1),
        ('Crédit Universel', 'Banque commerciale', 'Banque de détail et services financiers', 'CREUFRPP', 'CREUFRPPXXX', 'France', '50 Avenue de l\'Opéra, Paris', '+33 1 40 00 40 00', 'info@credituniversel.fr', 'https://credituniversel.fr', 1),
        ('Banque Digitale Pro', 'Banque en ligne', 'Services bancaires 100% digitaux', 'BDPRFRPP', 'BDPRFRPPXXX', 'France', '100 Rue du Numérique, Lyon', '+33 4 72 00 72 00', 'support@banquedigitale.fr', 'https://banquedigitale.fr', 1)");
    
    // 11. PLATEFORME - Réseau
    echo "Création de la table 'reseau'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS reseau (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        description TEXT,
        domaine VARCHAR(255),
        membres_count INT DEFAULT 0,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        statut VARCHAR(50) DEFAULT 'actif'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO reseau (nom, type, description, domaine, membres_count, user_id) VALUES
        ('Réseau des Entrepreneurs', 'Professionnel', 'Communauté d\'entrepreneurs et startups', 'Business', 1250, 1),
        ('Tech Community France', 'Technique', 'Réseau des professionnels de la tech', 'Technologie', 3400, 1),
        ('Réseau Alumni Sciences Po', 'Académique', 'Anciens élèves de Sciences Po', 'Éducation', 5600, 1),
        ('Creative Network', 'Créatif', 'Réseau des créatifs et designers', 'Design', 890, 1)");
    
    // 12. REGISTRE - Publication
    echo "Création de la table 'publication'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS publication (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        contenu TEXT,
        type VARCHAR(100),
        auteur VARCHAR(255),
        date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
        categorie VARCHAR(100),
        tags TEXT,
        statut VARCHAR(50) DEFAULT 'publié',
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO publication (titre, contenu, type, auteur, categorie, tags, user_id) VALUES
        ('Guide complet du développement web moderne', 'Un guide exhaustif sur les technologies web actuelles...', 'Article', 'Jean Dupont', 'Technologie', 'web,développement,javascript', 1),
        ('Les tendances marketing 2025', 'Analyse des nouvelles tendances du marketing digital...', 'Étude', 'Claire Martin', 'Marketing', 'marketing,tendances,digital', 1),
        ('Rapport annuel 2024', 'Bilan des activités et résultats de l\'année 2024...', 'Rapport', 'Sophie Bernard', 'Finance', 'rapport,finance,2024', 1),
        ('Innovation et Intelligence Artificielle', 'L\'impact de l\'IA sur les entreprises modernes...', 'Article', 'Paul Durand', 'Innovation', 'ia,innovation,technologie', 1)");
    
    // 13. REGISTRE - Archive
    echo "Création de la table 'archive'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS archive (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        reference VARCHAR(100),
        date_document DATE,
        date_archivage DATETIME DEFAULT CURRENT_TIMESTAMP,
        emplacement VARCHAR(255),
        format VARCHAR(50),
        taille VARCHAR(50),
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO archive (titre, description, type, reference, date_document, emplacement, format, taille, user_id) VALUES
        ('Contrats 2020-2023', 'Archive des contrats commerciaux', 'Contrat', 'ARC-2023-001', '2023-12-31', 'Armoire A, Étagère 3', 'PDF', '250 MB', 1),
        ('Documents comptables 2022', 'Bilans et documents financiers', 'Comptabilité', 'ARC-2023-002', '2022-12-31', 'Coffre-fort numérique', 'PDF', '180 MB', 1),
        ('Correspondances historiques', 'Lettres et échanges importants', 'Correspondance', 'ARC-2023-003', '2021-06-15', 'Armoire B, Étagère 1', 'Papier', 'N/A', 1),
        ('Projets archivés 2019-2021', 'Documentation des projets terminés', 'Projet', 'ARC-2023-004', '2021-12-31', 'Serveur Archive', 'ZIP', '1.2 GB', 1)");
    
    // 14. ECOLE - Média
    echo "Création de la table 'media'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        url VARCHAR(255),
        fichier VARCHAR(255),
        format VARCHAR(50),
        taille VARCHAR(50),
        duree VARCHAR(50),
        date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
        categorie VARCHAR(100),
        tags TEXT,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO media (titre, description, type, format, taille, duree, categorie, tags, user_id) VALUES
        ('Tutoriel : Introduction à React', 'Vidéo d\'introduction au framework React', 'Vidéo', 'MP4', '250 MB', '45:30', 'Formation', 'react,javascript,tutoriel', 1),
        ('Présentation WebAll 2025', 'Slides de présentation du projet', 'Présentation', 'PDF', '15 MB', 'N/A', 'Présentation', 'weball,présentation', 1),
        ('Podcast : L\'avenir du web', 'Discussion sur les technologies futures', 'Audio', 'MP3', '85 MB', '60:00', 'Podcast', 'web,technologie,futur', 1),
        ('Infographie : Statistiques 2024', 'Visualisation des données annuelles', 'Image', 'PNG', '5 MB', 'N/A', 'Infographie', 'stats,2024,données', 1)");
    
    // 15. ECOLE - Formation
    echo "Création de la table 'formation'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS formation (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        domaine VARCHAR(100),
        niveau VARCHAR(50),
        duree VARCHAR(100),
        formateur VARCHAR(255),
        prix DECIMAL(10,2),
        date_debut DATE,
        date_fin DATE,
        places_disponibles INT,
        lieu VARCHAR(255),
        format VARCHAR(50),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        statut VARCHAR(50) DEFAULT 'ouvert'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO formation (titre, description, domaine, niveau, duree, formateur, prix, date_debut, date_fin, places_disponibles, lieu, format, user_id) VALUES
        ('Développement Web Full-Stack', 'Formation complète sur le développement web moderne', 'Développement', 'Intermédiaire', '12 semaines', 'Jean Dupont', 2500.00, '2025-02-01', '2025-04-30', 20, 'Paris', 'Présentiel', 1),
        ('Marketing Digital Avancé', 'Stratégies avancées de marketing en ligne', 'Marketing', 'Avancé', '8 semaines', 'Claire Martin', 1800.00, '2025-03-01', '2025-04-30', 15, 'Lyon', 'Hybride', 1),
        ('Gestion de Projet Agile', 'Méthodologies agiles et Scrum', 'Management', 'Débutant', '4 semaines', 'Paul Durand', 1200.00, '2025-02-15', '2025-03-15', 25, 'En ligne', 'Distanciel', 1),
        ('Data Science et IA', 'Introduction à la science des données', 'Data Science', 'Intermédiaire', '10 semaines', 'Sophie Bernard', 2200.00, '2025-04-01', '2025-06-15', 18, 'Paris', 'Présentiel', 1)");
    
    // 16. SELECTION - Annonce
    echo "Création de la table 'annonce'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS annonce (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        categorie VARCHAR(100),
        prix DECIMAL(10,2),
        localisation VARCHAR(255),
        contact_nom VARCHAR(255),
        contact_email VARCHAR(255),
        contact_telephone VARCHAR(50),
        date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_expiration DATE,
        statut VARCHAR(50) DEFAULT 'active',
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO annonce (titre, description, type, categorie, prix, localisation, contact_nom, contact_email, contact_telephone, date_expiration, user_id) VALUES
        ('Développeur Full-Stack recherché', 'Nous recherchons un développeur expérimenté pour rejoindre notre équipe', 'Emploi', 'Technologie', 45000.00, 'Paris', 'Jean Dupont', 'recrutement@techcorp.fr', '+33 1 23 45 67 89', '2025-03-31', 1),
        ('Appartement 3 pièces à louer', 'Bel appartement lumineux en centre-ville', 'Immobilier', 'Location', 1200.00, 'Lyon', 'Claire Martin', 'location@immo.fr', '+33 4 12 34 56 78', '2025-04-30', 1),
        ('MacBook Pro 2022 à vendre', 'MacBook Pro 16 pouces, excellent état', 'Vente', 'Électronique', 1800.00, 'Marseille', 'Paul Durand', 'paul.durand@example.com', '+33 6 12 34 56 78', '2025-02-28', 1),
        ('Cours particuliers de mathématiques', 'Professeur certifié donne cours tous niveaux', 'Service', 'Éducation', 35.00, 'Toulouse', 'Sophie Bernard', 'sophie.cours@example.com', '+33 6 23 45 67 89', '2025-06-30', 1)");
    
    // 17. SELECTION - Catalogue
    echo "Création de la table 'catalogue'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS catalogue (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        categorie VARCHAR(100),
        reference VARCHAR(100),
        prix DECIMAL(10,2),
        stock INT DEFAULT 0,
        image VARCHAR(255),
        specifications TEXT,
        date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        statut VARCHAR(50) DEFAULT 'disponible'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO catalogue (nom, description, type, categorie, reference, prix, stock, specifications, user_id) VALUES
        ('Ordinateur portable Pro 15', 'Laptop professionnel haute performance', 'Électronique', 'Informatique', 'CAT-2025-001', 1299.99, 45, 'Intel i7, 16GB RAM, 512GB SSD', 1),
        ('Chaise de bureau ergonomique', 'Chaise confortable pour longues sessions', 'Mobilier', 'Bureau', 'CAT-2025-002', 349.99, 120, 'Réglable, Support lombaire, Accoudoirs 3D', 1),
        ('Écran 27 pouces 4K', 'Moniteur professionnel ultra HD', 'Électronique', 'Informatique', 'CAT-2025-003', 599.99, 30, '27 pouces, 4K UHD, IPS, 60Hz', 1),
        ('Pack Office Premium', 'Suite logicielle complète', 'Logiciel', 'Productivité', 'CAT-2025-004', 149.99, 999, 'Word, Excel, PowerPoint, Outlook', 1)");
    
    // 18. MARCHE - Boutique
    echo "Création de la table 'boutique'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS boutique (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        adresse TEXT,
        ville VARCHAR(100),
        code_postal VARCHAR(20),
        pays VARCHAR(100),
        telephone VARCHAR(50),
        email VARCHAR(255),
        site_web VARCHAR(255),
        horaires TEXT,
        specialites TEXT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        statut VARCHAR(50) DEFAULT 'ouvert'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO boutique (nom, description, type, adresse, ville, code_postal, pays, telephone, email, site_web, horaires, specialites, user_id) VALUES
        ('TechStore Paris', 'Boutique spécialisée en high-tech', 'Électronique', '50 Rue de Rivoli', 'Paris', '75001', 'France', '+33 1 40 00 00 00', 'contact@techstore.fr', 'https://techstore.fr', 'Lun-Sam: 9h-19h', 'Ordinateurs, Smartphones, Accessoires', 1),
        ('Librairie du Savoir', 'Librairie généraliste et spécialisée', 'Librairie', '25 Boulevard Saint-Germain', 'Paris', '75005', 'France', '+33 1 43 25 00 00', 'info@librairie-savoir.fr', 'https://librairie-savoir.fr', 'Lun-Dim: 10h-20h', 'Littérature, Sciences, Arts', 1),
        ('Mode & Style', 'Boutique de vêtements tendance', 'Mode', '15 Rue de la Mode', 'Lyon', '69002', 'France', '+33 4 78 00 00 00', 'contact@mode-style.fr', 'https://mode-style.fr', 'Mar-Sam: 10h-19h', 'Vêtements, Accessoires, Chaussures', 1),
        ('Bio Market', 'Supermarché bio et local', 'Alimentation', '30 Avenue Verte', 'Marseille', '13001', 'France', '+33 4 91 00 00 00', 'hello@biomarket.fr', 'https://biomarket.fr', 'Lun-Sam: 8h-20h', 'Produits bio, Fruits & Légumes, Épicerie', 1)");
    
    // 19. MARCHE - Adhésion
    echo "Création de la table 'adhesion'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS adhesion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        telephone VARCHAR(50),
        type_adhesion VARCHAR(100),
        duree VARCHAR(50),
        montant DECIMAL(10,2),
        date_debut DATE,
        date_fin DATE,
        statut VARCHAR(50) DEFAULT 'active',
        date_adhesion DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO adhesion (nom, prenom, email, telephone, type_adhesion, duree, montant, date_debut, date_fin, user_id) VALUES
        ('Dupont', 'Jean', 'jean.dupont@example.com', '+33 6 12 34 56 78', 'Premium', '1 an', 299.99, '2025-01-01', '2025-12-31', 1),
        ('Martin', 'Claire', 'claire.martin@example.com', '+33 6 23 45 67 89', 'Standard', '1 an', 149.99, '2025-01-15', '2026-01-14', 1),
        ('Durand', 'Paul', 'paul.durand@example.com', '+33 6 34 56 78 90', 'Premium', '6 mois', 179.99, '2025-02-01', '2025-07-31', 1),
        ('Bernard', 'Sophie', 'sophie.bernard@example.com', '+33 6 45 67 89 01', 'VIP', '1 an', 499.99, '2025-01-01', '2025-12-31', 1)");
    
    // 20. GUIDE - Projet
    echo "Création de la table 'projet'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS projet (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        objectifs TEXT,
        budget DECIMAL(12,2),
        date_debut DATE,
        date_fin_prevue DATE,
        statut VARCHAR(50) DEFAULT 'en_cours',
        responsable VARCHAR(255),
        equipe TEXT,
        avancement INT DEFAULT 0,
        priorite VARCHAR(50),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO projet (nom, description, objectifs, budget, date_debut, date_fin_prevue, responsable, equipe, avancement, priorite, user_id) VALUES
        ('Refonte du site web', 'Modernisation complète du site internet', 'Améliorer UX, Performance, SEO', 50000.00, '2025-01-15', '2025-06-30', 'Jean Dupont', 'Dev: 3, Design: 2, Marketing: 1', 35, 'Haute', 1),
        ('Lancement application mobile', 'Développement app iOS et Android', 'Atteindre 10k utilisateurs en 6 mois', 120000.00, '2025-02-01', '2025-12-31', 'Claire Martin', 'Dev: 5, Design: 2, QA: 2', 15, 'Haute', 1),
        ('Campagne marketing Q1', 'Campagne publicitaire multi-canal', 'Augmenter notoriété de 30%', 35000.00, '2025-01-01', '2025-03-31', 'Paul Durand', 'Marketing: 4, Communication: 2', 60, 'Moyenne', 1),
        ('Migration cloud', 'Transfert infrastructure vers le cloud', 'Réduire coûts IT de 25%', 80000.00, '2025-03-01', '2025-09-30', 'Sophie Bernard', 'DevOps: 3, Sysadmin: 2', 10, 'Haute', 1)");
    
    // 21. GUIDE - Carrière
    echo "Création de la table 'carriere'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS carriere (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        entreprise VARCHAR(255),
        type_contrat VARCHAR(100),
        localisation VARCHAR(255),
        salaire_min DECIMAL(10,2),
        salaire_max DECIMAL(10,2),
        experience_requise VARCHAR(100),
        competences TEXT,
        date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_limite DATE,
        contact_email VARCHAR(255),
        statut VARCHAR(50) DEFAULT 'ouvert',
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO carriere (titre, description, entreprise, type_contrat, localisation, salaire_min, salaire_max, experience_requise, competences, date_limite, contact_email, user_id) VALUES
        ('Développeur Full-Stack Senior', 'Rejoignez notre équipe technique innovante', 'TechCorp Solutions', 'CDI', 'Paris', 45000.00, 65000.00, '5+ ans', 'React, Node.js, PostgreSQL, Docker', '2025-03-31', 'recrutement@techcorp.fr', 1),
        ('Chef de Projet Digital', 'Pilotez nos projets digitaux stratégiques', 'Digital Agency', 'CDI', 'Lyon', 40000.00, 55000.00, '3-5 ans', 'Gestion de projet, Agile, Communication', '2025-04-15', 'rh@digitalagency.fr', 1),
        ('Data Scientist', 'Exploitez la puissance des données', 'Innovation Lab', 'CDI', 'Paris', 50000.00, 70000.00, '3+ ans', 'Python, Machine Learning, SQL, Statistiques', '2025-03-20', 'jobs@innovlab.fr', 1),
        ('Designer UX/UI', 'Créez des expériences utilisateur exceptionnelles', 'Creative Studio', 'Freelance', 'Remote', 35000.00, 50000.00, '2-4 ans', 'Figma, Adobe XD, Prototypage, User Research', '2025-04-30', 'contact@creativestudio.fr', 1)");
    
    // 22. GUIDE - Discipline
    echo "Création de la table 'discipline'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS discipline (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        domaine VARCHAR(100),
        niveau VARCHAR(50),
        prerequis TEXT,
        objectifs TEXT,
        contenu TEXT,
        duree VARCHAR(100),
        credits INT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO discipline (nom, description, domaine, niveau, prerequis, objectifs, contenu, duree, credits, user_id) VALUES
        ('Algorithmique Avancée', 'Étude approfondie des algorithmes et structures de données', 'Informatique', 'Master', 'Bases en programmation', 'Maîtriser les algorithmes complexes', 'Tri, Graphes, Programmation dynamique, Complexité', '48 heures', 6, 1),
        ('Marketing Stratégique', 'Fondamentaux du marketing et stratégies commerciales', 'Commerce', 'Licence', 'Aucun', 'Comprendre les stratégies marketing', 'Mix marketing, Segmentation, Positionnement, Analyse concurrentielle', '36 heures', 5, 1),
        ('Gestion Financière', 'Principes de la finance d\'entreprise', 'Finance', 'Master', 'Comptabilité de base', 'Analyser et gérer les finances', 'Analyse financière, Investissement, Financement, Risques', '42 heures', 6, 1),
        ('Intelligence Artificielle', 'Introduction aux concepts de l\'IA', 'Informatique', 'Master', 'Python, Mathématiques', 'Comprendre et appliquer l\'IA', 'Machine Learning, Deep Learning, NLP, Vision par ordinateur', '60 heures', 8, 1)");
    
    // 23. GUIDE - Concours
    echo "Création de la table 'concours'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS concours (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        domaine VARCHAR(100),
        organisateur VARCHAR(255),
        date_debut DATE,
        date_fin DATE,
        date_limite_inscription DATE,
        prix TEXT,
        conditions TEXT,
        reglement TEXT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        statut VARCHAR(50) DEFAULT 'ouvert'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO concours (nom, description, type, domaine, organisateur, date_debut, date_fin, date_limite_inscription, prix, conditions, user_id) VALUES
        ('Hackathon Innovation 2025', 'Compétition de développement sur 48h', 'Hackathon', 'Technologie', 'TechCorp Solutions', '2025-04-15', '2025-04-17', '2025-04-01', '1er: 5000€, 2ème: 3000€, 3ème: 1000€', 'Équipes de 2-5 personnes, Tous niveaux', 1),
        ('Concours de Design Web', 'Créez le meilleur site web responsive', 'Design', 'Web', 'Digital Agency', '2025-03-01', '2025-05-31', '2025-02-28', '1er: 3000€, 2ème: 2000€, 3ème: 500€', 'Individuel, Portfolio requis', 1),
        ('Prix de l\'Innovation Sociale', 'Récompense les projets à impact social', 'Prix', 'Social', 'Innovation Lab', '2025-02-01', '2025-06-30', '2025-05-31', '1er: 10000€ + Accompagnement', 'Associations et startups', 1),
        ('Challenge Data Science', 'Résolvez des problèmes complexes de data', 'Challenge', 'Data', 'Data Community', '2025-03-15', '2025-04-15', '2025-03-10', '1er: 4000€, 2ème: 2000€, 3ème: 1000€', 'Individuel ou équipe, Kaggle-style', 1)");
    
    // 24. GUIDE - Campagne
    echo "Création de la table 'campagne'...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS campagne (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        type VARCHAR(100),
        objectif TEXT,
        budget DECIMAL(12,2),
        date_debut DATE,
        date_fin DATE,
        cible TEXT,
        canaux TEXT,
        kpi TEXT,
        resultats TEXT,
        statut VARCHAR(50) DEFAULT 'planifiée',
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("INSERT INTO campagne (nom, description, type, objectif, budget, date_debut, date_fin, cible, canaux, kpi, user_id) VALUES
        ('Lancement Produit X', 'Campagne de lancement du nouveau produit', 'Lancement', 'Atteindre 50k impressions et 1000 conversions', 25000.00, '2025-03-01', '2025-04-30', 'Professionnels 25-45 ans', 'Google Ads, Facebook, LinkedIn, Email', 'Impressions, Clics, Conversions, ROI', 1),
        ('Campagne de Recrutement', 'Attirer les meilleurs talents', 'Recrutement', 'Recevoir 200 candidatures qualifiées', 15000.00, '2025-02-15', '2025-05-15', 'Développeurs et designers', 'LinkedIn, Indeed, Site carrière, Événements', 'Candidatures, Qualité profils, Embauches', 1),
        ('Sensibilisation Écologique', 'Promouvoir nos engagements environnementaux', 'Sensibilisation', 'Améliorer image de marque éco-responsable', 30000.00, '2025-04-01', '2025-06-30', 'Grand public', 'Réseaux sociaux, Presse, Événements, Partenariats', 'Notoriété, Engagement, Sentiment positif', 1),
        ('Black Friday 2025', 'Campagne promotionnelle annuelle', 'Promotion', 'Augmenter CA de 150% sur la période', 50000.00, '2025-11-20', '2025-11-30', 'Tous clients', 'Email, SMS, Réseaux sociaux, Display', 'CA, Panier moyen, Taux de conversion', 1)");
    
    echo "\n=== Initialisation terminée avec succès ! ===\n";
    echo "Toutes les tables ont été créées et les données de démo insérées.\n";
    
} catch (PDOException $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    exit(1);
}
