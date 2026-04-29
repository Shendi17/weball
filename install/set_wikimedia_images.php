<?php
/**
 * Mise à jour avec des images Wikimedia Commons (plus fiables)
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Mise à jour avec images Wikimedia ===\n\n";

// Logos autorités depuis Wikimedia Commons
$logosAutorites = [
    'Ministère de l\'Intérieur' => 'https://upload.wikimedia.org/wikipedia/fr/4/4b/Logo_Minist%C3%A8re_Int%C3%A9rieur_2020.svg',
    'Mairie de Paris' => 'https://upload.wikimedia.org/wikipedia/fr/8/8c/Blason_paris_75.svg',
    'Préfecture de Police de Paris' => 'https://upload.wikimedia.org/wikipedia/fr/5/5e/Logo_Prefecture_Police_Paris_2019.svg',
    'Croix-Rouge Française' => 'https://upload.wikimedia.org/wikipedia/fr/7/7b/Logo_Croix-Rouge_fran%C3%A7aise.svg',
    'Restos du Cœur' => 'https://upload.wikimedia.org/wikipedia/fr/d/d0/Logo_Restos_du_C%C5%93ur.svg',
    'WWF France' => 'https://upload.wikimedia.org/wikipedia/commons/1/1a/WWF_logo.svg',
    'LVMH' => 'https://upload.wikimedia.org/wikipedia/commons/8/89/Logo_LVMH.svg',
    'Total Energies' => 'https://upload.wikimedia.org/wikipedia/fr/0/0d/Logo_TotalEnergies.svg',
    'Airbus' => 'https://upload.wikimedia.org/wikipedia/commons/8/89/Airbus_Logo_2017.svg',
    'Banque de France' => 'https://upload.wikimedia.org/wikipedia/fr/4/49/Logo_Banque_de_France.svg',
    'BNP Paribas' => 'https://upload.wikimedia.org/wikipedia/commons/8/8c/BNP_Paribas.svg',
    'AXA' => 'https://upload.wikimedia.org/wikipedia/commons/2/26/AXA_Logo.svg',
    'Université Paris-Saclay' => 'https://upload.wikimedia.org/wikipedia/fr/6/6d/Logo_Universit%C3%A9_Paris-Saclay.svg',
    'HEC Paris' => 'https://upload.wikimedia.org/wikipedia/commons/6/6e/HEC_Paris.svg',
    'École Polytechnique' => 'https://upload.wikimedia.org/wikipedia/commons/e/e9/Logo_EP.svg',
    'AP-HP (Assistance Publique - Hôpitaux de Paris)' => 'https://upload.wikimedia.org/wikipedia/fr/8/8d/Logo_AP-HP_2019.svg',
    'Institut Pasteur' => 'https://upload.wikimedia.org/wikipedia/fr/e/e4/Logo_Institut_Pasteur.svg',
    'France Télévisions' => 'https://upload.wikimedia.org/wikipedia/commons/5/5f/France_T%C3%A9l%C3%A9visions_2018.svg',
    'Le Monde' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/Le_Monde.svg',
    'Radio France' => 'https://upload.wikimedia.org/wikipedia/fr/3/34/Logo_Radio_France_2022.svg',
    'UNESCO' => 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Logo_UNESCO.svg',
    'OCDE' => 'https://upload.wikimedia.org/wikipedia/commons/2/2c/OECD_logo.svg',
    'Musée du Louvre' => 'https://upload.wikimedia.org/wikipedia/commons/6/66/Louvre_Museum_Logo.svg',
    'Opéra National de Paris' => 'https://upload.wikimedia.org/wikipedia/fr/3/3e/Logo_Op%C3%A9ra_national_de_Paris.svg',
    'Comédie-Française' => 'https://upload.wikimedia.org/wikipedia/commons/9/9f/Logo_Com%C3%A9die-Fran%C3%A7aise.svg',
    'Paris Saint-Germain' => 'https://upload.wikimedia.org/wikipedia/fr/8/86/Paris_Saint-Germain_Logo.svg',
    'Fédération Française de Football' => 'https://upload.wikimedia.org/wikipedia/fr/1/10/Logo_F%C3%A9d%C3%A9ration_Fran%C3%A7aise_Football_2018.svg',
    'CNRS' => 'https://upload.wikimedia.org/wikipedia/fr/b/b4/Logo_CNRS_2019.svg',
    'CEA' => 'https://upload.wikimedia.org/wikipedia/fr/b/b7/Logo_CEA.svg'
];

// Photos personnalités depuis Wikimedia Commons
$photosPersonnalites = [
    'Elon Musk' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Elon_Musk_Royal_Society_%28crop2%29.jpg/220px-Elon_Musk_Royal_Society_%28crop2%29.jpg',
    'Leonardo DiCaprio' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Leonardo_Dicaprio_Cannes_2019.jpg/220px-Leonardo_Dicaprio_Cannes_2019.jpg',
    'Emmanuel Macron' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/Emmanuel_Macron_in_2019.jpg/220px-Emmanuel_Macron_in_2019.jpg',
    'J.K. Rowling' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/J._K._Rowling_2010.jpg/220px-J._K._Rowling_2010.jpg',
    'Kylian Mbappé' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Kylian_Mbapp%C3%A9_2018.jpg/220px-Kylian_Mbapp%C3%A9_2018.jpg',
    'Greta Thunberg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Greta_Thunberg_urges_MEPs_to_show_climate_leadership_%2849618310531%29_%28cropped%29.jpg/220px-Greta_Thunberg_urges_MEPs_to_show_climate_leadership_%2849618310531%29_%28cropped%29.jpg',
    'Nikola Tesla' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Tesla_circa_1890.jpeg/220px-Tesla_circa_1890.jpeg',
    'Thomas Edison' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Thomas_Edison2.jpg/220px-Thomas_Edison2.jpg',
    'Alan Turing' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a1/Alan_Turing_Aged_16.jpg/220px-Alan_Turing_Aged_16.jpg',
    'Albert Einstein' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Albert_Einstein_Head.jpg/220px-Albert_Einstein_Head.jpg',
    'Marie Curie' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Marie_Curie_c._1920s.jpg/220px-Marie_Curie_c._1920s.jpg',
    'Steve Jobs' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/Steve_Jobs_Headshot_2010-CROP2.jpg/220px-Steve_Jobs_Headshot_2010-CROP2.jpg',
    'Bill Gates' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/Bill_Gates_2017_%28cropped%29.jpg/220px-Bill_Gates_2017_%28cropped%29.jpg',
    'Mark Zuckerberg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/Mark_Zuckerberg_F8_2019_Keynote_%2832830578717%29_%28cropped%29.jpg/220px-Mark_Zuckerberg_F8_2019_Keynote_%2832830578717%29_%28cropped%29.jpg',
    'Napoléon Bonaparte' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Jacques-Louis_David_-_The_Emperor_Napoleon_in_His_Study_at_the_Tuileries_-_Google_Art_Project.jpg/220px-Jacques-Louis_David_-_The_Emperor_Napoleon_in_His_Study_at_the_Tuileries_-_Google_Art_Project.jpg',
    'Jeanne d\'Arc' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Joan_of_Arc_miniature_graded.jpg/220px-Joan_of_Arc_miniature_graded.jpg',
    'Jules César' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Gaius_Iulius_Caesar_%28Vatican_Museum%29.jpg/220px-Gaius_Iulius_Caesar_%28Vatican_Museum%29.jpg',
    'Cléopâtre VII' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Kleopatra-VII.-Altes-Museum-Berlin1.jpg/220px-Kleopatra-VII.-Altes-Museum-Berlin1.jpg',
    'Louis Pasteur' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Louis_Pasteur.jpg/220px-Louis_Pasteur.jpg',
    'Christopher Nolan' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Christopher_Nolan_Cannes_2018.jpg/220px-Christopher_Nolan_Cannes_2018.jpg',
    'Marion Cotillard' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Marion_Cotillard_Cabourg_2017_2.jpg/220px-Marion_Cotillard_Cabourg_2017_2.jpg',
    'Friedrich Nietzsche' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Nietzsche187a.jpg/220px-Nietzsche187a.jpg',
    'Jean-Paul Sartre' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Sartre_1967_crop.jpg/220px-Sartre_1967_crop.jpg',
    'Socrate' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Socrates_Louvre.jpg/220px-Socrates_Louvre.jpg',
    'Gengis Khan' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/YuanEmperorAlbumGenghisPortrait.jpg/220px-YuanEmperorAlbumGenghisPortrait.jpg'
];

echo "=== LOGOS AUTORITÉS ===\n";
$logosUpdated = 0;
foreach ($logosAutorites as $nom => $url) {
    $stmt = $pdo->prepare("UPDATE autorite SET logo = ? WHERE nom = ?");
    if ($stmt->execute([$url, $nom]) && $stmt->rowCount() > 0) {
        echo "✓ $nom\n";
        $logosUpdated++;
    }
}

echo "\n=== PHOTOS PERSONNALITÉS ===\n";
$photosUpdated = 0;
foreach ($photosPersonnalites as $nom => $url) {
    // Chercher par nom ou prénom
    $stmt = $pdo->prepare("UPDATE personnalite SET photo = ? WHERE nom LIKE ? OR CONCAT(prenom, ' ', nom) LIKE ?");
    if ($stmt->execute([$url, "%$nom%", "%$nom%"]) && $stmt->rowCount() > 0) {
        echo "✓ $nom\n";
        $photosUpdated++;
    }
}

echo "\n=== RÉSUMÉ ===\n";
echo "✓ Logos autorités mis à jour: $logosUpdated\n";
echo "✓ Photos personnalités mises à jour: $photosUpdated\n";
echo "\n=== Terminé ! ===\n";
