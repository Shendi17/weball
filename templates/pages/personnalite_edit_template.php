<?php 
/**
 * Template d'édition d'une personnalité (même présentation que la fiche)
 */
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST" id="editForm">
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/personnalite">Personnalités</a></li>
            <li class="breadcrumb-item"><a href="/personnalite_detail?id=<?= $personnalite['id'] ?>"><?= htmlspecialchars($personnalite['prenom'] . ' ' . $personnalite['nom']) ?></a></li>
            <li class="breadcrumb-item active">Édition</li>
        </ol>
    </nav>

    <!-- Barre d'actions sticky -->
    <div class="sticky-top bg-white shadow-sm p-3 mb-4" style="top: 60px; z-index: 1020;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>Mode Édition
            </h5>
            <div>
                <a href="/personnalite_detail?id=<?= $personnalite['id'] ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- Informations principales -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations Principales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($personnalite['prenom']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($personnalite['nom']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type Principal</label>
                            <select name="type_id" class="form-select" id="typeSelect">
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= $personnalite['type_id'] == $type['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie Principale</label>
                            <select name="category_id" class="form-select" id="categorySelect">
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" data-type="<?= $cat['type_id'] ?>" <?= $personnalite['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Types Supplémentaires (Sélection Multiple)</label>
                            <select name="types_ids[]" class="form-select" id="typesMultiSelect" multiple size="5">
                                <?php 
                                $selectedTypes = !empty($personnalite['types_ids']) ? json_decode($personnalite['types_ids'], true) : [];
                                foreach ($types as $type): 
                                    $selected = in_array($type['id'], $selectedTypes) ? 'selected' : '';
                                ?>
                                    <option value="<?= $type['id'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($type['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs types</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Catégories Supplémentaires (Sélection Multiple)</label>
                            <select name="categories_ids[]" class="form-select" id="categoriesMultiSelect" multiple size="5">
                                <?php 
                                $selectedCategories = !empty($personnalite['categories_ids']) ? json_decode($personnalite['categories_ids'], true) : [];
                                foreach ($categories as $cat): 
                                    $selected = in_array($cat['id'], $selectedCategories) ? 'selected' : '';
                                ?>
                                    <option value="<?= $cat['id'] ?>" data-type="<?= $cat['type_id'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs catégories</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Profession</label>
                            <input type="text" name="profession" class="form-control" value="<?= htmlspecialchars($personnalite['profession'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nationalité</label>
                            <input type="text" name="nationalite" class="form-control" value="<?= htmlspecialchars($personnalite['nationalite'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" value="<?= $personnalite['date_naissance'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" class="form-control" value="<?= htmlspecialchars($personnalite['lieu_naissance'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de décès</label>
                            <input type="date" name="date_deces" class="form-control" value="<?= $personnalite['date_deces'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu de décès</label>
                            <input type="text" name="lieu_deces" class="form-control" value="<?= htmlspecialchars($personnalite['lieu_deces'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cause du décès</label>
                            <input type="text" name="cause_deces" class="form-control" value="<?= htmlspecialchars($personnalite['cause_deces'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="vivant" <?= $personnalite['statut'] == 'vivant' ? 'selected' : '' ?>>Vivant</option>
                                <option value="decede" <?= $personnalite['statut'] == 'decede' ? 'selected' : '' ?>>Décédé</option>
                                <option value="legendaire" <?= $personnalite['statut'] == 'legendaire' ? 'selected' : '' ?>>Légendaire</option>
                                <option value="inconnu" <?= $personnalite['statut'] == 'inconnu' ? 'selected' : '' ?>>Inconnu</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Photo (URL)</label>
                            <input type="url" name="photo" class="form-control" value="<?= htmlspecialchars($personnalite['photo'] ?? '') ?>" placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biographie -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Biographie</h5>
                </div>
                <div class="card-body">
                    <textarea name="biographie" class="form-control" rows="8"><?= htmlspecialchars($personnalite['biographie'] ?? '') ?></textarea>
                    <small class="text-muted">Texte complet de la biographie</small>
                </div>
            </div>

            <!-- Œuvres principales -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Œuvres & Réalisations</h5>
                </div>
                <div class="card-body">
                    <textarea name="oeuvres_principales" class="form-control" rows="5" placeholder="Une œuvre par ligne"><?= jsonToText($personnalite['oeuvres_principales'] ?? '') ?></textarea>
                    <small class="text-muted">Une œuvre par ligne</small>
                </div>
            </div>

            <!-- Citations -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-quote-left me-2"></i>Citations Célèbres</h5>
                </div>
                <div class="card-body">
                    <textarea name="citations" class="form-control" rows="5" placeholder="Une citation par ligne"><?= jsonToText($personnalite['citations'] ?? '') ?></textarea>
                    <small class="text-muted">Une citation par ligne</small>
                </div>
            </div>

            <!-- Anecdotes -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Anecdotes</h5>
                </div>
                <div class="card-body">
                    <textarea name="anecdotes" class="form-control" rows="5"><?= htmlspecialchars($personnalite['anecdotes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Influence -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Influence & Héritage</h5>
                </div>
                <div class="card-body">
                    <textarea name="influence" class="form-control" rows="5"><?= htmlspecialchars($personnalite['influence'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Galerie Photos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>Galerie Photos</h5>
                </div>
                <div class="card-body">
                    <textarea name="galerie_photos" class="form-control" rows="5" placeholder="https://exemple.com/photo1.jpg&#10;https://exemple.com/photo2.jpg&#10;https://exemple.com/photo3.jpg"><?= jsonToText($personnalite['galerie_photos'] ?? '') ?></textarea>
                    <small class="text-muted">✏️ Collez simplement les URLs des images, une par ligne</small>
                </div>
            </div>

            <!-- Vidéos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fab fa-youtube me-2"></i>Vidéos YouTube</h5>
                </div>
                <div class="card-body">
                    <textarea name="videos" class="form-control" rows="5" placeholder="https://www.youtube.com/watch?v=xxxxx&#10;https://www.youtube.com/watch?v=yyyyy&#10;https://www.youtube.com/watch?v=zzzzz"><?= jsonToText($personnalite['videos'] ?? '') ?></textarea>
                    <small class="text-muted">✏️ Collez simplement les liens YouTube, un par ligne</small>
                </div>
            </div>

            <!-- Produits Associés -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Produits & Marques</h5>
                </div>
                <div class="card-body">
                    <textarea name="produits_associes" class="form-control" rows="5" placeholder="Tesla Model S&#10;iPhone 15 Pro&#10;Nike Air Jordan&#10;PlayStation 5"><?= jsonToText($personnalite['produits_associes'] ?? '') ?></textarea>
                    <small class="text-muted">✏️ Écrivez simplement les noms des produits, un par ligne</small>
                </div>
            </div>

            <!-- Lieux Associés -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Lieux Importants</h5>
                </div>
                <div class="card-body">
                    <textarea name="lieux_associes" class="form-control" rows="5" placeholder="Paris, France&#10;New York, USA&#10;Tokyo, Japon"><?= jsonToText($personnalite['lieux_associes'] ?? '') ?></textarea>
                    <small class="text-muted">✏️ Écrivez simplement les lieux, un par ligne</small>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Distinctions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-award me-2"></i>Distinctions</h5>
                </div>
                <div class="card-body">
                    <textarea name="distinctions" class="form-control" rows="4"><?= htmlspecialchars($personnalite['distinctions'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Liens externes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Liens Externes</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addLienExterne()">
                        <i class="fas fa-plus me-1"></i>Ajouter
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Site Web</label>
                        <input type="url" name="site_web" class="form-control" value="<?= htmlspecialchars($personnalite['site_web'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wikipedia</label>
                        <input type="url" name="wikipedia_url" class="form-control" value="<?= htmlspecialchars($personnalite['wikipedia_url'] ?? '') ?>">
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Liens Supplémentaires</h6>
                    <div id="liensExternesContainer">
                        <?php 
                        $liensExternes = !empty($personnalite['liens_externes']) ? json_decode($personnalite['liens_externes'], true) : [];
                        if (!empty($liensExternes)):
                            foreach ($liensExternes as $index => $lien):
                        ?>
                            <div class="lien-externe-item mb-3 p-3 border rounded" data-index="<?= $index ?>">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Lien #<?= $index + 1 ?></strong>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeLienExterne(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Titre</label>
                                    <input type="text" name="liens_externes[<?= $index ?>][titre]" class="form-control form-control-sm" value="<?= htmlspecialchars($lien['titre'] ?? '') ?>" placeholder="Ex: Instagram, LinkedIn, etc.">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">URL</label>
                                    <input type="url" name="liens_externes[<?= $index ?>][url]" class="form-control form-control-sm" value="<?= htmlspecialchars($lien['url'] ?? '') ?>" placeholder="https://...">
                                </div>
                                <div>
                                    <label class="form-label small">Icône (optionnel)</label>
                                    <input type="text" name="liens_externes[<?= $index ?>][icon]" class="form-control form-control-sm" value="<?= htmlspecialchars($lien['icon'] ?? '') ?>" placeholder="Ex: instagram, linkedin, globe">
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <!-- Sources -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>Sources</h5>
                </div>
                <div class="card-body">
                    <textarea name="sources" class="form-control" rows="4" placeholder="Une source par ligne"><?= jsonToText($personnalite['sources'] ?? '') ?></textarea>
                    <small class="text-muted">Une source par ligne</small>
                </div>
            </div>

            <!-- Contact -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Contact</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($personnalite['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($personnalite['telephone'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4 border-primary">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                    <a href="/personnalite_detail?id=<?= $personnalite['id'] ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script>
// Filtrer les catégories selon le type sélectionné
document.getElementById('typeSelect').addEventListener('change', function() {
    const typeId = this.value;
    const categorySelect = document.getElementById('categorySelect');
    const options = categorySelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        if (option.dataset.type === typeId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Réinitialiser la sélection si la catégorie n'est plus valide
    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    if (selectedOption.dataset.type !== typeId && selectedOption.value !== '') {
        categorySelect.value = '';
    }
});

// Gestion des liens externes dynamiques
let lienExterneIndex = <?= !empty($liensExternes) ? count($liensExternes) : 0 ?>;

function addLienExterne() {
    const container = document.getElementById('liensExternesContainer');
    const newLien = document.createElement('div');
    newLien.className = 'lien-externe-item mb-3 p-3 border rounded';
    newLien.dataset.index = lienExterneIndex;
    
    newLien.innerHTML = `
        <div class="d-flex justify-content-between mb-2">
            <strong>Lien #${lienExterneIndex + 1}</strong>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeLienExterne(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="mb-2">
            <label class="form-label small">Titre</label>
            <input type="text" name="liens_externes[${lienExterneIndex}][titre]" class="form-control form-control-sm" placeholder="Ex: Instagram, LinkedIn, etc.">
        </div>
        <div class="mb-2">
            <label class="form-label small">URL</label>
            <input type="url" name="liens_externes[${lienExterneIndex}][url]" class="form-control form-control-sm" placeholder="https://...">
        </div>
        <div>
            <label class="form-label small">Icône (optionnel)</label>
            <input type="text" name="liens_externes[${lienExterneIndex}][icon]" class="form-control form-control-sm" placeholder="Ex: instagram, linkedin, globe">
            <small class="text-muted">Icônes FontAwesome (sans le préfixe fa-)</small>
        </div>
    `;
    
    container.appendChild(newLien);
    lienExterneIndex++;
    formChanged = true;
}

function removeLienExterne(button) {
    if (confirm('Supprimer ce lien externe ?')) {
        button.closest('.lien-externe-item').remove();
        formChanged = true;
    }
}

// Confirmation avant de quitter si modifications
let formChanged = false;
document.getElementById('editForm').addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.getElementById('editForm').addEventListener('submit', function() {
    formChanged = false;
});
</script>

<style>
.sticky-top {
    z-index: 1020;
}

.card-header {
    font-weight: 600;
}

textarea.form-control {
    font-family: inherit;
}
</style>
