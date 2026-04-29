<?php
/**
 * Ajout de personnalités historiques, mystiques, héros et militaires
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout de Personnalités Historiques, Mystiques, Héros et Militaires ===\n\n";

// Récupérer les types
$typesData = $pdo->query("SELECT id, nom, slug FROM personnalite_types")->fetchAll(PDO::FETCH_ASSOC);
$types = [];
foreach ($typesData as $type) {
    $types[strtolower($type['nom'])] = $type['id'];
    $types[$type['slug']] = $type['id'];
}

$categories = $pdo->query("SELECT id, slug, type_id FROM personnalite_categories")->fetchAll(PDO::FETCH_ASSOC);

function getCategoryId($categorySlug, $typeSlug, $categories, $types) {
    $typeId = $types[$typeSlug] ?? null;
    if (!$typeId) return null;
    
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug && $cat['type_id'] == $typeId) {
            return $cat['id'];
        }
    }
    return null;
}

$personnalites = [
    // PERSONNALITÉS HISTORIQUES
    [
        'nom' => 'Ramsès II',
        'prenom' => 'Ramsès',
        'type' => 'historique',
        'category' => 'pharaon',
        'description' => 'Pharaon d\'Égypte de la XIXe dynastie, règne de 1279 à 1213 av. J.-C. (66 ans !). Surnommé "Ramsès le Grand", il est considéré comme le plus grand pharaon de l\'Égypte antique. Bâtisseur prolifique (Abou Simbel, Ramesséum), guerrier victorieux (bataille de Qadesh), il a eu plus de 100 enfants. Sa momie est conservée au Musée du Caire.',
        'nationalite' => 'Égyptienne',
        'date_naissance' => '-1303-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/70/Ramesses_II_in_the_Turin_Museum28.jpg/220px-Ramesses_II_in_the_Turin_Museum28.jpg'
    ],
    [
        'nom' => 'Cléopâtre VII',
        'prenom' => 'Cléopâtre',
        'type' => 'historique',
        'category' => 'reine',
        'description' => 'Dernière reine d\'Égypte de la dynastie ptolémaïque (51-30 av. J.-C.). Femme cultivée parlant 9 langues, elle a séduit Jules César puis Marc Antoine. Son alliance avec Rome visait à préserver l\'indépendance égyptienne. Vaincue à Actium, elle se suicide au venin d\'aspic. Figure légendaire de beauté, d\'intelligence et de pouvoir.',
        'nationalite' => 'Égyptienne',
        'date_naissance' => '-0069-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Kleopatra-VII.-Altes-Museum-Berlin1.jpg/220px-Kleopatra-VII.-Altes-Museum-Berlin1.jpg'
    ],
    [
        'nom' => 'Jules César',
        'prenom' => 'Jules',
        'type' => 'historique',
        'category' => 'empereur',
        'description' => 'Général, homme d\'État et écrivain romain. Conquérant de la Gaule (58-50 av. J.-C.), il franchit le Rubicon en 49 et devient dictateur de Rome. Réformateur brillant, il crée le calendrier julien. Assassiné par Brutus et Cassius aux Ides de Mars (15 mars 44 av. J.-C.). "Veni, vidi, vici" et "Tu quoque mi fili" sont ses phrases célèbres.',
        'nationalite' => 'Romaine',
        'date_naissance' => '-0100-07-12',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Gaius_Iulius_Caesar_%28Vatican_Museum%29.jpg/220px-Gaius_Iulius_Caesar_%28Vatican_Museum%29.jpg'
    ],
    [
        'nom' => 'Gengis Khan',
        'prenom' => 'Gengis',
        'type' => 'historique',
        'category' => 'conquerant',
        'description' => 'Fondateur et premier Khan de l\'Empire mongol (1206-1227), le plus vaste empire terrestre de l\'histoire. Né Temüjin, il unifie les tribus mongoles et conquiert l\'Asie centrale, la Chine, la Perse. Stratège militaire génial, il tue environ 40 millions de personnes. 16 millions d\'hommes actuels seraient ses descendants directs.',
        'nationalite' => 'Mongole',
        'date_naissance' => '1162-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/YuanEmperorAlbumGenghisPortrait.jpg/220px-YuanEmperorAlbumGenghisPortrait.jpg'
    ],
    [
        'nom' => 'Jeanne d\'Arc',
        'prenom' => 'Jeanne',
        'type' => 'historique',
        'category' => 'revolutionnaire',
        'description' => 'Héroïne française de la guerre de Cent Ans. Paysanne de 17 ans guidée par des "voix divines", elle convainc Charles VII, libère Orléans (1429) et fait sacrer le roi à Reims. Capturée par les Bourguignons, vendue aux Anglais, elle est brûlée vive pour hérésie à 19 ans (1431). Canonisée en 1920, sainte patronne de la France.',
        'nationalite' => 'Française',
        'date_naissance' => '1412-01-06',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Joan_of_Arc_miniature_graded.jpg/220px-Joan_of_Arc_miniature_graded.jpg'
    ],
    [
        'nom' => 'Christophe Colomb',
        'prenom' => 'Christophe',
        'type' => 'historique',
        'category' => 'explorateur_historique',
        'description' => 'Navigateur génois au service de l\'Espagne. Le 12 octobre 1492, il découvre l\'Amérique en cherchant une route vers les Indes. Quatre voyages transatlantiques (1492-1504). Il meurt en 1506 en croyant avoir atteint l\'Asie. Sa découverte bouleverse l\'histoire mondiale mais déclenche la colonisation et le génocide des peuples autochtones.',
        'nationalite' => 'Italienne',
        'date_naissance' => '1451-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Portrait_of_a_Man%2C_Said_to_be_Christopher_Columbus.jpg/220px-Portrait_of_a_Man%2C_Said_to_be_Christopher_Columbus.jpg'
    ],
    
    // PERSONNALITÉS MYSTIQUES
    [
        'nom' => 'Nostradamus',
        'prenom' => 'Michel de',
        'type' => 'mystique',
        'category' => 'mage',
        'description' => 'Apothicaire, médecin et astrologue français du XVIe siècle. Célèbre pour ses "Prophéties" (1555), recueil de quatrains énigmatiques prédisant l\'avenir. Ses prédictions sont interprétées a posteriori pour correspondre à des événements historiques (Hitler, 11 septembre, etc.). Figure majeure de l\'ésotérisme occidental.',
        'nationalite' => 'Française',
        'date_naissance' => '1503-12-14',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Nostradamus_by_Cesar.jpg/220px-Nostradamus_by_Cesar.jpg'
    ],
    [
        'nom' => 'Nicolas Flamel',
        'prenom' => 'Nicolas',
        'type' => 'mystique',
        'category' => 'alchimiste',
        'description' => 'Écrivain public et copiste parisien du XIVe siècle. La légende en fait un alchimiste ayant découvert la pierre philosophale et l\'élixir de longue vie. En réalité, bourgeois aisé ayant fait fortune dans l\'immobilier. Sa légende perdure : il apparaît dans Harry Potter comme inventeur de la pierre philosophale, immortel de 665 ans.',
        'nationalite' => 'Française',
        'date_naissance' => '1330-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/Nicolas_Flamel.jpg/220px-Nicolas_Flamel.jpg'
    ],
    [
        'nom' => 'Aleister Crowley',
        'prenom' => 'Aleister',
        'type' => 'mystique',
        'category' => 'sorcier',
        'description' => 'Occultiste, écrivain et poète britannique. Fondateur de la religion Thelema ("Fais ce que tu voudras"). Membre de l\'Ordre hermétique de l\'Aube dorée, il se proclame "la Bête 666". Figure controversée de l\'ésotérisme moderne, il influence le rock (Beatles, Led Zeppelin). Mort dans la pauvreté en 1947.',
        'nationalite' => 'Britannique',
        'date_naissance' => '1875-10-12',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Aleister_Crowley%2C_wickedest_man_in_the_world.jpg/220px-Aleister_Crowley%2C_wickedest_man_in_the_world.jpg'
    ],
    [
        'nom' => 'Bouddha',
        'prenom' => 'Siddhartha Gautama',
        'type' => 'mystique',
        'category' => 'prophete',
        'description' => 'Sage et fondateur du bouddhisme (VIe-Ve siècle av. J.-C.). Prince népalais, il renonce à sa vie de luxe à 29 ans pour chercher la fin de la souffrance. Après 6 ans d\'ascèse, il atteint l\'Éveil sous l\'arbre de la Bodhi. Il enseigne les Quatre Nobles Vérités et le Chemin octuple. 500 millions de bouddhistes aujourd\'hui.',
        'nationalite' => 'Népalaise',
        'date_naissance' => '-0563-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/Buddha_in_Sarnath_Museum_%28Dhammajak_Mutra%29.jpg/220px-Buddha_in_Sarnath_Museum_%28Dhammajak_Mutra%29.jpg'
    ],
    
    // HÉROS ET LÉGENDES
    [
        'nom' => 'Roi Arthur',
        'prenom' => 'Arthur',
        'type' => 'heros',
        'category' => 'legende_medievale',
        'description' => 'Roi légendaire de Bretagne (Ve-VIe siècle). Chef de guerre ayant repoussé les invasions saxonnes. Entouré des Chevaliers de la Table Ronde (Lancelot, Perceval, Gauvain), il recherche le Graal. Excalibur, son épée magique, fut retirée d\'un rocher. Trahi par Mordred et Guenièvre, il meurt à Avalon. Mythe fondateur de la chevalerie médiévale.',
        'nationalite' => 'Bretonne',
        'date_naissance' => '0475-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Boys_King_Arthur_-_N._C._Wyeth_-_p16.jpg/220px-Boys_King_Arthur_-_N._C._Wyeth_-_p16.jpg'
    ],
    [
        'nom' => 'Miyamoto Musashi',
        'prenom' => 'Miyamoto',
        'type' => 'heros',
        'category' => 'samourai',
        'description' => 'Samouraï légendaire japonais (1584-1645). Invaincu en 60 duels, maître du combat à deux sabres (Niten Ichi-ryū). Auteur du "Traité des cinq roues" (Go Rin No Sho), traité de stratégie et philosophie. Également peintre et calligraphe. Symbole de la perfection martiale et de la Voie du guerrier (Bushido).',
        'nationalite' => 'Japonaise',
        'date_naissance' => '1584-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Musashi_ts_pic.jpg/220px-Musashi_ts_pic.jpg'
    ],
    [
        'nom' => 'Barbe Noire',
        'prenom' => 'Edward Teach',
        'type' => 'heros',
        'category' => 'pirate',
        'description' => 'Pirate anglais légendaire (1680-1718). Terreur des Caraïbes et de la côte est américaine. Reconnaissable à sa barbe noire tressée avec des mèches enflammées. Commandant du Queen Anne\'s Revenge. Tué lors d\'un abordage par la Royal Navy, il reçoit 5 balles et 20 coups d\'épée avant de mourir. Archétype du pirate dans la culture populaire.',
        'nationalite' => 'Anglaise',
        'date_naissance' => '1680-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/48/Blackbeard%2C_Buccaneer_-_Cover.jpg/220px-Blackbeard%2C_Buccaneer_-_Cover.jpg'
    ],
    
    // MILITAIRES
    [
        'nom' => 'Napoléon Bonaparte',
        'prenom' => 'Napoléon',
        'type' => 'militaire',
        'category' => 'general',
        'description' => 'Général, Premier consul puis Empereur des Français (1804-1815). Génie militaire, il remporte 60 batailles (Austerlitz, Iéna, Wagram). Il réforme la France (Code civil, Banque de France, lycées). Vaincu à Waterloo (1815), exilé à Sainte-Hélène où il meurt en 1821. Figure majeure de l\'histoire mondiale.',
        'nationalite' => 'Française',
        'date_naissance' => '1769-08-15',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Jacques-Louis_David_-_The_Emperor_Napoleon_in_His_Study_at_the_Tuileries_-_Google_Art_Project.jpg/220px-Jacques-Louis_David_-_The_Emperor_Napoleon_in_His_Study_at_the_Tuileries_-_Google_Art_Project.jpg'
    ],
    [
        'nom' => 'Sun Tzu',
        'prenom' => 'Sun',
        'type' => 'militaire',
        'category' => 'stratege',
        'description' => 'Général et stratège chinois (VIe-Ve siècle av. J.-C.). Auteur de "L\'Art de la guerre", traité de stratégie militaire le plus influent de l\'histoire. Ses principes (connais ton ennemi, gagne sans combattre, adaptabilité) sont appliqués en business, politique et sport. "Toute guerre est fondée sur la tromperie."',
        'nationalite' => 'Chinoise',
        'date_naissance' => '-0544-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0f/Enchoen27n3200.jpg/220px-Enchoen27n3200.jpg'
    ],
    [
        'nom' => 'Jean Moulin',
        'prenom' => 'Jean',
        'type' => 'militaire',
        'category' => 'resistant',
        'description' => 'Résistant français, héros de la Seconde Guerre mondiale. Préfet, il refuse de collaborer et rejoint De Gaulle à Londres (1941). Parachuté en France, il unifie la Résistance et crée le Conseil National de la Résistance (1943). Arrêté par Klaus Barbie, torturé, il meurt sans parler. Panthéonisé en 1964. Symbole de la Résistance française.',
        'nationalite' => 'Française',
        'date_naissance' => '1899-06-20',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/Jean_Moulin_-_photo_d%27identit%C3%A9_%281940%29.jpg/220px-Jean_Moulin_-_photo_d%27identit%C3%A9_%281940%29.jpg'
    ]
];

// Insertion
$inserted = 0;
$errors = 0;

foreach ($personnalites as $perso) {
    try {
        $typeLower = strtolower($perso['type']);
        $typeId = $types[$typeLower] ?? $types[$perso['type']] ?? null;
        $categoryId = getCategoryId($perso['category'], $perso['type'], $categories, $types);
        
        if (!$typeId) {
            echo "✗ {$perso['prenom']} {$perso['nom']} - Type '{$perso['type']}' non trouvé\n";
            $errors++;
            continue;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO personnalite (nom, prenom, type_id, category_id, biographie, nationalite, date_naissance, photo, date_creation, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)
        ");
        
        $stmt->execute([
            $perso['nom'],
            $perso['prenom'] ?? '',
            $typeId,
            $categoryId,
            $perso['description'] ?? '',
            $perso['nationalite'] ?? '',
            $perso['date_naissance'] ?? null,
            $perso['photo'] ?? null
        ]);
        
        $inserted++;
        echo "✓ {$perso['prenom']} {$perso['nom']} - {$perso['type']}\n";
        
    } catch (PDOException $e) {
        echo "✗ {$perso['prenom']} {$perso['nom']} - Erreur: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Personnalités insérées: $inserted\n";
echo "✗ Erreurs: $errors\n";
echo "\n=== Terminé ! ===\n";
