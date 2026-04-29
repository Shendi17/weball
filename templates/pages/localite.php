<?php /* Template de la page Localité */ ?>
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-map-marker-alt me-2"></i>
                <?= $pageTitle ?>
            </h1>
            <p class="text-muted mb-0"><?= $pageDescription ?></p>
        </div>
        <?php if ($isAdmin): ?>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus me-1"></i> Ajouter
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Navbar secondaire -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 rounded">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#secondaryNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="secondaryNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        <i class="fas fa-list me-1"></i> Tous
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-star me-1"></i> Favoris
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-clock me-1"></i> Récents
                    </a>
                </li>
            </ul>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Rechercher..." aria-label="Rechercher">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="row">
    <!-- Sidebar secondaire -->
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filtres
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Catégories</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="cat1" checked>
                        <label class="form-check-label" for="cat1">Tous</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="cat2">
                        <label class="form-check-label" for="cat2">Actifs</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="cat3">
                        <label class="form-check-label" for="cat3">Archivés</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Période</label>
                    <select class="form-select form-select-sm">
                        <option>Tout</option>
                        <option>Aujourd'hui</option>
                        <option>Cette semaine</option>
                        <option>Ce mois</option>
                        <option>Cette année</option>
                    </select>
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total</span>
                    <strong><?= $totalCount ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Actifs</span>
                    <strong><?= $totalCount ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Ce mois</span>
                    <strong><?= min($totalCount, 3) ?></strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des fiches</h5>
                    <span class="badge bg-primary"><?= count($items) ?> fiche(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($items)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune fiche disponible pour le moment.</p>
                        <?php if ($isAdmin): ?>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fas fa-plus me-1"></i> Créer la première fiche
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Informations</th>
                                    <th>Date</th>
                                    <?php if ($isAdmin): ?>
                                        <th class="text-end">Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <strong>#<?= $item['id'] ?></strong>
                                        </td>
                                        <td class="align-middle">
                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars($item['nom'] ?? $item['titre'] ?? $item['name'] ?? 'N/A') ?>
                                                </strong>
                                                <?php if (isset($item['description'])): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars(substr($item['description'], 0, 100)) ?>
                                                        <?= strlen($item['description']) > 100 ? '...' : '' ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <small class="text-muted">
                                                <?= isset($item['date_creation']) ? date('d/m/Y', strtotime($item['date_creation'])) : 'N/A' ?>
                                            </small>
                                        </td>
                                        <?php if ($isAdmin): ?>
                                            <td class="align-middle text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-danger" title="Supprimer" 
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout (si admin) -->
<?php if ($isAdmin): ?>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Ajouter une fiche
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <p class="text-muted">Formulaire d'ajout à implémenter selon les besoins spécifiques.</p>
                    <!-- Champs du formulaire à ajouter ici -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
