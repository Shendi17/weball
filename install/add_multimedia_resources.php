<?php
/**
 * Ajout de la structure pour les ressources multimédias
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout des Ressources Multimédias ===\n\n";

// Ajouter les nouvelles colonnes pour les ressources
$columns = [
    "galerie_photos TEXT NULL COMMENT 'Galerie de photos (JSON)'",
    "videos TEXT NULL COMMENT 'Vidéos YouTube/Vimeo (JSON)'",
    "documentaires TEXT NULL COMMENT 'Documentaires et films (JSON)'",
    "livres TEXT NULL COMMENT 'Livres et biographies (JSON)'",
    "podcasts TEXT NULL COMMENT 'Podcasts et interviews (JSON)'",
    "articles TEXT NULL COMMENT 'Articles de presse (JSON)'",
    "produits_associes TEXT NULL COMMENT 'Produits, marques, entreprises (JSON)'",
    "lieux_associes TEXT NULL COMMENT 'Lieux importants (JSON)'",
    "evenements TEXT NULL COMMENT 'Événements marquants (JSON)'",
    "collaborations TEXT NULL COMMENT 'Collaborations et partenariats (JSON)'",
    "prix_recompenses TEXT NULL COMMENT 'Prix et récompenses détaillés (JSON)'",
    "controverses TEXT NULL COMMENT 'Controverses et polémiques (JSON)'",
    "heritage_culturel TEXT NULL COMMENT 'Héritage culturel (JSON)'",
    "musees_expositions TEXT NULL COMMENT 'Musées et expositions (JSON)'",
    "fondations TEXT NULL COMMENT 'Fondations et organisations créées (JSON)'",
    "timeline TEXT NULL COMMENT 'Chronologie détaillée (JSON)'"
];

echo "Ajout des colonnes multimédias...\n";
foreach ($columns as $column) {
    try {
        $pdo->exec("ALTER TABLE personnalite ADD COLUMN $column");
        $colName = explode(' ', $column)[0];
        echo "  ✓ Colonne ajoutée: $colName\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $colName = explode(' ', $column)[0];
            echo "  ⚠ Colonne existe déjà: $colName\n";
        } else {
            echo "  ✗ Erreur: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Structure enrichie avec ressources multimédias ! ===\n";
