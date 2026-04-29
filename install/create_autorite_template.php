<?php
/**
 * Création du template autorité détaillé
 */

$template = file_get_contents(__DIR__ . '/../templates/pages/autorite_detail_template_backup.php');

// Remplacer les variables personnalité par autorité
$template = str_replace('$personnalite', '$autorite', $template);
$template = str_replace('personnalite', 'autorite', $template);
$template = str_replace('Personnalité', 'Autorité', $template);

// Ajouter les décodages JSON au début
$jsonDecodes = "<?php
// Décoder les données JSON
\$reseauxSociaux = !empty(\$autorite['reseaux_sociaux']) ? json_decode(\$autorite['reseaux_sociaux'], true) : [];
\$videos = !empty(\$autorite['videos']) ? json_decode(\$autorite['videos'], true) : [];
\$galerieImages = !empty(\$autorite['galerie_images']) ? json_decode(\$autorite['galerie_images'], true) : [];
\$liensExternes = !empty(\$autorite['liens_externes']) ? json_decode(\$autorite['liens_externes'], true) : [];
\$chiffresCles = !empty(\$autorite['chiffres_cles']) ? json_decode(\$autorite['chiffres_cles'], true) : [];
\$comiteDirection = !empty(\$autorite['comite_direction']) ? json_decode(\$autorite['comite_direction'], true) : [];

// Récupérer tous les types et catégories
\$pdo = getPDO();
\$allTypes = [];
\$allCategories = [];

\$typesIds = !empty(\$autorite['types_ids']) ? json_decode(\$autorite['types_ids'], true) : [];
if (empty(\$typesIds) && !empty(\$autorite['type_id'])) {
    \$typesIds = [(int)\$autorite['type_id']];
}

\$categoriesIds = !empty(\$autorite['categories_ids']) ? json_decode(\$autorite['categories_ids'], true) : [];
if (empty(\$categoriesIds) && !empty(\$autorite['category_id'])) {
    \$categoriesIds = [(int)\$autorite['category_id']];
}

if (!empty(\$typesIds)) {
    \$placeholders = implode(',', array_fill(0, count(\$typesIds), '?'));
    \$stmt = \$pdo->prepare(\"SELECT id, nom, icon FROM autorite_types WHERE id IN (\$placeholders)\");
    \$stmt->execute(\$typesIds);
    \$allTypes = \$stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!empty(\$categoriesIds)) {
    \$placeholders = implode(',', array_fill(0, count(\$categoriesIds), '?'));
    \$stmt = \$pdo->prepare(\"SELECT id, nom FROM autorite_categories WHERE id IN (\$placeholders)\");
    \$stmt->execute(\$categoriesIds);
    \$allCategories = \$stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>\n\n";

$template = $jsonDecodes . substr($template, 6); // Enlever le <?php du début

file_put_contents(__DIR__ . '/../templates/pages/autorite_detail_template.php', $template);

echo "✓ Template créé avec succès\n";
