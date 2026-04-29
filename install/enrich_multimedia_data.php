<?php
/**
 * Enrichissement avec ressources multimédias réelles
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Enrichissement Multimédias ===\n\n";

$enrichments = [
    'Musk' => [
        'galerie_photos' => json_encode([
            'https://upload.wikimedia.org/wikipedia/commons/3/34/Elon_Musk_Royal_Society_%28crop2%29.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/Elon_Musk_2015.jpg/440px-Elon_Musk_2015.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Elon_Musk_2022.jpg/440px-Elon_Musk_2022.jpg'
        ]),
        'videos' => json_encode([
            ['titre' => 'Elon Musk: The future we\'re building', 'url' => 'https://www.youtube.com/watch?v=zIwLWfaAg-8', 'type' => 'TED Talk'],
            ['titre' => 'Starship Flight Test', 'url' => 'https://www.youtube.com/watch?v=L1otVXp94Qk', 'type' => 'SpaceX'],
            ['titre' => 'Tesla Cybertruck Reveal', 'url' => 'https://www.youtube.com/watch?v=m7atGkba-Z8', 'type' => 'Tesla']
        ]),
        'documentaires' => json_encode([
            ['titre' => 'Return to Space', 'annee' => 2022, 'plateforme' => 'Netflix'],
            ['titre' => 'Elon Musk: The Real Life Iron Man', 'annee' => 2018, 'plateforme' => 'YouTube']
        ]),
        'livres' => json_encode([
            ['titre' => 'Elon Musk: Tesla, SpaceX, and the Quest for a Fantastic Future', 'auteur' => 'Ashlee Vance', 'annee' => 2015],
            ['titre' => 'Power Play: Tesla, Elon Musk, and the Bet of the Century', 'auteur' => 'Tim Higgins', 'annee' => 2021]
        ]),
        'produits_associes' => json_encode([
            ['nom' => 'Tesla Model S', 'type' => 'Voiture électrique', 'annee' => 2012],
            ['nom' => 'Tesla Model 3', 'type' => 'Voiture électrique', 'annee' => 2017],
            ['nom' => 'Tesla Cybertruck', 'type' => 'Pick-up électrique', 'annee' => 2023],
            ['nom' => 'Starlink', 'type' => 'Internet satellite', 'annee' => 2019],
            ['nom' => 'Falcon 9', 'type' => 'Fusée réutilisable', 'annee' => 2010],
            ['nom' => 'Starship', 'type' => 'Vaisseau spatial', 'annee' => 2023]
        ]),
        'lieux_associes' => json_encode([
            ['nom' => 'Gigafactory Texas', 'ville' => 'Austin, Texas', 'description' => 'Usine Tesla'],
            ['nom' => 'SpaceX Starbase', 'ville' => 'Boca Chica, Texas', 'description' => 'Site de lancement Starship'],
            ['nom' => 'Tesla Fremont Factory', 'ville' => 'Fremont, Californie', 'description' => 'Usine principale Tesla']
        ]),
        'fondations' => json_encode([
            ['nom' => 'Musk Foundation', 'annee' => 2002, 'mission' => 'Éducation, énergie renouvelable, recherche spatiale'],
            ['nom' => 'X Prize Foundation', 'role' => 'Donateur majeur', 'domaine' => 'Innovation technologique']
        ]),
        'timeline' => json_encode([
            ['annee' => 1995, 'evenement' => 'Fonde Zip2 avec son frère'],
            ['annee' => 1999, 'evenement' => 'Vend Zip2 pour 307M$, fonde X.com (futur PayPal)'],
            ['annee' => 2002, 'evenement' => 'Fonde SpaceX'],
            ['annee' => 2004, 'evenement' => 'Investit dans Tesla Motors'],
            ['annee' => 2008, 'evenement' => 'Devient CEO de Tesla'],
            ['annee' => 2015, 'evenement' => 'Fonde OpenAI'],
            ['annee' => 2016, 'evenement' => 'Fonde Neuralink et The Boring Company'],
            ['annee' => 2020, 'evenement' => 'Premier vol habité SpaceX vers l\'ISS'],
            ['annee' => 2021, 'evenement' => 'Devient l\'homme le plus riche du monde'],
            ['annee' => 2022, 'evenement' => 'Rachète Twitter pour 44 milliards $']
        ])
    ],
    
    'Mbappé' => [
        'galerie_photos' => json_encode([
            'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Kylian_Mbapp%C3%A9_2018_%28cropped%29.jpg/440px-Kylian_Mbapp%C3%A9_2018_%28cropped%29.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Kylian_Mbapp%C3%A9_2019.jpg/440px-Kylian_Mbapp%C3%A9_2019.jpg'
        ]),
        'videos' => json_encode([
            ['titre' => 'Mbappé - All 44 Goals for PSG 2022/23', 'url' => 'https://www.youtube.com/watch?v=example1', 'type' => 'Highlights'],
            ['titre' => 'Kylian Mbappé - The Complete Forward', 'url' => 'https://www.youtube.com/watch?v=example2', 'type' => 'Compilation'],
            ['titre' => 'Finale Coupe du Monde 2022 - Hat-trick', 'url' => 'https://www.youtube.com/watch?v=example3', 'type' => 'Match']
        ]),
        'produits_associes' => json_encode([
            ['nom' => 'Nike Mercurial Mbappé', 'type' => 'Chaussures de foot', 'marque' => 'Nike'],
            ['nom' => 'Hublot Big Bang Mbappé', 'type' => 'Montre', 'marque' => 'Hublot'],
            ['nom' => 'EA Sports FC 24', 'type' => 'Jeu vidéo', 'role' => 'Ambassadeur']
        ]),
        'lieux_associes' => json_encode([
            ['nom' => 'Stade Santiago Bernabéu', 'ville' => 'Madrid', 'description' => 'Stade du Real Madrid'],
            ['nom' => 'Parc des Princes', 'ville' => 'Paris', 'description' => 'Ancien stade (PSG)'],
            ['nom' => 'Bondy', 'ville' => 'Seine-Saint-Denis', 'description' => 'Ville natale, terrain d\'entraînement jeunesse']
        ]),
        'evenements' => json_encode([
            ['date' => '2018-07-15', 'titre' => 'Champion du Monde', 'description' => 'Victoire en finale contre la Croatie (4-2)'],
            ['date' => '2022-12-18', 'titre' => 'Finale Coupe du Monde', 'description' => 'Hat-trick en finale (défaite aux tirs au but)'],
            ['date' => '2024-06-03', 'titre' => 'Signature au Real Madrid', 'description' => 'Transfert libre du PSG']
        ]),
        'prix_recompenses' => json_encode([
            ['annee' => 2018, 'prix' => 'Coupe du Monde FIFA', 'categorie' => 'Meilleur jeune joueur'],
            ['annee' => 2018, 'prix' => 'Trophée Kopa', 'categorie' => 'Meilleur jeune joueur mondial'],
            ['annee' => 2021, 'prix' => 'Ligue 1', 'categorie' => 'Meilleur joueur'],
            ['annee' => 2022, 'prix' => 'Coupe du Monde FIFA', 'categorie' => 'Soulier d\'or (8 buts)']
        ]),
        'fondations' => json_encode([
            ['nom' => 'Inspired by KM', 'annee' => 2020, 'mission' => 'Aide aux enfants défavorisés, éducation par le sport']
        ])
    ],
    
    'Beyoncé' => [
        'galerie_photos' => json_encode([
            'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Beyonce_-_Tottenham_Hotspur_Stadium_-_1st_June_2023_%2816_crop%29.jpg/440px-Beyonce_-_Tottenham_Hotspur_Stadium_-_1st_June_2023_%2816_crop%29.jpg'
        ]),
        'videos' => json_encode([
            ['titre' => 'Beyoncé - Homecoming (Netflix)', 'url' => 'https://www.netflix.com/title/81013626', 'type' => 'Documentaire'],
            ['titre' => 'Formation (Official Video)', 'url' => 'https://www.youtube.com/watch?v=WDZJPJV__bQ', 'type' => 'Clip'],
            ['titre' => 'Crazy in Love ft. Jay-Z', 'url' => 'https://www.youtube.com/watch?v=ViwtNLUqkMY', 'type' => 'Clip']
        ]),
        'documentaires' => json_encode([
            ['titre' => 'Homecoming: A Film by Beyoncé', 'annee' => 2019, 'plateforme' => 'Netflix'],
            ['titre' => 'Life Is But a Dream', 'annee' => 2013, 'plateforme' => 'HBO'],
            ['titre' => 'Black Is King', 'annee' => 2020, 'plateforme' => 'Disney+']
        ]),
        'produits_associes' => json_encode([
            ['nom' => 'Ivy Park', 'type' => 'Ligne de vêtements', 'partenaire' => 'Adidas'],
            ['nom' => 'Cécred', 'type' => 'Ligne de soins capillaires', 'annee' => 2024],
            ['nom' => 'House of Deréon', 'type' => 'Marque de mode', 'annee' => 2006]
        ]),
        'evenements' => json_encode([
            ['date' => '2013-02-03', 'titre' => 'Super Bowl XLVII', 'description' => 'Performance mi-temps'],
            ['date' => '2018-04-14', 'titre' => 'Coachella', 'description' => 'Performance historique "Beychella"'],
            ['date' => '2023-02-05', 'titre' => 'Grammy Awards', 'description' => '32e Grammy (record absolu)']
        ]),
        'prix_recompenses' => json_encode([
            ['annee' => 2023, 'prix' => 'Grammy Awards', 'total' => '32 Grammys (record)'],
            ['annee' => 2016, 'prix' => 'MTV Video Music Awards', 'categorie' => 'Video Vanguard Award'],
            ['annee' => 2019, 'prix' => 'NAACP Image Awards', 'categorie' => 'Entertainer of the Year']
        ]),
        'collaborations' => json_encode([
            ['artiste' => 'Jay-Z', 'projet' => 'Everything Is Love', 'annee' => 2018],
            ['artiste' => 'Destiny\'s Child', 'projet' => 'Groupe', 'periode' => '1997-2006'],
            ['artiste' => 'Shakira', 'projet' => 'Beautiful Liar', 'annee' => 2007]
        ])
    ],
    
    'Tesla' => [
        'galerie_photos' => json_encode([
            'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/N.Tesla.JPG/440px-N.Tesla.JPG',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Tesla_colorado_adjusted.jpg/440px-Tesla_colorado_adjusted.jpg'
        ]),
        'videos' => json_encode([
            ['titre' => 'The Genius of Nikola Tesla', 'url' => 'https://www.youtube.com/watch?v=example', 'type' => 'Documentaire'],
            ['titre' => 'Tesla Coil Demonstration', 'url' => 'https://www.youtube.com/watch?v=example2', 'type' => 'Expérience']
        ]),
        'documentaires' => json_encode([
            ['titre' => 'Tesla: Master of Lightning', 'annee' => 2000, 'plateforme' => 'PBS'],
            ['titre' => 'The Secret of Nikola Tesla', 'annee' => 1980, 'realisateur' => 'Krsto Papić']
        ]),
        'livres' => json_encode([
            ['titre' => 'My Inventions', 'auteur' => 'Nikola Tesla', 'annee' => 1919, 'type' => 'Autobiographie'],
            ['titre' => 'Tesla: Man Out of Time', 'auteur' => 'Margaret Cheney', 'annee' => 1981],
            ['titre' => 'Wizard: The Life and Times of Nikola Tesla', 'auteur' => 'Marc Seifer', 'annee' => 1996]
        ]),
        'produits_associes' => json_encode([
            ['nom' => 'Bobine Tesla', 'type' => 'Invention', 'annee' => 1891],
            ['nom' => 'Moteur à induction', 'type' => 'Invention', 'annee' => 1887],
            ['nom' => 'Système de courant alternatif', 'type' => 'Technologie', 'annee' => 1888],
            ['nom' => 'Tesla Motors', 'type' => 'Entreprise nommée en son honneur', 'annee' => 2003]
        ]),
        'lieux_associes' => json_encode([
            ['nom' => 'Musée Nikola Tesla', 'ville' => 'Belgrade, Serbie', 'description' => 'Musée dédié, contient ses cendres'],
            ['nom' => 'Wardenclyffe Tower', 'ville' => 'Long Island, NY', 'description' => 'Laboratoire de transmission sans fil'],
            ['nom' => 'Colorado Springs Laboratory', 'ville' => 'Colorado Springs', 'description' => 'Expériences haute tension (1899)']
        ]),
        'musees_expositions' => json_encode([
            ['nom' => 'Nikola Tesla Museum', 'ville' => 'Belgrade', 'pays' => 'Serbie'],
            ['nom' => 'Tesla Science Center', 'ville' => 'Wardenclyffe, NY', 'pays' => 'USA'],
            ['nom' => 'Exposition permanente', 'lieu' => 'Smithsonian Institution', 'ville' => 'Washington DC']
        ]),
        'controverses' => json_encode([
            ['sujet' => 'Guerre des courants', 'adversaire' => 'Thomas Edison', 'periode' => '1880-1890', 'issue' => 'Victoire du courant alternatif'],
            ['sujet' => 'Invention de la radio', 'conflit' => 'Marconi vs Tesla', 'resolution' => 'Cour suprême reconnaît Tesla (1943)'],
            ['sujet' => 'Rayon de la mort', 'description' => 'Arme à énergie dirigée jamais réalisée']
        ])
    ],
    
    'Napoléon Bonaparte' => [
        'galerie_photos' => json_encode([
            'https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Jacques-Louis_David_-_The_Emperor_Napoleon_in_His_Study_at_the_Tuileries_-_Google_Art_Project.jpg/440px-Jacques-Louis_David_-_The_Emperor_Napoleon_in_His_Study_at_the_Tuileries_-_Google_Art_Project.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Napoleon_on_his_Imperial_throne.jpg/440px-Napoleon_on_his_Imperial_throne.jpg'
        ]),
        'documentaires' => json_encode([
            ['titre' => 'Napoléon', 'annee' => 2023, 'realisateur' => 'Ridley Scott', 'type' => 'Film'],
            ['titre' => 'Napoléon, la destinée et la mort', 'annee' => 2021, 'plateforme' => 'Arte']
        ]),
        'livres' => json_encode([
            ['titre' => 'Napoléon', 'auteur' => 'Andrew Roberts', 'annee' => 2014],
            ['titre' => 'Napoléon Bonaparte', 'auteur' => 'Jean Tulard', 'annee' => 1977],
            ['titre' => 'Le Mémorial de Sainte-Hélène', 'auteur' => 'Emmanuel de Las Cases', 'annee' => 1823]
        ]),
        'lieux_associes' => json_encode([
            ['nom' => 'Les Invalides', 'ville' => 'Paris', 'description' => 'Tombeau de Napoléon'],
            ['nom' => 'Château de Fontainebleau', 'description' => 'Résidence impériale, lieu d\'abdication'],
            ['nom' => 'Sainte-Hélène', 'description' => 'Île d\'exil et lieu de décès'],
            ['nom' => 'Austerlitz', 'pays' => 'République tchèque', 'description' => 'Victoire légendaire (1805)']
        ]),
        'musees_expositions' => json_encode([
            ['nom' => 'Musée de l\'Armée', 'ville' => 'Paris', 'description' => 'Collection napoléonienne'],
            ['nom' => 'Musée Napoléon', 'ville' => 'Fontainebleau'],
            ['nom' => 'Maison Bonaparte', 'ville' => 'Ajaccio', 'description' => 'Maison natale']
        ]),
        'evenements' => json_encode([
            ['date' => '1799-11-09', 'titre' => 'Coup d\'État du 18 Brumaire', 'description' => 'Prise du pouvoir'],
            ['date' => '1804-12-02', 'titre' => 'Sacre', 'description' => 'Couronné Empereur à Notre-Dame'],
            ['date' => '1805-12-02', 'titre' => 'Bataille d\'Austerlitz', 'description' => 'Victoire décisive'],
            ['date' => '1815-06-18', 'titre' => 'Bataille de Waterloo', 'description' => 'Défaite finale']
        ]),
        'heritage_culturel' => json_encode([
            ['domaine' => 'Droit', 'heritage' => 'Code Civil (Code Napoléon)', 'impact' => 'Base du droit civil dans 70+ pays'],
            ['domaine' => 'Éducation', 'heritage' => 'Lycées', 'impact' => 'Système éducatif français moderne'],
            ['domaine' => 'Administration', 'heritage' => 'Préfets, départements', 'impact' => 'Organisation territoriale'],
            ['domaine' => 'Économie', 'heritage' => 'Banque de France', 'impact' => 'Système bancaire central']
        ])
    ]
];

$updated = 0;
foreach ($enrichments as $nom => $data) {
    $sql = "UPDATE personnalite SET ";
    $params = [];
    $sets = [];
    
    foreach ($data as $key => $value) {
        $sets[] = "$key = ?";
        $params[] = $value;
    }
    
    $sql .= implode(', ', $sets);
    $sql .= " WHERE nom = ?";
    $params[] = $nom;
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result && $stmt->rowCount() > 0) {
        echo "✓ $nom enrichi avec ressources multimédias\n";
        $updated++;
    } else {
        echo "⚠ $nom non trouvé\n";
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Personnalités enrichies: $updated\n";
echo "\n=== Terminé ! ===\n";
