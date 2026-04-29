<?php
/**
 * Ajout de personnalités réelles avec données authentiques
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout de Personnalités Réelles ===\n\n";

// Récupérer les types et catégories
$typesData = $pdo->query("SELECT id, nom, slug FROM personnalite_types")->fetchAll(PDO::FETCH_ASSOC);
$types = [];
foreach ($typesData as $type) {
    $types[strtolower($type['nom'])] = $type['id'];
    $types[$type['slug']] = $type['id'];
}

$categories = $pdo->query("SELECT id, slug, type_id FROM personnalite_categories")->fetchAll(PDO::FETCH_ASSOC);

// Fonction helper pour trouver une catégorie
function getCategoryId($categorySlug, $typeSlug, $categories, $types) {
    $typeId = $types[$typeSlug] ?? null;
    if (!$typeId) return null;
    
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug && $cat['type_id'] == $typeId) {
            return $cat['id'];
        }
    }
    return null;
}

// Personnalités réelles
$personnalites = [
    // Musiciens
    [
        'nom' => 'Beyoncé',
        'prenom' => 'Beyoncé',
        'type' => 'Musicien',
        'category' => 'pop',
        'description' => 'Chanteuse, auteure-compositrice et actrice américaine. Icône de la musique pop et R&B.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1981-09-04'
    ],
    [
        'nom' => 'Daft Punk',
        'prenom' => 'Thomas & Guy-Manuel',
        'type' => 'musicien',
        'category' => 'electro',
        'description' => 'Duo français de musique électronique, pionniers de la French Touch.',
        'nationalite' => 'Française',
        'date_naissance' => '1993-01-01'
    ],
    [
        'nom' => 'Eminem',
        'prenom' => 'Marshall',
        'type' => 'musicien',
        'category' => 'rap',
        'description' => 'Rappeur, producteur et acteur américain. L\'un des artistes les plus vendus de tous les temps.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1972-10-17'
    ],
    
    // Acteurs
    [
        'nom' => 'DiCaprio',
        'prenom' => 'Leonardo',
        'type' => 'acteur',
        'category' => 'cinema',
        'description' => 'Acteur et producteur américain. Oscar du meilleur acteur pour The Revenant.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1974-11-11'
    ],
    [
        'nom' => 'Cotillard',
        'prenom' => 'Marion',
        'type' => 'acteur',
        'category' => 'cinema',
        'description' => 'Actrice française. Oscar de la meilleure actrice pour La Môme.',
        'nationalite' => 'Française',
        'date_naissance' => '1975-09-30'
    ],
    [
        'nom' => 'Gad Elmaleh',
        'prenom' => 'Gad',
        'type' => 'acteur',
        'category' => 'comedie',
        'description' => 'Humoriste et acteur franco-marocain. Star du stand-up en France.',
        'nationalite' => 'Française',
        'date_naissance' => '1971-04-19'
    ],
    
    // Écrivains
    [
        'nom' => 'Rowling',
        'prenom' => 'J.K.',
        'type' => 'ecrivain',
        'category' => 'fantasy',
        'description' => 'Auteure britannique, créatrice de la saga Harry Potter.',
        'nationalite' => 'Britannique',
        'date_naissance' => '1965-07-31'
    ],
    [
        'nom' => 'Musso',
        'prenom' => 'Guillaume',
        'type' => 'ecrivain',
        'category' => 'roman',
        'description' => 'Écrivain français, auteur de nombreux best-sellers.',
        'nationalite' => 'Française',
        'date_naissance' => '1974-06-06'
    ],
    [
        'nom' => 'Houellebecq',
        'prenom' => 'Michel',
        'type' => 'ecrivain',
        'category' => 'roman',
        'description' => 'Écrivain, poète et essayiste français. Prix Goncourt 2010.',
        'nationalite' => 'Française',
        'date_naissance' => '1956-02-26'
    ],
    
    // Entrepreneurs
    [
        'nom' => 'Musk',
        'prenom' => 'Elon',
        'type' => 'entrepreneur',
        'category' => 'startup_tech',
        'description' => 'Entrepreneur et ingénieur. PDG de Tesla, SpaceX et X (Twitter).',
        'nationalite' => 'Américaine',
        'date_naissance' => '1971-06-28'
    ],
    [
        'nom' => 'Niel',
        'prenom' => 'Xavier',
        'type' => 'entrepreneur',
        'category' => 'startup_tech',
        'description' => 'Entrepreneur français, fondateur de Free et investisseur.',
        'nationalite' => 'Française',
        'date_naissance' => '1967-08-25'
    ],
    [
        'nom' => 'Bezos',
        'prenom' => 'Jeff',
        'type' => 'entrepreneur',
        'category' => 'e_commerce',
        'description' => 'Fondateur d\'Amazon, pionnier du e-commerce mondial.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1964-01-12'
    ],
    
    // Sportifs
    [
        'nom' => 'Mbappé',
        'prenom' => 'Kylian',
        'type' => 'sportif',
        'category' => 'football',
        'description' => 'Footballeur français. Champion du monde 2018. Attaquant du Real Madrid.',
        'nationalite' => 'Française',
        'date_naissance' => '1998-12-20'
    ],
    [
        'nom' => 'Federer',
        'prenom' => 'Roger',
        'type' => 'sportif',
        'category' => 'tennis',
        'description' => 'Joueur de tennis suisse. 20 titres du Grand Chelem.',
        'nationalite' => 'Suisse',
        'date_naissance' => '1981-08-08'
    ],
    [
        'nom' => 'Bolt',
        'prenom' => 'Usain',
        'type' => 'sportif',
        'category' => 'athletisme',
        'description' => 'Sprinteur jamaïcain. Recordman du monde du 100m et 200m.',
        'nationalite' => 'Jamaïcaine',
        'date_naissance' => '1986-08-21'
    ],
    
    // Influenceurs
    [
        'nom' => 'Squeezie',
        'prenom' => 'Lucas',
        'type' => 'influenceur',
        'category' => 'gaming',
        'description' => 'YouTubeur français gaming et divertissement. Plus de 18M d\'abonnés.',
        'nationalite' => 'Française',
        'date_naissance' => '1996-01-27'
    ],
    [
        'nom' => 'Nabilla',
        'prenom' => 'Nabilla',
        'type' => 'influenceur',
        'category' => 'lifestyle',
        'description' => 'Personnalité TV et influenceuse française.',
        'nationalite' => 'Française',
        'date_naissance' => '1992-02-05'
    ],
    [
        'nom' => 'Enjoy Phoenix',
        'prenom' => 'Marie',
        'type' => 'influenceur',
        'category' => 'beaute',
        'description' => 'YouTubeuse beauté et lifestyle française.',
        'nationalite' => 'Française',
        'date_naissance' => '1995-01-09'
    ],
    
    // Réalisateurs
    [
        'nom' => 'Nolan',
        'prenom' => 'Christopher',
        'type' => 'realisateur',
        'category' => 'cinema',
        'description' => 'Réalisateur britanno-américain. Inception, Interstellar, Oppenheimer.',
        'nationalite' => 'Britannique',
        'date_naissance' => '1970-07-30'
    ],
    [
        'nom' => 'Tarantino',
        'prenom' => 'Quentin',
        'type' => 'realisateur',
        'category' => 'cinema',
        'description' => 'Réalisateur américain. Pulp Fiction, Kill Bill, Django Unchained.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1963-03-27'
    ],
    
    // Politiques
    [
        'nom' => 'Macron',
        'prenom' => 'Emmanuel',
        'type' => 'politique',
        'category' => 'president',
        'description' => 'Président de la République française depuis 2017.',
        'nationalite' => 'Française',
        'date_naissance' => '1977-12-21'
    ],
    [
        'nom' => 'Obama',
        'prenom' => 'Barack',
        'type' => 'politique',
        'category' => 'president',
        'description' => '44e président des États-Unis (2009-2017). Prix Nobel de la Paix.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1961-08-04'
    ],
    
    // Conférenciers
    [
        'nom' => 'Robbins',
        'prenom' => 'Tony',
        'type' => 'conferencier',
        'category' => 'motivation',
        'description' => 'Coach et conférencier américain en développement personnel.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1960-02-29'
    ],
    [
        'nom' => 'Sinek',
        'prenom' => 'Simon',
        'type' => 'conferencier',
        'category' => 'leadership',
        'description' => 'Auteur et conférencier britannique. Théorie du "Start With Why".',
        'nationalite' => 'Britannique',
        'date_naissance' => '1973-10-09'
    ],
    
    // Chercheurs
    [
        'nom' => 'Hawking',
        'prenom' => 'Stephen',
        'type' => 'chercheur',
        'category' => 'physique',
        'description' => 'Physicien théoricien britannique. Spécialiste des trous noirs.',
        'nationalite' => 'Britannique',
        'date_naissance' => '1942-01-08'
    ],
    [
        'nom' => 'Curie',
        'prenom' => 'Marie',
        'type' => 'chercheur',
        'category' => 'physique',
        'description' => 'Physicienne et chimiste française. Double prix Nobel.',
        'nationalite' => 'Française',
        'date_naissance' => '1867-11-07'
    ],
    
    // Designers
    [
        'nom' => 'Starck',
        'prenom' => 'Philippe',
        'type' => 'designer',
        'category' => 'produit',
        'description' => 'Designer français. Créateur d\'objets iconiques et d\'architecture.',
        'nationalite' => 'Française',
        'date_naissance' => '1949-01-18'
    ],
    [
        'nom' => 'Jobs',
        'prenom' => 'Steve',
        'type' => 'designer',
        'category' => 'produit',
        'description' => 'Co-fondateur d\'Apple. Visionnaire du design et de la technologie.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1955-02-24'
    ]
];

// Insertion des personnalités
$inserted = 0;
$errors = 0;

foreach ($personnalites as $perso) {
    try {
        $typeLower = strtolower($perso['type']);
        $typeId = $types[$typeLower] ?? $types[$perso['type']] ?? null;
        $categoryId = getCategoryId($perso['category'], $perso['type'], $categories, $types);
        
        if (!$typeId) {
            echo "✗ {$perso['prenom']} {$perso['nom']} - Type '{$perso['type']}' non trouvé\n";
            $errors++;
            continue;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO personnalite (nom, prenom, type_id, category_id, biographie, nationalite, date_naissance, date_creation, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1)
        ");
        
        $stmt->execute([
            $perso['nom'],
            $perso['prenom'] ?? '',
            $typeId,
            $categoryId,
            $perso['description'] ?? '',
            $perso['nationalite'] ?? '',
            $perso['date_naissance'] ?? null
        ]);
        
        $inserted++;
        echo "✓ {$perso['prenom']} {$perso['nom']} - {$perso['type']}\n";
        
    } catch (PDOException $e) {
        echo "✗ {$perso['prenom']} {$perso['nom']} - Erreur: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Personnalités insérées: $inserted\n";
echo "✗ Erreurs: $errors\n";
echo "\n=== Terminé ! ===\n";
