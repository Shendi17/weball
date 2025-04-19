<?php
// Fichier métier pour la section Entité
$pageTitle = 'Entité';
ob_start();
?>
<div class="container mt-5">
    <!-- Contenu du module Entité -->
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
