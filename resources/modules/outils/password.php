<?php
$pageTitle = 'Gestion des mots de passe';
ob_start();
?>
<div class="container mt-5">
    <div class="module-header">
        <h1>Générateur de mots de passe</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux outils
        </a>
    </div>

    <div class="password-generator">
        <div class="password-display">
            <input type="text" id="password" readonly>
            <button id="copy" class="btn btn-primary" title="Copier">
                <i class="fas fa-copy"></i>
            </button>
            <button id="generate" class="btn btn-primary" title="Générer">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <div class="password-options">
            <div class="option-group">
                <label for="longueur">Longueur :</label>
                <div class="range-input">
                    <input type="range" id="longueur" min="8" max="32" value="12">
                    <span id="longueur-value">12</span>
                </div>
            </div>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" id="majuscules" checked>
                    Majuscules (A-Z)
                </label>
                <label>
                    <input type="checkbox" id="minuscules" checked>
                    Minuscules (a-z)
                </label>
                <label>
                    <input type="checkbox" id="chiffres" checked>
                    Chiffres (0-9)
                </label>
                <label>
                    <input type="checkbox" id="speciaux">
                    Caractères spéciaux (!@#$%^&*)
                </label>
            </div>
        </div>

        <div class="password-strength">
            <h3>Force du mot de passe</h3>
            <div class="strength-meter">
                <div id="strength-bar"></div>
            </div>
            <p id="strength-text">Mot de passe fort</p>
        </div>
    </div>
</div>

<script>
    // Fonction pour générer un mot de passe
    function genererMotDePasse() {
        const longueur = document.getElementById('longueur').value;
        const majuscules = document.getElementById('majuscules').checked;
        const minuscules = document.getElementById('minuscules').checked;
        const chiffres = document.getElementById('chiffres').checked;
        const speciaux = document.getElementById('speciaux').checked;
        
        fetch('password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=generate&longueur=${longueur}&majuscules=${majuscules}&minuscules=${minuscules}&chiffres=${chiffres}&speciaux=${speciaux}`
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('password').value = data.motDePasse;
            evaluerForce(data.motDePasse);
        })
        .catch(error => console.error('Erreur:', error));
    }
    
    // Fonction pour évaluer la force du mot de passe
    function evaluerForce(motDePasse) {
        let score = 0;
        const longueur = motDePasse.length;
        
        // Longueur
        score += Math.min(longueur * 4, 40);
        
        // Complexité
        if (/[A-Z]/.test(motDePasse)) score += 10;
        if (/[a-z]/.test(motDePasse)) score += 10;
        if (/[0-9]/.test(motDePasse)) score += 10;
        if (/[^A-Za-z0-9]/.test(motDePasse)) score += 15;
        
        // Variété des caractères
        const unique = new Set(motDePasse).size;
        score += unique * 2;
        
        // Mise à jour de l'interface
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        
        if (score < 40) {
            bar.style.width = '25%';
            bar.style.backgroundColor = '#ff4444';
            text.textContent = 'Mot de passe faible';
        } else if (score < 60) {
            bar.style.width = '50%';
            bar.style.backgroundColor = '#ffbb33';
            text.textContent = 'Mot de passe moyen';
        } else if (score < 80) {
            bar.style.width = '75%';
            bar.style.backgroundColor = '#00C851';
            text.textContent = 'Mot de passe fort';
        } else {
            bar.style.width = '100%';
            bar.style.backgroundColor = '#007E33';
            text.textContent = 'Mot de passe très fort';
        }
    }
    
    // Écouteurs d'événements
    document.getElementById('generate').addEventListener('click', genererMotDePasse);
    document.getElementById('copy').addEventListener('click', () => {
        const password = document.getElementById('password');
        password.select();
        document.execCommand('copy');
        alert('Mot de passe copié !');
    });
    
    document.getElementById('longueur').addEventListener('input', e => {
        document.getElementById('longueur-value').textContent = e.target.value;
        genererMotDePasse();
    });
    
    const checkboxes = ['majuscules', 'minuscules', 'chiffres', 'speciaux'];
    checkboxes.forEach(id => {
        document.getElementById(id).addEventListener('change', genererMotDePasse);
    });
    
    // Génération initiale
    genererMotDePasse();
</script>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
