<?php require_once ROOT_PATH . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h1 class="display-1"><i class="fas fa-database text-danger"></i></h1>
            <h2>Erreur de base de données</h2>
            <p class="lead">Une erreur est survenue lors de la connexion à la base de données. Veuillez contacter l'administrateur.</p>
            <a href="<?php echo BASE_PATH; ?>/" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
