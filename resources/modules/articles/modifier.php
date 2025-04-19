<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

$success = $error = '';

// Récupérer l'article
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $categorie = trim($_POST['categorie']);
    $statut = $_POST['statut'];
    $image_url = $article['image_url'];

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
                // Supprimer l'ancienne image si elle existe
                if ($article['image_url'] && file_exists('../../' . $article['image_url'])) {
                    unlink('../../' . $article['image_url']);
                }
                $image_url = 'uploads/articles/' . $new_filename;
            }
        }
    }

    if (empty($titre) || empty($contenu)) {
        $error = "Le titre et le contenu sont obligatoires.";
    } else {
        $sql = "UPDATE articles SET titre = ?, contenu = ?, image_url = ?, categorie = ?, statut = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $titre, $contenu, $image_url, $categorie, $statut, $id);

        if ($stmt->execute()) {
            header('Location: index.php?success=2');
            exit;
        } else {
            $error = "Erreur lors de la modification de l'article.";
        }
    }
}

$pageTitle = 'Modifier un Article';
ob_start();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">Modifier l'article</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="titre" name="titre" 
                           value="<?php echo htmlspecialchars($article['titre']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select class="form-select" id="categorie" name="categorie">
                        <?php
                        $sql = "SELECT * FROM categories_articles ORDER BY nom";
                        $categories = $conn->query($sql);
                        while ($categorie = $categories->fetch_assoc()) {
                            $selected = $categorie['nom'] === $article['categorie'] ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($categorie['nom']) . "' $selected>" . 
                                 htmlspecialchars($categorie['nom']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea class="form-control" id="contenu" name="contenu" rows="10" required>
                        <?php echo htmlspecialchars($article['contenu']); ?>
                    </textarea>
                </div>

                <?php if ($article['image_url']): ?>
                    <div class="mb-3">
                        <label class="form-label">Image actuelle</label>
                        <div>
                            <img src="<?php echo BASE_PATH . '/' . $article['image_url']; ?>" 
                                 alt="Image de l'article" style="max-width: 200px;">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="image" class="form-label">Nouvelle image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut">
                        <option value="brouillon" <?php echo $article['statut'] === 'brouillon' ? 'selected' : ''; ?>>
                            Brouillon
                        </option>
                        <option value="publie" <?php echo $article['statut'] === 'publie' ? 'selected' : ''; ?>>
                            Publié
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
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
