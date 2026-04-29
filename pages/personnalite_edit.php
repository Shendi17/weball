<?php
/**
 * Page d'édition d'une personnalité
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérifier l'authentification (optionnel selon vos besoins)
// if (!isAdmin()) {
//     header('Location: /login');
//     exit;
// }

$pdo = getPDO();

// Récupérer l'ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: /personnalite');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Préparer les données JSON
        $jsonFields = [
            'oeuvres_principales', 'citations', 'galerie_photos', 'videos', 
            'documentaires', 'livres', 'podcasts', 'articles', 'produits_associes',
            'lieux_associes', 'evenements', 'collaborations', 'prix_recompenses',
            'controverses', 'heritage_culturel', 'musees_expositions', 'fondations',
            'timeline', 'reseaux_sociaux', 'sources'
        ];
        
        $data = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, $jsonFields)) {
                // Convertir les textarea en JSON
                if (!empty($value)) {
                    $lines = array_filter(array_map('trim', explode("\n", $value)));
                    $data[$key] = json_encode(array_values($lines));
                } else {
                    $data[$key] = null;
                }
            } else {
                $data[$key] = !empty($value) ? $value : null;
            }
        }
        
        // Gérer les sélections multiples (types et catégories)
        if (isset($_POST['types_ids']) && is_array($_POST['types_ids'])) {
            $data['types_ids'] = json_encode(array_map('intval', $_POST['types_ids']));
        } else {
            $data['types_ids'] = null;
        }
        
        if (isset($_POST['categories_ids']) && is_array($_POST['categories_ids'])) {
            $data['categories_ids'] = json_encode(array_map('intval', $_POST['categories_ids']));
        } else {
            $data['categories_ids'] = null;
        }
        
        // Gérer les liens externes
        if (isset($_POST['liens_externes']) && is_array($_POST['liens_externes'])) {
            $liensExternes = [];
            foreach ($_POST['liens_externes'] as $lien) {
                if (!empty($lien['titre']) && !empty($lien['url'])) {
                    $liensExternes[] = [
                        'titre' => $lien['titre'],
                        'url' => $lien['url'],
                        'icon' => $lien['icon'] ?? 'link'
                    ];
                }
            }
            $data['liens_externes'] = !empty($liensExternes) ? json_encode($liensExternes) : null;
        } else {
            $data['liens_externes'] = null;
        }
        
        // Construire la requête UPDATE
        $sets = [];
        $params = [];
        
        $allowedFields = [
            'nom', 'prenom', 'type_id', 'category_id', 'biographie', 'nationalite',
            'date_naissance', 'date_deces', 'lieu_naissance', 'lieu_deces', 'cause_deces',
            'profession', 'distinctions', 'photo', 'email', 'telephone', 'site_web',
            'wikipedia_url', 'anecdotes', 'influence', 'statut', 'types_ids', 'categories_ids',
            'liens_externes'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $sets[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        foreach ($jsonFields as $field) {
            if (isset($data[$field])) {
                $sets[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        $params[] = $id;
        
        $sql = "UPDATE personnalite SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Rediriger vers la fiche
        header('Location: /personnalite_detail?id=' . $id);
        exit;
        
    } catch (Exception $e) {
        $error = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

// Récupérer les données de la personnalité
$stmt = $pdo->prepare("
    SELECT p.*, 
           pt.nom as type_nom, pt.icon as type_icon,
           pc.nom as category_nom, pc.icon as category_icon
    FROM personnalite p
    LEFT JOIN personnalite_types pt ON p.type_id = pt.id
    LEFT JOIN personnalite_categories pc ON p.category_id = pc.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$personnalite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$personnalite) {
    header('Location: /personnalite');
    exit;
}

// Récupérer les types et catégories pour les selects
$types = $pdo->query("SELECT id, nom FROM personnalite_types ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, nom, type_id FROM personnalite_categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour convertir JSON en texte multiligne
function jsonToText($json) {
    if (empty($json)) return '';
    $data = json_decode($json, true);
    if (!is_array($data)) return '';
    return implode("\n", $data);
}

// Titre de la page
$pageTitle = "Édition : " . $personnalite['prenom'] . ' ' . $personnalite['nom'];

// Contenu
ob_start();
include __DIR__ . '/../templates/pages/personnalite_edit_template.php';
$content = ob_get_clean();

// Layout
include __DIR__ . '/../templates/layout/main.php';
