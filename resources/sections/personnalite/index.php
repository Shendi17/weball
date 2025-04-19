<?php
// Page principale de la section Personnalité
$pageTitle = 'Personnalité';
ob_start();
?>
<div class="container mt-5">
    <h1>Personnalité</h1>
    <p>Bienvenue dans la section Personnalité. Ici s'afficheront les fiches et modules liés à cette section.</p>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
