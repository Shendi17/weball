<?php
/**
 * Remplissage des types et catégories pour tous les modules
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Remplissage des Types & Catégories ===\n\n";

// Définition des types et catégories par module
$data = [
    'personnalite' => [
        'types' => [
            ['nom' => 'Artiste', 'slug' => 'artiste', 'icon' => 'palette'],
            ['nom' => 'Influenceur', 'slug' => 'influenceur', 'icon' => 'users'],
            ['nom' => 'Conférencier', 'slug' => 'conferencier', 'icon' => 'microphone'],
            ['nom' => 'Entrepreneur', 'slug' => 'entrepreneur', 'icon' => 'briefcase'],
            ['nom' => 'Célébrité', 'slug' => 'celebrite', 'icon' => 'star']
        ],
        'categories' => [
            'artiste' => ['Musique', 'Peinture', 'Sculpture', 'Cinéma', 'Théâtre'],
            'influenceur' => ['Sport', 'Voyage', 'Mode', 'Tech', 'Lifestyle'],
            'conferencier' => ['Business', 'Motivation', 'Science', 'Technologie', 'Santé'],
            'entrepreneur' => ['Startup', 'PME', 'Grande Entreprise', 'Social', 'Tech'],
            'celebrite' => ['Acteur', 'Chanteur', 'Sportif', 'Politique', 'Médias']
        ]
    ],
    'autorite' => [
        'types' => [
            ['nom' => 'Gouvernementale', 'slug' => 'gouvernementale', 'icon' => 'landmark'],
            ['nom' => 'Judiciaire', 'slug' => 'judiciaire', 'icon' => 'gavel'],
            ['nom' => 'Administrative', 'slug' => 'administrative', 'icon' => 'building'],
            ['nom' => 'Régionale', 'slug' => 'regionale', 'icon' => 'map-marked-alt']
        ],
        'categories' => [
            'gouvernementale' => ['Ministère', 'Agence', 'Commission', 'Conseil'],
            'judiciaire' => ['Tribunal', 'Cour', 'Parquet', 'Juridiction'],
            'administrative' => ['Préfecture', 'Mairie', 'Département', 'Région'],
            'regionale' => ['Locale', 'Départementale', 'Régionale', 'Nationale']
        ]
    ],
    'entite' => [
        'types' => [
            ['nom' => 'Entreprise', 'slug' => 'entreprise', 'icon' => 'building'],
            ['nom' => 'Association', 'slug' => 'association', 'icon' => 'hands-helping'],
            ['nom' => 'Institution', 'slug' => 'institution', 'icon' => 'university'],
            ['nom' => 'Organisation', 'slug' => 'organisation', 'icon' => 'sitemap']
        ],
        'categories' => [
            'entreprise' => ['PME', 'Grande Entreprise', 'Startup', 'Multinationale'],
            'association' => ['Caritative', 'Culturelle', 'Sportive', 'Éducative'],
            'institution' => ['Publique', 'Privée', 'Internationale', 'Nationale'],
            'organisation' => ['ONG', 'Syndicat', 'Fédération', 'Réseau']
        ]
    ],
    'formation' => [
        'types' => [
            ['nom' => 'Diplômante', 'slug' => 'diplomante', 'icon' => 'graduation-cap'],
            ['nom' => 'Certifiante', 'slug' => 'certifiante', 'icon' => 'certificate'],
            ['nom' => 'Professionnelle', 'slug' => 'professionnelle', 'icon' => 'briefcase'],
            ['nom' => 'En ligne', 'slug' => 'en-ligne', 'icon' => 'laptop']
        ],
        'categories' => [
            'diplomante' => ['Licence', 'Master', 'Doctorat', 'DUT', 'BTS'],
            'certifiante' => ['Certification Pro', 'Titre RNCP', 'Badge', 'Attestation'],
            'professionnelle' => ['Management', 'Technique', 'Commercial', 'RH'],
            'en-ligne' => ['MOOC', 'E-learning', 'Webinaire', 'Tutoriel']
        ]
    ],
    'publication' => [
        'types' => [
            ['nom' => 'Article', 'slug' => 'article', 'icon' => 'newspaper'],
            ['nom' => 'Livre', 'slug' => 'livre', 'icon' => 'book'],
            ['nom' => 'Étude', 'slug' => 'etude', 'icon' => 'flask'],
            ['nom' => 'Rapport', 'slug' => 'rapport', 'icon' => 'file-alt']
        ],
        'categories' => [
            'article' => ['Actualité', 'Opinion', 'Analyse', 'Interview', 'Reportage'],
            'livre' => ['Roman', 'Essai', 'Manuel', 'Guide', 'Biographie'],
            'etude' => ['Scientifique', 'Économique', 'Sociale', 'Technique'],
            'rapport' => ['Annuel', 'Financier', 'Activité', 'Recherche']
        ]
    ],
    'projet' => [
        'types' => [
            ['nom' => 'Innovation', 'slug' => 'innovation', 'icon' => 'lightbulb'],
            ['nom' => 'Infrastructure', 'slug' => 'infrastructure', 'icon' => 'road'],
            ['nom' => 'Social', 'slug' => 'social', 'icon' => 'heart'],
            ['nom' => 'Environnemental', 'slug' => 'environnemental', 'icon' => 'leaf']
        ],
        'categories' => [
            'innovation' => ['Tech', 'Digital', 'R&D', 'Startup', 'Produit'],
            'infrastructure' => ['Transport', 'Énergie', 'Bâtiment', 'Réseau'],
            'social' => ['Éducation', 'Santé', 'Logement', 'Emploi'],
            'environnemental' => ['Écologie', 'Énergie Verte', 'Recyclage', 'Biodiversité']
        ]
    ],
    'annonce' => [
        'types' => [
            ['nom' => 'Emploi', 'slug' => 'emploi', 'icon' => 'briefcase'],
            ['nom' => 'Immobilier', 'slug' => 'immobilier', 'icon' => 'home'],
            ['nom' => 'Service', 'slug' => 'service', 'icon' => 'concierge-bell'],
            ['nom' => 'Vente', 'slug' => 'vente', 'icon' => 'shopping-cart']
        ],
        'categories' => [
            'emploi' => ['CDI', 'CDD', 'Stage', 'Freelance', 'Alternance'],
            'immobilier' => ['Vente', 'Location', 'Colocation', 'Bureau'],
            'service' => ['Conseil', 'Réparation', 'Cours', 'Assistance'],
            'vente' => ['Neuf', 'Occasion', 'Artisanat', 'Collection']
        ]
    ],
    'marche' => [
        'types' => [
            ['nom' => 'Financier', 'slug' => 'financier', 'icon' => 'chart-line'],
            ['nom' => 'Immobilier', 'slug' => 'immobilier', 'icon' => 'building'],
            ['nom' => 'Technologique', 'slug' => 'technologique', 'icon' => 'microchip'],
            ['nom' => 'Consommation', 'slug' => 'consommation', 'icon' => 'shopping-bag']
        ],
        'categories' => [
            'financier' => ['Bourse', 'Forex', 'Crypto', 'Obligations', 'Matières'],
            'immobilier' => ['Résidentiel', 'Commercial', 'Industriel', 'Terrain'],
            'technologique' => ['Software', 'Hardware', 'IA', 'Cloud', 'IoT'],
            'consommation' => ['Alimentaire', 'Mode', 'Électronique', 'Automobile']
        ]
    ]
];

// Modules avec types génériques
$genericModules = [
    'annuaire', 'localite', 'instrument', 'office', 'plateforme', 
    'banque', 'reseau', 'archive', 'ecole', 'media', 
    'catalogue', 'boutique', 'adhesion', 'carriere', 
    'discipline', 'concours', 'campagne'
];

foreach ($genericModules as $module) {
    $data[$module] = [
        'types' => [
            ['nom' => 'Standard', 'slug' => 'standard', 'icon' => 'circle'],
            ['nom' => 'Premium', 'slug' => 'premium', 'icon' => 'star'],
            ['nom' => 'Professionnel', 'slug' => 'professionnel', 'icon' => 'briefcase']
        ],
        'categories' => [
            'standard' => ['Général', 'Basique', 'Courant'],
            'premium' => ['Avancé', 'Exclusif', 'VIP'],
            'professionnel' => ['Business', 'Entreprise', 'Corporate']
        ]
    ];
}

// Insertion des données
foreach ($data as $module => $moduleData) {
    echo "--- Module: $module ---\n";
    
    $typesTable = "{$module}_types";
    $categoriesTable = "{$module}_categories";
    
    // Insérer les types
    foreach ($moduleData['types'] as $index => $type) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO $typesTable (nom, slug, icon, ordre) VALUES (?, ?, ?, ?)");
        $stmt->execute([$type['nom'], $type['slug'], $type['icon'], $index]);
    }
    
    echo "  Types insérés: " . count($moduleData['types']) . "\n";
    
    // Récupérer les IDs des types (slug => id)
    $types = $pdo->query("SELECT slug, id FROM $typesTable")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "  Types récupérés: " . count($types) . "\n";
    if (count($types) > 0) {
        echo "  Slugs disponibles: " . implode(', ', array_keys($types)) . "\n";
    }
    
    // Insérer les catégories
    $categoriesCount = 0;
    foreach ($moduleData['categories'] as $typeSlug => $categories) {
        echo "  Traitement type: $typeSlug\n";
        if (isset($types[$typeSlug])) {
            $typeId = $types[$typeSlug];
            echo "    Type ID: $typeId\n";
            foreach ($categories as $index => $categoryName) {
                $categorySlug = strtolower(str_replace([' ', 'é', 'è', 'ê', 'à', 'ô', 'û', 'ç'], ['_', 'e', 'e', 'e', 'a', 'o', 'u', 'c'], $categoryName));
                $stmt = $pdo->prepare("INSERT IGNORE INTO $categoriesTable (type_id, nom, slug, ordre) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$typeId, $categoryName, $categorySlug, $index]);
                if ($result) {
                    $categoriesCount++;
                    echo "      ✓ Catégorie: $categoryName\n";
                } else {
                    echo "      ✗ Erreur: $categoryName\n";
                }
            }
        } else {
            echo "    ✗ Type slug '$typeSlug' non trouvé!\n";
        }
    }
    
    echo "  ✓ Total catégories insérées: $categoriesCount\n\n";
}

echo "=== Données insérées avec succès ! ===\n";
