<?php
require_once '../config.php';

try {
    // Lecture du fichier SQL
    $sql = file_get_contents(__DIR__ . '/create_tables.sql');
    
    // Séparation des requêtes
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    // Exécution de chaque requête
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
            echo "Requête exécutée avec succès : " . substr($query, 0, 50) . "...\n";
        }
    }
    
    echo "\nToutes les tables ont été créées avec succès !\n";
    
} catch (PDOException $e) {
    echo "Erreur lors de la création des tables : " . $e->getMessage() . "\n";
}
