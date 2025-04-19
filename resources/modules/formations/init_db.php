<?php
require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../includes/functions.php';
require_once dirname(__FILE__) . '/../../includes/db.php';

try {
    // Lire et exécuter le fichier create_table.sql
    $sql = file_get_contents(__DIR__ . '/create_table.sql');
    $pdo->exec($sql);
    echo "Table formations_formations créée avec succès.\n";

    // Vérifier si la table est vide
    $stmt = $pdo->query("SELECT COUNT(*) FROM formations_formations");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Lire et exécuter le fichier sample_data.sql
        $sql = file_get_contents(__DIR__ . '/sample_data.sql');
        $pdo->exec($sql);
        echo "Données d'exemple insérées avec succès.\n";
    } else {
        echo "La table contient déjà des données.\n";
    }

    echo "Initialisation de la base de données terminée.\n";
} catch (PDOException $e) {
    die("Erreur lors de l'initialisation de la base de données : " . $e->getMessage());
}
?>
