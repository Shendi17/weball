<?php
require_once '../config.php';

try {
    // Lecture du fichier SQL
    $sql = file_get_contents('registre.sql');
    
    // Séparation des requêtes (en supposant qu'elles sont séparées par des points-virgules)
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    // Exécution de chaque requête
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }
    
    echo "Base de données importée avec succès !";
} catch (PDOException $e) {
    die("Erreur lors de l'importation : " . $e->getMessage());
}
