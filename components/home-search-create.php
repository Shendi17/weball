<?php
// Liste des 25 sections (pour le select)
$sections = [
    'adhesion' => 'Adhésion',
    'annonce' => 'Annonce',
    'annuaire' => 'ANNUAIRE',
    'archive' => 'Archive',
    'autorite' => 'Autorité',
    'banque' => 'Banque',
    'boutique' => 'Boutique',
    'campagne' => 'Campagne',
    'carriere' => 'Carrière',
    'catalogue' => 'Catalogue',
    'concours' => 'Concours',
    'discipline' => 'DISCIPLINE',
    'ecole' => 'ECOLE',
    'entite' => 'Entité',
    'formation' => 'Formation',
    'instrument' => 'Instrument',
    'marche' => 'MARCHE',
    'media' => 'Média',
    'office' => 'Office',
    'personnalite' => 'Personnalité',
    'place' => 'Place',
    'plateforme' => 'PLATEFORME',
    'projet' => 'Projet',
    'publication' => 'Publication',
    'reseau' => 'Réseau',
];
?>
<div class="home-search-create">
    <form method="post" action="/weball/create-fiche.php" class="form-inline" id="form-create-fiche">
        <input type="text" name="fiche_nom" class="form-control" placeholder="Rechercher ou créer une fiche..." required autocomplete="off">
        <select name="fiche_section" class="form-select">
            <?php foreach($sections as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
    <div id="search-results"></div>
</div>
<script>
// (Bonus JS) : auto-complétion simulée (à brancher sur une API plus tard)
document.getElementById('form-create-fiche').fiche_nom.addEventListener('input', function(e) {
    // Simuler une recherche (affichage local, à remplacer par un appel AJAX)
    const val = e.target.value.trim();
    const results = document.getElementById('search-results');
    if(val.length > 2) {
        results.innerHTML = '<div class="search-hint">Résultats simulés pour: <b>' + val + '</b></div>';
    } else {
        results.innerHTML = '';
    }
});
</script>
