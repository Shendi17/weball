<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    // Lire le contenu du fichier SQL
    $sql = file_get_contents(__DIR__ . '/../sql/init_tables.sql');

    // Diviser le fichier en requêtes individuelles
    $queries = array_filter(array_map('trim', explode(';', $sql)));

    // Exécuter chaque requête
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }

    echo "Les tables ont été créées avec succès et les données de démonstration ont été insérées.";

} catch (PDOException $e) {
    die("Erreur lors de l'initialisation de la base de données : " . $e->getMessage());
}
?>
