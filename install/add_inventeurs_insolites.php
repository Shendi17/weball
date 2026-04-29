<?php
/**
 * Ajout du type Inventeur et enrichissement Personnalité Atypique
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout Inventeurs et Personnalités Insolites ===\n\n";

// 1. Ajouter le type Inventeur
echo "Ajout du type Inventeur...\n";
$stmt = $pdo->prepare("INSERT INTO personnalite_types (nom, slug, icon, description, ordre) VALUES (?, ?, ?, ?, ?)");
$stmt->execute(['Inventeur', 'inventeur', 'lightbulb', 'Inventeurs et innovateurs', 104]);
$inventeurTypeId = $pdo->lastInsertId();
echo "✓ Type Inventeur créé (ID: $inventeurTypeId)\n\n";

// 2. Catégories pour Inventeur
$inventeurCategories = [
    'Électricité & Énergie', 'Mécanique & Automobile', 'Aéronautique', 
    'Télécommunications', 'Informatique', 'Médecine', 'Chimie', 
    'Optique', 'Robotique', 'Innovation Domestique', 'Physique',
    'Agriculture', 'Textile', 'Imprimerie'
];

echo "Ajout des catégories Inventeur...\n";
foreach ($inventeurCategories as $index => $catName) {
    $slug = strtolower(str_replace(
        [' ', '&', 'é', 'è', 'ê', 'à', 'ô', 'û', 'ç', 'ï'],
        ['_', 'et', 'e', 'e', 'e', 'a', 'o', 'u', 'c', 'i'],
        $catName
    ));
    $stmt = $pdo->prepare("INSERT INTO personnalite_categories (type_id, nom, slug, ordre) VALUES (?, ?, ?, ?)");
    $stmt->execute([$inventeurTypeId, $catName, $slug, $index]);
    echo "  ✓ $catName\n";
}

// 3. Ajouter des catégories insolites au type Atypique existant
echo "\nAjout de catégories insolites au type Atypique...\n";
$atypique = $pdo->query("SELECT id FROM personnalite_types WHERE slug = 'atypique'")->fetch();
$atypiqueTypeId = $atypique['id'];

$insoliteCategories = [
    'Insolite', 'Phénomène Viral', 'Record du Monde', 'Artiste de Rue',
    'Collectionneur', 'Ermite', 'Nomade Digital', 'Cascadeur'
];

foreach ($insoliteCategories as $index => $catName) {
    $slug = strtolower(str_replace(' ', '_', $catName));
    $stmt = $pdo->prepare("INSERT INTO personnalite_categories (type_id, nom, slug, ordre) VALUES (?, ?, ?, ?)");
    $stmt->execute([$atypiqueTypeId, $catName, $slug, 100 + $index]);
    echo "  ✓ $catName\n";
}

echo "\n=== Types et catégories ajoutés ! ===\n";
