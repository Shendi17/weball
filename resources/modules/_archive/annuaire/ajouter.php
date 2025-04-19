<?php
session_start();

// Inclure les fichiers nécessaires
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Suppression de la vérification de connexion temporairement
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ' . BASE_PATH . '/login.php');
//     exit;
// }

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'nom', 'prenom', 'email', 'telephone', 'mobile',
        'adresse', 'code_postal', 'ville', 'pays',
        'societe', 'fonction', 'notes'
    ];
    
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? '';
    }
    
    try {
        $sql = "INSERT INTO contacts (nom, prenom, email, telephone, mobile, adresse, code_postal, ville, pays, societe, fonction, notes)
                VALUES (:nom, :prenom, :email, :telephone, :mobile, :adresse, :code_postal, :ville, :pays, :societe, :fonction, :notes)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
        $_SESSION['success'] = "Le contact a été ajouté avec succès.";
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout du contact : " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout du contact.";
    }
}

// Définir le titre de la page
$pageTitle = 'Ajouter un contact';

// Début du contenu
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ajouter un contact</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                        <div class="invalid-feedback">Le nom est requis.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                        <div class="invalid-feedback">Le prénom est requis.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="societe" class="form-label">Société</label>
                        <input type="text" class="form-control" id="societe" name="societe">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fonction" class="form-label">Fonction</label>
                    <input type="text" class="form-control" id="fonction" name="fonction">
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" id="adresse" name="adresse">
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code_postal" class="form-label">Code postal</label>
                        <input type="text" class="form-control" id="code_postal" name="code_postal">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="ville" name="ville">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pays" class="form-label">Pays</label>
                        <input type="text" class="form-control" id="pays" name="pays" value="France">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation des formulaires Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
