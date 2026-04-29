<?php /* Template de la page Registre */ ?>
<div class="container mt-5">
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Colonne de gauche : Catégories et Filtres -->
            <div class="col-md-3">
                <!-- Catégories -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Catégories</h2>
                        <?php if ($isAdmin): ?>
                            <a href="#" class="btn btn-sm btn-primary">Ajouter</a>
                        <?php endif; ?>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($categories as $cat): ?>
                            <li class="list-group-item"><?= htmlspecialchars($cat) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <!-- Colonne centrale : Documents -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Documents</h2>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($documents as $doc): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars($doc['titre']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
