<?php
/**
 * Script de debug pour identifier pourquoi les données ne sont pas visibles
 */

require_once __DIR__ . '/../config/database.php';

echo "=== DEBUG IMPORTATION WEBALL ===\n\n";

try {
    $pdo = getPDO();
    
    // 1. Vérifier si les tables existent
    echo "🔍 Vérification des tables :\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $requiredTables = ['personnalite', 'personnalite_types', 'personnalite_categories', 'autorite', 'autorite_types', 'autorite_categories'];
    
    foreach ($requiredTables as $table) {
        if (in_array($table, $tables)) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "  ✅ $table : $count enregistrements\n";
        } else {
            echo "  ❌ $table : TABLE MANQUANTE\n";
        }
    }
    
    // 2. Vérifier les types de personnalités attendus
    echo "\n🔍 Types de personnalités attendus vs réels :\n";
    $expectedTypes = ['artiste', 'influenceur', 'conferencier', 'entrepreneur', 'celebrite', 'philosophe', 'yogi', 'atypique', 'penseur'];
    $actualTypes = $pdo->query("SELECT slug, nom FROM personnalite_types ORDER BY slug")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    foreach ($expectedTypes as $slug) {
        if (isset($actualTypes[$slug])) {
            $count = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE type_id = (SELECT id FROM personnalite_types WHERE slug = '$slug')")->fetchColumn();
            echo "  ✅ $slug ({$actualTypes[$slug]}) : $count personnalités\n";
        } else {
            echo "  ❌ $slug : TYPE MANQUANT\n";
        }
    }
    
    // 3. Vérifier les types d'autorités attendus
    echo "\n🔍 Types d'autorités attendus vs réels :\n";
    $expectedTypes = ['gouvernementale', 'judiciaire', 'administrative', 'regionale'];
    $actualTypes = $pdo->query("SELECT slug, nom FROM autorite_types ORDER BY slug")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    foreach ($expectedTypes as $slug) {
        if (isset($actualTypes[$slug])) {
            $count = $pdo->query("SELECT COUNT(*) FROM autorite WHERE type_id = (SELECT id FROM autorite_types WHERE slug = '$slug')")->fetchColumn();
            echo "  ✅ $slug ({$actualTypes[$slug]}) : $count autorités\n";
        } else {
            echo "  ❌ $slug : TYPE MANQUANT\n";
        }
    }
    
    // 4. Vérifier les catégories
    echo "\n🔍 Vérification des catégories :\n";
    
    // Catégories de personnalités
    $categoriesCount = $pdo->query("SELECT COUNT(*) FROM personnalite_categories")->fetchColumn();
    echo "  - Catégories de personnalités : $categoriesCount\n";
    
    if ($categoriesCount > 0) {
        $categories = $pdo->query("SELECT pt.slug as type_slug, pc.nom as category_name FROM personnalite_categories pc JOIN personnalite_types pt ON pc.type_id = pt.id LIMIT 10")->fetchAll();
        echo "  - Exemples de catégories :\n";
        foreach ($categories as $cat) {
            echo "    * {$cat['type_slug']} -> {$cat['category_name']}\n";
        }
    }
    
    // Catégories d'autorités
    $categoriesCount = $pdo->query("SELECT COUNT(*) FROM autorite_categories")->fetchColumn();
    echo "  - Catégories d'autorités : $categoriesCount\n";
    
    if ($categoriesCount > 0) {
        $categories = $pdo->query("SELECT at.slug as type_slug, ac.nom as category_name FROM autorite_categories ac JOIN autorite_types at ON ac.type_id = at.id LIMIT 10")->fetchAll();
        echo "  - Exemples de catégories :\n";
        foreach ($categories as $cat) {
            echo "    * {$cat['type_slug']} -> {$cat['category_name']}\n";
        }
    }
    
    // 5. Vérifier les données avec photos
    echo "\n🔍 Vérification des données avec photos :\n";
    $personnalitesAvecPhoto = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE photo IS NOT NULL AND photo != ''")->fetchColumn();
    $autoritesAvecLogo = $pdo->query("SELECT COUNT(*) FROM autorite WHERE logo IS NOT NULL AND logo != ''")->fetchColumn();
    
    echo "  - Personnalités avec photo : $personnalitesAvecPhoto/71\n";
    echo "  - Autorités avec logo : $autoritesAvecLogo/29\n";
    
    // 6. Vérifier quelques exemples concrets
    echo "\n🔍 Exemples concrets de données :\n";
    
    $personnalites = $pdo->query("SELECT nom, prenom, type_id FROM personnalite LIMIT 5")->fetchAll();
    echo "  - Personnalités (5 premiers) :\n";
    foreach ($personnalites as $p) {
        $stmt = $pdo->prepare("SELECT nom FROM personnalite_types WHERE id = ?");
        $stmt->execute([$p['type_id']]);
        $typeName = $stmt->fetchColumn();
        echo "    * {$p['prenom']} {$p['nom']} (Type: $typeName)\n";
    }
    
    $autorites = $pdo->query("SELECT nom, type_id FROM autorite LIMIT 5")->fetchAll();
    echo "  - Autorités (5 premiers) :\n";
    foreach ($autorites as $a) {
        $stmt = $pdo->prepare("SELECT nom FROM autorite_types WHERE id = ?");
        $stmt->execute([$a['type_id']]);
        $typeName = $stmt->fetchColumn();
        echo "    * {$a['nom']} (Type: $typeName)\n";
    }
    
    // 7. Diagnostic des problèmes possibles
    echo "\n🔍 Diagnostic des problèmes possibles :\n";
    
    // Vérifier si les types de base existent
    $basicTypes = ['artiste', 'influenceur', 'conferencier', 'entrepreneur', 'celebrite'];
    foreach ($basicTypes as $type) {
        $exists = $pdo->query("SELECT COUNT(*) FROM personnalite_types WHERE slug = '$type'")->fetchColumn();
        if ($exists == 0) {
            echo "  ❌ Type de base manquant : $type\n";
        }
    }
    
    $basicAuthTypes = ['gouvernementale', 'judiciaire', 'administrative', 'regionale'];
    foreach ($basicAuthTypes as $type) {
        $exists = $pdo->query("SELECT COUNT(*) FROM autorite_types WHERE slug = '$type'")->fetchColumn();
        if ($exists == 0) {
            echo "  ❌ Type d'autorité de base manquant : $type\n";
        }
    }
    
    echo "\n=== CONSEILS ===\n";
    echo "1. Si des types manquent, exécutez : php install/populate_types_categories.php\n";
    echo "2. Si les données sont là mais invisibles, vérifiez les permissions utilisateur\n";
    echo "3. Si les photos manquent, copiez le dossier uploads/\n";
    echo "4. Vérifiez que les fichiers de configuration sont corrects\n";
    
} catch (PDOException $e) {
    die("❌ Erreur de debug : " . $e->getMessage() . "\n");
}
?>
