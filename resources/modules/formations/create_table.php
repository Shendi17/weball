<?php
require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../includes/functions.php';
require_once dirname(__FILE__) . '/../../includes/db.php';

try {
    // Vérifier si la table existe
    $tableExists = $pdo->query("SHOW TABLES LIKE 'formations_formations'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Créer la table si elle n'existe pas
        $sql = "CREATE TABLE formations_formations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            description TEXT,
            niveau VARCHAR(50) DEFAULT 'intermediaire',
            duree VARCHAR(50) DEFAULT '40 heures',
            date_debut DATE,
            date_fin DATE,
            categorie VARCHAR(50) DEFAULT 'development',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "Table formations_formations créée avec succès.\n";
    } else {
        // Vérifier et ajouter les colonnes manquantes
        $columns = [
            'niveau' => "ALTER TABLE formations_formations ADD COLUMN niveau VARCHAR(50) DEFAULT 'intermediaire' AFTER description",
            'duree' => "ALTER TABLE formations_formations ADD COLUMN duree VARCHAR(50) DEFAULT '40 heures' AFTER niveau",
            'date_debut' => "ALTER TABLE formations_formations ADD COLUMN date_debut DATE AFTER duree",
            'date_fin' => "ALTER TABLE formations_formations ADD COLUMN date_fin DATE AFTER date_debut",
            'categorie' => "ALTER TABLE formations_formations ADD COLUMN categorie VARCHAR(50) DEFAULT 'development' AFTER date_fin",
            'created_at' => "ALTER TABLE formations_formations ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            'updated_at' => "ALTER TABLE formations_formations ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ];
        
        // Récupérer les colonnes existantes
        $existingColumns = [];
        $columnsQuery = $pdo->query("SHOW COLUMNS FROM formations_formations");
        while ($column = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
            $existingColumns[] = $column['Field'];
        }
        
        // Ajouter les colonnes manquantes
        foreach ($columns as $columnName => $alterQuery) {
            if (!in_array($columnName, $existingColumns)) {
                try {
                    $pdo->exec($alterQuery);
                    echo "Colonne $columnName ajoutée avec succès.\n";
                } catch (PDOException $e) {
                    echo "Erreur lors de l'ajout de la colonne $columnName : " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Structure de la table vérifiée et mise à jour avec succès.\n";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
