<?php
session_start();
require_once 'config.php';

$error = null;
$results = [];
$search_query = isset($_GET['q']) ? cleanInput($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : null;
$status_filter = isset($_GET['status']) ? cleanInput($_GET['status']) : null;
$priority_filter = isset($_GET['priority']) ? cleanInput($_GET['priority']) : null;

// Récupération des catégories pour le filtre
try {
    $stmt = $pdo->query("SELECT * FROM register_categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des catégories : " . $e->getMessage();
}

// Construction de la requête de recherche
if (!empty($search_query)) {
    try {
        $query = "SELECT e.*, c.name as category_name, c.color as category_color 
                  FROM register_entries e 
                  LEFT JOIN register_categories c ON e.category_id = c.id 
                  WHERE (e.title LIKE :search 
                     OR e.description LIKE :search 
                     OR e.tags LIKE :search)";
        
        $params = [':search' => "%$search_query%"];
        
        // Ajout des filtres
        if ($category_filter) {
            $query .= " AND e.category_id = :category";
            $params[':category'] = $category_filter;
        }
        if ($status_filter) {
            $query .= " AND e.status = :status";
            $params[':status'] = $status_filter;
        }
        if ($priority_filter) {
            $query .= " AND e.priority = :priority";
            $params[':priority'] = $priority_filter;
        }
        
        $query .= " ORDER BY e.date_creation DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        $error = "Erreur lors de la recherche : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - Registre - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    // Ancienne inclusion supprimée
    // <?php include '../../includes/sidebar.php'; ?>
    // Ajout du template global
    require_once dirname(__DIR__, 3) . '/includes/template.php';
    ?>
    
    <div class="main-content">
        <?php include '../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Recherche dans le registre</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Formulaire de recherche -->
            <div class="search-section">
                <form method="GET" class="search-form">
                    <div class="search-grid">
                        <div class="search-input">
                            <input type="text" name="q" placeholder="Rechercher..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        
                        <div class="search-filters">
                            <select name="category">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo $category_filter === $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select name="status">
                                <option value="">Tous les statuts</option>
                                <option value="en_cours" <?php echo $status_filter === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="termine" <?php echo $status_filter === 'termine' ? 'selected' : ''; ?>>Terminé</option>
                                <option value="en_attente" <?php echo $status_filter === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                                <option value="annule" <?php echo $status_filter === 'annule' ? 'selected' : ''; ?>>Annulé</option>
                            </select>
                            
                            <select name="priority">
                                <option value="">Toutes les priorités</option>
                                <option value="basse" <?php echo $priority_filter === 'basse' ? 'selected' : ''; ?>>Basse</option>
                                <option value="normale" <?php echo $priority_filter === 'normale' ? 'selected' : ''; ?>>Normale</option>
                                <option value="haute" <?php echo $priority_filter === 'haute' ? 'selected' : ''; ?>>Haute</option>
                                <option value="urgente" <?php echo $priority_filter === 'urgente' ? 'selected' : ''; ?>>Urgente</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </form>
            </div>

            <!-- Résultats de la recherche -->
            <?php if (!empty($search_query)): ?>
                <div class="search-results">
                    <h2>
                        <?php 
                        echo count($results) . ' résultat' . (count($results) > 1 ? 's' : '') . 
                             ' pour "' . htmlspecialchars($search_query) . '"';
                        ?>
                    </h2>
                    
                    <div class="entries-list">
                        <?php if (!empty($results)): ?>
                            <?php foreach ($results as $entry): ?>
                                <div class="entry-item">
                                    <div class="entry-header">
                                        <div class="entry-category" style="background-color: <?php echo htmlspecialchars($entry['category_color']); ?>">
                                            <?php echo htmlspecialchars($entry['category_name']); ?>
                                        </div>
                                        <div class="entry-date">
                                            <?php echo formatDate($entry['date_event']); ?>
                                        </div>
                                    </div>
                                    <div class="entry-content">
                                        <h3>
                                            <a href="voir.php?id=<?php echo $entry['id']; ?>">
                                                <?php echo htmlspecialchars($entry['title']); ?>
                                            </a>
                                        </h3>
                                        <p><?php echo nl2br(htmlspecialchars(substr($entry['description'], 0, 150))); ?>...</p>
                                    </div>
                                    <div class="entry-footer">
                                        <div class="entry-status">
                                            <?php $status = getStatusLabel($entry['status']); ?>
                                            <span class="status-badge <?php echo $status['class']; ?>">
                                                <?php echo $status['label']; ?>
                                            </span>
                                        </div>
                                        <?php if ($entry['priority'] !== 'normale'): ?>
                                            <div class="entry-priority">
                                                <?php $priority = getPriorityLabel($entry['priority']); ?>
                                                <span class="priority-badge <?php echo $priority['class']; ?>">
                                                    <?php echo $priority['label']; ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($entry['tags'])): ?>
                                            <div class="entry-tags">
                                                <?php foreach (getTagsArray($entry['tags']) as $tag): ?>
                                                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-results">Aucun résultat trouvé pour votre recherche</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
