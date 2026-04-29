<?php
/**
 * Suppression des données de démo et ajout d'autorités réelles
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Nettoyage et Ajout d'Autorités Réelles ===\n\n";

// 1. Supprimer les données de démo
echo "Suppression des données de démo...\n";
$pdo->exec("DELETE FROM autorite");
echo "✓ Données de démo supprimées\n\n";

// 2. Récupérer les types et catégories
$typesData = $pdo->query("SELECT id, slug FROM autorite_types")->fetchAll(PDO::FETCH_ASSOC);
$types = [];
foreach ($typesData as $type) {
    $types[$type['slug']] = $type['id'];
}

$categoriesData = $pdo->query("SELECT id, slug, type_id FROM autorite_categories")->fetchAll(PDO::FETCH_ASSOC);
$categories = [];
foreach ($categoriesData as $cat) {
    $categories[$cat['slug']] = $cat['id'];
}

// 3. Autorités réelles à ajouter
$autorites = [
    // ORGANISMES PUBLICS
    [
        'nom' => 'Ministère de l\'Intérieur',
        'type' => 'organisme_public',
        'category' => 'ministere',
        'description' => 'Ministère chargé de la sécurité intérieure, des collectivités territoriales et de l\'immigration en France.',
        'adresse' => 'Place Beauvau, 75008 Paris',
        'telephone' => '01 49 27 49 27',
        'site_web' => 'https://www.interieur.gouv.fr',
        'email' => 'contact@interieur.gouv.fr'
    ],
    [
        'nom' => 'Mairie de Paris',
        'type' => 'organisme_public',
        'category' => 'mairie',
        'description' => 'Administration municipale de la ville de Paris, gestion des services publics locaux.',
        'adresse' => '5 Rue de Lobau, 75004 Paris',
        'telephone' => '01 42 76 40 40',
        'site_web' => 'https://www.paris.fr',
        'email' => 'information@paris.fr'
    ],
    [
        'nom' => 'Préfecture de Police de Paris',
        'type' => 'organisme_public',
        'category' => 'prefecture',
        'description' => 'Institution chargée de la sécurité publique à Paris et dans la petite couronne.',
        'adresse' => '9 Boulevard du Palais, 75004 Paris',
        'telephone' => '01 53 71 53 71',
        'site_web' => 'https://www.prefecturedepolice.interieur.gouv.fr'
    ],
    
    // ASSOCIATIONS
    [
        'nom' => 'Croix-Rouge Française',
        'type' => 'association',
        'category' => 'association_humanitaire',
        'description' => 'Association humanitaire française, membre du Mouvement international de la Croix-Rouge et du Croissant-Rouge.',
        'adresse' => '98 Rue Didot, 75694 Paris Cedex 14',
        'telephone' => '01 44 43 11 00',
        'site_web' => 'https://www.croix-rouge.fr',
        'email' => 'contact@croix-rouge.fr'
    ],
    [
        'nom' => 'Restos du Cœur',
        'type' => 'association',
        'category' => 'association_caritative',
        'description' => 'Association loi 1901, reconnue d\'utilité publique, créée par Coluche en 1985 pour aider les personnes démunies.',
        'adresse' => '37 Rue Bergère, 75009 Paris',
        'telephone' => '01 53 32 23 23',
        'site_web' => 'https://www.restosducoeur.org',
        'email' => 'contact@restosducoeur.org'
    ],
    [
        'nom' => 'WWF France',
        'type' => 'association',
        'category' => 'association_environnementale',
        'description' => 'Organisation mondiale de protection de la nature, présente en France depuis 1973.',
        'adresse' => '35-37 Rue Baudin, 93310 Le Pré-Saint-Gervais',
        'telephone' => '01 55 25 84 84',
        'site_web' => 'https://www.wwf.fr',
        'email' => 'info@wwf.fr'
    ],
    
    // GRANDES ENTREPRISES
    [
        'nom' => 'LVMH',
        'type' => 'grande_entreprise',
        'category' => 'cac_40',
        'description' => 'Leader mondial du luxe, regroupant 75 maisons prestigieuses dans les secteurs de la mode, maroquinerie, parfums, vins et spiritueux.',
        'adresse' => '22 Avenue Montaigne, 75008 Paris',
        'telephone' => '01 44 13 22 22',
        'site_web' => 'https://www.lvmh.fr',
        'email' => 'contact@lvmh.fr'
    ],
    [
        'nom' => 'Total Energies',
        'type' => 'grande_entreprise',
        'category' => 'cac_40',
        'description' => 'Compagnie multi-énergies mondiale, producteur et fournisseur d\'énergies : pétrole, gaz naturel, électricité renouvelable.',
        'adresse' => '2 Place Jean Millier, 92400 Courbevoie',
        'telephone' => '01 47 44 45 46',
        'site_web' => 'https://www.totalenergies.fr'
    ],
    [
        'nom' => 'Airbus',
        'type' => 'grande_entreprise',
        'category' => 'multinationale',
        'description' => 'Leader mondial de l\'aéronautique, conception et fabrication d\'avions commerciaux, hélicoptères, avions militaires et satellites.',
        'adresse' => '2 Rond-Point Émile Dewoitine, 31700 Blagnac',
        'telephone' => '05 61 93 33 33',
        'site_web' => 'https://www.airbus.com'
    ],
    
    // INSTITUTIONS FINANCIÈRES
    [
        'nom' => 'Banque de France',
        'type' => 'institution_financiere',
        'category' => 'banque_centrale',
        'description' => 'Banque centrale de la France, membre de l\'Eurosystème. Missions : politique monétaire, stabilité financière, services à l\'économie.',
        'adresse' => '31 Rue Croix des Petits Champs, 75001 Paris',
        'telephone' => '01 42 92 42 92',
        'site_web' => 'https://www.banque-france.fr'
    ],
    [
        'nom' => 'BNP Paribas',
        'type' => 'institution_financiere',
        'category' => 'banque_commerciale',
        'description' => 'Première banque de la zone euro, services bancaires et financiers dans 65 pays.',
        'adresse' => '16 Boulevard des Italiens, 75009 Paris',
        'telephone' => '01 40 14 45 46',
        'site_web' => 'https://www.bnpparibas.fr'
    ],
    [
        'nom' => 'AXA',
        'type' => 'institution_financiere',
        'category' => 'compagnie_dassurance',
        'description' => 'Leader mondial de l\'assurance et de la gestion d\'actifs, protection des personnes, biens et actifs.',
        'adresse' => '25 Avenue Matignon, 75008 Paris',
        'telephone' => '01 40 75 57 00',
        'site_web' => 'https://www.axa.fr'
    ],
    
    // ÉTABLISSEMENTS D'ENSEIGNEMENT
    [
        'nom' => 'Université Paris-Saclay',
        'type' => 'etablissement_enseignement',
        'category' => 'universite',
        'description' => 'Université de recherche française, classée parmi les meilleures universités mondiales.',
        'adresse' => 'Rue André Ampère, 91400 Orsay',
        'telephone' => '01 69 15 78 00',
        'site_web' => 'https://www.universite-paris-saclay.fr'
    ],
    [
        'nom' => 'HEC Paris',
        'type' => 'etablissement_enseignement',
        'category' => 'ecole_de_commerce',
        'description' => 'Grande école de commerce française, leader européen de la formation en management.',
        'adresse' => '1 Rue de la Libération, 78350 Jouy-en-Josas',
        'telephone' => '01 39 67 70 00',
        'site_web' => 'https://www.hec.edu'
    ],
    [
        'nom' => 'École Polytechnique',
        'type' => 'etablissement_enseignement',
        'category' => 'ecole_dingenieurs',
        'description' => 'Grande école d\'ingénieurs française, formation scientifique et technologique de haut niveau.',
        'adresse' => 'Route de Saclay, 91128 Palaiseau',
        'telephone' => '01 69 33 33 33',
        'site_web' => 'https://www.polytechnique.edu'
    ],
    
    // ÉTABLISSEMENTS DE SANTÉ
    [
        'nom' => 'AP-HP (Assistance Publique - Hôpitaux de Paris)',
        'type' => 'etablissement_sante',
        'category' => 'hopital_public',
        'description' => 'Premier centre hospitalier universitaire d\'Europe, 39 hôpitaux à Paris et en Île-de-France.',
        'adresse' => '3 Avenue Victoria, 75004 Paris',
        'telephone' => '01 40 27 30 00',
        'site_web' => 'https://www.aphp.fr'
    ],
    [
        'nom' => 'Institut Pasteur',
        'type' => 'etablissement_sante',
        'category' => 'laboratoire_medical',
        'description' => 'Fondation française de recherche biomédicale, leader mondial dans la lutte contre les maladies infectieuses.',
        'adresse' => '25-28 Rue du Docteur Roux, 75015 Paris',
        'telephone' => '01 45 68 80 00',
        'site_web' => 'https://www.pasteur.fr'
    ],
    
    // MÉDIAS
    [
        'nom' => 'France Télévisions',
        'type' => 'media_communication',
        'category' => 'chaine_de_television',
        'description' => 'Groupe audiovisuel public français, regroupe France 2, France 3, France 4, France 5 et France Ô.',
        'adresse' => '7 Esplanade Henri de France, 75907 Paris',
        'telephone' => '01 56 22 60 00',
        'site_web' => 'https://www.francetelevisions.fr'
    ],
    [
        'nom' => 'Le Monde',
        'type' => 'media_communication',
        'category' => 'journal',
        'description' => 'Quotidien français fondé en 1944, journal de référence en France et dans le monde francophone.',
        'adresse' => '67-69 Avenue Pierre Mendès France, 75013 Paris',
        'telephone' => '01 57 28 20 00',
        'site_web' => 'https://www.lemonde.fr'
    ],
    [
        'nom' => 'Radio France',
        'type' => 'media_communication',
        'category' => 'radio',
        'description' => 'Groupe radiophonique public français : France Inter, France Culture, France Musique, FIP, Mouv\'.',
        'adresse' => '116 Avenue du Président Kennedy, 75220 Paris',
        'telephone' => '01 56 40 22 22',
        'site_web' => 'https://www.radiofrance.fr'
    ],
    
    // ORGANISATIONS INTERNATIONALES
    [
        'nom' => 'UNESCO',
        'type' => 'organisation_internationale',
        'category' => 'unesco',
        'description' => 'Organisation des Nations Unies pour l\'éducation, la science et la culture. Siège à Paris.',
        'adresse' => '7 Place de Fontenoy, 75007 Paris',
        'telephone' => '01 45 68 10 00',
        'site_web' => 'https://www.unesco.org'
    ],
    [
        'nom' => 'OCDE',
        'type' => 'organisation_internationale',
        'category' => 'ocde',
        'description' => 'Organisation de coopération et de développement économiques, 38 pays membres.',
        'adresse' => '2 Rue André Pascal, 75016 Paris',
        'telephone' => '01 45 24 82 00',
        'site_web' => 'https://www.oecd.org'
    ],
    
    // ORGANISATIONS CULTURELLES
    [
        'nom' => 'Musée du Louvre',
        'type' => 'organisation_culturelle',
        'category' => 'musee',
        'description' => 'Plus grand musée d\'art et d\'antiquités au monde, situé à Paris. Plus de 10 millions de visiteurs par an.',
        'adresse' => 'Rue de Rivoli, 75001 Paris',
        'telephone' => '01 40 20 50 50',
        'site_web' => 'https://www.louvre.fr'
    ],
    [
        'nom' => 'Opéra National de Paris',
        'type' => 'organisation_culturelle',
        'category' => 'opera',
        'description' => 'Institution culturelle française, gère l\'Opéra Garnier et l\'Opéra Bastille.',
        'adresse' => 'Place de l\'Opéra, 75009 Paris',
        'telephone' => '01 71 25 24 23',
        'site_web' => 'https://www.operadeparis.fr'
    ],
    [
        'nom' => 'Comédie-Française',
        'type' => 'organisation_culturelle',
        'category' => 'theatre',
        'description' => 'Théâtre national français, fondé en 1680, plus ancienne institution théâtrale française encore active.',
        'adresse' => '1 Place Colette, 75001 Paris',
        'telephone' => '01 44 58 15 15',
        'site_web' => 'https://www.comedie-francaise.fr'
    ],
    
    // ORGANISATIONS SPORTIVES
    [
        'nom' => 'Paris Saint-Germain',
        'type' => 'organisation_sportive',
        'category' => 'club_de_football',
        'description' => 'Club de football professionnel français, basé à Paris. Multiple champion de France.',
        'adresse' => '24 Rue du Commandant Guilbaud, 75016 Paris',
        'telephone' => '01 47 43 71 71',
        'site_web' => 'https://www.psg.fr'
    ],
    [
        'nom' => 'Fédération Française de Football',
        'type' => 'organisation_sportive',
        'category' => 'federation_sportive',
        'description' => 'Instance dirigeante du football en France, organise les compétitions nationales.',
        'adresse' => '87 Boulevard de Grenelle, 75015 Paris',
        'telephone' => '01 44 31 73 00',
        'site_web' => 'https://www.fff.fr'
    ],
    
    // ORGANISATIONS SCIENTIFIQUES
    [
        'nom' => 'CNRS',
        'type' => 'organisation_scientifique',
        'category' => 'cnrs',
        'description' => 'Centre National de la Recherche Scientifique, plus grand organisme public de recherche en France.',
        'adresse' => '3 Rue Michel-Ange, 75016 Paris',
        'telephone' => '01 44 96 40 00',
        'site_web' => 'https://www.cnrs.fr'
    ],
    [
        'nom' => 'CEA',
        'type' => 'organisation_scientifique',
        'category' => 'cea',
        'description' => 'Commissariat à l\'énergie atomique et aux énergies alternatives, recherche dans l\'énergie nucléaire et les nouvelles technologies.',
        'adresse' => '25 Rue Leblanc, 75015 Paris',
        'telephone' => '01 64 50 10 00',
        'site_web' => 'https://www.cea.fr'
    ]
];

// Insertion des autorités
$inserted = 0;
$errors = 0;

foreach ($autorites as $autorite) {
    try {
        // Récupérer le type_id
        $typeId = $types[$autorite['type']] ?? null;
        
        // Récupérer le category_id
        $categoryId = $categories[$autorite['category']] ?? null;
        
        if (!$typeId) {
            echo "✗ {$autorite['nom']} - Type '{$autorite['type']}' non trouvé\n";
            $errors++;
            continue;
        }
        
        // Construire la description complète avec toutes les infos
        $fullDescription = $autorite['description'] ?? '';
        if (!empty($autorite['adresse'])) {
            $fullDescription .= "\n\n📍 Adresse: " . $autorite['adresse'];
        }
        if (!empty($autorite['telephone'])) {
            $fullDescription .= "\n📞 Téléphone: " . $autorite['telephone'];
        }
        if (!empty($autorite['email'])) {
            $fullDescription .= "\n📧 Email: " . $autorite['email'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO autorite (nom, type_id, category_id, description, site_web, date_creation, user_id)
            VALUES (?, ?, ?, ?, ?, NOW(), 1)
        ");
        
        $stmt->execute([
            $autorite['nom'],
            $typeId,
            $categoryId,
            $fullDescription,
            $autorite['site_web'] ?? ''
        ]);
        
        $inserted++;
        echo "✓ {$autorite['nom']}\n";
        
    } catch (PDOException $e) {
        echo "✗ {$autorite['nom']} - Erreur: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Autorités insérées: $inserted\n";
echo "✗ Erreurs: $errors\n";
echo "\n=== Terminé ! ===\n";
