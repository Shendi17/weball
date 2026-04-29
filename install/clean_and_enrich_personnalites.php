<?php
/**
 * Nettoyer les anciennes données et enrichir les personnalités avec images
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Nettoyage et Enrichissement ===\n\n";

// 1. Supprimer les anciennes données de démo
echo "Suppression des anciennes données...\n";
$pdo->exec("DELETE FROM personnalite WHERE biographie IS NULL OR biographie = ''");
echo "✓ Anciennes données supprimées\n\n";

// 2. Ajouter des images et enrichir les descriptions
$enrichments = [
    'Beyoncé' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Beyonce_-_Tottenham_Hotspur_Stadium_-_1st_June_2023_%2816_crop%29.jpg/220px-Beyonce_-_Tottenham_Hotspur_Stadium_-_1st_June_2023_%2816_crop%29.jpg',
        'biographie' => 'Beyoncé Giselle Knowles-Carter est une chanteuse, auteure-compositrice, productrice et actrice américaine. Révélée à la fin des années 1990 en tant que chanteuse du groupe Destiny\'s Child, elle est aujourd\'hui l\'une des artistes les plus influentes de sa génération. Avec plus de 200 millions de disques vendus, elle a remporté 32 Grammy Awards. Son album "Lemonade" (2016) et "Renaissance" (2022) sont considérés comme des œuvres majeures de la musique contemporaine.',
        'email' => 'contact@beyonce.com',
        'telephone' => '+1-555-BEYONCE'
    ],
    'Daft Punk' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/Daft_Punk_in_2013.jpg/280px-Daft_Punk_in_2013.jpg',
        'biographie' => 'Daft Punk est un duo français de musique électronique formé en 1993 par Thomas Bangalter et Guy-Manuel de Homem-Christo. Pionniers de la French Touch, ils sont connus pour leurs casques de robot et leurs performances audiovisuelles spectaculaires. Leurs albums "Homework" (1997), "Discovery" (2001) et "Random Access Memories" (2013) ont révolutionné la musique électronique mondiale. Ils ont remporté 6 Grammy Awards avant leur séparation en 2021.',
        'email' => 'info@daftpunk.com'
    ],
    'Eminem' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Eminem_-_Concert_for_Valor_in_Washington%2C_D.C._Nov._11%2C_2014_%282%29_%28Cropped%29.jpg/220px-Eminem_-_Concert_for_Valor_in_Washington%2C_D.C._Nov._11%2C_2014_%282%29_%28Cropped%29.jpg',
        'biographie' => 'Marshall Bruce Mathers III, connu sous le nom d\'Eminem, est un rappeur, producteur et acteur américain. Considéré comme l\'un des plus grands rappeurs de tous les temps, il a vendu plus de 220 millions d\'albums dans le monde. Découvert par Dr. Dre, ses albums "The Marshall Mathers LP" et "The Eminem Show" sont des classiques du hip-hop. Il a remporté 15 Grammy Awards et un Oscar pour "Lose Yourself".',
        'email' => 'contact@eminem.com'
    ],
    'DiCaprio' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Leonardo_Dicaprio_Cannes_2019.jpg/220px-Leonardo_Dicaprio_Cannes_2019.jpg',
        'biographie' => 'Leonardo Wilhelm DiCaprio est un acteur, producteur et militant écologiste américain. Révélé dans "Titanic" (1997), il a collaboré à plusieurs reprises avec Martin Scorsese. Ses rôles dans "The Wolf of Wall Street", "Inception" et "The Revenant" (Oscar 2016) sont acclamés. Engagé pour l\'environnement, il a créé la Leonardo DiCaprio Foundation en 1998.',
        'email' => 'leo@dicaprio.com'
    ],
    'Cotillard' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0d/Marion_Cotillard_Cabourg_2017.jpg/220px-Marion_Cotillard_Cabourg_2017.jpg',
        'biographie' => 'Marion Cotillard est une actrice française. Elle a remporté l\'Oscar de la meilleure actrice en 2008 pour son interprétation d\'Édith Piaf dans "La Môme". Actrice internationale, elle a tourné avec les plus grands réalisateurs (Christopher Nolan, Woody Allen, les frères Dardenne). Elle est également engagée pour l\'environnement et ambassadrice de Greenpeace.',
        'email' => 'marion@cotillard.fr'
    ],
    'Gad Elmaleh' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Gad_Elmaleh_Cannes.jpg/220px-Gad_Elmaleh_Cannes.jpg',
        'biographie' => 'Gad Elmaleh est un humoriste et acteur franco-marocain. Surnommé le "Jerry Seinfeld français", il est l\'un des humoristes les plus populaires de France. Ses spectacles "Décalages", "Papa est en haut" et "Part en Live" ont connu un immense succès. Il a également joué dans de nombreux films français et américains.',
        'email' => 'contact@gadelmaleh.com'
    ],
    'Rowling' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/J._K._Rowling_2010.jpg/220px-J._K._Rowling_2010.jpg',
        'biographie' => 'Joanne Kathleen Rowling, connue sous le nom de J.K. Rowling, est une écrivaine britannique. Créatrice de la saga Harry Potter, elle est l\'une des auteures les plus lues au monde avec plus de 500 millions d\'exemplaires vendus. La série a été adaptée en 8 films à succès. Elle est également l\'auteure de romans pour adultes sous le pseudonyme Robert Galbraith.',
        'email' => 'jk@rowling.com'
    ],
    'Musso' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Guillaume_Musso_-_Salon_du_livre_de_Paris_2010.jpg/220px-Guillaume_Musso_-_Salon_du_livre_de_Paris_2010.jpg',
        'biographie' => 'Guillaume Musso est un écrivain français, auteur de nombreux best-sellers. Ses romans mêlent suspense, romance et fantastique. "Et après...", "Sauve-moi", "La Fille de papier" et "La Vie secrète des écrivains" ont tous été des succès internationaux. Il est l\'auteur français le plus lu en France depuis plus de 15 ans.',
        'email' => 'guillaume@musso.fr'
    ],
    'Musk' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Elon_Musk_Royal_Society_%28crop2%29.jpg/220px-Elon_Musk_Royal_Society_%28crop2%29.jpg',
        'biographie' => 'Elon Reeve Musk est un entrepreneur, chef d\'entreprise et ingénieur sud-africain, canadien et américain. PDG de Tesla (véhicules électriques), SpaceX (aérospatiale), et propriétaire de X (anciennement Twitter). Visionnaire controversé, il travaille sur des projets ambitieux : colonisation de Mars, Hyperloop, Neuralink (interface cerveau-machine). Fortune estimée à plus de 200 milliards de dollars.',
        'email' => 'elon@tesla.com'
    ],
    'Niel' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Xavier_Niel_%28cropped%29.jpg/220px-Xavier_Niel_%28cropped%29.jpg',
        'biographie' => 'Xavier Niel est un entrepreneur français, fondateur de Free (Iliad). Il a révolutionné le marché des télécoms en France avec des offres low-cost. Investisseur prolifique, il a créé la Station F (plus grand campus de startups au monde) et l\'École 42 (formation gratuite en informatique). Il investit dans de nombreuses startups tech.',
        'email' => 'xavier@free.fr'
    ],
    'Mbappé' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Kylian_Mbapp%C3%A9_2018_%28cropped%29.jpg/220px-Kylian_Mbapp%C3%A9_2018_%28cropped%29.jpg',
        'biographie' => 'Kylian Mbappé Lottin est un footballeur international français. Champion du monde 2018 à 19 ans, il est considéré comme l\'un des meilleurs joueurs du monde. Attaquant du Real Madrid depuis 2024, il a marqué plus de 300 buts en carrière. Connu pour sa vitesse fulgurante et son efficacité, il a remporté 7 titres de champion de France avec le PSG.',
        'email' => 'kylian@mbappe.fr'
    ],
    'Squeezie' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Squeezie_2019.jpg/220px-Squeezie_2019.jpg',
        'biographie' => 'Lucas Hauchard, connu sous le pseudonyme Squeezie, est un vidéaste français. Avec plus de 18 millions d\'abonnés sur YouTube, il est le YouTubeur français le plus suivi. Spécialisé dans le gaming et le divertissement, il a diversifié ses activités : musique (Oxyz), événements (GP Explorer), et entrepreneuriat. Il a organisé plusieurs événements caritatifs majeurs.',
        'email' => 'lucas@squeezie.fr'
    ],
    'Macron' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/Emmanuel_Macron_%282019-10-09%29.jpg/220px-Emmanuel_Macron_%282019-10-09%29.jpg',
        'biographie' => 'Emmanuel Jean-Michel Frédéric Macron est un homme d\'État français, président de la République depuis 2017. Ancien banquier d\'affaires et ministre de l\'Économie, il a fondé le mouvement En Marche! en 2016. Plus jeune président de la Ve République à 39 ans, il a été réélu en 2022. Sa présidence est marquée par des réformes économiques et sociales controversées.',
        'email' => 'contact@elysee.fr'
    ],
    'Nolan' => [
        'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Christopher_Nolan_Cannes_2018.jpg/220px-Christopher_Nolan_Cannes_2018.jpg',
        'biographie' => 'Christopher Edward Nolan est un réalisateur, scénariste et producteur britanno-américain. Maître du cinéma à grand spectacle intelligent, il a réalisé "Inception", "Interstellar", la trilogie "The Dark Knight" et "Oppenheimer" (Oscar 2024). Ses films explorent le temps, la mémoire et l\'identité. Il a généré plus de 6 milliards de dollars au box-office mondial.',
        'email' => 'chris@nolan-films.com'
    ]
];

echo "Enrichissement des personnalités...\n";
$updated = 0;

foreach ($enrichments as $nom => $data) {
    $stmt = $pdo->prepare("
        UPDATE personnalite 
        SET photo = ?, 
            biographie = ?,
            email = ?,
            telephone = ?
        WHERE nom = ?
    ");
    
    $result = $stmt->execute([
        $data['photo'],
        $data['biographie'],
        $data['email'] ?? null,
        $data['telephone'] ?? null,
        $nom
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo "✓ $nom enrichi\n";
        $updated++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Personnalités enrichies: $updated\n";
echo "\n=== Terminé ! ===\n";
