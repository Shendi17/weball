<?php
// Inclure les fichiers nécessaires en premier
$root = dirname(dirname(dirname(__FILE__)));
require_once $root . '/config.php';

// La session est déjà démarrée dans config.php si nécessaire
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

// Inclure les autres dépendances
require_once $root . '/includes/functions.php';
require_once $root . '/includes/db.php';

// Vérifier explicitement le rôle admin
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Récupération des formations
try {
    $sql = "SELECT * FROM formations_formations ORDER BY titre ASC";
    $stmt = $pdo->query($sql);
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $formations = [];
}

// Définir le titre de la page
$pageTitle = 'Formations';

// Inclure les modales d'abord
require_once dirname(__FILE__) . '/modals.php';

// Démarrer la capture de sortie
ob_start();
?>

<!-- Contenu principal -->
<div class="container mt-4">
    <div class="row">
        <!-- Filtres à gauche -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="search" class="form-label">Rechercher</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Rechercher une formation...">
                        </div>
                        <div class="mb-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau" name="niveau">
                                <option value="">Tous</option>
                                <option value="debutant">Débutant</option>
                                <option value="intermediaire">Intermédiaire</option>
                                <option value="avance">Avancé</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="categorie" class="form-label">Catégorie</label>
                            <select class="form-select" id="categorie" name="categorie">
                                <option value="">Toutes</option>
                                <option value="development">Développement</option>
                                <option value="design">Design</option>
                                <option value="marketing">Marketing</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des formations à droite -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Formations</h2>
                <?php if ($isAdmin): ?>
                <button type="button" class="btn btn-primary" id="addFormationBtn">
                    <i class="fa fa-plus"></i> Nouvelle formation
                </button>
                <?php endif; ?>
            </div>

            <div class="row" id="formationsList">
                <?php if (empty($formations)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        Aucune formation disponible pour le moment.
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($formations as $formation): 
                        // Définir des valeurs par défaut pour tous les champs
                        $formation = array_merge([
                            'id' => 0,
                            'titre' => '',
                            'description' => '',
                            'niveau' => 'intermediaire',
                            'categorie' => 'development',
                            'duree' => '40 heures',
                            'date_debut' => '',
                            'date_fin' => ''
                        ], $formation);
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($formation['titre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($formation['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?php 
                                        echo $formation['niveau'] === 'debutant' ? 'success' : 
                                            ($formation['niveau'] === 'intermediaire' ? 'warning' : 'danger'); 
                                    ?>" data-niveau="<?php echo htmlspecialchars($formation['niveau']); ?>">
                                        <?php 
                                        $niveauTexte = [
                                            'debutant' => 'Débutant',
                                            'intermediaire' => 'Intermédiaire',
                                            'avance' => 'Avancé'
                                        ];
                                        echo htmlspecialchars($niveauTexte[$formation['niveau']] ?? ucfirst($formation['niveau'])); 
                                        ?>
                                    </span>
                                    <span class="badge bg-info" data-categorie="<?php echo htmlspecialchars($formation['categorie']); ?>">
                                        <?php 
                                        $categorieTexte = [
                                            'development' => 'Développement',
                                            'design' => 'Design',
                                            'marketing' => 'Marketing'
                                        ];
                                        echo htmlspecialchars($categorieTexte[$formation['categorie']] ?? ucfirst($formation['categorie'])); 
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Durée: <?php echo htmlspecialchars($formation['duree']); ?></small>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary view-formation" 
                                                data-id="<?php echo $formation['id']; ?>" title="Voir les détails">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <?php if ($isAdmin): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-formation" 
                                                data-id="<?php echo $formation['id']; ?>" title="Modifier">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-formation" 
                                                data-id="<?php echo $formation['id']; ?>" title="Supprimer">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Capturer le contenu de la page
$pageContent = ob_get_clean();

// Inclure le template principal avec le contenu capturé
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>

<!-- JavaScript spécifique à la page -->
<script>
// Définir BASE_PATH pour JavaScript
const BASE_PATH = "<?php echo BASE_PATH; ?>";

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour filtrer les formations
    function filterFormations() {
        const searchTerm = document.getElementById('search').value.toLowerCase().trim();
        const niveau = document.getElementById('niveau').value.toLowerCase();
        const categorie = document.getElementById('categorie').value.toLowerCase();
        
        console.log('Termes de recherche:', { searchTerm, niveau, categorie });
        
        const formationsList = document.getElementById('formationsList');
        if (!formationsList) {
            console.error('Liste des formations non trouvée');
            return;
        }
        
        const formations = formationsList.querySelectorAll('.col-md-6');
        console.log('Nombre de formations trouvées:', formations.length);
        
        let visibleCount = 0;
        
        formations.forEach((formation, index) => {
            const titleEl = formation.querySelector('.card-title');
            const descEl = formation.querySelector('.card-text');
            const niveauBadge = formation.querySelector('.badge:not(.bg-info)');
            const categorieBadge = formation.querySelector('.badge.bg-info');
            
            const title = titleEl ? titleEl.textContent.toLowerCase() : '';
            const description = descEl ? descEl.textContent.toLowerCase() : '';
            const formationNiveau = niveauBadge ? niveauBadge.dataset.niveau.toLowerCase() : '';
            const formationCategorie = categorieBadge ? categorieBadge.dataset.categorie.toLowerCase() : '';
            
            console.log(`Formation ${index}:`, {
                title,
                description,
                formationNiveau,
                formationCategorie
            });
            
            const matchSearch = searchTerm === '' || 
                              title.includes(searchTerm) || 
                              description.includes(searchTerm);
                              
            const matchNiveau = niveau === '' || formationNiveau === niveau;
            const matchCategorie = categorie === '' || formationCategorie === categorie;
            
            console.log(`Correspondances pour formation ${index}:`, {
                matchSearch,
                matchNiveau,
                matchCategorie
            });
            
            const shouldShow = matchSearch && matchNiveau && matchCategorie;
            formation.style.display = shouldShow ? '' : 'none';
            
            if (shouldShow) {
                visibleCount++;
            }
        });
        
        console.log('Nombre de formations visibles:', visibleCount);
        
        // Gérer le message "Aucun résultat"
        const existingNoResults = document.getElementById('noResults');
        if (existingNoResults) {
            existingNoResults.remove();
        }
        
        if (visibleCount === 0) {
            const message = document.createElement('div');
            message.id = 'noResults';
            message.className = 'col-12';
            message.innerHTML = '<div class="alert alert-info">Aucune formation ne correspond à vos critères.</div>';
            formationsList.appendChild(message);
        }
    }
    
    // Écouter les événements de recherche et de filtre
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    const niveauSelect = document.getElementById('niveau');
    const categorieSelect = document.getElementById('categorie');
    
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            filterFormations();
        });
        
        filterForm.addEventListener('reset', function() {
            setTimeout(() => {
                searchInput.value = '';
                niveauSelect.value = '';
                categorieSelect.value = '';
                filterFormations();
            }, 0);
        });
    }
    
    if (searchInput) {
        let timeoutId;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(filterFormations, 300);
        });
    }
    
    if (niveauSelect) {
        niveauSelect.addEventListener('change', filterFormations);
    }
    
    if (categorieSelect) {
        categorieSelect.addEventListener('change', filterFormations);
    }

    // Récupérer les éléments des modales
    const formationModalEl = document.getElementById('formationModal');
    const addFormationModalEl = document.getElementById('addFormationModal');
    const editFormationModalEl = document.getElementById('editFormationModal');
    
    console.log('Modal de détails:', formationModalEl);
    console.log('Modal d\'ajout:', addFormationModalEl);
    console.log('Modal d\'édition:', editFormationModalEl);
    
    // Initialiser les modales Bootstrap si elles existent
    const formationModal = formationModalEl ? new bootstrap.Modal(formationModalEl) : null;
    const addFormationModal = addFormationModalEl ? new bootstrap.Modal(addFormationModalEl) : null;
    const editFormationModal = editFormationModalEl ? new bootstrap.Modal(editFormationModalEl) : null;
    
    // Gérer le bouton d'ajout
    const addBtn = document.getElementById('addFormationBtn');
    console.log('Bouton d\'ajout:', addBtn);
    
    if (addBtn && addFormationModal) {
        console.log('Bouton d\'ajout et modal trouvés');
        addBtn.addEventListener('click', function() {
            addFormationModal.show();
        });
    } else {
        console.log('Bouton d\'ajout ou modal non trouvé', {
            buttonFound: !!addBtn,
            modalFound: !!addFormationModal
        });
    }
    
    // Gérer les boutons de visualisation
    document.querySelectorAll('.view-formation').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`${BASE_PATH}/modules/formations/get_formation.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && formationModal) {
                        const formation = data.formation;
                        document.getElementById('formationTitle').textContent = formation.titre;
                        document.getElementById('formationDescription').textContent = formation.description;
                        document.getElementById('formationNiveau').textContent = formation.niveau;
                        document.getElementById('formationCategorie').textContent = formation.categorie;
                        document.getElementById('formationDuree').textContent = formation.duree;
                        document.getElementById('formationDateDebut').textContent = formation.date_debut;
                        document.getElementById('formationDateFin').textContent = formation.date_fin;
                        formationModal.show();
                    }
                })
                .catch(error => console.error('Erreur:', error));
        });
    });

    // Gérer les boutons de modification
    document.querySelectorAll('.edit-formation').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`${BASE_PATH}/modules/formations/get_formation.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && editFormationModal) {
                        const formation = data.formation;
                        document.getElementById('edit_id').value = formation.id;
                        document.getElementById('edit_titre').value = formation.titre;
                        document.getElementById('edit_description').value = formation.description;
                        document.getElementById('edit_niveau').value = formation.niveau;
                        document.getElementById('edit_categorie').value = formation.categorie;
                        document.getElementById('edit_duree').value = formation.duree;
                        document.getElementById('edit_date_debut').value = formation.date_debut;
                        document.getElementById('edit_date_fin').value = formation.date_fin;
                        editFormationModal.show();
                    }
                })
                .catch(error => console.error('Erreur:', error));
        });
    });

    // Gérer les boutons de suppression
    document.querySelectorAll('.delete-formation').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')) {
                const id = this.dataset.id;
                fetch(`${BASE_PATH}/modules/formations/delete_formation.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la suppression');
                });
            }
        });
    });

    // Gérer le formulaire d'ajout
    const addForm = document.getElementById('addFormationForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`${BASE_PATH}/modules/formations/add_formation.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addFormationModal.hide();
                    location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        });
    }

    // Gérer le formulaire de modification
    const editForm = document.getElementById('editFormationForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`${BASE_PATH}/modules/formations/edit_formation.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editFormationModal.hide();
                    location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        });
    }
});
</script>
