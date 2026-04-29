<?php
/**
 * Téléchargement de toutes les images (logos autorités + photos personnalités)
 * et mise à jour des URLs vers les fichiers locaux
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Téléchargement des images ===\n\n";

// Créer les dossiers nécessaires
$logosDir = __DIR__ . '/../public/logos';
$photosDir = __DIR__ . '/../public/photos';

if (!is_dir($logosDir)) {
    mkdir($logosDir, 0755, true);
    echo "✓ Dossier logos créé\n";
}

if (!is_dir($photosDir)) {
    mkdir($photosDir, 0755, true);
    echo "✓ Dossier photos créé\n";
}

echo "\n";

/**
 * Fonction pour télécharger une image
 */
function downloadImage($url, $destination) {
    if (empty($url)) {
        return false;
    }
    
    // Initialiser cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && $imageData) {
        file_put_contents($destination, $imageData);
        return true;
    }
    
    return false;
}

/**
 * Fonction pour générer un nom de fichier sécurisé
 */
function sanitizeFilename($name) {
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]+/', '-', $name);
    $name = trim($name, '-');
    return $name;
}

// 1. Télécharger les logos des autorités
echo "=== LOGOS DES AUTORITÉS ===\n";
$autorites = $pdo->query("SELECT id, nom, logo FROM autorite WHERE logo IS NOT NULL AND logo != ''")->fetchAll(PDO::FETCH_ASSOC);

$logosDownloaded = 0;
$logosFailed = 0;

foreach ($autorites as $autorite) {
    $filename = sanitizeFilename($autorite['nom']);
    
    // Déterminer l'extension
    $extension = 'png';
    if (strpos($autorite['logo'], '.svg') !== false) {
        $extension = 'svg';
    } elseif (strpos($autorite['logo'], '.jpg') !== false || strpos($autorite['logo'], '.jpeg') !== false) {
        $extension = 'jpg';
    }
    
    $localPath = $logosDir . '/' . $filename . '.' . $extension;
    $localUrl = '/logos/' . $filename . '.' . $extension;
    
    // Vérifier si le fichier existe déjà
    if (file_exists($localPath)) {
        echo "⚠ Existe déjà: {$autorite['nom']}\n";
        // Mettre à jour l'URL même si le fichier existe
        $pdo->prepare("UPDATE autorite SET logo = ? WHERE id = ?")->execute([$localUrl, $autorite['id']]);
        $logosDownloaded++;
        continue;
    }
    
    echo "Téléchargement: {$autorite['nom']}... ";
    
    if (downloadImage($autorite['logo'], $localPath)) {
        // Mettre à jour l'URL dans la base de données
        $pdo->prepare("UPDATE autorite SET logo = ? WHERE id = ?")->execute([$localUrl, $autorite['id']]);
        echo "✓\n";
        $logosDownloaded++;
    } else {
        echo "✗ Échec\n";
        $logosFailed++;
    }
    
    // Pause pour éviter de surcharger les serveurs
    usleep(500000); // 0.5 seconde
}

echo "\n";

// 2. Télécharger les photos des personnalités
echo "=== PHOTOS DES PERSONNALITÉS ===\n";
$personnalites = $pdo->query("SELECT id, nom, prenom, photo FROM personnalite WHERE photo IS NOT NULL AND photo != ''")->fetchAll(PDO::FETCH_ASSOC);

$photosDownloaded = 0;
$photosFailed = 0;

foreach ($personnalites as $perso) {
    $fullName = trim(($perso['prenom'] ?? '') . ' ' . ($perso['nom'] ?? ''));
    $filename = sanitizeFilename($fullName);
    
    // Déterminer l'extension
    $extension = 'jpg';
    if (strpos($perso['photo'], '.png') !== false) {
        $extension = 'png';
    } elseif (strpos($perso['photo'], '.webp') !== false) {
        $extension = 'webp';
    }
    
    $localPath = $photosDir . '/' . $filename . '.' . $extension;
    $localUrl = '/photos/' . $filename . '.' . $extension;
    
    // Vérifier si le fichier existe déjà
    if (file_exists($localPath)) {
        echo "⚠ Existe déjà: $fullName\n";
        // Mettre à jour l'URL même si le fichier existe
        $pdo->prepare("UPDATE personnalite SET photo = ? WHERE id = ?")->execute([$localUrl, $perso['id']]);
        $photosDownloaded++;
        continue;
    }
    
    echo "Téléchargement: $fullName... ";
    
    if (downloadImage($perso['photo'], $localPath)) {
        // Mettre à jour l'URL dans la base de données
        $pdo->prepare("UPDATE personnalite SET photo = ? WHERE id = ?")->execute([$localUrl, $perso['id']]);
        echo "✓\n";
        $photosDownloaded++;
    } else {
        echo "✗ Échec\n";
        $photosFailed++;
    }
    
    // Pause pour éviter de surcharger les serveurs
    usleep(500000); // 0.5 seconde
}

echo "\n=== RÉSUMÉ ===\n";
echo "Logos autorités:\n";
echo "  ✓ Téléchargés: $logosDownloaded\n";
echo "  ✗ Échecs: $logosFailed\n";
echo "\nPhotos personnalités:\n";
echo "  ✓ Téléchargées: $photosDownloaded\n";
echo "  ✗ Échecs: $photosFailed\n";
echo "\n=== Terminé ! ===\n";
