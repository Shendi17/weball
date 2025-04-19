<?php
session_start();
require_once '../../../config.php';

// Initialisation des variables
$error = null;
$success = false;
$formation = null;
$categories = [];
$formateurs = [];
$documents = [];

// Récupération de l'ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: liste.php");
    exit;
}

try {
    // Vérification de l'existence des tables nécessaires
    $tables_required = ['formations_formations', 'formations_categories', 'formations_formateurs', 'formations_documents'];
    $tables_missing = [];
    
    foreach ($tables_required as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $tables_missing[] = $table;
        }
    }

    // Récupération de la formation
    $stmt = $pdo->prepare("SELECT * FROM formations_formations WHERE id = ?");
    $stmt->execute([$id]);
    $formation = $stmt->fetch();

    if (!$formation) {
        throw new Exception("Formation non trouvée.");
    }

    // Récupération des catégories si la table existe
    if (!in_array('formations_categories', $tables_missing)) {
        $stmt = $pdo->query("SELECT * FROM formations_categories ORDER BY ordre");
        $categories = $stmt->fetchAll();
    }

    // Récupération des formateurs si la table existe
    if (!in_array('formations_formateurs', $tables_missing)) {
        $stmt = $pdo->query("SELECT * FROM formations_formateurs ORDER BY nom, prenom");
        $formateurs = $stmt->fetchAll();
    }

    // Récupération des documents si la table existe
    if (!in_array('formations_documents', $tables_missing)) {
        $stmt = $pdo->prepare("SELECT * FROM formations_documents WHERE formation_id = ? ORDER BY type, titre");
        $stmt->execute([$id]);
        $documents = $stmt->fetchAll();
    }

} catch (Exception $e) {
    $error = $e->getMessage();
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
            $sql = "UPDATE formations_formations SET 
                        category_id = ?, titre = ?, description = ?, objectifs = ?, 
                        prerequis = ?, programme = ?, duree = ?, max_participants = ?, 
                        lieu = ?, formateur_id = ?, date_debut = ?, date_fin = ?
                    WHERE id = ?";
            
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
                $date_fin,
                $id
            ]);
            
            // Suppression des documents
            if (!empty($_POST['documents_to_delete'])) {
                foreach ($_POST['documents_to_delete'] as $doc_id) {
                    $stmt = $pdo->prepare("SELECT filename FROM formations_documents WHERE id = ? AND formation_id = ?");
                    $stmt->execute([$doc_id, $id]);
                    $filename = $stmt->fetchColumn();
                    
                    if ($filename) {
                        $filepath = __DIR__ . '/../documents/' . $filename;
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                        
                        $stmt = $pdo->prepare("DELETE FROM formations_documents WHERE id = ?");
                        $stmt->execute([$doc_id]);
                    }
                }
            }
            
            // Ajout de nouveaux documents
            if (!empty($_FILES['documents']['name'][0])) {
                $upload_dir = __DIR__ . '/../documents/';
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
            $error = "Erreur lors de la modification de la formation : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($formation['titre']) ? htmlspecialchars($formation['titre']) : 'Modifier la formation'; ?> - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once dirname(__DIR__, 4) . '/includes/template.php'; ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">Formation modifiée avec succès.</div>
            <?php endif; ?>

            <?php if ($formation): ?>
            <div class="module-header">
                <h1>Modifier la formation</h1>
                <div class="header-actions">
                    <a href="voir.php?id=<?php echo htmlspecialchars($formation['id']); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div class="form-section">
                    <h2>Informations générales</h2>
                    
                    <?php if (!empty($categories)): ?>
                    <div class="form-group">
                        <label for="category_id">Catégorie</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($formation['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" name="titre" id="titre" class="form-control" 
                               value="<?php echo htmlspecialchars($formation['titre'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($formation['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="objectifs">Objectifs</label>
                        <textarea name="objectifs" id="objectifs" class="form-control" rows="4"><?php echo htmlspecialchars($formation['objectifs'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="prerequis">Prérequis</label>
                        <textarea name="prerequis" id="prerequis" class="form-control" rows="4"><?php echo htmlspecialchars($formation['prerequis'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="programme">Programme</label>
                        <textarea name="programme" id="programme" class="form-control" rows="4"><?php echo htmlspecialchars($formation['programme'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Organisation</h2>

                    <?php if (!empty($formateurs)): ?>
                    <div class="form-group">
                        <label for="formateur_id">Formateur</label>
                        <select name="formateur_id" id="formateur_id" class="form-control">
                            <option value="">Sélectionner un formateur</option>
                            <?php foreach ($formateurs as $formateur): ?>
                            <option value="<?php echo $formateur['id']; ?>" <?php echo ($formation['formateur_id'] ?? '') == $formateur['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="date_debut">Date de début *</label>
                        <input type="datetime-local" name="date_debut" id="date_debut" class="form-control"
                               value="<?php echo isset($formation['date_debut']) ? date('Y-m-d\TH:i', strtotime($formation['date_debut'])) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="date_fin">Date de fin *</label>
                        <input type="datetime-local" name="date_fin" id="date_fin" class="form-control"
                               value="<?php echo isset($formation['date_fin']) ? date('Y-m-d\TH:i', strtotime($formation['date_fin'])) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="duree">Durée (minutes) *</label>
                        <input type="number" name="duree" id="duree" class="form-control" 
                               value="<?php echo htmlspecialchars($formation['duree'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="max_participants">Nombre maximum de participants *</label>
                        <input type="number" name="max_participants" id="max_participants" class="form-control"
                               value="<?php echo htmlspecialchars($formation['max_participants'] ?? 10); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="lieu">Lieu</label>
                        <input type="text" name="lieu" id="lieu" class="form-control"
                               value="<?php echo htmlspecialchars($formation['lieu'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Documents existants</h2>
                    
                    <?php if (!empty($documents)): ?>
                        <div class="documents-list">
                            <?php foreach ($documents as $document): ?>
                                <div class="document-item">
                                    <div class="document-info">
                                        <h3><?php echo htmlspecialchars($document['titre']); ?></h3>
                                        <?php if ($document['description']): ?>
                                            <p><?php echo htmlspecialchars($document['description']); ?></p>
                                        <?php endif; ?>
                                        <div class="document-type">
                                            Type : <?php echo ucfirst($document['type']); ?>
                                        </div>
                                    </div>
                                    <div class="document-actions">
                                        <label class="checkbox-container">
                                            <input type="checkbox" name="documents_to_delete[]" 
                                                   value="<?php echo $document['id']; ?>">
                                            Supprimer
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-results">Aucun document</p>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h2>Nouveaux documents</h2>
                    
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
            <?php endif; ?>
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
