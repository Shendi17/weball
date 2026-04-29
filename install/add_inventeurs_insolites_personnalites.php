<?php
/**
 * Ajout de personnalités inventeurs et insolites
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout de Personnalités Inventeurs et Insolites ===\n\n";

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
    // INVENTEURS
    [
        'nom' => 'Tesla',
        'prenom' => 'Nikola',
        'type' => 'inventeur',
        'category' => 'electricite_et_energie',
        'description' => 'Inventeur, ingénieur et physicien serbo-américain. Pionnier de l\'électricité, il a développé le courant alternatif, la bobine Tesla et de nombreuses innovations en électromagnétisme. Visionnaire génial mais ruiné, il a déposé plus de 300 brevets. Son rivalité avec Edison est légendaire.',
        'nationalite' => 'Serbe-Américaine',
        'date_naissance' => '1856-07-10',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/N.Tesla.JPG/220px-N.Tesla.JPG'
    ],
    [
        'nom' => 'Edison',
        'prenom' => 'Thomas',
        'type' => 'inventeur',
        'category' => 'electricite_et_energie',
        'description' => 'Inventeur et homme d\'affaires américain. Détenteur de 1093 brevets, il a inventé l\'ampoule électrique incandescente, le phonographe et le kinétoscope. Fondateur de General Electric. Célèbre pour sa phrase "Le génie est fait de 1% d\'inspiration et 99% de transpiration".',
        'nationalite' => 'Américaine',
        'date_naissance' => '1847-02-11',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Thomas_Edison2.jpg/220px-Thomas_Edison2.jpg'
    ],
    [
        'nom' => 'Wright',
        'prenom' => 'Orville & Wilbur',
        'type' => 'inventeur',
        'category' => 'aeronautique',
        'description' => 'Frères américains pionniers de l\'aviation. Le 17 décembre 1903, ils réalisent le premier vol motorisé contrôlé de l\'histoire à Kitty Hawk. Leur Flyer I a volé 12 secondes sur 36 mètres. Ils ont révolutionné le transport et ouvert l\'ère de l\'aviation.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1871-08-19',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/Wrightbrothers.jpg/280px-Wrightbrothers.jpg'
    ],
    [
        'nom' => 'Bell',
        'prenom' => 'Alexander Graham',
        'type' => 'inventeur',
        'category' => 'telecommunications',
        'description' => 'Scientifique, inventeur et ingénieur écossais naturalisé américain. Inventeur du téléphone (1876), il a révolutionné les télécommunications. Il a également travaillé sur l\'aviation, l\'hydroptère et l\'eugénisme. Fondateur de la Bell Telephone Company.',
        'nationalite' => 'Écossaise-Américaine',
        'date_naissance' => '1847-03-03',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Alexander_Graham_Bell.jpg/220px-Alexander_Graham_Bell.jpg'
    ],
    [
        'nom' => 'Turing',
        'prenom' => 'Alan',
        'type' => 'inventeur',
        'category' => 'informatique',
        'description' => 'Mathématicien et cryptologue britannique, père de l\'informatique moderne. Il a conçu la machine de Turing (modèle théorique de l\'ordinateur) et cassé le code Enigma pendant la Seconde Guerre mondiale, sauvant des millions de vies. Persécuté pour son homosexualité, il s\'est suicidé à 41 ans.',
        'nationalite' => 'Britannique',
        'date_naissance' => '1912-06-23',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a1/Alan_Turing_Aged_16.jpg/220px-Alan_Turing_Aged_16.jpg'
    ],
    [
        'nom' => 'Pasteur',
        'prenom' => 'Louis',
        'type' => 'inventeur',
        'category' => 'medecine',
        'description' => 'Scientifique français, pionnier de la microbiologie. Il a découvert les principes de la vaccination, de la pasteurisation et de la fermentation. Ses vaccins contre la rage et le charbon ont sauvé des millions de vies. Fondateur de l\'Institut Pasteur (1887).',
        'nationalite' => 'Française',
        'date_naissance' => '1822-12-27',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Louis_Pasteur.jpg/220px-Louis_Pasteur.jpg'
    ],
    [
        'nom' => 'Gutenberg',
        'prenom' => 'Johannes',
        'type' => 'inventeur',
        'category' => 'imprimerie',
        'description' => 'Imprimeur allemand, inventeur de l\'imprimerie à caractères mobiles en Europe (vers 1450). Sa Bible à 42 lignes est le premier livre imprimé en série. Cette invention a révolutionné la diffusion du savoir et déclenché la Renaissance et la Réforme.',
        'nationalite' => 'Allemande',
        'date_naissance' => '1400-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Gutenberg.jpg/220px-Gutenberg.jpg'
    ],
    
    // PERSONNALITÉS INSOLITES
    [
        'nom' => 'Banksy',
        'prenom' => 'Banksy',
        'type' => 'atypique',
        'category' => 'artiste_de_rue',
        'description' => 'Artiste de rue britannique anonyme, figure majeure du street art. Ses œuvres satiriques et subversives apparaissent mystérieusement dans le monde entier. En 2018, son tableau "Girl with Balloon" s\'est autodétruit après sa vente aux enchères pour 1,4M£. Son identité reste un mystère.',
        'nationalite' => 'Britannique',
        'date_naissance' => '1974-07-28',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Banksy.on.the.thekla.arp.jpg/220px-Banksy.on.the.thekla.arp.jpg'
    ],
    [
        'nom' => 'Baumgartner',
        'prenom' => 'Felix',
        'type' => 'atypique',
        'category' => 'record_du_monde',
        'description' => 'Parachutiste et cascadeur autrichien. Le 14 octobre 2012, il saute depuis la stratosphère (39 km d\'altitude) et franchit le mur du son en chute libre (1357 km/h). Record du monde du saut le plus haut. L\'événement est suivi par 8 millions de personnes en direct sur YouTube.',
        'nationalite' => 'Autrichienne',
        'date_naissance' => '1969-04-20',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Felix_Baumgartner_GQ_Men_Of_The_Year_Awards_2012_%28crop%29.jpg/220px-Felix_Baumgartner_GQ_Men_Of_The_Year_Awards_2012_%28crop%29.jpg'
    ],
    [
        'nom' => 'Diogène de Sinope',
        'prenom' => 'Diogène',
        'type' => 'atypique',
        'category' => 'insolite',
        'description' => 'Philosophe grec cynique de l\'Antiquité. Il vivait dans un tonneau sur la place publique d\'Athènes, rejetant tout confort matériel. Célèbre pour ses provocations : il cherchait "un homme" en plein jour avec une lanterne. Quand Alexandre le Grand lui offrit ce qu\'il voulait, il répondit : "Ôte-toi de mon soleil".',
        'nationalite' => 'Grecque',
        'date_naissance' => '-0412-01-01',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Jean-L%C3%A9on_G%C3%A9r%C3%B4me_-_Diogenes_-_Walters_37131.jpg/280px-Jean-L%C3%A9on_G%C3%A9r%C3%B4me_-_Diogenes_-_Walters_37131.jpg'
    ],
    [
        'nom' => 'Houdini',
        'prenom' => 'Harry',
        'type' => 'atypique',
        'category' => 'cascadeur',
        'description' => 'Illusionniste et cascadeur américano-hongrois. Maître de l\'évasion, il s\'échappait de menottes, camisoles de force, cercueils et réservoirs d\'eau. Ses numéros spectaculaires ont fasciné le monde entier. Il a également combattu les faux médiums et spirites. Mort mystérieusement à 52 ans.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1874-03-24',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Harry_Houdini%2C_1899.jpg/220px-Harry_Houdini%2C_1899.jpg'
    ],
    [
        'nom' => 'Kaczynski',
        'prenom' => 'Theodore',
        'type' => 'atypique',
        'category' => 'ermite',
        'description' => 'Mathématicien américain devenu terroriste, surnommé "Unabomber". Génie précoce (QI 167), il devient professeur à Berkeley à 25 ans puis se retire dans une cabane isolée du Montana. De 1978 à 1995, il envoie des colis piégés tuant 3 personnes. Arrêté en 1996, condamné à perpétuité. Cas extrême de rejet de la société technologique.',
        'nationalite' => 'Américaine',
        'date_naissance' => '1942-05-22',
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Ted_Kaczynski.jpg/220px-Ted_Kaczynski.jpg'
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
