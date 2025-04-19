<?php
session_start();
require_once 'config.php';

$error = null;
$category = null;
$entries = [];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Récupération de la catégorie
        $stmt = $pdo->prepare("SELECT * FROM register_categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
        
        if (!$category) {
            header("Location: index.php");
            exit;
        }
        
        // Récupération des entrées de la catégorie
        $stmt = $pdo->prepare("SELECT * FROM register_entries WHERE category_id = ? ORDER BY date_creation DESC");
        $stmt->execute([$id]);
        $entries = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des données : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category ? htmlspecialchars($category['name']) : 'Catégorie'; ?> - Registre - WebAllOne</title>
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
            <?php if ($category): ?>
                <div class="module-header">
                    <div class="category-title">
                        <i class="<?php echo htmlspecialchars($category['icon']); ?>" 
                           style="color: <?php echo htmlspecialchars($category['color']); ?>"></i>
                        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
                    </div>
                    <div class="header-actions">
                        <a href="ajouter.php?category=<?php echo $category['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle entrée
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($category['description'])): ?>
                    <div class="category-description">
                        <?php echo nl2br(htmlspecialchars($category['description'])); ?>
                    </div>
                <?php endif; ?>

                <div class="entries-list">
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $entry): ?>
                            <div class="entry-item">
                                <div class="entry-header">
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
                        <p class="no-results">Aucune entrée dans cette catégorie</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
