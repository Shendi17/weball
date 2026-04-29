<?php
/**
 * Enrichissement des types et catégories pour le module Personnalité
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Enrichissement Personnalité ===\n\n";

// Supprimer les anciennes données
echo "Nettoyage des anciennes données...\n";
$pdo->exec("DELETE FROM personnalite_categories");
$pdo->exec("DELETE FROM personnalite_types");
$pdo->exec("ALTER TABLE personnalite_types AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE personnalite_categories AUTO_INCREMENT = 1");

// Types enrichis pour Personnalité
$types = [
    // Arts & Culture
    ['nom' => 'Artiste', 'slug' => 'artiste', 'icon' => 'palette', 'description' => 'Créateurs et artistes'],
    ['nom' => 'Musicien', 'slug' => 'musicien', 'icon' => 'music', 'description' => 'Musiciens et compositeurs'],
    ['nom' => 'Acteur', 'slug' => 'acteur', 'icon' => 'theater-masks', 'description' => 'Acteurs et comédiens'],
    ['nom' => 'Écrivain', 'slug' => 'ecrivain', 'icon' => 'pen-fancy', 'description' => 'Auteurs et écrivains'],
    ['nom' => 'Réalisateur', 'slug' => 'realisateur', 'icon' => 'film', 'description' => 'Réalisateurs et cinéastes'],
    
    // Médias & Communication
    ['nom' => 'Influenceur', 'slug' => 'influenceur', 'icon' => 'users', 'description' => 'Influenceurs digitaux'],
    ['nom' => 'Journaliste', 'slug' => 'journaliste', 'icon' => 'newspaper', 'description' => 'Journalistes et reporters'],
    ['nom' => 'Animateur', 'slug' => 'animateur', 'icon' => 'microphone-alt', 'description' => 'Animateurs TV/Radio'],
    ['nom' => 'Créateur Contenu', 'slug' => 'createur-contenu', 'icon' => 'video', 'description' => 'Créateurs de contenu'],
    
    // Business & Entrepreneuriat
    ['nom' => 'Entrepreneur', 'slug' => 'entrepreneur', 'icon' => 'briefcase', 'description' => 'Entrepreneurs et fondateurs'],
    ['nom' => 'Chef Entreprise', 'slug' => 'chef-entreprise', 'icon' => 'building', 'description' => 'PDG et dirigeants'],
    ['nom' => 'Investisseur', 'slug' => 'investisseur', 'icon' => 'chart-line', 'description' => 'Investisseurs et business angels'],
    
    // Éducation & Conférences
    ['nom' => 'Conférencier', 'slug' => 'conferencier', 'icon' => 'microphone', 'description' => 'Conférenciers et speakers'],
    ['nom' => 'Formateur', 'slug' => 'formateur', 'icon' => 'chalkboard-teacher', 'description' => 'Formateurs et coachs'],
    ['nom' => 'Chercheur', 'slug' => 'chercheur', 'icon' => 'flask', 'description' => 'Chercheurs et scientifiques'],
    ['nom' => 'Professeur', 'slug' => 'professeur', 'icon' => 'graduation-cap', 'description' => 'Professeurs et enseignants'],
    
    // Sport
    ['nom' => 'Sportif', 'slug' => 'sportif', 'icon' => 'running', 'description' => 'Sportifs professionnels'],
    ['nom' => 'Entraîneur', 'slug' => 'entraineur', 'icon' => 'whistle', 'description' => 'Entraîneurs sportifs'],
    
    // Politique & Société
    ['nom' => 'Politique', 'slug' => 'politique', 'icon' => 'landmark', 'description' => 'Personnalités politiques'],
    ['nom' => 'Activiste', 'slug' => 'activiste', 'icon' => 'fist-raised', 'description' => 'Activistes et militants'],
    ['nom' => 'Philanthrope', 'slug' => 'philanthrope', 'icon' => 'hand-holding-heart', 'description' => 'Philanthropes'],
    
    // Autres
    ['nom' => 'Célébrité', 'slug' => 'celebrite', 'icon' => 'star', 'description' => 'Célébrités diverses'],
    ['nom' => 'Expert', 'slug' => 'expert', 'icon' => 'user-tie', 'description' => 'Experts et consultants'],
    ['nom' => 'Designer', 'slug' => 'designer', 'icon' => 'drafting-compass', 'description' => 'Designers et créatifs']
];

// Catégories par type
$categoriesByType = [
    'artiste' => [
        'Peinture', 'Sculpture', 'Photographie', 'Art Numérique', 'Street Art',
        'Art Contemporain', 'Art Abstrait', 'Installation', 'Performance', 'Illustration'
    ],
    'musicien' => [
        'Pop', 'Rock', 'Jazz', 'Classique', 'Hip-Hop', 'Électro', 'R&B',
        'Rap', 'Reggae', 'Blues', 'Metal', 'Folk', 'Soul', 'Country', 'Indie'
    ],
    'acteur' => [
        'Cinéma', 'Théâtre', 'Série TV', 'Comédie', 'Drame', 'Action',
        'Doublage', 'Stand-up', 'Improvisation', 'One-man-show'
    ],
    'ecrivain' => [
        'Roman', 'Poésie', 'Essai', 'Science-Fiction', 'Fantasy', 'Thriller',
        'Policier', 'Biographie', 'Jeunesse', 'BD', 'Manga', 'Nouvelle', 'Autobiographie'
    ],
    'realisateur' => [
        'Cinéma', 'Documentaire', 'Court-métrage', 'Série', 'Clip Musical',
        'Publicité', 'Web-série', 'Animation'
    ],
    'influenceur' => [
        'Mode', 'Beauté', 'Lifestyle', 'Voyage', 'Food', 'Sport', 'Gaming',
        'Tech', 'Finance', 'Parentalité', 'Déco', 'DIY', 'Fitness', 'Bien-être'
    ],
    'journaliste' => [
        'Politique', 'Économie', 'Sport', 'Culture', 'Investigation', 'International',
        'Société', 'Environnement', 'Tech', 'Science', 'People'
    ],
    'animateur' => [
        'Télévision', 'Radio', 'Podcast', 'Talk-show', 'Divertissement',
        'Information', 'Débat', 'Jeu', 'Émission Culturelle'
    ],
    'createur-contenu' => [
        'YouTube', 'TikTok', 'Instagram', 'Twitch', 'Podcast', 'Blog',
        'Vlog', 'Tutorial', 'Review', 'Gaming', 'ASMR'
    ],
    'entrepreneur' => [
        'Startup Tech', 'E-commerce', 'SaaS', 'Fintech', 'Foodtech', 'Healthtech',
        'Edtech', 'Greentech', 'Retail', 'Service', 'Industrie', 'Consulting'
    ],
    'chef-entreprise' => [
        'PME', 'ETI', 'Grande Entreprise', 'Multinationale', 'Familiale',
        'Cotée', 'Scale-up', 'Holding'
    ],
    'investisseur' => [
        'Venture Capital', 'Business Angel', 'Private Equity', 'Immobilier',
        'Bourse', 'Crypto', 'Art', 'Startup', 'Impact'
    ],
    'conferencier' => [
        'Business', 'Motivation', 'Leadership', 'Innovation', 'Marketing',
        'Vente', 'Management', 'Développement Personnel', 'Transformation Digitale',
        'Entrepreneuriat', 'Créativité', 'Futurisme'
    ],
    'formateur' => [
        'Management', 'Commercial', 'Marketing', 'Digital', 'Soft Skills',
        'Leadership', 'Communication', 'Coaching', 'Développement Personnel', 'Technique'
    ],
    'chercheur' => [
        'Physique', 'Chimie', 'Biologie', 'Médecine', 'Informatique', 'Mathématiques',
        'Astronomie', 'Neurosciences', 'Écologie', 'Sociologie', 'Psychologie', 'Histoire'
    ],
    'professeur' => [
        'Université', 'Grande École', 'Lycée', 'Collège', 'Primaire',
        'Formation Continue', 'En ligne', 'Recherche'
    ],
    'sportif' => [
        'Football', 'Tennis', 'Basketball', 'Rugby', 'Athlétisme', 'Natation',
        'Cyclisme', 'Boxe', 'MMA', 'Golf', 'Ski', 'Formule 1', 'Handball', 'Volleyball',
        'Judo', 'Escalade', 'Surf', 'Équitation', 'Esport'
    ],
    'entraineur' => [
        'Football', 'Tennis', 'Basketball', 'Fitness', 'Préparation Physique',
        'Nutrition Sportive', 'Mental', 'Collectif', 'Individuel'
    ],
    'politique' => [
        'Président', 'Ministre', 'Député', 'Sénateur', 'Maire', 'Conseiller',
        'Diplomate', 'Européen', 'International', 'Local'
    ],
    'activiste' => [
        'Environnement', 'Droits Humains', 'Féminisme', 'LGBTQ+', 'Antiracisme',
        'Paix', 'Éducation', 'Santé', 'Pauvreté', 'Animal'
    ],
    'philanthrope' => [
        'Éducation', 'Santé', 'Environnement', 'Culture', 'Recherche',
        'Humanitaire', 'Social', 'Art', 'Sport'
    ],
    'celebrite' => [
        'Télé-réalité', 'People', 'Mondain', 'Héritier', 'Famille Royale',
        'Scandale', 'Buzz', 'Viral'
    ],
    'expert' => [
        'Juridique', 'Financier', 'Immobilier', 'Marketing', 'RH', 'IT',
        'Stratégie', 'Communication', 'Cybersécurité', 'Data', 'IA', 'Blockchain'
    ],
    'designer' => [
        'Graphique', 'Produit', 'Mode', 'Intérieur', 'UX/UI', 'Web',
        'Industriel', 'Textile', 'Mobilier', 'Architecture'
    ]
];

// Insertion des types
echo "Insertion des types...\n";
$typeIds = [];
foreach ($types as $index => $type) {
    $stmt = $pdo->prepare("INSERT INTO personnalite_types (nom, slug, icon, description, ordre) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$type['nom'], $type['slug'], $type['icon'], $type['description'], $index]);
    $typeIds[$type['slug']] = $pdo->lastInsertId();
    echo "  ✓ {$type['nom']}\n";
}

echo "\nTotal types: " . count($types) . "\n\n";

// Insertion des catégories
echo "Insertion des catégories...\n";
$totalCategories = 0;
foreach ($categoriesByType as $typeSlug => $categories) {
    if (isset($typeIds[$typeSlug])) {
        $typeId = $typeIds[$typeSlug];
        foreach ($categories as $index => $categoryName) {
            $categorySlug = strtolower(str_replace(
                [' ', 'é', 'è', 'ê', 'à', 'ô', 'û', 'ç', 'ï', '/', '-', '\'', '+'],
                ['_', 'e', 'e', 'e', 'a', 'o', 'u', 'c', 'i', '_', '_', '', ''],
                $categoryName
            ));
            $stmt = $pdo->prepare("INSERT INTO personnalite_categories (type_id, nom, slug, ordre) VALUES (?, ?, ?, ?)");
            $stmt->execute([$typeId, $categoryName, $categorySlug, $index]);
            $totalCategories++;
        }
        echo "  ✓ Type '{$typeSlug}': " . count($categories) . " catégories\n";
    }
}

echo "\nTotal catégories: $totalCategories\n";
echo "\n=== Enrichissement terminé ! ===\n";
