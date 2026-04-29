<?php
/**
 * Correction des logos des autorités avec des URLs alternatives
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Correction des logos des autorités ===\n\n";

// Logos alternatifs (URLs directes et stables)
$logos = [
    'Ministère de l\'Intérieur' => 'https://www.interieur.gouv.fr/sites/minint/themes/custom/minint/logo.svg',
    'Mairie de Paris' => 'https://cdn.paris.fr/paris/2019/07/24/huge-e60e43a1a4e3e2e8c1e0e8e8e8e8e8e8.png',
    'Préfecture de Police de Paris' => 'https://www.prefecturedepolice.interieur.gouv.fr/sites/default/files/logo-pp.png',
    'Croix-Rouge Française' => 'https://www.croix-rouge.fr/var/site/storage/images/_aliases/max_width_1920/6/1/4/8/8416-1-fre-FR/Logo-CRf-2019-RVB.png',
    'Restos du Cœur' => 'https://www.restosducoeur.org/wp-content/themes/restos/img/logo.svg',
    'WWF France' => 'https://www.wwf.fr/themes/custom/wwf/logo.svg',
    'LVMH' => 'https://r.lvmh-static.com/uploads/2021/03/lvmh_logo.svg',
    'Total Energies' => 'https://totalenergies.com/themes/custom/totalenergies/logo.svg',
    'Airbus' => 'https://www.airbus.com/themes/custom/airbus_brand/logo.svg',
    'Banque de France' => 'https://www.banque-france.fr/themes/custom/banque_de_france/logo.svg',
    'BNP Paribas' => 'https://group.bnpparibas/themes/custom/bnpp_group/logo.svg',
    'AXA' => 'https://www.axa.com/etc/designs/axa-com/clientlib-site/img/logo-axa.svg',
    'Université Paris-Saclay' => 'https://www.universite-paris-saclay.fr/sites/default/files/media/2020-09/logo_ups_0.png',
    'HEC Paris' => 'https://www.hec.edu/themes/custom/hec_paris/logo.svg',
    'École Polytechnique' => 'https://www.polytechnique.edu/themes/custom/ecole_polytechnique/logo.svg',
    'AP-HP (Assistance Publique - Hôpitaux de Paris)' => 'https://www.aphp.fr/themes/custom/aphp/logo.svg',
    'Institut Pasteur' => 'https://www.pasteur.fr/themes/custom/pasteur/logo.png',
    'France Télévisions' => 'https://www.francetelevisions.fr/themes/custom/ftv_corporate/logo.svg',
    'Le Monde' => 'https://www.lemonde.fr/dist/assets/img/logos/lm-logo.svg',
    'Radio France' => 'https://www.radiofrance.fr/themes/custom/radiofrance/logo.svg',
    'UNESCO' => 'https://en.unesco.org/themes/custom/unesco/logo.svg',
    'OCDE' => 'https://www.oecd.org/themes/custom/oecd_theme/logo.svg',
    'Musée du Louvre' => 'https://www.louvre.fr/themes/custom/louvre/logo.svg',
    'Opéra National de Paris' => 'https://www.operadeparis.fr/themes/custom/opera/logo.svg',
    'Comédie-Française' => 'https://www.comedie-francaise.fr/themes/custom/comedie_francaise/logo.svg',
    'Paris Saint-Germain' => 'https://www.psg.fr/media/192063/psg-logo.png',
    'Fédération Française de Football' => 'https://www.fff.fr/common/bib_img/logo-fff.svg',
    'CNRS' => 'https://www.cnrs.fr/themes/custom/cnrs/logo.svg',
    'CEA' => 'https://www.cea.fr/PublishingImages/logo-cea.png'
];

$updated = 0;
$notFound = 0;

foreach ($logos as $nom => $logo) {
    $stmt = $pdo->prepare("UPDATE autorite SET logo = ? WHERE nom = ?");
    $result = $stmt->execute([$logo, $nom]);
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Logo mis à jour: $nom\n";
        $updated++;
    } else {
        echo "⚠ Non trouvé: $nom\n";
        $notFound++;
    }
}

echo "\n=== Résumé ===\n";
echo "✓ Logos mis à jour: $updated\n";
echo "⚠ Non trouvés: $notFound\n";
echo "\n💡 Note: Certains logos peuvent ne pas s'afficher si les sites bloquent l'accès externe.\n";
echo "   Vous pouvez les modifier manuellement via la page d'édition.\n";
echo "\n=== Terminé ! ===\n";
