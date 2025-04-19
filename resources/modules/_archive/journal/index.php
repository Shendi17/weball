<?php
$pageTitle = 'Journal';
ob_start();
?>
<div class="container mt-5">
    <!-- Contenu du module Journal -->
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
