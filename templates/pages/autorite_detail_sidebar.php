<!-- Actions -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h5>
    </div>
    <div class="card-body">
        <a href="/autorite" class="btn btn-outline-secondary w-100 mb-2">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
        <?php if ($isAdmin): ?>
        <a href="/autorite_edit?id=<?= $autorite['id'] ?>" class="btn btn-warning w-100 mb-2">
            <i class="fas fa-edit me-1"></i> Modifier
        </a>
        <button class="btn btn-danger w-100" onclick="if(confirm('Supprimer cette autorité ?')) window.location.href='/autorite_delete?id=<?= $autorite['id'] ?>'">
            <i class="fas fa-trash me-1"></i> Supprimer
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Distinctions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Distinctions</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($allTypes)): ?>
        <div class="mb-3">
            <strong class="d-block mb-2">Types :</strong>
            <?php foreach ($allTypes as $type): ?>
                <span class="badge bg-primary me-1 mb-1">
                    <i class="fas fa-<?= htmlspecialchars($type['icon']) ?> me-1"></i>
                    <?= htmlspecialchars($type['nom']) ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($allCategories)): ?>
        <div class="mb-3">
            <strong class="d-block mb-2">Catégories :</strong>
            <?php foreach ($allCategories as $category): ?>
                <span class="badge bg-secondary me-1 mb-1">
                    <?= htmlspecialchars($category['nom']) ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($autorite['date_creation'])): ?>
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-calendar me-1"></i>
                Ajouté le <?= date('d/m/Y', strtotime($autorite['date_creation'])) ?>
            </small>
        </div>
        <?php endif; ?>
    </div>
</div>
