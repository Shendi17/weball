<?php
// Fichier métier pour la section Cadran
$pageTitle = 'Cadran';
ob_start();
?>
<div class="container mt-5">
    <!-- Contenu du module Cadran -->
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
