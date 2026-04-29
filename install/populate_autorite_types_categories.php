<?php
/**
 * Population des types et catégories pour Autorité
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Population Types et Catégories Autorité ===\n\n";

// Types d'autorités avec leurs catégories
$typesData = [
    [
        'nom' => 'Organisme Public',
        'slug' => 'organisme_public',
        'icon' => 'landmark',
        'description' => 'Organismes et institutions publiques',
        'ordre' => 1,
        'categories' => [
            'Ministère', 'Administration Centrale', 'Préfecture', 'Mairie',
            'Conseil Régional', 'Conseil Départemental', 'Agence Publique',
            'Établissement Public', 'Service Public', 'Autorité Administrative Indépendante'
        ]
    ],
    [
        'nom' => 'Association',
        'slug' => 'association',
        'icon' => 'users',
        'description' => 'Associations loi 1901 et organisations à but non lucratif',
        'ordre' => 2,
        'categories' => [
            'Association Caritative', 'Association Culturelle', 'Association Sportive',
            'Association Éducative', 'Association Environnementale', 'Association Humanitaire',
            'Association Professionnelle', 'Association de Consommateurs', 'ONG',
            'Fondation', 'Syndicat', 'Fédération'
        ]
    ],
    [
        'nom' => 'Société Commerciale',
        'slug' => 'societe_commerciale',
        'icon' => 'briefcase',
        'description' => 'Entreprises et sociétés commerciales',
        'ordre' => 3,
        'categories' => [
            'SA (Société Anonyme)', 'SAS (Société par Actions Simplifiée)', 
            'SARL', 'SNC (Société en Nom Collectif)', 'SCA (Société en Commandite par Actions)',
            'Entreprise Individuelle', 'Auto-Entrepreneur', 'Micro-Entreprise',
            'EURL', 'SASU', 'Holding', 'Filiale'
        ]
    ],
    [
        'nom' => 'Grande Entreprise',
        'slug' => 'grande_entreprise',
        'icon' => 'building',
        'description' => 'Multinationales et grands groupes',
        'ordre' => 4,
        'categories' => [
            'CAC 40', 'Fortune 500', 'Multinationale', 'Groupe International',
            'Leader Mondial', 'Licorne (Startup)', 'ETI (Entreprise de Taille Intermédiaire)',
            'PME', 'TPE', 'Startup', 'Scale-up'
        ]
    ],
    [
        'nom' => 'Institution Financière',
        'slug' => 'institution_financiere',
        'icon' => 'university',
        'description' => 'Banques, assurances et institutions financières',
        'ordre' => 5,
        'categories' => [
            'Banque Centrale', 'Banque Commerciale', 'Banque d\'Investissement',
            'Banque en Ligne', 'Néobanque', 'Compagnie d\'Assurance',
            'Mutuelle', 'Fonds d\'Investissement', 'Société de Gestion',
            'Bourse', 'Organisme de Crédit', 'Fintech'
        ]
    ],
    [
        'nom' => 'Établissement d\'Enseignement',
        'slug' => 'etablissement_enseignement',
        'icon' => 'graduation-cap',
        'description' => 'Écoles, universités et centres de formation',
        'ordre' => 6,
        'categories' => [
            'Université', 'Grande École', 'École d\'Ingénieurs', 'École de Commerce',
            'Lycée', 'Collège', 'École Primaire', 'Maternelle',
            'Centre de Formation', 'École Privée', 'Institut de Recherche',
            'Business School', 'École d\'Art', 'Conservatoire'
        ]
    ],
    [
        'nom' => 'Établissement de Santé',
        'slug' => 'etablissement_sante',
        'icon' => 'hospital',
        'description' => 'Hôpitaux, cliniques et centres de santé',
        'ordre' => 7,
        'categories' => [
            'Hôpital Public', 'CHU (Centre Hospitalier Universitaire)', 'Clinique Privée',
            'Centre de Santé', 'Maison de Santé', 'EHPAD', 'Laboratoire Médical',
            'Centre de Rééducation', 'Pharmacie', 'Cabinet Médical',
            'Centre de Radiologie', 'Urgences'
        ]
    ],
    [
        'nom' => 'Média & Communication',
        'slug' => 'media_communication',
        'icon' => 'broadcast-tower',
        'description' => 'Médias, presse et agences de communication',
        'ordre' => 8,
        'categories' => [
            'Chaîne de Télévision', 'Radio', 'Journal', 'Magazine',
            'Agence de Presse', 'Média en Ligne', 'Réseau Social',
            'Agence de Communication', 'Agence de Publicité', 'Maison d\'Édition',
            'Plateforme Streaming', 'Podcast'
        ]
    ],
    [
        'nom' => 'Organisation Internationale',
        'slug' => 'organisation_internationale',
        'icon' => 'globe',
        'description' => 'Organisations et institutions internationales',
        'ordre' => 9,
        'categories' => [
            'ONU', 'Union Européenne', 'OTAN', 'UNESCO', 'OMS',
            'FMI', 'Banque Mondiale', 'OMC', 'OCDE', 'Interpol',
            'Croix-Rouge', 'Amnesty International', 'Greenpeace'
        ]
    ],
    [
        'nom' => 'Institution Religieuse',
        'slug' => 'institution_religieuse',
        'icon' => 'church',
        'description' => 'Institutions et organisations religieuses',
        'ordre' => 10,
        'categories' => [
            'Église', 'Mosquée', 'Synagogue', 'Temple', 'Monastère',
            'Diocèse', 'Paroisse', 'Congrégation', 'Ordre Religieux',
            'Association Cultuelle', 'Centre Spirituel'
        ]
    ],
    [
        'nom' => 'Organisation Culturelle',
        'slug' => 'organisation_culturelle',
        'icon' => 'theater-masks',
        'description' => 'Musées, théâtres et centres culturels',
        'ordre' => 11,
        'categories' => [
            'Musée', 'Théâtre', 'Opéra', 'Cinéma', 'Galerie d\'Art',
            'Centre Culturel', 'Bibliothèque', 'Médiathèque', 'Salle de Concert',
            'Festival', 'Compagnie de Théâtre', 'Orchestre'
        ]
    ],
    [
        'nom' => 'Organisation Sportive',
        'slug' => 'organisation_sportive',
        'icon' => 'futbol',
        'description' => 'Clubs, fédérations et organisations sportives',
        'ordre' => 12,
        'categories' => [
            'Club de Football', 'Club de Basketball', 'Club de Rugby',
            'Club de Tennis', 'Fédération Sportive', 'Comité Olympique',
            'Ligue Professionnelle', 'Salle de Sport', 'Centre d\'Entraînement',
            'Stade', 'Complexe Sportif'
        ]
    ],
    [
        'nom' => 'Organisation Environnementale',
        'slug' => 'organisation_environnementale',
        'icon' => 'leaf',
        'description' => 'Organisations de protection de l\'environnement',
        'ordre' => 13,
        'categories' => [
            'ONG Environnementale', 'Association Écologique', 'Parc National',
            'Réserve Naturelle', 'Centre de Recyclage', 'Agence Environnementale',
            'Observatoire Climatique', 'Fondation Écologique'
        ]
    ],
    [
        'nom' => 'Organisation Judiciaire',
        'slug' => 'organisation_judiciaire',
        'icon' => 'gavel',
        'description' => 'Tribunaux et institutions judiciaires',
        'ordre' => 14,
        'categories' => [
            'Cour Suprême', 'Cour d\'Appel', 'Tribunal de Grande Instance',
            'Tribunal de Commerce', 'Tribunal Administratif', 'Conseil d\'État',
            'Cour des Comptes', 'Parquet', 'Cabinet d\'Avocat', 'Étude de Notaire'
        ]
    ],
    [
        'nom' => 'Organisation Scientifique',
        'slug' => 'organisation_scientifique',
        'icon' => 'flask',
        'description' => 'Centres de recherche et organisations scientifiques',
        'ordre' => 15,
        'categories' => [
            'CNRS', 'INSERM', 'CEA', 'INRIA', 'Laboratoire de Recherche',
            'Centre de Recherche', 'Observatoire', 'Institut Scientifique',
            'Académie des Sciences', 'Société Savante'
        ]
    ]
];

// Insertion des types et catégories
$totalTypes = 0;
$totalCategories = 0;

foreach ($typesData as $typeData) {
    // Insérer le type
    $stmt = $pdo->prepare("
        INSERT INTO autorite_types (nom, slug, icon, description, ordre)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $typeData['nom'],
        $typeData['slug'],
        $typeData['icon'],
        $typeData['description'],
        $typeData['ordre']
    ]);
    $typeId = $pdo->lastInsertId();
    $totalTypes++;
    
    echo "✓ Type créé: {$typeData['nom']} (ID: $typeId)\n";
    
    // Insérer les catégories
    foreach ($typeData['categories'] as $index => $categoryName) {
        $categorySlug = strtolower(str_replace(
            [' ', '\'', '(', ')', 'é', 'è', 'ê', 'à', 'ô', 'û', 'ç', 'ï'],
            ['_', '', '', '', 'e', 'e', 'e', 'a', 'o', 'u', 'c', 'i'],
            $categoryName
        ));
        
        $stmt = $pdo->prepare("
            INSERT INTO autorite_categories (type_id, nom, slug, ordre)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$typeId, $categoryName, $categorySlug, $index]);
        $totalCategories++;
    }
    
    echo "  → " . count($typeData['categories']) . " catégories créées\n\n";
}

echo "=== Résumé ===\n";
echo "✓ Types créés: $totalTypes\n";
echo "✓ Catégories créées: $totalCategories\n";
echo "\n=== Population terminée ! ===\n";
