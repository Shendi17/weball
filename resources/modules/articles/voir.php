<?php
require_once '../../config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

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

try {
    // Récupérer l'article
    $sql = "SELECT a.*, u.username as auteur_nom 
            FROM articles a 
            LEFT JOIN users u ON a.auteur_id = u.id 
            WHERE a.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if (!$article) {
        $_SESSION['error'] = "L'article demandé n'existe pas.";
        header('Location: index.php');
        exit;
    }

    // Vérifier si l'article est en brouillon et si l'utilisateur n'est pas admin
    if ($article['statut'] === 'brouillon' && !isAdmin()) {
        $_SESSION['error'] = "Vous n'avez pas accès à cet article.";
        header('Location: index.php');
        exit;
    }

    $pageTitle = 'Voir un Article';
    ob_start();
    ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Articles</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($article['titre']); ?></li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title"><?php echo htmlspecialchars($article['titre']); ?></h1>
                        
                        <div class="article-metadata text-muted mb-4">
                            <small>
                                <?php if ($article['auteur_nom']): ?>
                                    Par <?php echo htmlspecialchars($article['auteur_nom']); ?> |
                                <?php endif; ?>
                                Catégorie : <?php echo htmlspecialchars($article['categorie']); ?> |
                                Publié le <?php echo date('d/m/Y', strtotime($article['date_creation'])); ?>
                                <?php if ($article['date_modification'] != $article['date_creation']): ?>
                                    | Modifié le <?php echo date('d/m/Y', strtotime($article['date_modification'])); ?>
                                <?php endif; ?>
                            </small>
                        </div>

                        <?php if ($article['image_url']): ?>
                            <img src="<?php echo BASE_PATH . '/' . htmlspecialchars($article['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($article['titre']); ?>" 
                                 class="img-fluid mb-4">
                        <?php endif; ?>

                        <div class="article-content">
                            <?php echo $article['contenu']; ?>
                        </div>

                        <div class="mt-4">
                            <a href="index.php" class="btn btn-secondary">Retour à la liste</a>
                            <?php if (isAdmin()): ?>
                                <a href="modifier.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">Modifier</a>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) { window.location.href='supprimer.php?id=<?php echo $article['id']; ?>'; }">
                                    Supprimer
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $content = ob_get_clean();
    require_once dirname(__DIR__, 3) . '/includes/template.php';

} catch (PDOException $e) {
    // Log l'erreur et affiche un message convivial
    error_log("Erreur dans voir.php : " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue lors de la récupération de l'article.";
    header('Location: index.php');
    exit;
}
