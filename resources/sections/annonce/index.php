<?php
// Page principale de la section Annonce
$pageTitle = 'Annonce';
ob_start();
?>
<div class="container mt-5">
    <h1>Annonce</h1>
    <p>Bienvenue dans la section Annonce. Ici s'afficheront les fiches et modules liés à cette section.</p>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
