<?php
session_start();
require_once 'config.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = cleanInput($_POST['title']);
    $url = cleanInput($_POST['url']);
    $description = cleanInput($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $tags = cleanInput($_POST['tags']);
    
    // Validation
    $errors = [];
    if (empty($title)) {
        $errors[] = "Le titre est requis";
    }
    if (!isValidUrl($url)) {
        $errors[] = "L'URL n'est pas valide";
    }
    if (empty($category_id)) {
        $errors[] = "La catégorie est requise";
    }
    
    // Si pas d'erreurs, on enregistre
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO links (title, url, description, category_id, tags, date_added) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $url, $description, $category_id, $tags]);
            
            // Redirection avec message de succès
            header("Location: index.php?success=1");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}

// Récupération des catégories pour le select
try {
    $sql = "SELECT * FROM link_categories ORDER BY ordre";
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Erreur lors de la récupération des catégories : " . $e->getMessage();
}

// Définir le titre de la page
$pageTitle = "Ajouter un lien";

// Inclure le template
ob_start();
?>

<div class="add-link-container">
    <div class="module-header">
        <h1>Ajouter un lien</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="" class="add-link-form">
            <div class="form-group">
                <label for="title">Titre *</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="url">URL *</label>
                <input type="url" class="form-control" id="url" name="url" 
                       value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"
                          ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="category_id">Catégorie *</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>
                                style="color: <?php echo htmlspecialchars($category['color']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tags">Tags (séparés par des virgules)</label>
                <input type="text" class="form-control" id="tags" name="tags" 
                       value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>"
                       placeholder="ex: development, tools, productivity">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
