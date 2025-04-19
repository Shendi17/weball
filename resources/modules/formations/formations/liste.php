<?php
session_start();
require_once '../../../config.php';

// Initialisation des variables
$error = null;
$success = isset($_GET['success']) ? true : false;
$formations = [];
$categories = [];
$formateurs = [];
$tables_missing = [];

// Paramètres de pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Paramètres de filtrage
$status = isset($_GET['status']) ? $_GET['status'] : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$formateur = isset($_GET['formateur']) ? intval($_GET['formateur']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';

try {
    // Vérification de l'existence des tables nécessaires
    $tables_required = ['formations_formations', 'formations_categories', 'formations_formateurs', 'formations_participants'];
    
    foreach ($tables_required as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $tables_missing[] = $table;
        }
    }

    // Construction de la requête de base
    $sql = "SELECT f.*";

    // Ajout des champs conditionnels
    if (!in_array('formations_participants', $tables_missing)) {
        $sql .= ", COUNT(p.id) as nb_participants";
    } else {
        $sql .= ", 0 as nb_participants";
    }

    if (!in_array('formations_categories', $tables_missing)) {
        $sql .= ", c.nom as categorie_nom";
    } else {
        $sql .= ", 'Non catégorisé' as categorie_nom";
    }

    if (!in_array('formations_formateurs', $tables_missing)) {
        $sql .= ", CONCAT(fo.prenom, ' ', fo.nom) as formateur_nom";
    } else {
        $sql .= ", NULL as formateur_nom";
    }

    // Table principale
    $sql .= " FROM formations_formations f";

    // Jointures conditionnelles
    if (!in_array('formations_participants', $tables_missing)) {
        $sql .= " LEFT JOIN formations_participants p ON f.id = p.formation_id";
    }

    if (!in_array('formations_categories', $tables_missing)) {
        $sql .= " LEFT JOIN formations_categories c ON f.categorie_id = c.id";
    }

    if (!in_array('formations_formateurs', $tables_missing)) {
        $sql .= " LEFT JOIN formations_formateurs fo ON f.formateur_id = fo.id";
    }

    // Filtres
    $where_clauses = [];
    $params = [];
    
    if ($status !== '') {
        $where_clauses[] = "f.statut = ?";
        $params[] = $status;
    }
    
    if ($category > 0) {
        $where_clauses[] = "f.categorie_id = ?";
        $params[] = $category;
    }
    
    if ($formateur > 0) {
        $where_clauses[] = "f.formateur_id = ?";
        $params[] = $formateur;
    }
    
    if ($search !== '') {
        $where_clauses[] = "(f.titre LIKE ? OR f.description LIKE ? OR f.lieu LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($date_debut !== '') {
        $where_clauses[] = "f.date_debut >= ?";
        $params[] = $date_debut;
    }
    
    if ($date_fin !== '') {
        $where_clauses[] = "f.date_fin <= ?";
        $params[] = $date_fin;
    }
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    // Groupement et tri
    $sql .= " GROUP BY f.id ORDER BY f.date_debut DESC LIMIT ? OFFSET ?";

    // Exécution de la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($params, [$limit, $offset]));
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des catégories si la table existe
    if (!in_array('formations_categories', $tables_missing)) {
        $stmt = $pdo->query("SELECT * FROM formations_categories ORDER BY nom");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupération des formateurs si la table existe
    if (!in_array('formations_formateurs', $tables_missing)) {
        $stmt = $pdo->query("SELECT * FROM formations_formateurs ORDER BY nom, prenom");
        $formateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des formations - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <style>
        .formation-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
        }
        .formation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .formation-title {
            margin: 0;
            font-size: 1.2em;
        }
        .formation-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .status-planifiee { background-color: #e3f2fd; color: #1976d2; }
        .status-en-cours { background-color: #e8f5e9; color: #388e3c; }
        .status-terminee { background-color: #f5f5f5; color: #616161; }
        .status-annulee { background-color: #ffebee; color: #d32f2f; }
        .formation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }
        .formation-details > div {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .formation-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php 
    // Ancienne inclusion supprimée
    // <?php include '../../../includes/sidebar.php'; ?>
    // Ajout du template global
    require_once dirname(__DIR__, 4) . '/includes/template.php';
    ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">Opération effectuée avec succès.</div>
            <?php endif; ?>

            <div class="module-header">
                <h1>Liste des formations</h1>
                <div class="header-actions">
                    <a href="<?php echo BASE_PATH; ?>/modules/formations/formations/ajouter.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle formation
                    </a>
                </div>
            </div>

            <?php if (!empty($tables_missing)): ?>
            <div class="alert alert-warning">
                <strong>Attention :</strong> Certaines fonctionnalités peuvent être limitées car les tables suivantes sont manquantes :
                <ul>
                    <?php foreach ($tables_missing as $table): ?>
                    <li><?php echo htmlspecialchars($table); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($formations)): ?>
            <div class="formations-grid">
                <?php foreach ($formations as $formation): ?>
                <div class="formation-card">
                    <div class="formation-header">
                        <h3 class="formation-title">
                            <a href="<?php echo BASE_PATH; ?>/modules/formations/formations/voir.php?id=<?php echo $formation['id']; ?>">
                                <?php echo htmlspecialchars($formation['titre']); ?>
                            </a>
                        </h3>
                        <div class="formation-status">
                            <?php 
                            $statut = isset($formation['statut']) ? $formation['statut'] : 'planifiee';
                            $statusLabels = [
                                'planifiee' => 'Planifiée',
                                'en_cours' => 'En cours',
                                'terminee' => 'Terminée',
                                'annulee' => 'Annulée'
                            ];
                            $statusClass = 'status-' . $statut;
                            ?>
                            <span class="<?php echo htmlspecialchars($statusClass); ?>">
                                <?php echo htmlspecialchars($statusLabels[$statut] ?? $statut); ?>
                            </span>
                        </div>
                    </div>

                    <div class="formation-details">
                        <div class="formation-category">
                            <i class="fas fa-folder"></i>
                            <?php echo htmlspecialchars($formation['categorie_nom'] ?? 'Non catégorisé'); ?>
                        </div>

                        <?php if (!empty($formation['formateur_nom'])): ?>
                        <div class="formation-trainer">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($formation['formateur_nom']); ?>
                        </div>
                        <?php endif; ?>

                        <div class="formation-dates">
                            <i class="fas fa-calendar"></i>
                            <?php if (!empty($formation['date_debut']) && !empty($formation['date_fin'])): ?>
                            Du <?php echo date('d/m/Y', strtotime($formation['date_debut'])); ?>
                            au <?php echo date('d/m/Y', strtotime($formation['date_fin'])); ?>
                            <?php else: ?>
                            Dates non définies
                            <?php endif; ?>
                        </div>

                        <div class="formation-participants">
                            <i class="fas fa-users"></i>
                            <?php 
                            $nbParticipants = isset($formation['nb_participants']) ? (int)$formation['nb_participants'] : 0;
                            $maxParticipants = isset($formation['max_participants']) ? (int)$formation['max_participants'] : 0;
                            echo $nbParticipants;
                            if ($maxParticipants > 0) {
                                echo " / " . $maxParticipants;
                            }
                            ?> participant<?php echo $nbParticipants > 1 ? 's' : ''; ?>
                        </div>
                    </div>

                    <div class="formation-actions">
                        <a href="<?php echo BASE_PATH; ?>/modules/formations/formations/voir.php?id=<?php echo $formation['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="<?php echo BASE_PATH; ?>/modules/formations/formations/modifier.php?id=<?php echo $formation['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="no-results">
                <p>Aucune formation trouvée</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
