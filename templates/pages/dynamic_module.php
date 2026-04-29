<?php 
/**
 * Template dynamique avec navbar secondaire (types) et sidebar secondaire (catégories)
 * Variables requises: $moduleName, $types, $categories, $items, $selectedType, $selectedCategory
 */
?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-<?= $moduleIcon ?? 'circle' ?> me-2"></i>
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

<!-- Navbar Secondaire (Types) -->
<nav class="secondary-navbar mb-4">
    <div class="nav nav-tabs" id="typesNav" role="tablist">
        <button class="nav-link <?= empty($selectedType) ? 'active' : '' ?>" 
                data-type="all" 
                onclick="filterByType('all')">
            <i class="fas fa-th me-2"></i>
            Tous
        </button>
        <?php foreach ($types as $type): ?>
            <button class="nav-link <?= $selectedType == $type['id'] ? 'active' : '' ?>" 
                    data-type="<?= $type['id'] ?>"
                    onclick="filterByType(<?= $type['id'] ?>)">
                <i class="fas fa-<?= $type['icon'] ?> me-2"></i>
                <?= htmlspecialchars($type['nom']) ?>
            </button>
        <?php endforeach; ?>
    </div>
</nav>

<div class="row">
    <!-- Sidebar Secondaire (Catégories) -->
    <div class="col-md-3 mb-4">
        <div class="card secondary-sidebar">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Catégories
                </h5>
            </div>
            <div class="card-body p-0">
                <div id="categoriesContainer">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action <?= empty($selectedCategory) ? 'active' : '' ?>"
                           onclick="filterByCategory(null); return false;">
                            <i class="fas fa-th me-2"></i> Toutes
                        </a>
                        <?php 
                        // Si un type est sélectionné, afficher ses catégories
                        // Sinon, afficher toutes les catégories
                        $displayCategories = $selectedType 
                            ? array_filter($categories, function($cat) use ($selectedType) {
                                return $cat['type_id'] == $selectedType;
                            })
                            : $categories;
                        
                        foreach ($displayCategories as $category): 
                        ?>
                            <a href="#" class="list-group-item list-group-item-action <?= $selectedCategory == $category['id'] ? 'active' : '' ?>"
                               onclick="filterByCategory(<?= $category['id'] ?>); return false;">
                                <i class="fas fa-<?= $category['icon'] ?> me-2"></i>
                                <?= htmlspecialchars($category['nom']) ?>
                                <?php if (!$selectedType): ?>
                                    <small class="text-muted d-block">
                                        <?php 
                                        // Afficher le type parent
                                        $parentType = array_filter($types, function($t) use ($category) {
                                            return $t['id'] == $category['type_id'];
                                        });
                                        $parentType = reset($parentType);
                                        echo $parentType ? $parentType['nom'] : '';
                                        ?>
                                    </small>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total</span>
                    <strong id="totalCount"><?= $totalCount ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Affichés</span>
                    <strong id="displayedCount"><?= count($items) ?></strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenu Principal -->
    <div class="col-md-9">
        <!-- Barre de recherche et filtres -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher..." onkeyup="searchItems()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sortSelect" onchange="sortItems()">
                            <option value="recent">Plus récents</option>
                            <option value="old">Plus anciens</option>
                            <option value="name">Nom (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="viewList" onclick="switchView('list')">
                                <i class="fas fa-list"></i> Liste
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="viewGrid" onclick="switchView('grid')">
                                <i class="fas fa-th"></i> Grille
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des items -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des fiches</h5>
                    <span class="badge bg-primary" id="itemsCount"><?= count($items) ?> fiche(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="itemsContainer">
                    <?php if (empty($items)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-<?= $moduleIcon ?? 'circle' ?> fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune fiche disponible.</p>
                            <?php if ($isAdmin): ?>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus me-1"></i> Créer la première fiche
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Informations</th>
                                        <th>Type</th>
                                        <th>Catégorie</th>
                                        <th>Date</th>
                                        <?php if ($isAdmin): ?>
                                            <th class="text-end">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr class="item-row" 
                                            data-type="<?= $item['type_id'] ?? '' ?>" 
                                            data-category="<?= $item['category_id'] ?? '' ?>"
                                            data-name="<?= htmlspecialchars($item['nom'] ?? $item['titre'] ?? $item['name'] ?? '') ?>">
                                            <td class="align-middle">
                                                <strong>#<?= $item['id'] ?></strong>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                    // Afficher logo (pour autorité) ou photo (pour personnalité)
                                                    $imageUrl = $item['logo'] ?? $item['photo'] ?? null;
                                                    if (!empty($imageUrl)): 
                                                    ?>
                                                        <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                                             alt="<?= htmlspecialchars($item['nom'] ?? '') ?>"
                                                             class="rounded me-3"
                                                             style="width: 50px; height: 50px; object-fit: <?= isset($item['logo']) ? 'contain' : 'cover' ?>; <?= isset($item['logo']) ? 'padding: 5px; background: white; border: 1px solid #dee2e6;' : '' ?>">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong>
                                                            <a href="/<?= $moduleName ?>_detail?id=<?= $item['id'] ?>" class="text-decoration-none text-dark">
                                                                <?= htmlspecialchars(($item['prenom'] ?? '') . ' ' . ($item['nom'] ?? $item['titre'] ?? $item['name'] ?? 'N/A')) ?>
                                                            </a>
                                                        </strong>
                                                        <?php if (!empty($item['biographie']) || !empty($item['description'])): ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?= htmlspecialchars(substr($item['biographie'] ?? $item['description'] ?? '', 0, 80)) ?>...
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($item['type_id'])): ?>
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($item['type_nom'] ?? 'Type') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($item['category_id'])): ?>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($item['category_nom'] ?? 'Catégorie') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <small class="text-muted">
                                                    <?= isset($item['date_creation']) ? date('d/m/Y', strtotime($item['date_creation'])) : 'N/A' ?>
                                                </small>
                                            </td>
                                            <?php if ($isAdmin): ?>
                                                <td class="align-middle text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="/<?= $moduleName ?>_detail?id=<?= $item['id'] ?>" class="btn btn-outline-primary" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-warning" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-danger" title="Supprimer" 
                                                           onclick="return confirm('Êtes-vous sûr ?')">
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
                
                <!-- Vue Grille (cachée par défaut) -->
                <div id="gridView" class="row g-3 p-3" style="display: none;">
                    <?php foreach ($items as $item): ?>
                        <div class="col-md-4 col-lg-3 item-card" 
                             data-type="<?= $item['type_id'] ?? '' ?>" 
                             data-category="<?= $item['category_id'] ?? '' ?>"
                             data-name="<?= htmlspecialchars($item['nom'] ?? $item['titre'] ?? $item['name'] ?? '') ?>">
                            <div class="card h-100 shadow-sm hover-card">
                                <?php if (!empty($item['photo'])): ?>
                                    <img src="<?= htmlspecialchars($item['photo']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($item['nom'] ?? '') ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-user fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title mb-2">
                                        <?= htmlspecialchars(($item['prenom'] ?? '') . ' ' . ($item['nom'] ?? $item['titre'] ?? $item['name'] ?? 'N/A')) ?>
                                    </h6>
                                    <div class="mb-2">
                                        <?php if (!empty($item['type_id'])): ?>
                                            <span class="badge bg-info badge-sm">
                                                <i class="fas fa-<?= $item['type_icon'] ?? 'circle' ?> me-1"></i>
                                                <?= htmlspecialchars($item['type_nom'] ?? 'Type') ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($item['biographie'])): ?>
                                        <p class="card-text small text-muted">
                                            <?= htmlspecialchars(substr($item['biographie'], 0, 100)) ?>...
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?= isset($item['date_creation']) ? date('d/m/Y', strtotime($item['date_creation'])) : 'N/A' ?>
                                        </small>
                                        <a href="/<?= $moduleName ?>_detail?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.secondary-navbar {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 10px;
}
.secondary-navbar .nav-link {
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    margin: 0 5px;
    transition: all 0.3s;
    cursor: pointer;
}
.secondary-navbar .nav-link:hover {
    background: #f0f0f0;
}
.secondary-navbar .nav-link.active {
    background: #007bff;
    color: white;
}
.secondary-sidebar .list-group-item {
    border-left: 3px solid transparent;
    transition: all 0.3s;
}
.secondary-sidebar .list-group-item:hover {
    background: #f8f9fa;
    border-left-color: #007bff;
}
.secondary-sidebar .list-group-item.active {
    background: #007bff;
    color: white;
    border-left-color: #0056b3;
}
.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2) !important;
}
</style>

