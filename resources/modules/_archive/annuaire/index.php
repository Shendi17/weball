<?php
session_start();
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Suppression de la vérification de connexion temporairement
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ' . BASE_PATH . '/login.php');
//     exit;
// }

try {
    $pageTitle = 'Annuaire';
    ob_start();
    ?>
    <div class="container mt-5">
        <!-- Contenu du module Annuaire -->
        <h1>Annuaire</h1>
        <p>Section dédiée à la consultation des membres et contacts de la plateforme.</p>
    </div>
    <?php
    $content = ob_get_clean();
    require_once dirname(__DIR__, 3) . '/includes/template.php';

} catch (PDOException $e) {
    error_log("Erreur dans index.php : " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue lors de la connexion à la base de données.";
    
    $pageTitle = 'Erreur';
    ob_start();
    ?>
    <div class="container mt-4">
        <div class="alert alert-danger">
            <h4 class="alert-heading">Erreur</h4>
            <p>Désolé, une erreur est survenue lors de la connexion à la base de données.</p>
            <hr>
            <p class="mb-0">Veuillez réessayer plus tard ou contacter l'administrateur si le problème persiste.</p>
        </div>
        <a href="<?php echo BASE_PATH; ?>" class="btn btn-primary">Retour à l'accueil</a>
    </div>
    <?php
    $content = ob_get_clean();
    require_once dirname(__DIR__, 3) . '/includes/template.php';
}
