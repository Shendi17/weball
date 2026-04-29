<?php /* Layout principal */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'WebAll' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/public/assets/css/sidebar-modern.css">
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Layout structure */
        body {
            margin: 0;
            padding: 0;
        }
        .main-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1000;
        }
        .main-zone {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            margin-left: 220px; /* Largeur de la sidebar */
            width: calc(100% - 220px);
            max-width: 100%;
            overflow-x: hidden;
        }
        .sidebar.collapsed ~ .main-zone {
            margin-left: 60px;
        }
        .main-content {
            padding: 28px;
        }
        
        /* Topbar styles - Force full width */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px 15px 20px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
            gap: 20px;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 200px;
        }
        .topbar-left .site-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            white-space: nowrap;
        }
        .header-search {
            flex: 1;
            max-width: 600px;
            display: flex;
            gap: 8px;
        }
        .header-search input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .header-search button {
            padding: 10px 25px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .header-search button:hover {
            background: #0056b3;
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0;
        }
        .topbar-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 10px;
        }
        .topbar-menu li {
            margin: 0;
        }
        .topbar-menu a {
            color: #333;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s;
            font-size: 14px;
        }
        .topbar-menu a:hover {
            color: #007bff;
            background: #f0f0f0;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.3s;
            white-space: nowrap;
        }
        .btn-outline {
            border: 1px solid #007bff;
            color: #007bff;
            background: white;
        }
        .btn-outline:hover {
            background: #007bff;
            color: white;
        }
        .btn-primary {
            background: #007bff;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
    <?php
    // Inclusion dynamique du CSS spécifique à la page courante (si existe)
    $pageScript = isset($pageTitle) ? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pageTitle)) : '';
    $cssPath = "/styles/{$pageScript}.css";
    if ($pageScript && file_exists(__DIR__ . "/../../styles/{$pageScript}.css")) {
        echo '<link rel="stylesheet" href="' . $cssPath . '">';
    }
    ?>
    <!-- Autres CSS globaux ici -->
</head>
<body>
    <div class="main-layout">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main-zone">
            <?php include __DIR__ . '/../../includes/topbar.php'; ?>
            <section class="main-content">
                <?php if (isset($content)) echo $content; ?>
            </section>
        </div>
    </div>
    <!-- JS globaux -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/sidebar-test.js"></script>
</body>
</html>