<script>
let currentView = 'list';

function switchView(view) {
    currentView = view;
    const listView = document.getElementById('itemsTable')?.closest('.table-responsive');
    const gridView = document.getElementById('gridView');
    const btnList = document.getElementById('viewList');
    const btnGrid = document.getElementById('viewGrid');
    
    if (view === 'list') {
        if (listView) listView.style.display = 'block';
        if (gridView) gridView.style.display = 'none';
        btnList?.classList.add('active');
        btnGrid?.classList.remove('active');
    } else {
        if (listView) listView.style.display = 'none';
        if (gridView) gridView.style.display = 'flex';
        btnList?.classList.remove('active');
        btnGrid?.classList.add('active');
    }
}
</script>

<script>
let currentType = <?= json_encode($selectedType ?? null) ?>;
let currentCategory = <?= json_encode($selectedCategory ?? null) ?>;
let allCategories = <?= json_encode($categories) ?>;

function filterByType(typeId) {
    currentType = typeId === 'all' ? null : typeId;
    currentCategory = null;
    
    // Mettre à jour l'UI
    document.querySelectorAll('.secondary-navbar .nav-link').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.nav-link').classList.add('active');
    
    // Mettre à jour les catégories
    updateCategories();
    
    // Filtrer les items
    filterItems();
}

