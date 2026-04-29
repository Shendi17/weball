<?php
/**
 * Enrichissement des données des personnalités
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Enrichissement des Données Personnalités ===\n\n";

// Données enrichies pour chaque personnalité
$enrichments = [
    'Tesla' => [
        'date_deces' => '1943-01-07',
        'lieu_naissance' => 'Smiljan, Empire d\'Autriche (Croatie)',
        'lieu_deces' => 'New York, États-Unis',
        'cause_deces' => 'Thrombose coronaire',
        'profession' => 'Ingénieur, Inventeur, Physicien',
        'statut' => 'decede',
        'distinctions' => 'Médaille Edison (1916), Médaille John Scott (1934)',
        'oeuvres_principales' => json_encode(['Courant alternatif', 'Bobine Tesla', 'Moteur à induction', 'Radio', 'Rayon X']),
        'citations' => json_encode([
            'Le présent est à eux, mais le futur est à moi',
            'Si vous voulez découvrir les secrets de l\'univers, pensez en termes d\'énergie, de fréquence et de vibration'
        ]),
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Nikola_Tesla',
        'sources' => json_encode([
            'Tesla: Man Out of Time - Margaret Cheney',
            'My Inventions - Nikola Tesla (autobiographie)',
            'IEEE Tesla Biography'
        ]),
        'anecdotes' => 'Tesla était amoureux d\'un pigeon blanc. Il dormait 2h par jour. Il parlait 8 langues. Il visualisait ses inventions en 3D dans sa tête.',
        'influence' => 'Père de l\'électricité moderne. L\'unité de mesure du champ magnétique porte son nom. Elon Musk a nommé Tesla Motors en son honneur.'
    ],
    'Einstein' => [
        'date_deces' => '1955-04-18',
        'lieu_naissance' => 'Ulm, Allemagne',
        'lieu_deces' => 'Princeton, États-Unis',
        'cause_deces' => 'Anévrisme de l\'aorte abdominale',
        'profession' => 'Physicien théoricien',
        'statut' => 'decede',
        'distinctions' => 'Prix Nobel de Physique (1921), Médaille Copley (1925)',
        'oeuvres_principales' => json_encode(['Théorie de la relativité restreinte (1905)', 'E=mc²', 'Théorie de la relativité générale (1915)', 'Effet photoélectrique']),
        'citations' => json_encode([
            'L\'imagination est plus importante que le savoir',
            'La folie, c\'est de faire toujours la même chose et de s\'attendre à un résultat différent',
            'Deux choses sont infinies : l\'Univers et la bêtise humaine. Mais en ce qui concerne l\'Univers, je n\'en ai pas encore acquis la certitude absolue'
        ]),
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Albert_Einstein',
        'anecdotes' => 'Il a refusé la présidence d\'Israël en 1952. Son cerveau a été prélevé et étudié après sa mort. Il ne portait jamais de chaussettes.',
        'influence' => 'Révolution de la physique moderne. Père de la bombe atomique (indirectement). Icône de la science et du génie.'
    ],
    'Mbappé' => [
        'lieu_naissance' => 'Paris, France',
        'profession' => 'Footballeur professionnel',
        'statut' => 'vivant',
        'distinctions' => 'Coupe du Monde 2018, Ballon d\'Or 2018 (Kopa), Golden Boy 2017, 7x Champion de France',
        'oeuvres_principales' => json_encode(['Finale Coupe du Monde 2018 (1 but)', 'Finale Coupe du Monde 2022 (hat-trick)', 'Record PSG (256 buts)']),
        'site_web' => 'https://www.k.mbappe.com',
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Kylian_Mbappé',
        'reseaux_sociaux' => json_encode([
            'instagram' => 'https://www.instagram.com/k.mbappe',
            'twitter' => 'https://twitter.com/KMbappe'
        ]),
        'anecdotes' => 'Plus jeune joueur français champion du monde depuis Pelé. A reversé tous ses gains de la Coupe du Monde 2018 à une association caritative. Fan de Cristiano Ronaldo enfant.',
        'influence' => 'Nouvelle génération du football mondial. Ambassadeur de la jeunesse française. Icône marketing (Nike, Hublot, EA Sports).'
    ],
    'Musk' => [
        'lieu_naissance' => 'Pretoria, Afrique du Sud',
        'profession' => 'Entrepreneur, Ingénieur, Inventeur',
        'statut' => 'vivant',
        'distinctions' => 'Personne de l\'année Time (2021), Membre de la Royal Society',
        'oeuvres_principales' => json_encode(['Tesla (voitures électriques)', 'SpaceX (fusées réutilisables)', 'Starlink (internet satellite)', 'Neuralink (interface cerveau-machine)', 'The Boring Company']),
        'citations' => json_encode([
            'Quand quelque chose est assez important, vous le faites même si les probabilités ne sont pas en votre faveur',
            'Je préfère être optimiste et me tromper plutôt que pessimiste et avoir raison'
        ]),
        'site_web' => 'https://www.tesla.com',
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Elon_Musk',
        'reseaux_sociaux' => json_encode([
            'twitter' => 'https://twitter.com/elonmusk',
            'instagram' => 'https://www.instagram.com/elonmusk'
        ]),
        'anecdotes' => 'A vendu son premier jeu vidéo à 12 ans pour 500$. Dort dans l\'usine Tesla. A acheté Twitter pour 44 milliards $ en 2022. Veut coloniser Mars.',
        'influence' => 'Révolution de l\'automobile électrique et de l\'aérospatiale. Homme le plus riche du monde (2021-2023). Figure controversée mais visionnaire.'
    ],
    'Napoléon Bonaparte' => [
        'date_deces' => '1821-05-05',
        'lieu_naissance' => 'Ajaccio, Corse',
        'lieu_deces' => 'Sainte-Hélène',
        'cause_deces' => 'Cancer de l\'estomac (probable empoisonnement à l\'arsenic)',
        'profession' => 'Militaire, Homme d\'État',
        'statut' => 'decede',
        'distinctions' => 'Empereur des Français, Roi d\'Italie, Protecteur de la Confédération du Rhin',
        'oeuvres_principales' => json_encode(['Code Civil (1804)', 'Victoire d\'Austerlitz (1805)', 'Concordat (1801)', 'Banque de France', 'Légion d\'honneur']),
        'citations' => json_encode([
            'Impossible n\'est pas français',
            'Du sublime au ridicule, il n\'y a qu\'un pas',
            'Un bon croquis vaut mieux qu\'un long discours'
        ]),
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Napoléon_Ier',
        'anecdotes' => 'Mesurait 1m68 (taille moyenne pour l\'époque). A été couronné empereur par lui-même. Sa main dans le gilet était une pose classique de l\'époque.',
        'influence' => 'Réforme profonde de la France et de l\'Europe. Le Code Civil est encore en vigueur. Modèle de génie militaire étudié dans toutes les académies.'
    ],
    'Cléopâtre VII' => [
        'date_deces' => '-0030-08-12',
        'lieu_naissance' => 'Alexandrie, Égypte',
        'lieu_deces' => 'Alexandrie, Égypte',
        'cause_deces' => 'Suicide (morsure d\'aspic selon la légende)',
        'profession' => 'Reine, Pharaon',
        'statut' => 'decede',
        'oeuvres_principales' => json_encode(['Alliance avec Jules César', 'Alliance avec Marc Antoine', 'Dernière dynastie ptolémaïque']),
        'citations' => json_encode([
            'Je ne serai pas exposée dans un triomphe romain'
        ]),
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Cléopâtre_VII',
        'anecdotes' => 'Parlait 9 langues dont l\'égyptien (rare pour les Ptolémées). Sa beauté était moyenne selon Plutarque, mais son intelligence et son charme exceptionnels. Elle s\'est fait livrer à César enroulée dans un tapis.',
        'influence' => 'Symbole de la femme de pouvoir et de séduction. Inspiratrice d\'innombrables œuvres (Shakespeare, Hollywood). Dernière pharaon d\'Égypte avant la conquête romaine.'
    ],
    'Jeanne d\'Arc' => [
        'date_deces' => '1431-05-30',
        'lieu_naissance' => 'Domrémy, France',
        'lieu_deces' => 'Rouen, France',
        'cause_deces' => 'Brûlée vive pour hérésie',
        'profession' => 'Chef militaire, Sainte',
        'statut' => 'decede',
        'distinctions' => 'Canonisée en 1920, Sainte patronne de la France',
        'oeuvres_principales' => json_encode(['Libération d\'Orléans (1429)', 'Sacre de Charles VII à Reims', 'Symbole de la Résistance française']),
        'citations' => json_encode([
            'Je ne crains rien, car Dieu est avec moi',
            'Les hommes d\'armes batailleront et Dieu donnera la victoire'
        ]),
        'wikipedia_url' => 'https://fr.wikipedia.org/wiki/Jeanne_d%27Arc',
        'anecdotes' => 'Analphabète, elle dictait ses lettres. A reconnu le Dauphin déguisé parmi sa cour. Son procès a été révisé 25 ans après sa mort et elle a été réhabilitée.',
        'influence' => 'Héroïne nationale française. Symbole de courage et de foi. Inspiratrice de mouvements patriotiques et féministes. Figure de la résistance contre l\'occupation.'
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
        echo "✓ $nom enrichi\n";
        $updated++;
    } else {
        echo "⚠ $nom non trouvé ou déjà à jour\n";
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Personnalités enrichies: $updated\n";
echo "\n=== Terminé ! ===\n";
