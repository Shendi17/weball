<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$success = $error = '';

// Traitement du formulaire d'ajout de catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'ajouter') {
            $nom = trim($_POST['nom']);
            $description = trim($_POST['description']);

            if (empty($nom)) {
                $error = "Le nom de la catégorie est obligatoire.";
            } else {
                try {
                    $sql = "INSERT INTO categories_articles (nom, description) VALUES (:nom, :description)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nom' => $nom,
                        ':description' => $description
                    ]);
                    
                    $success = "Catégorie ajoutée avec succès.";
                } catch (PDOException $e) {
                    error_log("Erreur lors de l'ajout de la catégorie : " . $e->getMessage());
                    $error = "Erreur lors de l'ajout de la catégorie.";
                }
            }
        } elseif ($_POST['action'] === 'supprimer' && isset($_POST['categorie_id'])) {
            $id = (int)$_POST['categorie_id'];
            try {
                $sql = "DELETE FROM categories_articles WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $id]);
                
                $success = "Catégorie supprimée avec succès.";
            } catch (PDOException $e) {
                error_log("Erreur lors de la suppression de la catégorie : " . $e->getMessage());
                $error = "Erreur lors de la suppression de la catégorie.";
            }
        }
    }
}

$pageTitle = 'Gestion des Catégories';
ob_start();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Gestion des Catégories</h1>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux articles
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ajouter une catégorie</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la catégorie</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des catégories</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $sql = "SELECT * FROM categories_articles ORDER BY nom";
                                    $stmt = $pdo->query($sql);
                                    
                                    if ($stmt->rowCount() > 0) {
                                        while ($categorie = $stmt->fetch()) {
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($categorie['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($categorie['description']); ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="supprimer">
                                                        <input type="hidden" name="categorie_id" value="<?php echo $categorie['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <div class="p-4">
                                                    <i class="fas fa-tags fa-3x text-muted mb-3 d-block"></i>
                                                    <p class="mb-0">Aucune catégorie n'a été créée pour le moment.</p>
                                                    <p class="text-muted">Utilisez le formulaire ci-dessus pour ajouter votre première catégorie.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } catch (PDOException $e) {
                                    error_log("Erreur lors de la récupération des catégories : " . $e->getMessage());
                                    echo '<tr><td colspan="3" class="text-center text-danger">Une erreur est survenue lors du chargement des catégories.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aide</h5>
                </div>
                <div class="card-body">
                    <p>Les catégories vous permettent d'organiser vos articles de manière logique.</p>
                    <ul>
                        <li>Créez des catégories pertinentes pour votre contenu</li>
                        <li>Utilisez des noms courts mais descriptifs</li>
                        <li>La description est optionnelle mais recommandée</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Les catégories permettent aux lecteurs de trouver plus facilement les articles qui les intéressent.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
