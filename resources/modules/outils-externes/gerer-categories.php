<?php
session_start();
require_once 'config.php';

// Traitement de l'ajout/modification de catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name']);
    $description = cleanInput($_POST['description']);
    $color = cleanInput($_POST['color']);
    $ordre = intval($_POST['ordre']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    
    try {
        if ($id) {
            // Modification
            $sql = "UPDATE link_categories SET name = ?, description = ?, color = ?, ordre = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $color, $ordre, $id]);
            $success = "Catégorie modifiée avec succès";
        } else {
            // Ajout
            $sql = "INSERT INTO link_categories (name, description, color, ordre) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $color, $ordre]);
            $success = "Catégorie ajoutée avec succès";
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de l'enregistrement de la catégorie : " . $e->getMessage();
    }
}

// Traitement de la suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        // Vérifier si la catégorie a des liens associés
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM links WHERE category_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $error = "Impossible de supprimer cette catégorie car elle contient des liens";
        } else {
            $stmt = $pdo->prepare("DELETE FROM link_categories WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Catégorie supprimée avec succès";
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression de la catégorie : " . $e->getMessage();
    }
}

// Récupération des catégories
try {
    $query = "SELECT * FROM link_categories ORDER BY ordre";
    $stmt = $pdo->query($query);
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des catégories : " . $e->getMessage();
}

// Définir le titre de la page
$pageTitle = "Gérer les catégories";

// Inclure le template
ob_start();
?>

<div class="categories-container">
    <div class="module-header">
        <h1>Gérer les catégories</h1>
        <div class="header-actions">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryModal">
                <i class="fas fa-plus"></i> Nouvelle catégorie
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="categories-list">
        <?php if (empty($categories)): ?>
            <div class="alert alert-info">
                Aucune catégorie n'a été créée
            </div>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <div class="category-item" style="border-left: 4px solid <?php echo !empty($category['color']) ? htmlspecialchars($category['color']) : '#6c757d'; ?>">
                    <div class="category-info">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                    </div>
                    <div class="category-meta">
                        <span class="ordre">Ordre: <?php echo $category['ordre']; ?></span>
                    </div>
                    <div class="category-actions">
                        <button type="button" class="btn btn-warning btn-sm edit-category" 
                                data-id="<?php echo $category['id']; ?>"
                                data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                data-description="<?php echo htmlspecialchars($category['description'] ?? ''); ?>"
                                data-color="<?php echo !empty($category['color']) ? htmlspecialchars($category['color']) : '#6c757d'; ?>"
                                data-ordre="<?php echo $category['ordre']; ?>">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <a href="?delete=<?php echo $category['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal pour ajouter/modifier une catégorie -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Nouvelle catégorie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="category_id">
                    
                    <div class="form-group">
                        <label for="name">Nom *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="color">Couleur</label>
                        <input type="color" class="form-control" id="color" name="color" value="#6c757d">
                    </div>

                    <div class="form-group">
                        <label for="ordre">Ordre</label>
                        <input type="number" class="form-control" id="ordre" name="ordre" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour le bouton de modification
    const editButtons = document.querySelectorAll('.edit-category');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = document.querySelector('#categoryModal');
            const modalTitle = modal.querySelector('.modal-title');
            
            // Mise à jour du titre
            modalTitle.textContent = 'Modifier la catégorie';
            
            // Remplissage des champs
            modal.querySelector('#category_id').value = this.dataset.id;
            modal.querySelector('#name').value = this.dataset.name;
            modal.querySelector('#description').value = this.dataset.description;
            modal.querySelector('#color').value = this.dataset.color;
            modal.querySelector('#ordre').value = this.dataset.ordre;
            
            // Affichage du modal
            $(modal).modal('show');
        });
    });
    
    // Réinitialisation du formulaire lors de l'ouverture du modal pour une nouvelle catégorie
    $('#categoryModal').on('show.bs.modal', function(event) {
        if (!event.relatedTarget.classList.contains('edit-category')) {
            const modal = this;
            const form = modal.querySelector('form');
            const modalTitle = modal.querySelector('.modal-title');
            
            form.reset();
            modalTitle.textContent = 'Nouvelle catégorie';
            modal.querySelector('#category_id').value = '';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
