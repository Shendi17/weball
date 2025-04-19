<?php
// Fichier métier pour la section Personnalité
$pageTitle = 'Personnalité';
ob_start();
?>
<div class="container mt-5">
    <!-- Contenu du module Personnalité -->
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
