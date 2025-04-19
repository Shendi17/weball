<?php
$pageTitle = "Mon Profil - WebAllOne";
require_once __DIR__ . '/../../includes/template.php';
?>

<div class="container mt-4">
    <h1>Mon Profil</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Informations personnelles</h5>
            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">Nom complet</label>
                    <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </form>
        </div>
    </div>
</div>
