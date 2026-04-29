<?php
/**
 * Ajout des logos aux autorités
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout des logos aux autorités ===\n\n";

// Logos des autorités (URLs publiques)
$logos = [
    'Ministère de l\'Intérieur' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/4/4b/Logo_Minist%C3%A8re_Int%C3%A9rieur_2020.svg/200px-Logo_Minist%C3%A8re_Int%C3%A9rieur_2020.svg.png',
    'Mairie de Paris' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/8/8c/Blason_paris_75.svg/200px-Blason_paris_75.svg.png',
    'Croix-Rouge Française' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/7/7b/Logo_Croix-Rouge_fran%C3%A7aise.svg/200px-Logo_Croix-Rouge_fran%C3%A7aise.svg.png',
    'Restos du Cœur' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/d/d0/Logo_Restos_du_C%C5%93ur.svg/200px-Logo_Restos_du_C%C5%93ur.svg.png',
    'WWF France' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/WWF_logo.svg/200px-WWF_logo.svg.png',
    'LVMH' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Logo_LVMH.svg/200px-Logo_LVMH.svg.png',
    'Total Energies' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/0/0d/Logo_TotalEnergies.svg/200px-Logo_TotalEnergies.svg.png',
    'Airbus' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Airbus_Logo_2017.svg/200px-Airbus_Logo_2017.svg.png',
    'Banque de France' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/4/49/Logo_Banque_de_France.svg/200px-Logo_Banque_de_France.svg.png',
    'BNP Paribas' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/BNP_Paribas.svg/200px-BNP_Paribas.svg.png',
    'AXA' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/AXA_Logo.svg/200px-AXA_Logo.svg.png',
    'Université Paris-Saclay' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/6/6d/Logo_Universit%C3%A9_Paris-Saclay.svg/200px-Logo_Universit%C3%A9_Paris-Saclay.svg.png',
    'HEC Paris' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/HEC_Paris.svg/200px-HEC_Paris.svg.png',
    'École Polytechnique' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e9/Logo_EP.svg/200px-Logo_EP.svg.png',
    'AP-HP (Assistance Publique - Hôpitaux de Paris)' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/8/8d/Logo_AP-HP_2019.svg/200px-Logo_AP-HP_2019.svg.png',
    'Institut Pasteur' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/e/e4/Logo_Institut_Pasteur.svg/200px-Logo_Institut_Pasteur.svg.png',
    'France Télévisions' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/France_T%C3%A9l%C3%A9visions_2018.svg/200px-France_T%C3%A9l%C3%A9visions_2018.svg.png',
    'Le Monde' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Le_Monde.svg/200px-Le_Monde.svg.png',
    'Radio France' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/3/34/Logo_Radio_France_2022.svg/200px-Logo_Radio_France_2022.svg.png',
    'UNESCO' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Logo_UNESCO.svg/200px-Logo_UNESCO.svg.png',
    'OCDE' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/OECD_logo.svg/200px-OECD_logo.svg.png',
    'Musée du Louvre' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/Louvre_Museum_Logo.svg/200px-Louvre_Museum_Logo.svg.png',
    'Opéra National de Paris' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/3/3e/Logo_Op%C3%A9ra_national_de_Paris.svg/200px-Logo_Op%C3%A9ra_national_de_Paris.svg.png',
    'Paris Saint-Germain' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/8/86/Paris_Saint-Germain_Logo.svg/200px-Paris_Saint-Germain_Logo.svg.png',
    'Fédération Française de Football' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/1/10/Logo_F%C3%A9d%C3%A9ration_Fran%C3%A7aise_Football_2018.svg/200px-Logo_F%C3%A9d%C3%A9ration_Fran%C3%A7aise_Football_2018.svg.png',
    'CNRS' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/b/b4/Logo_CNRS_2019.svg/200px-Logo_CNRS_2019.svg.png',
    'CEA' => 'https://upload.wikimedia.org/wikipedia/fr/thumb/b/b7/Logo_CEA.svg/200px-Logo_CEA.svg.png'
];

$updated = 0;
$notFound = 0;

foreach ($logos as $nom => $logo) {
    $stmt = $pdo->prepare("UPDATE autorite SET logo = ? WHERE nom = ?");
    $result = $stmt->execute([$logo, $nom]);
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Logo ajouté: $nom\n";
        $updated++;
    } else {
        echo "⚠ Non trouvé: $nom\n";
        $notFound++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Logos ajoutés: $updated\n";
echo "⚠ Non trouvés: $notFound\n";
echo "\n=== Terminé ! ===\n";
