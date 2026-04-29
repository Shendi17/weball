<?php /* Template de la page Annuaire */ ?>
<div class="container mt-5">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Annuaire des personnes</h2>
                        <?php if ($isAdmin): ?>
                            <a href="#" class="btn btn-sm btn-primary">Ajouter</a>
                        <?php endif; ?>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($personnes as $p): ?>
                            <li class="list-group-item">
                                <?= htmlspecialchars($p['nom']) ?> <?= htmlspecialchars($p['prenom']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