function filterByCategory(categoryId) {
    currentCategory = categoryId;
    filterItems();
}

function updateCategories() {
    const container = document.getElementById('categoriesContainer');
    
    // Filtrer les catégories selon le type sélectionné
    const displayCategories = currentType 
        ? allCategories.filter(cat => cat.type_id == currentType)
        : allCategories;
    
    let html = '<div class="list-group list-group-flush">';
    html += '<a href="#" class="list-group-item list-group-item-action active" onclick="filterByCategory(null); return false;"><i class="fas fa-th me-2"></i> Toutes</a>';
    
    displayCategories.forEach(cat => {
        html += `<a href="#" class="list-group-item list-group-item-action" onclick="filterByCategory(${cat.id}); return false;">
            <i class="fas fa-${cat.icon} me-2"></i> ${cat.nom}
        </a>`;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function filterItems() {
    // Filtrer les lignes du tableau
    const rows = document.querySelectorAll('.item-row');
    // Filtrer les cartes de la grille
    const cards = document.querySelectorAll('.item-card');
    let visibleCount = 0;
    
    // Filtrer les lignes
    rows.forEach(row => {
        const itemType = row.dataset.type;
        const itemCategory = row.dataset.category;
        
        let show = true;
        
        if (currentType && itemType != currentType) {
            show = false;
        }
        
        if (currentCategory && itemCategory != currentCategory) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    // Filtrer les cartes
    visibleCount = 0;
    cards.forEach(card => {
        const itemType = card.dataset.type;
        const itemCategory = card.dataset.category;
        
        let show = true;
        
        if (currentType && itemType != currentType) {
            show = false;
        }
        
        if (currentCategory && itemCategory != currentCategory) {
            show = false;
        }
        
        card.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    document.getElementById('displayedCount').textContent = visibleCount;
    document.getElementById('itemsCount').textContent = visibleCount + ' fiche(s)';
}

function searchItems() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    const cards = document.querySelectorAll('.item-card');
    let visibleCount = 0;
    
    // Recherche dans les lignes
    rows.forEach(row => {
        const name = row.dataset.name.toLowerCase();
        const show = name.includes(search);
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    // Recherche dans les cartes
    visibleCount = 0;
    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const show = name.includes(search);
        card.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    document.getElementById('displayedCount').textContent = visibleCount;
    document.getElementById('itemsCount').textContent = visibleCount + ' fiche(s)';
}

function sortItems() {
    // TODO: Implémenter le tri
    console.log('Sort:', document.getElementById('sortSelect').value);
}
</script>
