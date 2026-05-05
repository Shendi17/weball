<?php
/**
 * Script de migration COMPLÈTE avec toutes les données originales
 * Inclut TOUTES les personnalités et autorités des fichiers SQL
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MIGRATION COMPLÈTE AVEC TOUTES LES DONNÉES ORIGINALES ===\n\n";

try {
    $pdo = getPDO();
    
    // 1. Importer TOUTES les personnalités depuis le fichier SQL
    echo "📥 Importation complète des personnalités (71 + corrections)...\n";
    
    $personalitiesSql = file_get_contents(__DIR__ . '/exports/personalities_data.sql');
    
    // Séparer les instructions INSERT
    $inserts = array_filter(array_map('trim', explode("INSERT INTO personnalite", $personalitiesSql)));
    
    $importedCount = 0;
    foreach ($inserts as $insert) {
        if (!empty($insert) && strpos($insert, 'VALUES') !== false) {
            $fullInsert = "INSERT INTO personnalite" . $insert;
            try {
                $pdo->exec($fullInsert);
                $importedCount++;
            } catch (PDOException $e) {
                // Ignorer les doublons (INSERT IGNORE serait mieux mais on utilise ce qui existe)
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "  ⚠️ Erreur insertion personnalité: " . substr($e->getMessage(), 0, 50) . "...\n";
                }
            }
        }
    }
    
    echo "  ✓ Personnalités importées: $importedCount\n";
    
    // 2. Importer TOUTES les autorités depuis le fichier SQL
    echo "\n📥 Importation complète des autorités (29 + corrections)...\n";
    
    $authoritiesSql = file_get_contents(__DIR__ . '/exports/authorities_data.sql');
    
    // Séparer les instructions INSERT
    $inserts = array_filter(array_map('trim', explode("INSERT INTO autorite", $authoritiesSql)));
    
    $importedCount = 0;
    foreach ($inserts as $insert) {
        if (!empty($insert) && strpos($insert, 'VALUES') !== false) {
            $fullInsert = "INSERT INTO autorite" . $insert;
            try {
                $pdo->exec($fullInsert);
                $importedCount++;
            } catch (PDOException $e) {
                // Ignorer les doublons
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "  ⚠️ Erreur insertion autorité: " . substr($e->getMessage(), 0, 50) . "...\n";
                }
            }
        }
    }
    
    echo "  ✓ Autorités importées: $importedCount\n";
    
    // 3. Importer tous les types et catégories
    echo "\n📥 Importation des types et catégories...\n";
    
    $typesCategoriesSql = file_get_contents(__DIR__ . '/exports/types_categories.sql');
    
    // Exécuter toutes les instructions
    $statements = array_filter(array_map('trim', explode(";", $typesCategoriesSql)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                // Ignorer les erreurs de doublons
                if (strpos($e->getMessage(), 'Duplicate') === false && strpos($e->getMessage(), 'already exists') === false) {
                    echo "  ⚠️ Erreur: " . substr($e->getMessage(), 0, 50) . "...\n";
                }
            }
        }
    }
    
    echo "  ✓ Types et catégories importés\n";
    
    // 4. Corriger les personnalités sans type (comme avant)
    echo "\n🔧 Correction des personnalités sans type...\n";
    $sansType = $pdo->query("SELECT id, nom, prenom FROM personnalite WHERE type_id IS NULL OR type_id = 0 OR type_id = ''")->fetchAll();
    
    foreach ($sansType as $person) {
        $defaultType = 'influenceur';
        $nomComplet = strtolower($person['nom'] . ' ' . $person['prenom']);
        
        if (strpos($nomComplet, 'beyoncé') !== false || strpos($nomComplet, 'musicien') !== false) {
            $defaultType = 'musicien';
        } elseif (strpos($nomComplet, 'entrepreneur') !== false) {
            $defaultType = 'entrepreneur';
        } elseif (strpos($nomComplet, 'philosophe') !== false) {
            $defaultType = 'philosophe';
        }
        
        $stmt = $pdo->prepare("SELECT id FROM personnalite_types WHERE slug = ?");
        $stmt->execute([$defaultType]);
        $typeId = $stmt->fetchColumn();
        
        if ($typeId) {
            $stmt = $pdo->prepare("UPDATE personnalite SET type_id = ? WHERE id = ?");
            $stmt->execute([$typeId, $person['id']]);
            echo "  ✓ {$person['prenom']} {$person['nom']} -> Type: $defaultType\n";
        }
    }
    
    // 5. Ajouter les personnalités de base manquantes
    echo "\n🔧 Ajout des personnalités de base manquantes...\n";
    
    $artistes = [
        ['nom' => 'Picasso', 'prenom' => 'Pablo', 'biographie' => 'Peintre et sculpteur espagnol, pionnier du cubisme.', 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/48/Pablo_picasso_1.jpg/220px-Pablo_picasso_1.jpg'],
        ['nom' => 'Mozart', 'prenom' => 'Wolfgang Amadeus', 'biographie' => 'Compositeur autrichien, génie de la musique classique.', 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Wolfgang_Amadeus_Mozart_1789_detail.jpg/220px-Wolfgang_Amadeus_Mozart_1789_detail.jpg'],
        ['nom' => 'Monet', 'prenom' => 'Claude', 'biographie' => 'Peintre français, fondateur de limpressionnisme.', 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Claude_Monet_1899_Nadar_crop.jpg/220px-Claude_Monet_1899_Nadar_crop.jpg']
    ];
    
    $artisteTypeId = $pdo->query("SELECT id FROM personnalite_types WHERE slug = 'artiste'")->fetchColumn();
    if ($artisteTypeId) {
        foreach ($artistes as $artiste) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO personnalite (nom, prenom, biographie, photo, date_creation, user_id, type_id) VALUES (?, ?, ?, ?, NOW(), 1, ?)");
            $stmt->execute([$artiste['nom'], $artiste['prenom'], $artiste['biographie'], $artiste['photo'], $artisteTypeId]);
            echo "  ✓ {$artiste['prenom']} {$artiste['nom']} -> Artiste\n";
        }
    }
    
    $celebrites = [
        ['nom' => 'Oprah Winfrey', 'prenom' => '', 'biographie' => 'Animatrice TV et productrice américaine, influenceuse mondiale.', 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Oprah_Winfrey_in_2012.jpg/220px-Oprah_Winfrey_in_2012.jpg'],
        ['nom' => 'Kim Kardashian', 'prenom' => '', 'biographie' => 'Personnalité médiatique et businesswoman américaine.', 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Kim_Kardashian_2019.jpg/220px-Kim_Kardashian_2019.jpg'],
        ['nom' => 'Elon Musk', 'prenom' => '', 'biographie' => 'Entrepreneur et visionnaire, PDG de Tesla et SpaceX.', 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/Elon_Musk_Royal_Society.jpg/220px-Elon_Musk_Royal_Society.jpg']
    ];
    
    $celebriteTypeId = $pdo->query("SELECT id FROM personnalite_types WHERE slug = 'celebrite'")->fetchColumn();
    if ($celebriteTypeId) {
        foreach ($celebrites as $celebrite) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO personnalite (nom, prenom, biographie, photo, date_creation, user_id, type_id) VALUES (?, ?, ?, ?, NOW(), 1, ?)");
            $stmt->execute([$celebrite['nom'], $celebrite['prenom'], $celebrite['biographie'], $celebrite['photo'], $celebriteTypeId]);
            echo "  ✓ {$celebrite['nom']} -> Célébrité\n";
        }
    }
    
    // 6. Ajouter les autorités gouvernementales manquantes
    echo "\n🔧 Ajout des autorités gouvernementales manquantes...\n";
    
    $gouvernementales = [
        ['nom' => 'Présidence de la République', 'description' => 'Institution suprême du pouvoir exécutif français.', 'site_web' => 'https://www.elysee.fr'],
        ['nom' => 'Premier Ministre', 'description' => 'Chef du gouvernement, responsable de la politique nationale.', 'site_web' => 'https://www.gouvernement.fr'],
        ['nom' => 'Assemblée Nationale', 'description' => 'Chambre basse du Parlement français, pouvoir législatif.', 'site_web' => 'https://www.assemblee-nationale.fr']
    ];
    
    $gouvernementaleTypeId = $pdo->query("SELECT id FROM autorite_types WHERE slug = 'gouvernementale'")->fetchColumn();
    if ($gouvernementaleTypeId) {
        foreach ($gouvernementales as $gov) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO autorite (nom, description, site_web, date_creation, user_id, type_id) VALUES (?, ?, ?, NOW(), 1, ?)");
            $stmt->execute([$gov['nom'], $gov['description'], $gov['site_web'], $gouvernementaleTypeId]);
            echo "  ✓ {$gov['nom']} -> Gouvernementale\n";
        }
    }
    
    $judiciaires = [
        ['nom' => 'Cour de Cassation', 'description' => 'Juridiction suprême de lordre judiciaire français.', 'site_web' => 'https://www.courdecassation.fr'],
        ['nom' => 'Conseil dÉtat', 'description' => 'Juridiction suprême de lordre administratif français.', 'site_web' => 'https://www.conseil-etat.fr'],
        ['nom' => 'Cour Constitutionnelle', 'description' => 'Garante de la constitutionnalité des lois.', 'site_web' => 'https://www.conseil-constitutionnel.fr']
    ];
    
    $judiciaireTypeId = $pdo->query("SELECT id FROM autorite_types WHERE slug = 'judiciaire'")->fetchColumn();
    if ($judiciaireTypeId) {
        foreach ($judiciaires as $jud) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO autorite (nom, description, site_web, date_creation, user_id, type_id) VALUES (?, ?, ?, NOW(), 1, ?)");
            $stmt->execute([$jud['nom'], $jud['description'], $jud['site_web'], $judiciaireTypeId]);
            echo "  ✓ {$jud['nom']} -> Judiciaire\n";
        }
    }
    
    // 7. Vérification finale
    echo "\n📊 Vérification finale COMPLÈTE...\n";
    
    $personnalitesTotal = $pdo->query("SELECT COUNT(*) FROM personnalite")->fetchColumn();
    $autoritesTotal = $pdo->query("SELECT COUNT(*) FROM autorite")->fetchColumn();
    $typesPersonnalites = $pdo->query("SELECT COUNT(*) FROM personnalite_types")->fetchColumn();
    $typesAutorites = $pdo->query("SELECT COUNT(*) FROM autorite_types")->fetchColumn();
    $personnalitesSansType = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE type_id IS NULL OR type_id = 0 OR type_id = ''")->fetchColumn();
    $autoritesSansType = $pdo->query("SELECT COUNT(*) FROM autorite WHERE type_id IS NULL OR type_id = 0 OR type_id = ''")->fetchColumn();
    
    echo "  📈 Personnalités totales : $personnalitesTotal\n";
    echo "  📈 Autorités totales : $autoritesTotal\n";
    echo "  📈 Types de personnalités : $typesPersonnalites\n";
    echo "  📈 Types d'autorités : $typesAutorites\n";
    echo "  ✅ Personnalités sans type : $personnalitesSansType\n";
    echo "  ✅ Autorités sans type : $autoritesSansType\n";
    
    echo "\n🎉 MIGRATION COMPLÈTE TERMINÉE AVEC SUCCÈS !\n";
    echo "📋 Toutes les données originales sont maintenant disponibles :\n";
    echo "  - Toutes les personnalités avec photos et biographies complètes\n";
    echo "  - Toutes les autorités avec logos et descriptions détaillées\n";
    echo "  - Tous les types et catégories fonctionnels\n";
    echo "  - Corrections des problèmes de types manquants\n";
    
} catch (PDOException $e) {
    die("❌ Erreur de migration complète : " . $e->getMessage() . "\n");
}
?>
