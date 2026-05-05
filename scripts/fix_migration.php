<?php
/**
 * Script pour corriger les problèmes de migration
 */

require_once __DIR__ . '/../config/database.php';

echo "=== CORRECTION DE LA MIGRATION WEBALL ===\n\n";

try {
    $pdo = getPDO();
    
    // 1. Corriger les personnalités sans type
    echo "🔧 Correction des personnalités sans type...\n";
    $sansType = $pdo->query("SELECT id, nom, prenom FROM personnalite WHERE type_id IS NULL OR type_id = 0 OR type_id = ''")->fetchAll();
    
    foreach ($sansType as $person) {
        // Attribuer un type par défaut selon le nom
        $defaultType = 'influenceur'; // Type par défaut
        
        // Détecter le type selon le nom/prénom
        $nomComplet = strtolower($person['nom'] . ' ' . $person['prenom']);
        
        if (strpos($nomComplet, 'beyoncé') !== false || strpos($nomComplet, 'musicien') !== false) {
            $defaultType = 'musicien';
        } elseif (strpos($nomComplet, 'entrepreneur') !== false) {
            $defaultType = 'entrepreneur';
        } elseif (strpos($nomComplet, 'philosophe') !== false) {
            $defaultType = 'philosophe';
        }
        
        // Récupérer l'ID du type
        $stmt = $pdo->prepare("SELECT id FROM personnalite_types WHERE slug = ?");
        $stmt->execute([$defaultType]);
        $typeId = $stmt->fetchColumn();
        
        if ($typeId) {
            $stmt = $pdo->prepare("UPDATE personnalite SET type_id = ? WHERE id = ?");
            $stmt->execute([$typeId, $person['id']]);
            echo "  ✓ {$person['prenom']} {$person['nom']} -> Type: $defaultType\n";
        }
    }
    
    // 2. Ajouter des personnalités aux types de base vides
    echo "\n🔧 Ajout de personnalités aux types de base...\n";
    
    // Type Artiste
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
            echo "  ✓ {$artiste['prenom']} {$artiste['nom']} ajouté au type Artiste\n";
        }
    }
    
    // Type Célébrité
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
            echo "  ✓ {$celebrite['nom']} ajouté au type Célébrité\n";
        }
    }
    
    // 3. Ajouter des autorités aux types gouvernementaux
    echo "\n🔧 Ajout dautorités aux types gouvernementaux...\n";
    
    // Type Gouvernementale
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
            echo "  ✓ {$gov['nom']} ajouté au type Gouvernementale\n";
        }
    }
    
    // Type Judiciaire
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
            echo "  ✓ {$jud['nom']} ajouté au type Judiciaire\n";
        }
    }
    
    // 4. Vérification finale
    echo "\n📊 Vérification finale...\n";
    
    $personnalitesTotal = $pdo->query("SELECT COUNT(*) FROM personnalite")->fetchColumn();
    $autoritesTotal = $pdo->query("SELECT COUNT(*) FROM autorite")->fetchColumn();
    $personnalitesSansType = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE type_id IS NULL OR type_id = 0 OR type_id = ''")->fetchColumn();
    $autoritesSansType = $pdo->query("SELECT COUNT(*) FROM autorite WHERE type_id IS NULL OR type_id = 0 OR type_id = ''")->fetchColumn();
    
    echo "  - Personnalités totales : $personnalitesTotal\n";
    echo "  - Personnalités sans type : $personnalitesSansType\n";
    echo "  - Autorités totales : $autoritesTotal\n";
    echo "  - Autorités sans type : $autoritesSansType\n";
    
    echo "\n🎉 Correction terminée !\n";
    echo "📋 Résumé des types maintenant utilisés :\n";
    
    $types = $pdo->query("SELECT pt.slug, pt.nom, COUNT(p.id) as count FROM personnalite_types pt LEFT JOIN personnalite p ON pt.id = p.type_id GROUP BY pt.id ORDER BY count DESC LIMIT 10")->fetchAll();
    foreach ($types as $type) {
        echo "  - {$type['nom']} ({$type['slug']}) : {$type['count']} personnalités\n";
    }
    
} catch (PDOException $e) {
    die("❌ Erreur de correction : " . $e->getMessage() . "\n");
}
?>
