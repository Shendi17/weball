<?php
/**
 * Enrichissement de la structure de la table personnalite
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Enrichissement de la structure Personnalité ===\n\n";

// Ajouter les nouvelles colonnes
$columns = [
    "date_deces DATE NULL COMMENT 'Date de décès'",
    "lieu_naissance VARCHAR(255) NULL COMMENT 'Lieu de naissance'",
    "lieu_deces VARCHAR(255) NULL COMMENT 'Lieu de décès'",
    "cause_deces VARCHAR(255) NULL COMMENT 'Cause du décès'",
    "profession VARCHAR(255) NULL COMMENT 'Profession principale'",
    "distinctions TEXT NULL COMMENT 'Prix, médailles, distinctions'",
    "oeuvres_principales TEXT NULL COMMENT 'Œuvres majeures (JSON)'",
    "citations TEXT NULL COMMENT 'Citations célèbres (JSON)'",
    "site_web VARCHAR(255) NULL COMMENT 'Site web officiel'",
    "wikipedia_url VARCHAR(255) NULL COMMENT 'Lien Wikipedia'",
    "reseaux_sociaux TEXT NULL COMMENT 'Réseaux sociaux (JSON)'",
    "sources TEXT NULL COMMENT 'Sources et références (JSON)'",
    "anecdotes TEXT NULL COMMENT 'Anecdotes et faits marquants'",
    "influence TEXT NULL COMMENT 'Influence et héritage'",
    "statut ENUM('vivant', 'decede', 'legendaire', 'inconnu') DEFAULT 'vivant' COMMENT 'Statut vital'"
];

echo "Ajout des colonnes...\n";
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

echo "\n=== Structure enrichie ! ===\n";
