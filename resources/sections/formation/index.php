<?php
// Page principale de la section Formation
$pageTitle = 'Formation';
ob_start();
?>
<div class="container mt-5">
    <h1>Formation</h1>
    <p>Bienvenue dans la section Formation. Ici s'afficheront les fiches et modules liés à cette section.</p>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
