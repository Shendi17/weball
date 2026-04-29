<?php 
/**
 * Template de détail d'une personnalité
 */
?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/personnalite">Personnalités</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($personnalite['prenom'] . ' ' . $personnalite['nom']) ?></li>
        </ol>
    </nav>

    <!-- Navigation par sections (sticky) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 sticky-top" style="top: 60px; z-index: 1020;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sectionsNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="sectionsNav">
                <ul class="navbar-nav nav-pills flex-nowrap overflow-auto" style="flex-wrap: nowrap;">
                    <li class="nav-item">
                        <a class="nav-link" href="#informations">
                            <i class="fas fa-info-circle me-1"></i>Informations
                        </a>
                    </li>
                    <?php if (!empty($personnalite['biographie'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#biographie">
                            <i class="fas fa-book me-1"></i>Biographie
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['oeuvres_principales'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#oeuvres">
                            <i class="fas fa-trophy me-1"></i>Œuvres
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['citations'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#citations">
                            <i class="fas fa-quote-left me-1"></i>Citations
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['anecdotes'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#anecdotes">
                            <i class="fas fa-lightbulb me-1"></i>Anecdotes
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['galerie_photos'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#galerie">
                            <i class="fas fa-images me-1"></i>Galerie
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['videos'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#videos">
                            <i class="fab fa-youtube me-1"></i>Vidéos
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['documentaires'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#documentaires">
                            <i class="fas fa-film me-1"></i>Documentaires
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['livres'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#livres">
                            <i class="fas fa-book me-1"></i>Livres
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['produits_associes'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#produits">
                            <i class="fas fa-shopping-bag me-1"></i>Produits
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['timeline'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#timeline">
                            <i class="fas fa-clock me-1"></i>Timeline
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['evenements'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#evenements">
                            <i class="fas fa-calendar-alt me-1"></i>Événements
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($personnalite['lieux_associes'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#lieux">
                            <i class="fas fa-map-marker-alt me-1"></i>Lieux
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- En-tête de la fiche -->
            <div class="card mb-4" id="informations">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <?php if (!empty($personnalite['photo'])): ?>
                                <img src="<?= htmlspecialchars($personnalite['photo']) ?>" 
                                     alt="<?= htmlspecialchars($personnalite['prenom'] . ' ' . $personnalite['nom']) ?>"
                                     class="img-fluid rounded shadow-sm mb-3"
                                     style="max-height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 250px;">
                                    <i class="fas fa-user fa-5x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h1 class="h2 mb-2">
                                <?= htmlspecialchars($personnalite['prenom'] . ' ' . $personnalite['nom']) ?>
                            </h1>
                            
                            <div class="mb-3">
                                <?php if (!empty($personnalite['type_nom'])): ?>
                                    <span class="badge bg-primary me-2">
                                        <i class="fas fa-<?= $personnalite['type_icon'] ?> me-1"></i>
                                        <?= htmlspecialchars($personnalite['type_nom']) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['category_nom'])): ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-<?= $personnalite['category_icon'] ?> me-1"></i>
                                        <?= htmlspecialchars($personnalite['category_nom']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($personnalite['titre'])): ?>
                                <p class="lead text-muted mb-3">
                                    <?= htmlspecialchars($personnalite['titre']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($personnalite['profession'])): ?>
                                <p class="lead text-muted mb-3">
                                    <i class="fas fa-briefcase me-2"></i><?= htmlspecialchars($personnalite['profession']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="row g-3">
                                <?php if (!empty($personnalite['nationalite'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Nationalité</small>
                                        <strong><?= htmlspecialchars($personnalite['nationalite']) ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['date_naissance'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">
                                            <?= !empty($personnalite['date_deces']) ? 'Naissance' : 'Date de naissance' ?>
                                        </small>
                                        <strong><?= date('d/m/Y', strtotime($personnalite['date_naissance'])) ?></strong>
                                        <?php if (empty($personnalite['date_deces'])): ?>
                                            <small class="text-muted">
                                                (<?= floor((time() - strtotime($personnalite['date_naissance'])) / 31536000) ?> ans)
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['lieu_naissance'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Lieu de naissance</small>
                                        <strong><?= htmlspecialchars($personnalite['lieu_naissance']) ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['date_deces'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Décès</small>
                                        <strong><?= date('d/m/Y', strtotime($personnalite['date_deces'])) ?></strong>
                                        <?php 
                                        $age = floor((strtotime($personnalite['date_deces']) - strtotime($personnalite['date_naissance'])) / 31536000);
                                        ?>
                                        <small class="text-muted">(<?= $age ?> ans)</small>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['lieu_deces'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Lieu de décès</small>
                                        <strong><?= htmlspecialchars($personnalite['lieu_deces']) ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['cause_deces'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Cause du décès</small>
                                        <strong><?= htmlspecialchars($personnalite['cause_deces']) ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($personnalite['statut'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Statut</small>
                                        <?php
                                        $statusColors = [
                                            'vivant' => 'success',
                                            'decede' => 'secondary',
                                            'legendaire' => 'warning',
                                            'inconnu' => 'info'
                                        ];
                                        $color = $statusColors[$personnalite['statut']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <?= ucfirst($personnalite['statut']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Biographie -->
            <?php if (!empty($personnalite['biographie'])): ?>
                <div class="card mb-4" id="biographie">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book me-2"></i>Biographie
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-justify" style="line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($personnalite['biographie'])) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Œuvres principales -->
            <?php if (!empty($personnalite['oeuvres_principales'])): 
                $oeuvres = json_decode($personnalite['oeuvres_principales'], true);
                if ($oeuvres):
            ?>
                <div class="card mb-4" id="oeuvres">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy me-2"></i>Œuvres & Réalisations Principales
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($oeuvres as $oeuvre): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <?= htmlspecialchars($oeuvre) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Citations -->
            <?php if (!empty($personnalite['citations'])): 
                $citations = json_decode($personnalite['citations'], true);
                if ($citations):
            ?>
                <div class="card mb-4" id="citations">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-quote-left me-2"></i>Citations Célèbres
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($citations as $citation): ?>
                            <blockquote class="blockquote mb-3 p-3 bg-light rounded">
                                <p class="mb-0"><i class="fas fa-quote-left text-muted me-2"></i><?= htmlspecialchars($citation) ?><i class="fas fa-quote-right text-muted ms-2"></i></p>
                            </blockquote>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Anecdotes -->
            <?php if (!empty($personnalite['anecdotes'])): ?>
                <div class="card mb-4" id="anecdotes">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Anecdotes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-justify" style="line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($personnalite['anecdotes'])) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Influence -->
            <?php if (!empty($personnalite['influence'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star me-2"></i>Influence & Héritage
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-justify" style="line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($personnalite['influence'])) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Galerie Photos -->
            <?php if (!empty($personnalite['galerie_photos'])): 
                $photos = json_decode($personnalite['galerie_photos'], true);
                if ($photos):
            ?>
                <div class="card mb-4" id="galerie">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-images me-2"></i>Galerie Photos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php foreach ($photos as $photo): ?>
                                <div class="col-md-4">
                                    <img src="<?= htmlspecialchars($photo) ?>" class="img-fluid rounded shadow-sm" alt="Photo" style="cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Vidéos -->
            <?php if (!empty($personnalite['videos'])): 
                $videos = json_decode($personnalite['videos'], true);
                if ($videos):
            ?>
                <div class="card mb-4" id="videos">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fab fa-youtube me-2"></i>Vidéos & Interviews
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($videos as $video): ?>
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="mb-2"><?= htmlspecialchars($video['titre']) ?></h6>
                                <small class="text-muted d-block mb-2"><?= htmlspecialchars($video['type']) ?></small>
                                <a href="<?= htmlspecialchars($video['url']) ?>" target="_blank" class="btn btn-sm btn-danger">
                                    <i class="fab fa-youtube me-1"></i>Voir la vidéo
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Documentaires -->
            <?php if (!empty($personnalite['documentaires'])): 
                $documentaires = json_decode($personnalite['documentaires'], true);
                if ($documentaires):
            ?>
                <div class="card mb-4" id="documentaires">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-film me-2"></i>Documentaires & Films
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($documentaires as $doc): ?>
                                <li class="list-group-item">
                                    <strong><?= htmlspecialchars($doc['titre']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= isset($doc['annee']) ? $doc['annee'] : '' ?>
                                        <?= isset($doc['plateforme']) ? ' - ' . $doc['plateforme'] : '' ?>
                                        <?= isset($doc['realisateur']) ? ' - Réalisé par ' . $doc['realisateur'] : '' ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Livres -->
            <?php if (!empty($personnalite['livres'])): 
                $livres = json_decode($personnalite['livres'], true);
                if ($livres):
            ?>
                <div class="card mb-4" id="livres">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book me-2"></i>Livres & Biographies
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($livres as $livre): ?>
                                <li class="list-group-item">
                                    <strong><?= htmlspecialchars($livre['titre']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= isset($livre['auteur']) ? 'Par ' . $livre['auteur'] : '' ?>
                                        <?= isset($livre['annee']) ? ' (' . $livre['annee'] . ')' : '' ?>
                                        <?= isset($livre['type']) ? ' - ' . $livre['type'] : '' ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Produits Associés -->
            <?php if (!empty($personnalite['produits_associes'])): 
                $produits = json_decode($personnalite['produits_associes'], true);
                if ($produits):
            ?>
                <div class="card mb-4" id="produits">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-bag me-2"></i>Produits & Marques Associés
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php foreach ($produits as $produit): ?>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded">
                                        <strong class="d-block"><?= htmlspecialchars($produit['nom']) ?></strong>
                                        <small class="text-muted">
                                            <?= isset($produit['type']) ? $produit['type'] : '' ?>
                                            <?= isset($produit['annee']) ? ' (' . $produit['annee'] . ')' : '' ?>
                                            <?= isset($produit['marque']) ? ' - ' . $produit['marque'] : '' ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Timeline -->
            <?php if (!empty($personnalite['timeline'])): 
                $timeline = json_decode($personnalite['timeline'], true);
                if ($timeline):
            ?>
                <div class="card mb-4" id="timeline">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Chronologie
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($timeline as $event): ?>
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                            <strong><?= htmlspecialchars($event['annee']) ?></strong>
                                        </div>
                                        <div class="ms-3">
                                            <p class="mb-0"><?= htmlspecialchars($event['evenement']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Événements Marquants -->
            <?php if (!empty($personnalite['evenements'])): 
                $evenements = json_decode($personnalite['evenements'], true);
                if ($evenements):
            ?>
                <div class="card mb-4" id="evenements">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Événements Marquants
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($evenements as $event): ?>
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong><?= htmlspecialchars($event['titre']) ?></strong>
                                    <span class="badge bg-secondary"><?= isset($event['date']) ? date('d/m/Y', strtotime($event['date'])) : '' ?></span>
                                </div>
                                <p class="mb-0 small"><?= htmlspecialchars($event['description']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Lieux Associés -->
            <?php if (!empty($personnalite['lieux_associes'])): 
                $lieux = json_decode($personnalite['lieux_associes'], true);
                if ($lieux):
            ?>
                <div class="card mb-4" id="lieux">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Lieux Importants
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($lieux as $lieu): ?>
                            <div class="mb-3">
                                <strong class="d-block"><?= htmlspecialchars($lieu['nom']) ?></strong>
                                <small class="text-muted">
                                    <?= isset($lieu['ville']) ? $lieu['ville'] : '' ?>
                                    <?= isset($lieu['pays']) ? ', ' . $lieu['pays'] : '' ?>
                                </small>
                                <?php if (isset($lieu['description'])): ?>
                                    <p class="mb-0 small mt-1"><?= htmlspecialchars($lieu['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Fondations -->
            <?php if (!empty($personnalite['fondations'])): 
                $fondations = json_decode($personnalite['fondations'], true);
                if ($fondations):
            ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-hands-helping me-2"></i>Fondations & Organisations
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($fondations as $fondation): ?>
                            <div class="mb-3">
                                <strong class="d-block"><?= htmlspecialchars($fondation['nom']) ?></strong>
                                <small class="text-muted">
                                    <?= isset($fondation['annee']) ? 'Créée en ' . $fondation['annee'] : '' ?>
                                    <?= isset($fondation['role']) ? $fondation['role'] : '' ?>
                                </small>
                                <?php if (isset($fondation['mission'])): ?>
                                    <p class="mb-0 small mt-1"><?= htmlspecialchars($fondation['mission']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Distinctions -->
            <?php if (!empty($personnalite['distinctions'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-award me-2"></i>Distinctions
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($personnalite['distinctions'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Liens externes -->
            <?php 
            $hasLinks = !empty($personnalite['site_web']) || !empty($personnalite['wikipedia_url']) || !empty($personnalite['reseaux_sociaux']);
            if ($hasLinks): 
            ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-external-link-alt me-2"></i>Liens Externes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($personnalite['site_web'])): ?>
                            <div class="mb-2">
                                <a href="<?= htmlspecialchars($personnalite['site_web']) ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-globe me-2"></i>Site Web Officiel
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($personnalite['wikipedia_url'])): ?>
                            <div class="mb-2">
                                <a href="<?= htmlspecialchars($personnalite['wikipedia_url']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="fab fa-wikipedia-w me-2"></i>Wikipedia
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($personnalite['reseaux_sociaux'])): 
                            $reseaux = json_decode($personnalite['reseaux_sociaux'], true);
                            if ($reseaux):
                        ?>
                            <?php if (isset($reseaux['instagram'])): ?>
                                <div class="mb-2">
                                    <a href="<?= htmlspecialchars($reseaux['instagram']) ?>" target="_blank" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="fab fa-instagram me-2"></i>Instagram
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($reseaux['twitter'])): ?>
                                <div class="mb-2">
                                    <a href="<?= htmlspecialchars($reseaux['twitter']) ?>" target="_blank" class="btn btn-sm btn-outline-info w-100">
                                        <i class="fab fa-twitter me-2"></i>Twitter / X
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($reseaux['facebook'])): ?>
                                <div class="mb-2">
                                    <a href="<?= htmlspecialchars($reseaux['facebook']) ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fab fa-facebook me-2"></i>Facebook
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Sources -->
            <?php if (!empty($personnalite['sources'])): 
                $sources = json_decode($personnalite['sources'], true);
                if ($sources):
            ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book-open me-2"></i>Sources & Références
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($sources as $source): ?>
                                <li class="mb-2">
                                    <i class="fas fa-link text-muted me-2"></i>
                                    <small><?= htmlspecialchars($source) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; endif; ?>
            
            <!-- Informations de contact -->
            <?php if (!empty($personnalite['email']) || !empty($personnalite['telephone'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-address-card me-2"></i>Contact
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($personnalite['email'])): ?>
                            <div class="mb-3">
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:<?= htmlspecialchars($personnalite['email']) ?>">
                                    <i class="fas fa-envelope me-2"></i>
                                    <?= htmlspecialchars($personnalite['email']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($personnalite['telephone'])): ?>
                            <div class="mb-3">
                                <small class="text-muted d-block">Téléphone</small>
                                <a href="tel:<?= htmlspecialchars($personnalite['telephone']) ?>">
                                    <i class="fas fa-phone me-2"></i>
                                    <?= htmlspecialchars($personnalite['telephone']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/personnalite?type=<?= $personnalite['type_id'] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>
                            Voir toutes les <?= htmlspecialchars($personnalite['type_nom']) ?>s
                        </a>
                        
                        <?php if (isAdmin()): ?>
                            <a href="/personnalite_edit?id=<?= $personnalite['id'] ?>" class="btn btn-outline-warning">
                                <i class="fas fa-edit me-2"></i>
                                Modifier
                            </a>
                            
                            <button class="btn btn-outline-danger" onclick="if(confirm('Êtes-vous sûr ?')) { window.location.href='/personnalite/delete?id=<?= $personnalite['id'] ?>'; }">
                                <i class="fas fa-trash me-2"></i>
                                Supprimer
                            </button>
                        <?php endif; ?>
                        
                        <a href="/personnalite" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Informations supplémentaires -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations
                    </h5>
                </div>
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Fiche créée le</small>
                    <p class="mb-3">
                        <?= date('d/m/Y à H:i', strtotime($personnalite['date_creation'])) ?>
                    </p>
                    
                    <small class="text-muted d-block mb-2">ID de la fiche</small>
                    <p class="mb-0">
                        <code>#<?= $personnalite['id'] ?></code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-justify {
    text-align: justify;
}

/* Navbar sections sticky */
.sticky-top {
    position: sticky;
    z-index: 1020;
}

/* Scroll offset pour les ancres */
html {
    scroll-padding-top: 140px;
    scroll-behavior: smooth;
}

/* Style des onglets de navigation */
#sectionsNav .nav-link {
    color: #495057;
    border-radius: 20px;
    padding: 8px 16px;
    margin: 0 4px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

#sectionsNav .nav-link:hover {
    background-color: #e9ecef;
    color: #0d6efd;
}

#sectionsNav .nav-link.active {
    background-color: #0d6efd;
    color: white;
}

/* Overflow horizontal pour mobile */
#sectionsNav .navbar-nav {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

#sectionsNav .navbar-nav::-webkit-scrollbar {
    height: 4px;
}

#sectionsNav .navbar-nav::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 4px;
}
</style>

<script>
// Smooth scroll et activation des onglets
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('#sectionsNav .nav-link');
    const sections = document.querySelectorAll('[id]');
    
    // Gestion du clic sur les onglets
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer la classe active de tous les liens
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Ajouter la classe active au lien cliqué
            this.classList.add('active');
            
            // Scroll vers la section
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 140; // Offset pour la navbar sticky
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Activation automatique de l'onglet lors du scroll
    let isScrolling = false;
    
    window.addEventListener('scroll', function() {
        if (!isScrolling) {
            window.requestAnimationFrame(function() {
                let current = '';
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 150;
                    const sectionHeight = section.offsetHeight;
                    
                    if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });
                
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active');
                    }
                });
                
                isScrolling = false;
            });
            
            isScrolling = true;
        }
    });
    
    // Activer le premier onglet au chargement
    if (navLinks.length > 0) {
        navLinks[0].classList.add('active');
    }
});
</script>
