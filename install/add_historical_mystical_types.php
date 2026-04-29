<?php
/**
 * Ajout des types : Personnalité Historique, Mystique, Héros/Légende, Militaire
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout des Types Historiques, Mystiques, Légendaires et Militaires ===\n\n";

// Nouveaux types
$newTypes = [
    [
        'nom' => 'Personnalité Historique',
        'slug' => 'historique',
        'icon' => 'landmark',
        'description' => 'Figures historiques et souverains',
        'ordre' => 105
    ],
    [
        'nom' => 'Personnalité Mystique',
        'slug' => 'mystique',
        'icon' => 'magic',
        'description' => 'Figures spirituelles et ésotériques',
        'ordre' => 106
    ],
    [
        'nom' => 'Héros/Légende',
        'slug' => 'heros',
        'icon' => 'shield-alt',
        'description' => 'Héros mythologiques et légendaires',
        'ordre' => 107
    ],
    [
        'nom' => 'Militaire',
        'slug' => 'militaire',
        'icon' => 'medal',
        'description' => 'Stratèges et militaires',
        'ordre' => 108
    ]
];

// Catégories par type
$categoriesByType = [
    'historique' => [
        'Pharaon', 'Empereur', 'Roi', 'Reine', 'Conquérant', 
        'Révolutionnaire', 'Explorateur Historique', 'Personnalité Antique',
        'Sultan', 'Tsar', 'Calife', 'Shogun', 'Mandarin'
    ],
    'mystique' => [
        'Druide', 'Mage', 'Sorcier', 'Alchimiste', 'Prophète', 
        'Saint', 'Sainte', 'Mystique', 'Chaman', 'Oracle',
        'Kabbaliste', 'Astrologue', 'Médium', 'Guérisseur'
    ],
    'heros' => [
        'Héros Mythologique', 'Légende Médiévale', 'Samouraï', 
        'Chevalier', 'Pirate', 'Ninja', 'Viking', 'Gladiateur',
        'Corsaire', 'Mousquetaire', 'Croisé', 'Templier'
    ],
    'militaire' => [
        'Général', 'Amiral', 'Stratège', 'Résistant', 'Espion',
        'Commandant', 'Maréchal', 'Colonel', 'Pilote de Guerre',
        'Sniper', 'Forces Spéciales', 'Partisan'
    ]
];

// Insertion des types
echo "Insertion des types...\n";
$typeIds = [];
foreach ($newTypes as $type) {
    $stmt = $pdo->prepare("INSERT INTO personnalite_types (nom, slug, icon, description, ordre) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$type['nom'], $type['slug'], $type['icon'], $type['description'], $type['ordre']]);
    $typeIds[$type['slug']] = $pdo->lastInsertId();
    echo "  ✓ {$type['nom']} (ID: {$typeIds[$type['slug']]})\n";
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
echo "\n=== Types et catégories ajoutés ! ===\n";
