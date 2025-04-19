<?php
// Fichier métier pour la section Localité
$pageTitle = 'Localité';
ob_start();
?>
<div class="container mt-5">
    <!-- Contenu du module Localité -->
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
