<?php
if (!isset($pageTitle)) {
    $pageTitle = "WebAllOne";
}

ob_start();
?>

<div class="container mt-4">
    <div class="page-content">
        <?php if (isset($pageContent)) echo $pageContent; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/template.php';
?>
