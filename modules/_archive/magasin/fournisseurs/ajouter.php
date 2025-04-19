<?php
session_start();
require_once '../config.php';

$error = null;
$success = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name']);
    $contact_name = cleanInput($_POST['contact_name']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $address = cleanInput($_POST['address']);
    $notes = cleanInput($_POST['notes']);
    
    // Validation
    if (empty($name)) {
        $error = "Le nom du fournisseur est obligatoire.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        try {
            $sql = "INSERT INTO magasin_fournisseurs (name, contact_name, email, phone, address, notes) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $contact_name, $email, $phone, $address, $notes]);
            
            header("Location: liste.php?success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du fournisseur : " . $e->getMessage();
        }
    }
}

// Définir le titre de la page
$pageTitle = "Ajouter un fournisseur";

// Inclure le template
ob_start();
?>

<div class="add-fournisseur-container">
    <div class="module-header">
        <h1>Ajouter un fournisseur</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="" class="add-fournisseur-form">
            <div class="form-group">
                <label for="name">Nom du fournisseur *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="contact_name">Nom du contact</label>
                <input type="text" class="form-control" id="contact_name" name="contact_name" 
                       value="<?php echo isset($_POST['contact_name']) ? htmlspecialchars($_POST['contact_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="address">Adresse</label>
                <textarea class="form-control" id="address" name="address" rows="3"
                          ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"
                          ><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="liste.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once '../../../includes/template.php';
?>
