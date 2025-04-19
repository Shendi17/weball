<?php
/**
 * Fiche type pour la section Cadran
 * Personnalise ce modèle pour chaque fiche !
 */
$pageTitle = isset($pageTitle) ? $pageTitle : 'Fiche type Cadran';
ob_start();
?>
<div class="fiche-type card my-4">
    <div class="card-header bg-primary text-white">
        <h2 class="h5 mb-0">Titre de la fiche</h2>
    </div>
    <div class="card-body">
        <p><strong>Description :</strong> Ceci est un exemple de fiche pour la section Cadran.</p>
        <ul>
            <li>Attribut 1 : Valeur</li>
            <li>Attribut 2 : Valeur</li>
            <li>Attribut 3 : Valeur</li>
        </ul>
        <a href="#" class="btn btn-outline-primary">Action</a>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/template.php';
