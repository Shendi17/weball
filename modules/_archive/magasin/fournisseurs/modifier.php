<?php
session_start();
require_once '../config.php';

$error = null;
$success = false;
$fournisseur = null;

// Récupération de l'ID du fournisseur
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: liste.php");
    exit;
}

try {
    // Récupération du fournisseur
    $stmt = $pdo->prepare("SELECT * FROM magasin_fournisseurs WHERE id = ?");
    $stmt->execute([$id]);
    $fournisseur = $stmt->fetch();
    
    if (!$fournisseur) {
        header("Location: liste.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du fournisseur : " . $e->getMessage();
}

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
            $sql = "UPDATE magasin_fournisseurs 
                    SET name = ?, contact_name = ?, email = ?, phone = ?, address = ?, notes = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $contact_name, $email, $phone, $address, $notes, $id]);
            
            header("Location: voir.php?id=$id&success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification du fournisseur : " . $e->getMessage();
        }
    }
}

// Définir le titre de la page
$pageTitle = "Modifier " . $fournisseur['name'];

// Inclure le template
ob_start();
?>

<div class="edit-fournisseur-container">
    <div class="module-header">
        <h1>Modifier <?php echo htmlspecialchars($fournisseur['name']); ?></h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="" class="edit-fournisseur-form">
            <div class="form-group">
                <label for="name">Nom du fournisseur *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($fournisseur['name']); ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="contact_name">Nom du contact</label>
                <input type="text" class="form-control" id="contact_name" name="contact_name" 
                       value="<?php echo htmlspecialchars($fournisseur['contact_name']); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($fournisseur['email']); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($fournisseur['phone']); ?>">
            </div>

            <div class="form-group">
                <label for="address">Adresse</label>
                <textarea class="form-control" id="address" name="address" rows="3"
                          ><?php echo htmlspecialchars($fournisseur['address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"
                          ><?php echo htmlspecialchars($fournisseur['notes']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="voir.php?id=<?php echo $id; ?>" class="btn btn-secondary">
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
