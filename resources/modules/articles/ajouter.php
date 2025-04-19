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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $categorie = trim($_POST['categorie']);
    $statut = $_POST['statut'];
    $image_url = '';

    // Gestion de l'upload d'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/articles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_url = 'uploads/articles/' . $new_filename;
            }
        }
    }

    if (empty($titre) || empty($contenu)) {
        $error = "Le titre et le contenu sont obligatoires.";
    } else {
        try {
            $sql = "INSERT INTO articles (titre, contenu, image_url, categorie, statut, auteur_id) 
                    VALUES (:titre, :contenu, :image_url, :categorie, :statut, :auteur_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre' => $titre,
                ':contenu' => $contenu,
                ':image_url' => $image_url,
                ':categorie' => $categorie,
                ':statut' => $statut,
                ':auteur_id' => $_SESSION['user_id']
            ]);

            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout de l'article : " . $e->getMessage());
            $error = "Erreur lors de l'ajout de l'article.";
        }
    }
}

$pageTitle = 'Ajouter un Article';
ob_start();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Ajouter un Article</h1>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux articles
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" required 
                                   value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="categorie" class="form-label">Catégorie</label>
                            <select class="form-select" id="categorie" name="categorie">
                                <?php
                                try {
                                    $sql = "SELECT * FROM categories_articles ORDER BY nom";
                                    $stmt = $pdo->query($sql);
                                    while ($categorie = $stmt->fetch()) {
                                        $selected = isset($_POST['categorie']) && $_POST['categorie'] === $categorie['nom'] ? ' selected' : '';
                                        echo '<option value="' . htmlspecialchars($categorie['nom']) . '"' . $selected . '>' . 
                                             htmlspecialchars($categorie['nom']) . '</option>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Erreur lors de la récupération des catégories : " . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu</label>
                            <textarea class="form-control" id="contenu" name="contenu" rows="10" required><?php 
                                echo isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''; 
                            ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Formats acceptés : JPG, JPEG, PNG, GIF</div>
                        </div>

                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                <option value="brouillon"<?php echo (!isset($_POST['statut']) || $_POST['statut'] === 'brouillon') ? ' selected' : ''; ?>>Brouillon</option>
                                <option value="publie"<?php echo (isset($_POST['statut']) && $_POST['statut'] === 'publie') ? ' selected' : ''; ?>>Publié</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#contenu',
    height: 400,
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic backcolor | \
             alignleft aligncenter alignright alignjustify | \
             bullist numlist outdent indent | removeformat | help'
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
