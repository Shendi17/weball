<?php
// Page principale de la section Cadran
$pageTitle = 'Cadran';
ob_start();
?>
<div class="container mt-5">
    <h1>Cadran</h1>
    <p>Bienvenue dans la section Cadran. Ici s'afficheront les fiches et modules liés à cette section.</p>
    <!-- Inclusion dynamique des fiches et modules à venir -->
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
