<?php
/**
 * Vérification de toutes les images
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== VÉRIFICATION DES IMAGES ===\n\n";

// 1. Vérifier les logos des autorités
echo "=== LOGOS AUTORITÉS ===\n";
$totalAutorites = $pdo->query("SELECT COUNT(*) FROM autorite")->fetchColumn();
$withLogo = $pdo->query("SELECT COUNT(*) FROM autorite WHERE logo IS NOT NULL AND logo != ''")->fetchColumn();
$withoutLogo = $totalAutorites - $withLogo;

echo "Total autorités: $totalAutorites\n";
echo "✓ Avec logo: $withLogo (" . round(($withLogo/$totalAutorites)*100, 1) . "%)\n";
echo "✗ Sans logo: $withoutLogo\n";

if ($withoutLogo > 0) {
    echo "\nAutorités sans logo:\n";
    $missing = $pdo->query("SELECT id, nom FROM autorite WHERE logo IS NULL OR logo = ''")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($missing as $item) {
        echo "  - ID {$item['id']}: {$item['nom']}\n";
    }
}

echo "\n";

// 2. Vérifier les photos des personnalités
echo "=== PHOTOS PERSONNALITÉS ===\n";
$totalPersonnalites = $pdo->query("SELECT COUNT(*) FROM personnalite")->fetchColumn();
$withPhoto = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE photo IS NOT NULL AND photo != ''")->fetchColumn();
$withoutPhoto = $totalPersonnalites - $withPhoto;

echo "Total personnalités: $totalPersonnalites\n";
echo "✓ Avec photo: $withPhoto (" . round(($withPhoto/$totalPersonnalites)*100, 1) . "%)\n";
echo "✗ Sans photo: $withoutPhoto\n";

if ($withoutPhoto > 0) {
    echo "\nPersonnalités sans photo:\n";
    $missing = $pdo->query("SELECT id, prenom, nom FROM personnalite WHERE photo IS NULL OR photo = ''")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($missing as $item) {
        echo "  - ID {$item['id']}: " . trim($item['prenom'] . ' ' . $item['nom']) . "\n";
    }
}

echo "\n=== RÉSUMÉ GLOBAL ===\n";
$totalImages = $withLogo + $withPhoto;
$totalItems = $totalAutorites + $totalPersonnalites;
$percentage = round(($totalImages/$totalItems)*100, 1);

echo "Total fiches: $totalItems\n";
echo "✓ Total images: $totalImages ($percentage%)\n";
echo "✗ Total manquantes: " . ($totalItems - $totalImages) . "\n";

if ($percentage >= 90) {
    echo "\n🎉 Excellent ! La plupart des images sont présentes.\n";
} elseif ($percentage >= 70) {
    echo "\n👍 Bien ! La majorité des images sont présentes.\n";
} else {
    echo "\n⚠️ Attention : Beaucoup d'images manquantes.\n";
}

echo "\n=== Terminé ! ===\n";
