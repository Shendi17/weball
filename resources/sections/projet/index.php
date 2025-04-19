<?php
// Page principale de la section Projet
$pageTitle = 'Projet';
ob_start();
?>
<div class="container mt-5">
    <h1>Projet</h1>
    <p>Bienvenue dans la section Projet. Ici s'afficheront les fiches et modules liés à cette section.</p>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
