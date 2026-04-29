<?php
/**
 * Script pour vérifier que toutes les pages utilisent bien le template
 */

$pages = [
    'cadran', 'journal', 'ecran', 'personnalite', 'autorite', 'entite', 'localite',
    'instrument', 'office', 'banque', 'reseau', 'publication', 'archive',
    'media', 'formation', 'annonce', 'catalogue', 'boutique', 'adhesion', 'projet',
    'carriere', 'discipline', 'concours', 'campagne'
];

echo "=== Vérification des templates ===\n\n";

$ok = 0;
$nok = 0;

foreach ($pages as $page) {
    $controllerFile = __DIR__ . "/../pages/{$page}.php";
    $templateFile = __DIR__ . "/../templates/pages/{$page}.php";
    
    if (!file_exists($controllerFile)) {
        echo "✗ {$page}: Contrôleur manquant\n";
        $nok++;
        continue;
    }
    
    if (!file_exists($templateFile)) {
        echo "✗ {$page}: Template manquant\n";
        $nok++;
        continue;
    }
    
    $controllerContent = file_get_contents($controllerFile);
    
    // Vérifier que le contrôleur utilise bien le template
    if (strpos($controllerContent, "include __DIR__ . '/../templates/layout/main.php'") !== false) {
        echo "✓ {$page}: Template appliqué\n";
        $ok++;
    } else {
        echo "⚠ {$page}: Template non appliqué\n";
        $nok++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ OK: $ok\n";
echo "✗ Problèmes: $nok\n";
