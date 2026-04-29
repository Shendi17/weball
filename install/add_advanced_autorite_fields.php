<?php
/**
 * Ajout de champs avancés pour les autorités
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout champs avancés autorités ===\n\n";

$columns = [
    'latitude' => 'DECIMAL(10, 8) NULL',
    'longitude' => 'DECIMAL(11, 8) NULL',
    'horaires' => 'TEXT NULL',
    'fax' => 'VARCHAR(50) NULL',
    'code_postal' => 'VARCHAR(20) NULL',
    'ville' => 'VARCHAR(255) NULL',
    'region' => 'VARCHAR(255) NULL',
    'contact_presse' => 'VARCHAR(255) NULL',
    'email_presse' => 'VARCHAR(255) NULL',
    'telephone_presse' => 'VARCHAR(50) NULL',
    'videos' => 'TEXT NULL COMMENT "JSON array of video URLs"',
    'galerie_images' => 'TEXT NULL COMMENT "JSON array of image URLs"',
    'documents' => 'TEXT NULL COMMENT "JSON array of documents"',
    'liens_externes' => 'TEXT NULL COMMENT "JSON array of external links"',
    'certifications' => 'TEXT NULL',
    'distinctions' => 'TEXT NULL',
    'historique' => 'TEXT NULL',
    'valeurs' => 'TEXT NULL',
    'engagement_rse' => 'TEXT NULL',
    'chiffres_cles' => 'TEXT NULL COMMENT "JSON array of key figures"',
    'filiales' => 'TEXT NULL',
    'actionnaires' => 'TEXT NULL',
    'comite_direction' => 'TEXT NULL COMMENT "JSON array of executives"',
    'conseil_administration' => 'TEXT NULL COMMENT "JSON array of board members"',
    'offres_emploi_url' => 'VARCHAR(500) NULL',
    'newsletter_url' => 'VARCHAR(500) NULL',
    'blog_url' => 'VARCHAR(500) NULL',
    'wikipedia_url' => 'VARCHAR(500) NULL',
    'linkedin_url' => 'VARCHAR(500) NULL',
    'twitter_url' => 'VARCHAR(500) NULL',
    'facebook_url' => 'VARCHAR(500) NULL',
    'instagram_url' => 'VARCHAR(500) NULL',
    'youtube_url' => 'VARCHAR(500) NULL',
    'siret' => 'VARCHAR(50) NULL',
    'tva_intracommunautaire' => 'VARCHAR(50) NULL',
    'forme_juridique' => 'VARCHAR(100) NULL',
    'capital_social' => 'VARCHAR(100) NULL',
    'bourse_cotation' => 'VARCHAR(255) NULL',
    'code_bourse' => 'VARCHAR(50) NULL'
];

foreach ($columns as $column => $type) {
    try {
        $pdo->exec("ALTER TABLE autorite ADD COLUMN $column $type");
        echo "✓ Colonne $column ajoutée\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠ Colonne $column existe déjà\n";
        } else {
            echo "✗ Erreur $column: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Terminé ! ===\n";
echo "Total colonnes: " . count($columns) . "\n";
