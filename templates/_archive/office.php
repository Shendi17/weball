<?php
$pageTitle = "Office - Weball";
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Office | Weball</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h1>Office</h1>
  <p>Section dédiée à la gestion et à la consultation des offices référencés sur la plateforme.</p>
  <?php
  ob_start();
  include_once dirname(__DIR__) . '/components/office-content.php';
  $content = ob_get_clean();
  echo $content;
  ?>
</div>
</body>
</html>
