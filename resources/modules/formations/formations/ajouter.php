<?php
session_start();
require_once '../../../config.php';

$error = null;
$success = false;

try {
    // Récupération des catégories
    $stmt = $pdo->query("SELECT * FROM formations_categories ORDER BY ordre");
    $categories = $stmt->fetchAll();
    
    // Récupération des formateurs
    $stmt = $pdo->query("SELECT * FROM formations_formateurs ORDER BY nom, prenom");
    $formateurs = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $titre = cleanInput($_POST['titre']);
    $description = cleanInput($_POST['description']);
    $objectifs = cleanInput($_POST['objectifs']);
    $prerequis = cleanInput($_POST['prerequis']);
    $programme = cleanInput($_POST['programme']);
    $duree = intval($_POST['duree']);
    $max_participants = intval($_POST['max_participants']);
    $lieu = cleanInput($_POST['lieu']);
    $formateur_id = !empty($_POST['formateur_id']) ? intval($_POST['formateur_id']) : null;
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    
    // Validation
    if (empty($titre)) {
        $error = "Le titre est obligatoire.";
    } elseif (empty($date_debut) || empty($date_fin)) {
        $error = "Les dates sont obligatoires.";
    } elseif ($duree <= 0) {
        $error = "La durée doit être supérieure à 0.";
    } elseif ($max_participants <= 0) {
        $error = "Le nombre maximum de participants doit être supérieur à 0.";
    } else {
        try {
            $sql = "INSERT INTO formations_formations (
                        category_id, titre, description, objectifs, prerequis, programme,
                        duree, max_participants, lieu, formateur_id, date_debut, date_fin
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $category_id,
                $titre,
                $description,
                $objectifs,
                $prerequis,
                $programme,
                $duree,
                $max_participants,
                $lieu,
                $formateur_id,
                $date_debut,
                $date_fin
            ]);
            
            $id = $pdo->lastInsertId();
            
            // Gestion des documents
            if (!empty($_FILES['documents']['name'][0])) {
                $upload_dir = __DIR__ . '/../../../documents/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                foreach ($_FILES['documents']['name'] as $key => $name) {
                    if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['documents']['tmp_name'][$key];
                        $type = $_POST['document_types'][$key];
                        $titre_doc = $_POST['document_titres'][$key];
                        $description_doc = $_POST['document_descriptions'][$key];
                        
                        // Génération d'un nom de fichier unique
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        $filename = uniqid() . '.' . $extension;
                        
                        if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
                            $sql = "INSERT INTO formations_documents (
                                        formation_id, titre, description, filename, type
                                    ) VALUES (?, ?, ?, ?, ?)";
                            
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                $id,
                                $titre_doc,
                                $description_doc,
                                $filename,
                                $type
                            ]);
                        }
                    }
                }
            }
            
            header("Location: voir.php?id=$id&success=1");
            exit;
            
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout de la formation : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle formation - Formations - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    // Ancienne inclusion supprimée
    // <?php include '../../../includes/sidebar.php'; ?>
    // Ajout du template global
    require_once dirname(__DIR__, 4) . '/includes/template.php';
    ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Nouvelle formation</h1>
                <a href="liste.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div class="form-section">
                    <h2>Informations générales</h2>
                    
                    <div class="form-group">
                        <label for="category_id">Catégorie</label>
                        <select id="category_id" name="category_id">
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="objectifs">Objectifs</label>
                        <textarea id="objectifs" name="objectifs" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="prerequis">Prérequis</label>
                        <textarea id="prerequis" name="prerequis" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="programme">Programme</label>
                        <textarea id="programme" name="programme" rows="8"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Organisation</h2>
                    
                    <div class="form-group">
                        <label for="formateur_id">Formateur</label>
                        <select id="formateur_id" name="formateur_id">
                            <option value="">Sélectionner un formateur</option>
                            <?php foreach ($formateurs as $formateur): ?>
                                <option value="<?php echo $formateur['id']; ?>">
                                    <?php echo htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_debut">Date de début *</label>
                        <input type="datetime-local" id="date_debut" name="date_debut" required>
                    </div>

                    <div class="form-group">
                        <label for="date_fin">Date de fin *</label>
                        <input type="datetime-local" id="date_fin" name="date_fin" required>
                    </div>

                    <div class="form-group">
                        <label for="duree">Durée (minutes) *</label>
                        <input type="number" id="duree" name="duree" min="1" value="60" required>
                    </div>

                    <div class="form-group">
                        <label for="max_participants">Nombre maximum de participants *</label>
                        <input type="number" id="max_participants" name="max_participants" min="1" value="10" required>
                    </div>

                    <div class="form-group">
                        <label for="lieu">Lieu</label>
                        <input type="text" id="lieu" name="lieu">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Documents</h2>
                    
                    <div id="documents-container">
                        <div class="document-item">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="document_types[]">
                                    <option value="support">Support</option>
                                    <option value="exercice">Exercice</option>
                                    <option value="correction">Correction</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Titre</label>
                                <input type="text" name="document_titres[]">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" name="document_descriptions[]">
                            </div>
                            <div class="form-group">
                                <label>Fichier</label>
                                <input type="file" name="documents[]">
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-secondary" onclick="addDocument()">
                        <i class="fas fa-plus"></i> Ajouter un document
                    </button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function addDocument() {
        const container = document.getElementById('documents-container');
        const template = container.querySelector('.document-item').cloneNode(true);
        
        // Réinitialisation des valeurs
        template.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        
        container.appendChild(template);
    }
    </script>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
