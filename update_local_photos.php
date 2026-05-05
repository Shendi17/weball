<?php
require_once 'config.php';
require_once 'includes/db.php';

$pdo = getPDO();

echo "=== Mise à jour des photos vers les fichiers locaux ===\n\n";

// Mapping des personnalités avec leurs photos locales
$photoMapping = [
    'Alan Turing' => '/photos/alan-turing.jpg',
    'Alexander Graham Bell' => '/photos/alexander-graham-bell.jpg',
    'Christopher Nolan' => '/photos/christopher-nolan.jpg',
    'Cléopâtre' => '/photos/cl-op-tre-cl-op-tre-vii.jpg',
    'Diogène' => '/photos/diog-ne-diog-ne-de-sinope.jpg',
    'Elon Musk' => '/photos/elon-musk.jpg',
    'Friedrich Nietzsche' => '/photos/friedrich-nietzsche.jpg',
    'Gengis Khan' => '/photos/gengis-gengis-khan.jpg',
    'J.K. Rowling' => '/photos/j-k-rowling.jpg',
    'Jeanne d\'Arc' => '/photos/jeanne-jeanne-d-arc.jpg',
    'Johannes Gutenberg' => '/photos/johannes-gutenberg.jpg',
    'Leonardo DiCaprio' => '/photos/leonardo-dicaprio.jpg',
    'Nikola Tesla' => '/photos/nikola-tesla.jpg',
    'Socrate' => '/photos/socrate-socrate.jpg',
    'Thomas Edison' => '/photos/thomas-edison.jpg',
];

$updated = 0;

foreach ($photoMapping as $name => $photoPath) {
    // Chercher la personnalité par nom (prenom ou nom)
    $stmt = $pdo->prepare("
        SELECT id, prenom, nom 
        FROM personnalite 
        WHERE CONCAT(prenom, ' ', nom) LIKE ? 
           OR nom LIKE ?
           OR prenom LIKE ?
        LIMIT 1
    ");
    
    $searchPattern = "%{$name}%";
    $stmt->execute([$searchPattern, $searchPattern, $searchPattern]);
    $person = $stmt->fetch();
    
    if ($person) {
        // Vérifier que le fichier existe
        $fullPath = __DIR__ . '/public' . $photoPath;
        if (file_exists($fullPath)) {
            $updateStmt = $pdo->prepare("UPDATE personnalite SET photo = ? WHERE id = ?");
            $updateStmt->execute([$photoPath, $person['id']]);
            echo "✓ ID {$person['id']}: {$person['prenom']} {$person['nom']} -> {$photoPath}\n";
            $updated++;
        } else {
            echo "✗ Fichier non trouvé: {$fullPath}\n";
        }
    } else {
        echo "⚠ Personnalité non trouvée: {$name}\n";
    }
}

echo "\n=== Résumé ===\n";
echo "Photos mises à jour: {$updated}\n\n";

// Afficher quelques exemples
echo "Vérification des résultats:\n";
$stmt = $pdo->query("SELECT id, prenom, nom, photo FROM personnalite WHERE photo LIKE '/photos/%' LIMIT 5");
while ($r = $stmt->fetch()) {
    echo "  - {$r['prenom']} {$r['nom']}: {$r['photo']}\n";
}
