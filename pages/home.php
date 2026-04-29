<?php
// Logique métier page d'accueil
session_start();
$pageTitle = "Accueil";
$content = '';
// Ici, tu peux charger des données, modules, etc.
ob_start();
include __DIR__ . '/../templates/pages/home.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
