<?php
$pageTitle = "Banque - Weball";
ob_start();
if (!file_exists(dirname(__DIR__) . '/components/banque-content.php') || filesize(dirname(__DIR__) . '/components/banque-content.php') == 0) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Banque | Weball</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h1>Banque</h1>
  <p>Section dédiée à la gestion et à la consultation des banques référencées sur la plateforme.</p>
</div>
</body>
</html>
<?php
} else {
  include_once dirname(__DIR__) . '/components/banque-content.php';
}
$content = ob_get_clean();
require_once __DIR__ . '/../includes/template.php';
