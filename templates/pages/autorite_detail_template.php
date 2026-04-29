<?php
/**
 * Template de détail d'une autorité
 */

// Décoder les données JSON
$reseauxSociaux = !empty($autorite['reseaux_sociaux']) ? json_decode($autorite['reseaux_sociaux'], true) : [];
$videos = !empty($autorite['videos']) ? json_decode($autorite['videos'], true) : [];
$galerieImages = !empty($autorite['galerie_images']) ? json_decode($autorite['galerie_images'], true) : [];
$liensExternes = !empty($autorite['liens_externes']) ? json_decode($autorite['liens_externes'], true) : [];
$chiffresCles = !empty($autorite['chiffres_cles']) ? json_decode($autorite['chiffres_cles'], true) : [];
$comiteDirection = !empty($autorite['comite_direction']) ? json_decode($autorite['comite_direction'], true) : [];

// Récupérer tous les types et catégories
$pdo = getPDO();
$allTypes = [];
$allCategories = [];

$typesIds = !empty($autorite['types_ids']) ? json_decode($autorite['types_ids'], true) : [];
if (empty($typesIds) && !empty($autorite['type_id'])) {
    $typesIds = [(int)$autorite['type_id']];
}

$categoriesIds = !empty($autorite['categories_ids']) ? json_decode($autorite['categories_ids'], true) : [];
if (empty($categoriesIds) && !empty($autorite['category_id'])) {
    $categoriesIds = [(int)$autorite['category_id']];
}

