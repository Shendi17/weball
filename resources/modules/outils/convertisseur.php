<?php
session_start();
$base_path = '/weball';

// Définition des unités de conversion
$unites = [
    'longueur' => [
        'mm' => 'Millimètres',
        'cm' => 'Centimètres',
        'm' => 'Mètres',
        'km' => 'Kilomètres',
        'in' => 'Pouces',
        'ft' => 'Pieds',
        'yd' => 'Yards',
        'mi' => 'Miles'
    ],
    'poids' => [
        'mg' => 'Milligrammes',
        'g' => 'Grammes',
        'kg' => 'Kilogrammes',
        'oz' => 'Onces',
        'lb' => 'Livres',
        't' => 'Tonnes'
    ],
    'temperature' => [
        'c' => 'Celsius',
        'f' => 'Fahrenheit',
        'k' => 'Kelvin'
    ]
];

// Traitement de la conversion via AJAX
if (isset($_POST['action']) && $_POST['action'] === 'convert') {
    header('Content-Type: application/json');
    
    $valeur = floatval($_POST['valeur']);
    $de = $_POST['de'];
    $vers = $_POST['vers'];
    $type = $_POST['type'];
    
    $resultat = convertir($valeur, $de, $vers, $type);
    
    echo json_encode(['resultat' => $resultat]);
    exit;
}

function convertir($valeur, $de, $vers, $type) {
    // Conversion en unité de base puis vers l'unité cible
    switch ($type) {
        case 'longueur':
            // Conversion en mètres puis vers l'unité cible
            $enMetres = convertirEnMetres($valeur, $de);
            return convertirDepuisMetres($enMetres, $vers);
            
        case 'poids':
            // Conversion en grammes puis vers l'unité cible
            $enGrammes = convertirEnGrammes($valeur, $de);
            return convertirDepuisGrammes($enGrammes, $vers);
            
        case 'temperature':
            // Conversion spéciale pour les températures
            return convertirTemperature($valeur, $de, $vers);
    }
    return 0;
}

function convertirEnMetres($valeur, $unite) {
    switch ($unite) {
        case 'mm': return $valeur * 0.001;
        case 'cm': return $valeur * 0.01;
        case 'm': return $valeur;
        case 'km': return $valeur * 1000;
        case 'in': return $valeur * 0.0254;
        case 'ft': return $valeur * 0.3048;
        case 'yd': return $valeur * 0.9144;
        case 'mi': return $valeur * 1609.344;
    }
    return 0;
}

function convertirDepuisMetres($valeur, $unite) {
    switch ($unite) {
        case 'mm': return $valeur * 1000;
        case 'cm': return $valeur * 100;
        case 'm': return $valeur;
        case 'km': return $valeur * 0.001;
        case 'in': return $valeur * 39.3701;
        case 'ft': return $valeur * 3.28084;
        case 'yd': return $valeur * 1.09361;
        case 'mi': return $valeur * 0.000621371;
    }
    return 0;
}

function convertirEnGrammes($valeur, $unite) {
    switch ($unite) {
        case 'mg': return $valeur * 0.001;
        case 'g': return $valeur;
        case 'kg': return $valeur * 1000;
        case 'oz': return $valeur * 28.3495;
        case 'lb': return $valeur * 453.592;
        case 't': return $valeur * 1000000;
    }
    return 0;
}

function convertirDepuisGrammes($valeur, $unite) {
    switch ($unite) {
        case 'mg': return $valeur * 1000;
        case 'g': return $valeur;
        case 'kg': return $valeur * 0.001;
        case 'oz': return $valeur * 0.035274;
        case 'lb': return $valeur * 0.00220462;
        case 't': return $valeur * 0.000001;
    }
    return 0;
}

function convertirTemperature($valeur, $de, $vers) {
    // D'abord convertir en Celsius
    $celsius = 0;
    switch ($de) {
        case 'c': $celsius = $valeur; break;
        case 'f': $celsius = ($valeur - 32) * 5/9; break;
        case 'k': $celsius = $valeur - 273.15; break;
    }
    
    // Puis convertir vers l'unité cible
    switch ($vers) {
        case 'c': return $celsius;
        case 'f': return ($celsius * 9/5) + 32;
        case 'k': return $celsius + 273.15;
    }
    return 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertisseur d'unités - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    // Ancienne inclusion supprimée
    // <?php include '../../includes/sidebar.php'; ?>
    // Ajout du template global
    require_once dirname(__DIR__, 3) . '/includes/template.php';
    ?>
    
    <div class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="content">
            <div class="module-header">
                <h1>Convertisseur d'unités</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux outils
                </a>
            </div>

            <div class="converter-container">
                <div class="converter-type">
                    <label for="type">Type de conversion :</label>
                    <select id="type" class="form-control">
                        <option value="longueur">Longueur</option>
                        <option value="poids">Poids</option>
                        <option value="temperature">Température</option>
                    </select>
                </div>

                <div class="converter-inputs">
                    <div class="input-group">
                        <input type="number" id="valeur" class="form-control" step="any" value="1">
                        <select id="de" class="form-control">
                            <?php foreach ($unites['longueur'] as $code => $nom): ?>
                                <option value="<?php echo $code; ?>"><?php echo $nom; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="converter-arrow">
                        <i class="fas fa-exchange-alt"></i>
                    </div>

                    <div class="input-group">
                        <input type="number" id="resultat" class="form-control" readonly>
                        <select id="vers" class="form-control">
                            <?php foreach ($unites['longueur'] as $code => $nom): ?>
                                <option value="<?php echo $code; ?>"><?php echo $nom; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $base_path; ?>/assets/js/main.js"></script>
    <script>
        // Stockage des unités
        const unites = <?php echo json_encode($unites); ?>;
        
        // Mise à jour des options des sélecteurs
        function updateSelects(type) {
            const deSelect = document.getElementById('de');
            const versSelect = document.getElementById('vers');
            
            // Vider les sélecteurs
            deSelect.innerHTML = '';
            versSelect.innerHTML = '';
            
            // Ajouter les nouvelles options
            Object.entries(unites[type]).forEach(([code, nom]) => {
                deSelect.add(new Option(nom, code));
                versSelect.add(new Option(nom, code));
            });
            
            // Déclencher la conversion
            convertir();
        }
        
        // Fonction de conversion
        function convertir() {
            const valeur = document.getElementById('valeur').value;
            const de = document.getElementById('de').value;
            const vers = document.getElementById('vers').value;
            const type = document.getElementById('type').value;
            
            // Requête AJAX
            fetch('convertisseur.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=convert&valeur=${valeur}&de=${de}&vers=${vers}&type=${type}`
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('resultat').value = Number(data.resultat).toFixed(6);
            })
            .catch(error => console.error('Erreur:', error));
        }
        
        // Écouteurs d'événements
        document.getElementById('type').addEventListener('change', e => updateSelects(e.target.value));
        document.getElementById('valeur').addEventListener('input', convertir);
        document.getElementById('de').addEventListener('change', convertir);
        document.getElementById('vers').addEventListener('change', convertir);
        
        // Conversion initiale
        convertir();
    </script>
</body>
</html>
