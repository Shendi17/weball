<?php
/**
 * Ajout des nouveaux types de personnalités : Philosophe, Yogi, Atypique, Penseur
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout des Nouveaux Types de Personnalités ===\n\n";

// Nouveaux types
$newTypes = [
    [
        'nom' => 'Philosophe',
        'slug' => 'philosophe',
        'icon' => 'brain',
        'description' => 'Philosophes et penseurs',
        'ordre' => 100
    ],
    [
        'nom' => 'Yogi',
        'slug' => 'yogi',
        'icon' => 'om',
        'description' => 'Maîtres spirituels et yogis',
        'ordre' => 101
    ],
    [
        'nom' => 'Personnalité Atypique',
        'slug' => 'atypique',
        'icon' => 'star-of-life',
        'description' => 'Personnalités aux parcours exceptionnels',
        'ordre' => 102
    ],
    [
        'nom' => 'Penseur',
        'slug' => 'penseur',
        'icon' => 'book-reader',
        'description' => 'Intellectuels et penseurs',
        'ordre' => 103
    ]
];

// Catégories par type
$categoriesByType = [
    'philosophe' => [
        'Philosophie Antique', 'Philosophie Moderne', 'Existentialisme', 'Stoïcisme',
        'Phénoménologie', 'Éthique', 'Métaphysique', 'Épistémologie',
        'Philosophie Politique', 'Philosophie des Sciences', 'Logique', 'Esthétique'
    ],
    'yogi' => [
        'Yoga', 'Méditation', 'Bouddhisme', 'Hindouisme', 'Soufisme',
        'Zen', 'Tantra', 'Kundalini', 'Qi Gong', 'Chamanisme',
        'Taoïsme', 'Pleine Conscience', 'Reiki', 'Ayurveda'
    ],
    'atypique' => [
        'Aventurier', 'Explorateur', 'Survivant', 'Prodige', 'Génie Autodidacte',
        'Visionnaire', 'Excentrique', 'Pionnier', 'Révolutionnaire', 'Héros Anonyme',
        'Lanceur d\'Alerte', 'Résistant', 'Innovateur Social'
    ],
    'penseur' => [
        'Essayiste', 'Théoricien', 'Critique', 'Humaniste', 'Moraliste',
        'Sociologue', 'Anthropologue', 'Psychanalyste', 'Historien',
        'Économiste', 'Politologue', 'Théologien'
    ]
];

// Insertion des types
echo "Insertion des types...\n";
$typeIds = [];
foreach ($newTypes as $type) {
    $stmt = $pdo->prepare("INSERT INTO personnalite_types (nom, slug, icon, description, ordre) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$type['nom'], $type['slug'], $type['icon'], $type['description'], $type['ordre']]);
    $typeIds[$type['slug']] = $pdo->lastInsertId();
    echo "  ✓ {$type['nom']}\n";
}

echo "\nTotal nouveaux types: " . count($newTypes) . "\n\n";

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
echo "\n=== Nouveaux types ajoutés ! ===\n";
