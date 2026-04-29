<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-edit me-2"></i>Modifier l'autorité
            </h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($autorite['nom']) ?></p>
        </div>
        <a href="/autorite_detail?id=<?= $autorite['id'] ?>" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i> Annuler
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" id="editForm">
        <div class="row">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Informations de base -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de base</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?= htmlspecialchars($autorite['nom']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo (URL)</label>
                            <input type="url" class="form-control" id="logo" name="logo" 
                                   value="<?= htmlspecialchars($autorite['logo'] ?? '') ?>"
                                   placeholder="https://exemple.com/logo.png">
                            <small class="text-muted">URL complète du logo (PNG, JPG, SVG)</small>
                            <?php if (!empty($autorite['logo'])): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($autorite['logo']) ?>" 
                                         alt="Logo actuel" 
                                         class="rounded border"
                                         style="max-width: 150px; max-height: 150px; object-fit: contain; padding: 10px; background: white;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div style="display: none;" class="alert alert-warning mt-2">
                                        ⚠️ Le logo actuel ne peut pas être chargé. Vérifiez l'URL ou ajoutez-en un nouveau.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="types_ids" class="form-label">Types <span class="text-danger">*</span></label>
                                <select class="form-select" id="types_ids" name="types_ids[]" multiple size="5" required>
                                    <?php 
                                    $selectedTypes = !empty($autorite['types_ids']) ? json_decode($autorite['types_ids'], true) : [];
                                    if (empty($selectedTypes) && !empty($autorite['type_id'])) {
                                        $selectedTypes = [(int)$autorite['type_id']];
                                    }
                                    foreach ($types as $type): 
                                    ?>
                                        <option value="<?= $type['id'] ?>" 
                                                <?= in_array($type['id'], $selectedTypes) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs types</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="categories_ids" class="form-label">Catégories</label>
                                <select class="form-select" id="categories_ids" name="categories_ids[]" multiple size="5">
                                    <?php 
                                    $selectedCategories = !empty($autorite['categories_ids']) ? json_decode($autorite['categories_ids'], true) : [];
                                    if (empty($selectedCategories) && !empty($autorite['category_id'])) {
                                        $selectedCategories = [(int)$autorite['category_id']];
                                    }
                                    foreach ($categories as $category): 
                                    ?>
                                        <option value="<?= $category['id'] ?>" 
                                                data-type="<?= $category['type_id'] ?>"
                                                <?= in_array($category['id'], $selectedCategories) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs catégories</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description courte</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($autorite['description'] ?? '') ?></textarea>
                            <small class="text-muted">Résumé court (affiché dans les listes)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="domaine" class="form-label">Domaine d'activité</label>
                                <input type="text" class="form-control" id="domaine" name="domaine" 
                                       value="<?= htmlspecialchars($autorite['domaine'] ?? '') ?>"
                                       placeholder="Ex: Technologie, Finance, Santé...">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="pays" name="pays" 
                                       value="<?= htmlspecialchars($autorite['pays'] ?? '') ?>"
                                       placeholder="Ex: France, États-Unis...">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="site_web" class="form-label">Site Web</label>
                            <input type="url" class="form-control" id="site_web" name="site_web" 
                                   value="<?= htmlspecialchars($autorite['site_web'] ?? '') ?>"
                                   placeholder="https://exemple.com">
                        </div>
                    </div>
                </div>

                <!-- Coordonnées -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Coordonnées</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse complète</label>
                            <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($autorite['adresse'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?= htmlspecialchars($autorite['telephone'] ?? '') ?>"
                                       placeholder="+33 1 23 45 67 89">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($autorite['email'] ?? '') ?>"
                                       placeholder="contact@exemple.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations organisationnelles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Informations organisationnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dirigeant" class="form-label">Dirigeant</label>
                                <input type="text" class="form-control" id="dirigeant" name="dirigeant" 
                                       value="<?= htmlspecialchars($autorite['dirigeant'] ?? '') ?>"
                                       placeholder="Ex: Jean Dupont (PDG)">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_creation_org" class="form-label">Date de création</label>
                                <input type="text" class="form-control" id="date_creation_org" name="date_creation_org" 
                                       value="<?= htmlspecialchars($autorite['date_creation_org'] ?? '') ?>"
                                       placeholder="Ex: 1990 ou 15/03/1990">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="effectifs" class="form-label">Effectifs</label>
                                <input type="text" class="form-control" id="effectifs" name="effectifs" 
                                       value="<?= htmlspecialchars($autorite['effectifs'] ?? '') ?>"
                                       placeholder="Ex: 500 employés">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="chiffre_affaires" class="form-label">Chiffre d'affaires</label>
                                <input type="text" class="form-control" id="chiffre_affaires" name="chiffre_affaires" 
                                       value="<?= htmlspecialchars($autorite['chiffre_affaires'] ?? '') ?>"
                                       placeholder="Ex: 10 millions € (2023)">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="secteur" class="form-label">Secteur d'activité</label>
                                <input type="text" class="form-control" id="secteur" name="secteur" 
                                       value="<?= htmlspecialchars($autorite['secteur'] ?? '') ?>"
                                       placeholder="Ex: Aéronautique et spatial">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description détaillée -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Description détaillée</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="description_longue" class="form-label">Description longue</label>
                            <textarea class="form-control" id="description_longue" name="description_longue" rows="6"><?= htmlspecialchars($autorite['description_longue'] ?? '') ?></textarea>
                            <small class="text-muted">Description complète de l'organisation</small>
                        </div>

                        <div class="mb-3">
                            <label for="missions" class="form-label">Missions</label>
                            <textarea class="form-control" id="missions" name="missions" rows="4"><?= htmlspecialchars($autorite['missions'] ?? '') ?></textarea>
                            <small class="text-muted">Objectifs et missions principales</small>
                        </div>

                        <div class="mb-3">
                            <label for="realisations" class="form-label">Réalisations</label>
                            <textarea class="form-control" id="realisations" name="realisations" rows="4"><?= htmlspecialchars($autorite['realisations'] ?? '') ?></textarea>
                            <small class="text-muted">Produits, services, accomplissements majeurs</small>
                        </div>

                        <div class="mb-0">
                            <label for="partenaires" class="form-label">Partenaires</label>
                            <textarea class="form-control" id="partenaires" name="partenaires" rows="3"><?= htmlspecialchars($autorite['partenaires'] ?? '') ?></textarea>
                            <small class="text-muted">Partenaires principaux et concurrents</small>
                        </div>
                    </div>
                </div>

                <!-- Réseaux sociaux -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Réseaux sociaux</h5>
                    </div>
                    <div class="card-body">
                        <div id="reseauxContainer">
                            <?php 
                            $reseaux = !empty($autorite['reseaux_sociaux']) ? json_decode($autorite['reseaux_sociaux'], true) : [];
                            if (empty($reseaux)) {
                                $reseaux = [['nom' => '', 'url' => '']];
                            }
                            foreach ($reseaux as $index => $reseau): 
                            ?>
                            <div class="reseau-item mb-3 p-3 border rounded" data-index="<?= $index ?>">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Réseau #<?= $index + 1 ?></strong>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeReseau(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Nom</label>
                                        <input type="text" name="reseaux[<?= $index ?>][nom]" class="form-control form-control-sm" 
                                               value="<?= htmlspecialchars($reseau['nom'] ?? '') ?>" placeholder="Ex: LinkedIn, Twitter">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">URL</label>
                                        <input type="url" name="reseaux[<?= $index ?>][url]" class="form-control form-control-sm" 
                                               value="<?= htmlspecialchars($reseau['url'] ?? '') ?>" placeholder="https://...">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addReseau()">
                            <i class="fas fa-plus me-1"></i> Ajouter un réseau
                        </button>
                    </div>
                </div>

                <!-- Informations avancées -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Informations avancées</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="0.00000001" class="form-control" id="latitude" name="latitude" 
                                       value="<?= htmlspecialchars($autorite['latitude'] ?? '') ?>" placeholder="Ex: 48.8566">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="0.00000001" class="form-control" id="longitude" name="longitude" 
                                       value="<?= htmlspecialchars($autorite['longitude'] ?? '') ?>" placeholder="Ex: 2.3522">
                            </div>
                        </div>
                        <small class="text-muted">Pour obtenir les coordonnées GPS, utilisez <a href="https://www.google.com/maps" target="_blank">Google Maps</a> (clic droit sur le lieu)</small>
                        
                        <div class="mt-3 mb-3">
                            <label for="historique" class="form-label">Historique</label>
                            <textarea class="form-control" id="historique" name="historique" rows="4"><?= htmlspecialchars($autorite['historique'] ?? '') ?></textarea>
                            <small class="text-muted">Histoire et évolution de l'organisation</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="certifications" class="form-label">Certifications</label>
                            <textarea class="form-control" id="certifications" name="certifications" rows="2"><?= htmlspecialchars($autorite['certifications'] ?? '') ?></textarea>
                            <small class="text-muted">Ex: ISO 9001, ISO 14001, etc.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="engagement_rse" class="form-label">Engagement RSE</label>
                            <textarea class="form-control" id="engagement_rse" name="engagement_rse" rows="3"><?= htmlspecialchars($autorite['engagement_rse'] ?? '') ?></textarea>
                            <small class="text-muted">Responsabilité Sociale et Environnementale</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="filiales" class="form-label">Filiales</label>
                                <textarea class="form-control" id="filiales" name="filiales" rows="3"><?= htmlspecialchars($autorite['filiales'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="actionnaires" class="form-label">Actionnaires</label>
                                <textarea class="form-control" id="actionnaires" name="actionnaires" rows="3"><?= htmlspecialchars($autorite['actionnaires'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations légales -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Informations légales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="siret" class="form-label">SIRET</label>
                                <input type="text" class="form-control" id="siret" name="siret" 
                                       value="<?= htmlspecialchars($autorite['siret'] ?? '') ?>" placeholder="123 456 789 00012">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tva_intracommunautaire" class="form-label">TVA Intracommunautaire</label>
                                <input type="text" class="form-control" id="tva_intracommunautaire" name="tva_intracommunautaire" 
                                       value="<?= htmlspecialchars($autorite['tva_intracommunautaire'] ?? '') ?>" placeholder="FR12345678901">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="forme_juridique" class="form-label">Forme juridique</label>
                                <input type="text" class="form-control" id="forme_juridique" name="forme_juridique" 
                                       value="<?= htmlspecialchars($autorite['forme_juridique'] ?? '') ?>" placeholder="Ex: SA, SAS, SARL">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="capital_social" class="form-label">Capital social</label>
                                <input type="text" class="form-control" id="capital_social" name="capital_social" 
                                       value="<?= htmlspecialchars($autorite['capital_social'] ?? '') ?>" placeholder="Ex: 1 000 000 €">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bourse_cotation" class="form-label">Bourse (cotation)</label>
                                <input type="text" class="form-control" id="bourse_cotation" name="bourse_cotation" 
                                       value="<?= htmlspecialchars($autorite['bourse_cotation'] ?? '') ?>" placeholder="Ex: Euronext Paris">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="code_bourse" class="form-label">Code bourse</label>
                                <input type="text" class="form-control" id="code_bourse" name="code_bourse" 
                                       value="<?= htmlspecialchars($autorite['code_bourse'] ?? '') ?>" placeholder="Ex: AIR (EPA)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liens externes directs -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i>Liens externes directs</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="wikipedia_url" class="form-label">Wikipedia</label>
                                <input type="url" class="form-control" id="wikipedia_url" name="wikipedia_url" 
                                       value="<?= htmlspecialchars($autorite['wikipedia_url'] ?? '') ?>" placeholder="https://fr.wikipedia.org/wiki/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                       value="<?= htmlspecialchars($autorite['linkedin_url'] ?? '') ?>" placeholder="https://www.linkedin.com/company/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="twitter_url" class="form-label">Twitter</label>
                                <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                       value="<?= htmlspecialchars($autorite['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="facebook_url" class="form-label">Facebook</label>
                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                       value="<?= htmlspecialchars($autorite['facebook_url'] ?? '') ?>" placeholder="https://www.facebook.com/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instagram_url" class="form-label">Instagram</label>
                                <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                       value="<?= htmlspecialchars($autorite['instagram_url'] ?? '') ?>" placeholder="https://www.instagram.com/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="youtube_url" class="form-label">YouTube</label>
                                <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                       value="<?= htmlspecialchars($autorite['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="blog_url" class="form-label">Blog</label>
                                <input type="url" class="form-control" id="blog_url" name="blog_url" 
                                       value="<?= htmlspecialchars($autorite['blog_url'] ?? '') ?>" placeholder="https://blog.exemple.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="offres_emploi_url" class="form-label">Offres d'emploi</label>
                                <input type="url" class="form-control" id="offres_emploi_url" name="offres_emploi_url" 
                                       value="<?= htmlspecialchars($autorite['offres_emploi_url'] ?? '') ?>" placeholder="https://careers.exemple.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-save me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <a href="/autorite_detail?id=<?= $autorite['id'] ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                </div>

                <!-- Aide -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Aide</h5>
                    </div>
                    <div class="card-body">
                        <h6>Logo</h6>
                        <p class="small text-muted">
                            Pour trouver des logos officiels, utilisez :
                        </p>
                        <ul class="small text-muted">
                            <li><a href="https://commons.wikimedia.org" target="_blank">Wikimedia Commons</a></li>
                            <li>Site officiel de l'organisation</li>
                            <li>Recherche Google Images (droits d'utilisation)</li>
                        </ul>
                        
                        <h6 class="mt-3">Format recommandé</h6>
                        <ul class="small text-muted mb-0">
                            <li>Format : PNG ou SVG</li>
                            <li>Fond transparent de préférence</li>
                            <li>Taille : 200-500px</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Filtrer les catégories selon les types sélectionnés
document.getElementById('types_ids').addEventListener('change', function() {
    const selectedTypes = Array.from(this.selectedOptions).map(opt => opt.value);
    const categorySelect = document.getElementById('categories_ids');
    const options = categorySelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (selectedTypes.length === 0) {
            // Si aucun type sélectionné, afficher toutes les catégories
            option.style.display = 'block';
        } else if (selectedTypes.includes(option.dataset.type)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
            // Désélectionner si la catégorie n'est plus valide
            option.selected = false;
        }
    });
});

// Déclencher le filtre au chargement
document.getElementById('types_ids').dispatchEvent(new Event('change'));

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

// Prévisualisation du logo en temps réel
document.getElementById('logo').addEventListener('input', function() {
    const url = this.value;
    const preview = this.parentElement.querySelector('img');
    
    if (url && preview) {
        preview.src = url;
        preview.style.display = 'block';
        preview.nextElementSibling.style.display = 'none';
    }
});

// Gestion des réseaux sociaux
let reseauIndex = <?= count($reseaux) ?>;

function addReseau() {
    const container = document.getElementById('reseauxContainer');
    const html = `
        <div class="reseau-item mb-3 p-3 border rounded" data-index="${reseauIndex}">
            <div class="d-flex justify-content-between mb-2">
                <strong>Réseau #${reseauIndex + 1}</strong>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeReseau(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label small">Nom</label>
                    <input type="text" name="reseaux[${reseauIndex}][nom]" class="form-control form-control-sm" placeholder="Ex: LinkedIn, Twitter">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label small">URL</label>
                    <input type="url" name="reseaux[${reseauIndex}][url]" class="form-control form-control-sm" placeholder="https://...">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    reseauIndex++;
    formChanged = true;
}

function removeReseau(button) {
    if (confirm('Supprimer ce réseau social ?')) {
        button.closest('.reseau-item').remove();
        formChanged = true;
    }
}
</script>
