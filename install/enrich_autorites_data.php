<?php
/**
 * Enrichissement des données des autorités
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Enrichissement données autorités ===\n\n";

$enrichedData = [
    'Airbus' => [
        'adresse' => '2 Rond-Point Emile Dewoitine, 31700 Blagnac',
        'telephone' => '+33 5 61 93 33 33',
        'email' => 'contact@airbus.com',
        'effectifs' => '134 000 employés',
        'chiffre_affaires' => '58,8 milliards € (2022)',
        'date_creation_org' => '1970',
        'dirigeant' => 'Guillaume Faury (PDG)',
        'secteur' => 'Aéronautique et spatial',
        'description_longue' => 'Airbus est un constructeur aéronautique européen dont le siège social se situe à Blagnac, dans la banlieue de Toulouse, en France. Airbus est le leader mondial de l\'aéronautique, conception et fabrication d\'avions commerciaux, hélicoptères, avions militaires et satellites.',
        'missions' => 'Concevoir, fabriquer et livrer des produits, services et solutions aérospatiaux à des clients à l\'échelle mondiale.',
        'realisations' => 'A320, A350, A380, Hélicoptères H145, Satellites de télécommunication',
        'partenaires' => 'Boeing (concurrent), Safran, Thales, Dassault Aviation',
        'reseaux_sociaux' => json_encode([
            ['nom' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/airbus/'],
            ['nom' => 'Twitter', 'url' => 'https://twitter.com/Airbus'],
            ['nom' => 'Facebook', 'url' => 'https://www.facebook.com/Airbus']
        ])
    ],
    
    'LVMH' => [
        'adresse' => '22 avenue Montaigne, 75008 Paris',
        'telephone' => '+33 1 44 13 22 22',
        'email' => 'contact@lvmh.fr',
        'effectifs' => '213 000 employés',
        'chiffre_affaires' => '79,2 milliards € (2022)',
        'date_creation_org' => '1987',
        'dirigeant' => 'Bernard Arnault (PDG)',
        'secteur' => 'Luxe et mode',
        'description_longue' => 'LVMH Moët Hennessy Louis Vuitton, plus connu sous le nom de LVMH, est un groupe français d\'entreprises, leader mondial du luxe. Le groupe possède un portefeuille de plus de 75 marques prestigieuses.',
        'missions' => 'Être l\'ambassadeur de l\'art de vivre occidental en termes de produits et de services de haute qualité.',
        'realisations' => 'Louis Vuitton, Dior, Moët & Chandon, Hennessy, Sephora, TAG Heuer',
        'partenaires' => 'Kering (concurrent), Hermès, Richemont',
        'reseaux_sociaux' => json_encode([
            ['nom' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/lvmh/'],
            ['nom' => 'Instagram', 'url' => 'https://www.instagram.com/lvmh/']
        ])
    ],
    
    'BNP Paribas' => [
        'adresse' => '16 boulevard des Italiens, 75009 Paris',
        'telephone' => '+33 1 40 14 45 46',
        'email' => 'contact@bnpparibas.com',
        'effectifs' => '189 000 employés',
        'chiffre_affaires' => '46,2 milliards € (2022)',
        'date_creation_org' => '2000 (fusion BNP-Paribas)',
        'dirigeant' => 'Jean-Laurent Bonnafé (Directeur Général)',
        'secteur' => 'Banque et services financiers',
        'description_longue' => 'BNP Paribas est une banque française, leader européen des services bancaires et financiers. Le groupe est présent dans 65 pays avec plus de 189 000 collaborateurs.',
        'missions' => 'Accompagner tous ses clients (particuliers, entrepreneurs, PME, grandes entreprises et institutionnels) pour les aider à réaliser leurs projets.',
        'realisations' => 'Banque de détail en France, Europe et international, CIB (Corporate & Institutional Banking)',
        'partenaires' => 'Société Générale, Crédit Agricole, HSBC',
        'reseaux_sociaux' => json_encode([
            ['nom' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/bnpparibas/'],
            ['nom' => 'Twitter', 'url' => 'https://twitter.com/BNPParibas']
        ])
    ],
    
    'Institut Pasteur' => [
        'adresse' => '25-28 rue du Docteur Roux, 75015 Paris',
        'telephone' => '+33 1 45 68 80 00',
        'email' => 'communication@pasteur.fr',
        'effectifs' => '2 700 personnes',
        'chiffre_affaires' => '330 millions € (budget annuel)',
        'date_creation_org' => '1887',
        'dirigeant' => 'Yasmine Belkaid (Directrice générale)',
        'secteur' => 'Recherche biomédicale',
        'description_longue' => 'L\'Institut Pasteur est une fondation française privée à but non lucratif qui se consacre à l\'étude de la biologie, des micro-organismes, des maladies et des vaccins. Créé en 1887 par Louis Pasteur, l\'institut a été à l\'origine de découvertes révolutionnaires.',
        'missions' => 'Contribuer à la prévention et au traitement des maladies, en priorité infectieuses, par la recherche, l\'enseignement et des actions de santé publique.',
        'realisations' => 'Découverte du VIH, vaccins contre la rage, diphtérie, tétanos, tuberculose, poliomyélite',
        'partenaires' => 'INSERM, CNRS, Universités, OMS',
        'reseaux_sociaux' => json_encode([
            ['nom' => 'Twitter', 'url' => 'https://twitter.com/institutpasteur'],
            ['nom' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/institut-pasteur/']
        ])
    ],
    
    'CNRS' => [
        'adresse' => '3 rue Michel-Ange, 75016 Paris',
        'telephone' => '+33 1 44 96 40 00',
        'email' => 'communication@cnrs.fr',
        'effectifs' => '32 000 personnes',
        'chiffre_affaires' => '3,3 milliards € (budget annuel)',
        'date_creation_org' => '1939',
        'dirigeant' => 'Antoine Petit (PDG)',
        'secteur' => 'Recherche scientifique',
        'description_longue' => 'Le Centre national de la recherche scientifique est le plus grand organisme public français de recherche scientifique. Pluridisciplinaire, le CNRS couvre l\'ensemble des domaines scientifiques.',
        'missions' => 'Produire du savoir et mettre ce savoir au service de la société.',
        'realisations' => '20 Prix Nobel, Médaille Fields, recherches en physique, chimie, biologie, sciences humaines',
        'partenaires' => 'Universités françaises, INSERM, CEA, INRIA',
        'reseaux_sociaux' => json_encode([
            ['nom' => 'Twitter', 'url' => 'https://twitter.com/CNRS'],
            ['nom' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/cnrs/']
        ])
    ]
];

$updated = 0;

foreach ($enrichedData as $nom => $data) {
    $stmt = $pdo->prepare("
        UPDATE autorite 
        SET adresse = ?, telephone = ?, email = ?, effectifs = ?, chiffre_affaires = ?,
            date_creation_org = ?, dirigeant = ?, secteur = ?, description_longue = ?,
            missions = ?, realisations = ?, partenaires = ?, reseaux_sociaux = ?
        WHERE nom = ?
    ");
    
    if ($stmt->execute([
        $data['adresse'],
        $data['telephone'],
        $data['email'],
        $data['effectifs'],
        $data['chiffre_affaires'],
        $data['date_creation_org'],
        $data['dirigeant'],
        $data['secteur'],
        $data['description_longue'],
        $data['missions'],
        $data['realisations'],
        $data['partenaires'],
        $data['reseaux_sociaux'],
        $nom
    ])) {
        echo "✓ $nom enrichi\n";
        $updated++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Autorités enrichies: $updated\n";
echo "\n=== Terminé ! ===\n";
