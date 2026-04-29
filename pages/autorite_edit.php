<?php
/**
 * Page d'édition d'une autorité
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pdo = getPDO();

// Récupérer l'ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: /autorite');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Gérer les réseaux sociaux
        $reseauxSociaux = [];
        if (isset($_POST['reseaux']) && is_array($_POST['reseaux'])) {
            foreach ($_POST['reseaux'] as $reseau) {
                if (!empty($reseau['nom']) && !empty($reseau['url'])) {
                    $reseauxSociaux[] = [
                        'nom' => $reseau['nom'],
                        'url' => $reseau['url']
                    ];
                }
            }
        }
        
        // Gérer les types multiples
        $typesIds = [];
        if (isset($_POST['types_ids']) && is_array($_POST['types_ids'])) {
            $typesIds = array_map('intval', $_POST['types_ids']);
        }
        
        // Gérer les catégories multiples
        $categoriesIds = [];
        if (isset($_POST['categories_ids']) && is_array($_POST['categories_ids'])) {
            $categoriesIds = array_map('intval', $_POST['categories_ids']);
        }
        
        // Garder la compatibilité avec type_id et category_id (prendre le premier)
        $typeId = !empty($typesIds) ? $typesIds[0] : null;
        $categoryId = !empty($categoriesIds) ? $categoriesIds[0] : null;
        
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'logo' => $_POST['logo'] ?? null,
            'type_id' => $typeId,
            'category_id' => $categoryId,
            'types_ids' => !empty($typesIds) ? json_encode($typesIds) : null,
            'categories_ids' => !empty($categoriesIds) ? json_encode($categoriesIds) : null,
            'description' => $_POST['description'] ?? null,
            'description_longue' => $_POST['description_longue'] ?? null,
            'domaine' => $_POST['domaine'] ?? null,
            'pays' => $_POST['pays'] ?? null,
            'site_web' => $_POST['site_web'] ?? null,
            'adresse' => $_POST['adresse'] ?? null,
            'telephone' => $_POST['telephone'] ?? null,
            'email' => $_POST['email'] ?? null,
            'dirigeant' => $_POST['dirigeant'] ?? null,
            'date_creation_org' => $_POST['date_creation_org'] ?? null,
            'effectifs' => $_POST['effectifs'] ?? null,
            'chiffre_affaires' => $_POST['chiffre_affaires'] ?? null,
            'secteur' => $_POST['secteur'] ?? null,
            'missions' => $_POST['missions'] ?? null,
            'realisations' => $_POST['realisations'] ?? null,
            'partenaires' => $_POST['partenaires'] ?? null,
            'reseaux_sociaux' => !empty($reseauxSociaux) ? json_encode($reseauxSociaux) : null,
            // Nouveaux champs
            'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
            'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            'code_postal' => $_POST['code_postal'] ?? null,
            'ville' => $_POST['ville'] ?? null,
            'region' => $_POST['region'] ?? null,
            'horaires' => $_POST['horaires'] ?? null,
            'contact_presse' => $_POST['contact_presse'] ?? null,
            'email_presse' => $_POST['email_presse'] ?? null,
            'telephone_presse' => $_POST['telephone_presse'] ?? null,
            'historique' => $_POST['historique'] ?? null,
            'valeurs' => $_POST['valeurs'] ?? null,
            'engagement_rse' => $_POST['engagement_rse'] ?? null,
            'certifications' => $_POST['certifications'] ?? null,
            'distinctions' => $_POST['distinctions'] ?? null,
            'filiales' => $_POST['filiales'] ?? null,
            'actionnaires' => $_POST['actionnaires'] ?? null,
            'siret' => $_POST['siret'] ?? null,
            'tva_intracommunautaire' => $_POST['tva_intracommunautaire'] ?? null,
            'forme_juridique' => $_POST['forme_juridique'] ?? null,
            'capital_social' => $_POST['capital_social'] ?? null,
            'bourse_cotation' => $_POST['bourse_cotation'] ?? null,
            'code_bourse' => $_POST['code_bourse'] ?? null,
            'wikipedia_url' => $_POST['wikipedia_url'] ?? null,
            'linkedin_url' => $_POST['linkedin_url'] ?? null,
            'twitter_url' => $_POST['twitter_url'] ?? null,
            'facebook_url' => $_POST['facebook_url'] ?? null,
            'instagram_url' => $_POST['instagram_url'] ?? null,
            'youtube_url' => $_POST['youtube_url'] ?? null,
            'blog_url' => $_POST['blog_url'] ?? null,
            'offres_emploi_url' => $_POST['offres_emploi_url'] ?? null
        ];
        
        $stmt = $pdo->prepare("
            UPDATE autorite 
            SET nom = ?, logo = ?, type_id = ?, category_id = ?, types_ids = ?, categories_ids = ?,
                description = ?, description_longue = ?, domaine = ?, pays = ?, site_web = ?,
                adresse = ?, telephone = ?, email = ?, dirigeant = ?, date_creation_org = ?,
                effectifs = ?, chiffre_affaires = ?, secteur = ?, missions = ?,
                realisations = ?, partenaires = ?, reseaux_sociaux = ?,
                latitude = ?, longitude = ?, code_postal = ?, ville = ?, region = ?,
                horaires = ?, contact_presse = ?, email_presse = ?, telephone_presse = ?,
                historique = ?, valeurs = ?, engagement_rse = ?, certifications = ?, distinctions = ?,
                filiales = ?, actionnaires = ?, siret = ?, tva_intracommunautaire = ?,
                forme_juridique = ?, capital_social = ?, bourse_cotation = ?, code_bourse = ?,
                wikipedia_url = ?, linkedin_url = ?, twitter_url = ?, facebook_url = ?,
                instagram_url = ?, youtube_url = ?, blog_url = ?, offres_emploi_url = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['nom'],
            $data['logo'],
            $data['type_id'],
            $data['category_id'],
            $data['types_ids'],
            $data['categories_ids'],
            $data['description'],
            $data['description_longue'],
            $data['domaine'],
            $data['pays'],
            $data['site_web'],
            $data['adresse'],
            $data['telephone'],
            $data['email'],
            $data['dirigeant'],
            $data['date_creation_org'],
            $data['effectifs'],
            $data['chiffre_affaires'],
            $data['secteur'],
            $data['missions'],
            $data['realisations'],
            $data['partenaires'],
            $data['reseaux_sociaux'],
            $data['latitude'],
            $data['longitude'],
            $data['code_postal'],
            $data['ville'],
            $data['region'],
            $data['horaires'],
            $data['contact_presse'],
            $data['email_presse'],
            $data['telephone_presse'],
            $data['historique'],
            $data['valeurs'],
            $data['engagement_rse'],
            $data['certifications'],
            $data['distinctions'],
            $data['filiales'],
            $data['actionnaires'],
            $data['siret'],
            $data['tva_intracommunautaire'],
            $data['forme_juridique'],
            $data['capital_social'],
            $data['bourse_cotation'],
            $data['code_bourse'],
            $data['wikipedia_url'],
            $data['linkedin_url'],
            $data['twitter_url'],
            $data['facebook_url'],
            $data['instagram_url'],
            $data['youtube_url'],
            $data['blog_url'],
            $data['offres_emploi_url'],
            $id
        ]);
        
        $_SESSION['success'] = "Autorité modifiée avec succès !";
        header("Location: /autorite_detail?id=$id");
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
    }
}

// Récupérer l'autorité
$stmt = $pdo->prepare("
    SELECT a.*, 
           t.nom as type_nom, t.icon as type_icon,
           c.nom as category_nom
    FROM autorite a
    LEFT JOIN autorite_types t ON a.type_id = t.id
    LEFT JOIN autorite_categories c ON a.category_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$autorite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$autorite) {
    header('Location: /autorite');
    exit;
}

// Récupérer les types
$types = $pdo->query("
    SELECT * FROM autorite_types 
    ORDER BY ordre, nom
")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les catégories
$categories = $pdo->query("
    SELECT c.*, t.slug as type_slug
    FROM autorite_categories c
    LEFT JOIN autorite_types t ON c.type_id = t.id
    ORDER BY c.ordre, c.nom
")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Modifier : " . $autorite['nom'];
$pageDescription = "Modification de l'autorité";
$isAdmin = isAdmin();

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/autorite_edit_template.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
