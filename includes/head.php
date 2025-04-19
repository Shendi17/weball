<?php
if (!defined('BASE_PATH')) {
    require_once dirname(__DIR__) . '/config.php';
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'WAO'; ?></title>

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?php echo getFullUrl('public/assets/images/favicon.ico'); ?>">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Styles personnalisés -->
<link rel="stylesheet" href="<?php echo getFullUrl('public/assets/css/style-assets.css'); ?>">

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?php echo getFullUrl('public/assets/js/main-assets.js'); ?>" defer></script>
