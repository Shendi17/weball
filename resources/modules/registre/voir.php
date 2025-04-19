<?php
session_start();
require_once 'config.php';

$error = null;
$entry = null;
$comments = [];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Récupération de l'entrée
        $query = "SELECT e.*, c.name as category_name, c.color as category_color 
                  FROM register_entries e 
                  LEFT JOIN register_categories c ON e.category_id = c.id 
                  WHERE e.id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $entry = $stmt->fetch();
        
        if (!$entry) {
            header("Location: index.php");
            exit;
        }
        
        // Récupération des commentaires
        $stmt = $pdo->prepare("SELECT * FROM register_comments WHERE entry_id = ? ORDER BY date_creation DESC");
        $stmt->execute([$id]);
        $comments = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des données : " . $e->getMessage();
    }
}

// Traitement de l'ajout d'un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $content = cleanInput($_POST['comment']);
    $author = cleanInput($_POST['author']);
    
    if (!empty($content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO register_comments (entry_id, content, author) VALUES (?, ?, ?)");
            $stmt->execute([$id, $content, $author]);
            
            // Redirection pour éviter la soumission multiple
            header("Location: voir.php?id=$id#comments");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du commentaire : " . $e->getMessage();
        }
    }
}

$pageTitle = 'Voir fiche Registre';
ob_start();
?>

<div class="container mt-5">
    <?php if (isset($_GET['created'])): ?>
        <div class="alert alert-success">
            L'entrée a été créée avec succès.
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($entry): ?>
        <div class="module-header">
            <h1><?php echo htmlspecialchars($entry['title']); ?></h1>
            <div class="header-actions">
                <a href="modifier.php?id=<?php echo $entry['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="entry-details">
            <div class="entry-meta">
                <div class="meta-item">
                    <span class="meta-label">Catégorie:</span>
                    <span class="entry-category" style="background-color: <?php echo htmlspecialchars($entry['category_color']); ?>">
                        <?php echo htmlspecialchars($entry['category_name']); ?>
                    </span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-label">Statut:</span>
                    <?php $status = getStatusLabel($entry['status']); ?>
                    <span class="status-badge <?php echo $status['class']; ?>">
                        <?php echo $status['label']; ?>
                    </span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-label">Priorité:</span>
                    <?php $priority = getPriorityLabel($entry['priority']); ?>
                    <span class="priority-badge <?php echo $priority['class']; ?>">
                        <?php echo $priority['label']; ?>
                    </span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-label">Date:</span>
                    <?php echo formatDate($entry['date_event']); ?>
                </div>
                
                <?php if ($entry['date_deadline']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Date limite:</span>
                        <?php echo formatDate($entry['date_deadline']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($entry['assigned_to']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Assigné à:</span>
                        <?php echo htmlspecialchars($entry['assigned_to']); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="entry-content">
                <h2>Description</h2>
                <div class="description">
                    <?php echo nl2br(htmlspecialchars($entry['description'])); ?>
                </div>

                <?php if (!empty($entry['tags'])): ?>
                    <div class="entry-tags">
                        <?php foreach (getTagsArray($entry['tags']) as $tag): ?>
                            <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($entry['attachments'])): ?>
                    <div class="attachments">
                        <h2>Pièces jointes</h2>
                        <div class="attachments-list">
                            <?php foreach (getAttachmentsArray($entry['attachments']) as $attachment): ?>
                                <a href="telecharger.php?file=<?php echo urlencode($attachment['path']); ?>" 
                                   class="attachment-item" target="_blank">
                                    <i class="fas fa-file"></i>
                                    <span class="attachment-name"><?php echo htmlspecialchars($attachment['name']); ?></span>
                                    <span class="attachment-size">
                                        <?php echo number_format($attachment['size'] / 1024, 2); ?> Ko
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="comments-section" id="comments">
                <h2>Commentaires</h2>
                
                <form method="POST" class="comment-form">
                    <div class="form-group">
                        <label for="author">Nom (optionnel)</label>
                        <input type="text" id="author" name="author">
                    </div>
                    <div class="form-group">
                        <label for="comment">Votre commentaire</label>
                        <textarea id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-comment"></i> Ajouter un commentaire
                    </button>
                </form>

                <div class="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-author">
                                        <?php echo !empty($comment['author']) ? 
                                            htmlspecialchars($comment['author']) : 'Anonyme'; ?>
                                    </span>
                                    <span class="comment-date">
                                        <?php echo formatDate($comment['date_creation']); ?>
                                    </span>
                                </div>
                                <div class="comment-content">
                                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-results">Aucun commentaire pour le moment</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
