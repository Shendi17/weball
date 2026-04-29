<?php
/**
 * Ajout des photos pour les personnalités restantes
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout photos personnalités restantes ===\n\n";

// Photos pour les personnalités manquantes
$photosById = [
    // Personnalités de test (photos génériques ou vraies)
    1 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Profile_avatar_placeholder_large.png/200px-Profile_avatar_placeholder_large.png',
    2 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Profile_avatar_placeholder_large.png/200px-Profile_avatar_placeholder_large.png',
    3 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Profile_avatar_placeholder_large.png/200px-Profile_avatar_placeholder_large.png',
    4 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Profile_avatar_placeholder_large.png/200px-Profile_avatar_placeholder_large.png',
    
    // Vraies personnalités
    13 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Michel_Houellebecq_2008_b.jpg/220px-Michel_Houellebecq_2008_b.jpg',
    18 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/Roger_Federer_%2826163412282%29_%28cropped%29.jpg/220px-Roger_Federer_%2826163412282%29_%28cropped%29.jpg',
    19 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Usain_Bolt_Olympics_cropped.jpg/220px-Usain_Bolt_Olympics_cropped.jpg',
    21 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Nabilla_Benattia_Cannes_2014.jpg/220px-Nabilla_Benattia_Cannes_2014.jpg',
    22 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Profile_avatar_placeholder_large.png/200px-Profile_avatar_placeholder_large.png',
    24 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Quentin_Tarantino_by_Gage_Skidmore.jpg/220px-Quentin_Tarantino_by_Gage_Skidmore.jpg',
    26 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/President_Barack_Obama.jpg/220px-President_Barack_Obama.jpg',
    27 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Tony_Robbins_2009.jpg/220px-Tony_Robbins_2009.jpg',
    28 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Simon_Sinek_2011.jpg/220px-Simon_Sinek_2011.jpg',
    29 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Stephen_Hawking.StarChild.jpg/220px-Stephen_Hawking.StarChild.jpg',
    31 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Philippe_Starck_2012.jpg/220px-Philippe_Starck_2012.jpg'
];

$updated = 0;
$failed = 0;

foreach ($photosById as $id => $url) {
    $stmt = $pdo->prepare("UPDATE personnalite SET photo = ? WHERE id = ?");
    
    if ($stmt->execute([$url, $id])) {
        // Récupérer le nom pour affichage
        $name = $pdo->query("SELECT CONCAT(prenom, ' ', nom) as fullname FROM personnalite WHERE id = $id")->fetchColumn();
        echo "✓ ID $id - $name\n";
        $updated++;
    } else {
        echo "✗ ID $id - Échec\n";
        $failed++;
    }
}

echo "\n=== RÉSUMÉ ===\n";
echo "✓ Photos ajoutées: $updated\n";
echo "✗ Échecs: $failed\n";
echo "\n=== Terminé ! ===\n";