if (!empty($typesIds)) {
    $placeholders = implode(',', array_fill(0, count($typesIds), '?'));
    $stmt = $pdo->prepare("SELECT id, nom, icon FROM autorite_types WHERE id IN ($placeholders)");
    $stmt->execute($typesIds);
    $allTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!empty($categoriesIds)) {
    $placeholders = implode(',', array_fill(0, count($categoriesIds), '?'));
    $stmt = $pdo->prepare("SELECT id, nom FROM autorite_categories WHERE id IN ($placeholders)");
    $stmt->execute($categoriesIds);
    $allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/autorite">Autorités</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($autorite['nom']) ?></li>
        </ol>
    </nav>

    <!-- Navigation sticky -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 sticky-top" style="top: 60px; z-index: 1020;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sectionsNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="sectionsNav">
                <ul class="navbar-nav nav-pills flex-nowrap overflow-auto">
                    <li class="nav-item"><a class="nav-link" href="#informations"><i class="fas fa-info-circle me-1"></i>Informations</a></li>
                    <?php if (!empty($autorite['description_longue']) || !empty($autorite['description'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#description"><i class="fas fa-align-left me-1"></i>Description</a></li>
                    <?php endif; ?>
                    <?php if (!empty($autorite['historique'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#historique"><i class="fas fa-history me-1"></i>Historique</a></li>
                    <?php endif; ?>
                    <?php if (!empty($autorite['missions'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#missions"><i class="fas fa-bullseye me-1"></i>Missions</a></li>
                    <?php endif; ?>
                    <?php if (!empty($autorite['realisations'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#realisations"><i class="fas fa-trophy me-1"></i>Réalisations</a></li>
                    <?php endif; ?>
                    <?php if (!empty($chiffresCles)): ?>
                    <li class="nav-item"><a class="nav-link" href="#chiffres"><i class="fas fa-chart-bar me-1"></i>Chiffres</a></li>
                    <?php endif; ?>
                    <?php if (!empty($comiteDirection)): ?>
                    <li class="nav-item"><a class="nav-link" href="#direction"><i class="fas fa-users-cog me-1"></i>Direction</a></li>
                    <?php endif; ?>
                    <?php if (!empty($autorite['latitude']) && !empty($autorite['longitude'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#localisation"><i class="fas fa-map-marker-alt me-1"></i>Carte</a></li>
                    <?php endif; ?>
                    <?php if (!empty($galerieImages)): ?>
                    <li class="nav-item"><a class="nav-link" href="#galerie"><i class="fas fa-images me-1"></i>Galerie</a></li>
                    <?php endif; ?>
                    <?php if (!empty($videos)): ?>
                    <li class="nav-item"><a class="nav-link" href="#videos"><i class="fas fa-video me-1"></i>Vidéos</a></li>
                    <?php endif; ?>
                    <?php if (!empty($liensExternes)): ?>
                    <li class="nav-item"><a class="nav-link" href="#liens"><i class="fas fa-link me-1"></i>Liens</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- En-tête -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <?php if (!empty($autorite['logo'])): ?>
                        <img src="<?= htmlspecialchars($autorite['logo']) ?>" alt="Logo" class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: contain; padding: 20px; background: white; border: 1px solid #dee2e6;">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                            <i class="fas fa-building fa-5x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="/autorite_edit?id=<?= $autorite['id'] ?>" class="btn btn-warning btn-sm w-100"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3"><?= htmlspecialchars($autorite['nom']) ?></h1>
                    <div class="mb-3">
                        <?php foreach ($allTypes as $type): ?>
                            <span class="badge bg-primary me-2 mb-2"><i class="fas fa-<?= htmlspecialchars($type['icon']) ?> me-1"></i><?= htmlspecialchars($type['nom']) ?></span>
                        <?php endforeach; ?>
                        <?php foreach ($allCategories as $category): ?>
                            <span class="badge bg-secondary me-2 mb-2"><?= htmlspecialchars($category['nom']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="row">
                        <?php if (!empty($autorite['dirigeant'])): ?><div class="col-md-6 mb-2"><strong><i class="fas fa-user-tie me-2"></i>Dirigeant:</strong> <?= htmlspecialchars($autorite['dirigeant']) ?></div><?php endif; ?>
                        <?php if (!empty($autorite['date_creation_org'])): ?><div class="col-md-6 mb-2"><strong><i class="fas fa-calendar me-2"></i>Création:</strong> <?= htmlspecialchars($autorite['date_creation_org']) ?></div><?php endif; ?>
                        <?php if (!empty($autorite['effectifs'])): ?><div class="col-md-6 mb-2"><strong><i class="fas fa-users me-2"></i>Effectifs:</strong> <?= htmlspecialchars($autorite['effectifs']) ?></div><?php endif; ?>
                        <?php if (!empty($autorite['chiffre_affaires'])): ?><div class="col-md-6 mb-2"><strong><i class="fas fa-chart-line me-2"></i>CA:</strong> <?= htmlspecialchars($autorite['chiffre_affaires']) ?></div><?php endif; ?>
                        <?php if (!empty($autorite['secteur'])): ?><div class="col-md-6 mb-2"><strong><i class="fas fa-industry me-2"></i>Secteur:</strong> <?= htmlspecialchars($autorite['secteur']) ?></div><?php endif; ?>
                        <?php if (!empty($autorite['ville'])): ?><div class="col-md-6 mb-2"><strong><i class="fas fa-map-marker-alt me-2"></i>Ville:</strong> <?= htmlspecialchars($autorite['ville']) ?><?php if (!empty($autorite['pays'])): ?>, <?= htmlspecialchars($autorite['pays']) ?><?php endif; ?></div><?php endif; ?>
                    </div>
                    <div class="mt-3">
                        <?php if (!empty($autorite['site_web'])): ?><a href="<?= htmlspecialchars($autorite['site_web']) ?>" target="_blank" class="btn btn-primary me-2"><i class="fas fa-globe me-1"></i>Site Web</a><?php endif; ?>
                        <?php if (!empty($autorite['linkedin_url'])): ?><a href="<?= htmlspecialchars($autorite['linkedin_url']) ?>" target="_blank" class="btn btn-outline-primary me-2"><i class="fab fa-linkedin me-1"></i>LinkedIn</a><?php endif; ?>
                        <?php if (!empty($autorite['twitter_url'])): ?><a href="<?= htmlspecialchars($autorite['twitter_url']) ?>" target="_blank" class="btn btn-outline-info me-2"><i class="fab fa-twitter me-1"></i>Twitter</a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            
            <!-- Informations -->
            <div class="card mb-4" id="informations">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Coordonnées</h5></div>
                <div class="card-body">
                    <?php if (!empty($autorite['adresse'])): ?>
                    <div class="mb-3"><strong><i class="fas fa-map-marker-alt me-2"></i>Adresse</strong><br><?= nl2br(htmlspecialchars($autorite['adresse'])) ?><?php if (!empty($autorite['code_postal']) || !empty($autorite['ville'])): ?><br><?= htmlspecialchars($autorite['code_postal']) ?> <?= htmlspecialchars($autorite['ville']) ?><?php endif; ?></div>
                    <?php endif; ?>
                    <div class="row">
                        <?php if (!empty($autorite['telephone'])): ?><div class="col-md-6 mb-3"><strong><i class="fas fa-phone me-2"></i>Téléphone</strong><br><a href="tel:<?= htmlspecialchars($autorite['telephone']) ?>"><?= htmlspecialchars($autorite['telephone']) ?></a></div><?php endif; ?>
                        <?php if (!empty($autorite['email'])): ?><div class="col-md-6 mb-3"><strong><i class="fas fa-envelope me-2"></i>Email</strong><br><a href="mailto:<?= htmlspecialchars($autorite['email']) ?>"><?= htmlspecialchars($autorite['email']) ?></a></div><?php endif; ?>
                        <?php if (!empty($autorite['horaires'])): ?><div class="col-md-12 mb-3"><strong><i class="fas fa-clock me-2"></i>Horaires</strong><br><?= nl2br(htmlspecialchars($autorite['horaires'])) ?></div><?php endif; ?>
                    </div>
                    <?php if (!empty($autorite['contact_presse']) || !empty($autorite['email_presse'])): ?>
                    <hr><h6><i class="fas fa-newspaper me-2"></i>Contact Presse</h6>
                    <div class="row">
                        <?php if (!empty($autorite['contact_presse'])): ?><div class="col-md-6 mb-2"><strong>Contact:</strong> <?= htmlspecialchars($autorite['contact_presse']) ?></div><?php endif; ?>
                        <?php if (!empty($autorite['email_presse'])): ?><div class="col-md-6 mb-2"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($autorite['email_presse']) ?>"><?= htmlspecialchars($autorite['email_presse']) ?></a></div><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($autorite['description_longue']) || !empty($autorite['description'])): ?>
            <div class="card mb-4" id="description">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Description</h5></div>
                <div class="card-body"><?= nl2br(htmlspecialchars($autorite['description_longue'] ?? $autorite['description'])) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($autorite['historique'])): ?>
            <div class="card mb-4" id="historique">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-history me-2"></i>Historique</h5></div>
                <div class="card-body"><?= nl2br(htmlspecialchars($autorite['historique'])) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($autorite['missions'])): ?>
            <div class="card mb-4" id="missions">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Missions</h5></div>
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($autorite['missions'])) ?>
                    <?php if (!empty($autorite['valeurs'])): ?><hr><h6><i class="fas fa-heart me-2"></i>Valeurs</h6><p><?= htmlspecialchars($autorite['valeurs']) ?></p><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($autorite['realisations'])): ?>
            <div class="card mb-4" id="realisations">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Réalisations</h5></div>
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($autorite['realisations'])) ?>
                    <?php if (!empty($autorite['distinctions'])): ?><hr><h6><i class="fas fa-award me-2"></i>Distinctions</h6><p><?= nl2br(htmlspecialchars($autorite['distinctions'])) ?></p><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($chiffresCles)): ?>
            <div class="card mb-4" id="chiffres">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Chiffres Clés</h5></div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($chiffresCles as $chiffre): ?>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-primary mb-1"><?= htmlspecialchars($chiffre['valeur']) ?></h3>
                                <small class="text-muted"><?= htmlspecialchars($chiffre['label']) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($comiteDirection)): ?>
            <div class="card mb-4" id="direction">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>Comité de Direction</h5></div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($comiteDirection as $membre): ?>
                        <div class="list-group-item">
                            <h6 class="mb-1"><?= htmlspecialchars($membre['nom']) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($membre['poste']) ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($autorite['latitude']) && !empty($autorite['longitude'])): ?>
            <div class="card mb-4" id="localisation">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Localisation</h5></div>
                <div class="card-body">
                    <div id="map" style="height: 400px;"></div>
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var map = L.map('map').setView([<?= $autorite['latitude'] ?>, <?= $autorite['longitude'] ?>], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
                        L.marker([<?= $autorite['latitude'] ?>, <?= $autorite['longitude'] ?>]).addTo(map).bindPopup('<b><?= htmlspecialchars($autorite['nom']) ?></b>').openPopup();
                    });
                    </script>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($galerieImages)): ?>
            <div class="card mb-4" id="galerie">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-images me-2"></i>Galerie Photos</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($galerieImages as $image): ?>
                        <div class="col-md-4"><img src="<?= htmlspecialchars($image) ?>" class="img-fluid rounded" alt="Image" style="cursor: pointer;" onclick="window.open(this.src, '_blank')"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($videos)): ?>
            <div class="card mb-4" id="videos">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-video me-2"></i>Vidéos</h5></div>
                <div class="card-body">
                    <?php foreach ($videos as $video): ?>
                    <div class="mb-3">
                        <h6><?= htmlspecialchars($video['titre']) ?></h6>
                        <a href="<?= htmlspecialchars($video['url']) ?>" target="_blank" class="btn btn-outline-danger btn-sm"><i class="fab fa-youtube me-1"></i>Voir la vidéo</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($liensExternes)): ?>
            <div class="card mb-4" id="liens">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-link me-2"></i>Liens Externes</h5></div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($liensExternes as $lien): ?>
                        <a href="<?= htmlspecialchars($lien['url']) ?>" target="_blank" class="list-group-item list-group-item-action"><i class="fas fa-external-link-alt me-2"></i><?= htmlspecialchars($lien['nom']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div class="col-lg-4">
            <?php include __DIR__ . '/autorite_detail_sidebar.php'; ?>
            
            <?php if (!empty($autorite['siret']) || !empty($autorite['forme_juridique'])): ?>
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Informations Légales</h5></div>
                <div class="card-body small">
                    <?php if (!empty($autorite['forme_juridique'])): ?><div class="mb-2"><strong>Forme juridique:</strong><br><?= htmlspecialchars($autorite['forme_juridique']) ?></div><?php endif; ?>
                    <?php if (!empty($autorite['siret'])): ?><div class="mb-2"><strong>SIRET:</strong><br><?= htmlspecialchars($autorite['siret']) ?></div><?php endif; ?>
                    <?php if (!empty($autorite['capital_social'])): ?><div class="mb-2"><strong>Capital social:</strong><br><?= htmlspecialchars($autorite['capital_social']) ?></div><?php endif; ?>
                    <?php if (!empty($autorite['bourse_cotation'])): ?><div class="mb-0"><strong>Cotation:</strong><br><?= htmlspecialchars($autorite['bourse_cotation']) ?><?php if (!empty($autorite['code_bourse'])): ?><br><span class="badge bg-success"><?= htmlspecialchars($autorite['code_bourse']) ?></span><?php endif; ?></div><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($autorite['linkedin_url']) || !empty($autorite['twitter_url']) || !empty($autorite['facebook_url']) || !empty($autorite['instagram_url']) || !empty($autorite['youtube_url'])): ?>
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Réseaux Sociaux</h5></div>
                <div class="card-body">
                    <?php if (!empty($autorite['linkedin_url'])): ?><a href="<?= htmlspecialchars($autorite['linkedin_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100 mb-2"><i class="fab fa-linkedin me-1"></i>LinkedIn</a><?php endif; ?>
                    <?php if (!empty($autorite['twitter_url'])): ?><a href="<?= htmlspecialchars($autorite['twitter_url']) ?>" target="_blank" class="btn btn-outline-info btn-sm w-100 mb-2"><i class="fab fa-twitter me-1"></i>Twitter</a><?php endif; ?>
                    <?php if (!empty($autorite['facebook_url'])): ?><a href="<?= htmlspecialchars($autorite['facebook_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100 mb-2"><i class="fab fa-facebook me-1"></i>Facebook</a><?php endif; ?>
                    <?php if (!empty($autorite['instagram_url'])): ?><a href="<?= htmlspecialchars($autorite['instagram_url']) ?>" target="_blank" class="btn btn-outline-danger btn-sm w-100 mb-2"><i class="fab fa-instagram me-1"></i>Instagram</a><?php endif; ?>
                    <?php if (!empty($autorite['youtube_url'])): ?><a href="<?= htmlspecialchars($autorite['youtube_url']) ?>" target="_blank" class="btn btn-outline-danger btn-sm w-100 mb-2"><i class="fab fa-youtube me-1"></i>YouTube</a><?php endif; ?>
                    <?php if (!empty($autorite['wikipedia_url'])): ?><a href="<?= htmlspecialchars($autorite['wikipedia_url']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm w-100"><i class="fab fa-wikipedia-w me-1"></i>Wikipedia</a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
html { scroll-behavior: smooth; }
[id] { scroll-margin-top: 140px; }
#sectionsNav .nav-link.active { color: #0d6efd; font-weight: 600; border-bottom: 2px solid #0d6efd; }
.breadcrumb { background-color: transparent; padding: 0; }
.breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('#sectionsNav .nav-link');
    const sections = document.querySelectorAll('[id]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    window.addEventListener('scroll', function() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
    
    if (navLinks.length > 0) {
        navLinks[0].classList.add('active');
    }
});
</script>