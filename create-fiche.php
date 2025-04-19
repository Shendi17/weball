<?php
// Traitement backend pour la création d'une fiche dans n'importe quelle section (version PDO/MySQL)
require_once __DIR__ . '/config.db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['fiche_nom'] ?? '');
    $section = trim($_POST['fiche_section'] ?? '');
    $sections = [
        'adhesion','annonce','annuaire','archive','autorite','banque','boutique','campagne','carriere','catalogue','concours','discipline','ecole','entite','formation','instrument','marche','media','office','personnalite','place','plateforme','projet','publication','reseau'
    ];
    if ($nom === '' || !in_array($section, $sections)) {
        header('Location: /weball/?err=1');
        exit;
    }
    // Gestion des champs personnalisés (tous sauf nom/section)
    $data = [];
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ['fiche_nom', 'fiche_section'])) {
            $data[$k] = $v;
        }
    }
    $pdo = getPDO();
    $stmt = $pdo->prepare('INSERT INTO fiches (nom, section, date, user, data) VALUES (?, ?, NOW(), ?, ?)');
    $user = 'demo'; // À remplacer par l'utilisateur connecté
    $stmt->execute([$nom, $section, $user, json_encode($data)]);
    header('Location: /weball/' . $section . '.php?success=1&nom=' . urlencode($nom));
    exit;
}
// Si accès direct, redirection accueil
header('Location: /weball/');
exit;
