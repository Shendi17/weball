<?php
/**
 * Ajout de personnalités pour les nouveaux types
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout de Nouvelles Personnalités ===\n\n";

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
    // Philosophes
    [
        'nom' => 'Socrate',
        'prenom' => 'Socrate',
        'type' => 'philosophe',
        'category' => 'philosophie_antique',
        'description' => 'Philosophe grec de l\'Antiquité, considéré comme le père de la philosophie occidentale. Connu pour sa méthode maïeutique (l\'art de faire accoucher les esprits) et son célèbre "Connais-toi toi-même". Condamné à mort pour impiété et corruption de la jeunesse, il but la ciguë en 399 av. J.-C.',
        'nationalite' => 'Grecque',
        'date_naissance' => '-0470-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Socrates_Louvre.jpg/220px-Socrates_Louvre.jpg'
    ],
    [
        'nom' => 'Nietzsche',
        'prenom' => 'Friedrich',
        'type' => 'philosophe',
        'category' => 'philosophie_moderne',
        'description' => 'Philosophe allemand, philologue et poète. Critique radical de la culture occidentale, il a proclamé la "mort de Dieu" et développé les concepts de Surhomme et de volonté de puissance. Auteur de "Ainsi parlait Zarathoustra" et "Par-delà bien et mal".',
        'nationalite' => 'Allemande',
        'date_naissance' => '1844-10-15',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Nietzsche187a.jpg/220px-Nietzsche187a.jpg'
    ],
    [
        'nom' => 'Sartre',
        'prenom' => 'Jean-Paul',
        'type' => 'philosophe',
        'category' => 'existentialisme',
        'description' => 'Philosophe, écrivain et dramaturge français. Figure majeure de l\'existentialisme, il a développé la notion d\'engagement et affirmé que "l\'existence précède l\'essence". Prix Nobel de littérature 1964 (refusé). Compagnon de Simone de Beauvoir.',
        'nationalite' => 'Française',
        'date_naissance' => '1905-06-21',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Sartre_1967_crop.jpg/220px-Sartre_1967_crop.jpg'
    ],
    
    // Yogis / Maîtres spirituels
    [
        'nom' => 'Yogananda',
        'prenom' => 'Paramahansa',
        'type' => 'yogi',
        'category' => 'yoga',
        'description' => 'Maître spirituel indien, auteur de "Autobiographie d\'un Yogi" (1946), l\'un des livres spirituels les plus influents du XXe siècle. Il a introduit le Kriya Yoga en Occident et fondé la Self-Realization Fellowship. Steve Jobs lisait son livre chaque année.',
        'nationalite' => 'Indienne',
        'date_naissance' => '1893-01-05',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Paramahansa_Yogananda_at_Biltmore_Hotel.jpg/220px-Paramahansa_Yogananda_at_Biltmore_Hotel.jpg'
    ],
    [
        'nom' => 'Thich Nhat Hanh',
        'prenom' => 'Thích Nhất Hạnh',
        'type' => 'yogi',
        'category' => 'bouddhisme',
        'description' => 'Moine bouddhiste vietnamien, maître zen et militant pour la paix. Pionnier du bouddhisme engagé, il a popularisé la pleine conscience en Occident. Auteur de plus de 100 livres dont "La Paix en soi, la paix en marche". Nominé pour le prix Nobel de la paix par Martin Luther King.',
        'nationalite' => 'Vietnamienne',
        'date_naissance' => '1926-10-11',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Thich_Nhat_Hanh_12_%28cropped%29.jpg/220px-Thich_Nhat_Hanh_12_%28cropped%29.jpg'
    ],
    [
        'nom' => 'Iyengar',
        'prenom' => 'B.K.S.',
        'type' => 'yogi',
        'category' => 'yoga',
        'description' => 'Maître de yoga indien, fondateur du Yoga Iyengar. Il a révolutionné la pratique du yoga en développant l\'utilisation d\'accessoires (briques, sangles). Auteur de "Lumière sur le Yoga" (1966), bible des pratiquants. Il a enseigné jusqu\'à 95 ans.',
        'nationalite' => 'Indienne',
        'date_naissance' => '1918-12-14',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/BKS_Iyengar_2010_Pune.jpg/220px-BKS_Iyengar_2010_Pune.jpg'
    ],
    
    // Personnalités Atypiques
    [
        'nom' => 'Malala Yousafzai',
        'prenom' => 'Malala',
        'type' => 'atypique',
        'category' => 'revolutionnaire',
        'description' => 'Militante pakistanaise pour l\'éducation des filles. Survivante d\'une tentative d\'assassinat par les talibans à 15 ans, elle est devenue la plus jeune lauréate du prix Nobel de la paix (2014) à 17 ans. Symbole mondial du droit à l\'éducation.',
        'nationalite' => 'Pakistanaise',
        'date_naissance' => '1997-07-12',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Malala_Yousafzai_2015.jpg/220px-Malala_Yousafzai_2015.jpg'
    ],
    [
        'nom' => 'Thunberg',
        'prenom' => 'Greta',
        'type' => 'atypique',
        'category' => 'visionnaire',
        'description' => 'Militante suédoise pour le climat. À 15 ans, elle a lancé les "Vendredis pour le futur" (grèves scolaires pour le climat). Son discours "How dare you?" à l\'ONU en 2019 est devenu viral. Personnalité de l\'année 2019 du Time Magazine. Autiste Asperger assumée.',
        'nationalite' => 'Suédoise',
        'date_naissance' => '2003-01-03',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Greta_Thunberg_urges_MEPs_to_show_climate_leadership_%2849618310531%29_%28cropped%29.jpg/220px-Greta_Thunberg_urges_MEPs_to_show_climate_leadership_%2849618310531%29_%28cropped%29.jpg'
    ],
    [
        'nom' => 'Grandin',
        'prenom' => 'Temple',
        'type' => 'atypique',
        'category' => 'prodige',
        'description' => 'Scientifique américaine, professeure en sciences animales et autiste de haut niveau. Elle a révolutionné le traitement du bétail et est devenue une voix majeure pour l\'autisme. Son histoire a été adaptée au cinéma. Elle pense en images et a conçu plus de la moitié des installations d\'élevage en Amérique du Nord.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1947-08-29',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6d/Temple_Grandin.jpg/220px-Temple_Grandin.jpg'
    ],
    
    // Penseurs
    [
        'nom' => 'Yuval Noah Harari',
        'prenom' => 'Yuval Noah',
        'type' => 'penseur',
        'category' => 'historien',
        'description' => 'Historien et philosophe israélien. Auteur de best-sellers mondiaux : "Sapiens : Une brève histoire de l\'humanité", "Homo Deus" et "21 leçons pour le XXIe siècle". Ses réflexions sur l\'avenir de l\'humanité, l\'IA et le transhumanisme influencent leaders mondiaux et entrepreneurs.',
        'nationalite' => 'Israélienne',
        'date_naissance' => '1976-02-24',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Yuval_Noah_Harari_in_2023.jpg/220px-Yuval_Noah_Harari_in_2023.jpg'
    ],
    [
        'nom' => 'Chomsky',
        'prenom' => 'Noam',
        'type' => 'penseur',
        'category' => 'theoricien',
        'description' => 'Linguiste, philosophe et militant politique américain. Père de la linguistique moderne avec sa grammaire générative. Intellectuel engagé, critique virulent de la politique étrangère américaine. Auteur de plus de 100 livres. Considéré comme l\'un des intellectuels vivants les plus influents.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1928-12-07',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Noam_Chomsky_portrait_2017.jpg/220px-Noam_Chomsky_portrait_2017.jpg'
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
