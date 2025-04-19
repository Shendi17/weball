# Script PowerShell pour archiver les anciens modules et sections inutiles
# Ce script déplace les dossiers obsolètes dans un dossier _archive pour vérification avant suppression définitive

# Dossier de base
$baseModules = "C:\Users\Anthony\CascadeProjects\weball\modules"
$baseResourcesModules = "C:\Users\Anthony\CascadeProjects\weball\resources\modules"

# Dossiers à archiver dans resources/modules (sections du menu latéral)
$sections = @(
  "adhesion","annonce","annuaire","archive","autorite","banque","boutique","cadran","campagne","carriere","catalogue","concours","discipline","ecole","ecran","entite","formation","instrument","journal","localite","marche","media","office","personnalite","plateforme","projet","publication","reseau"
)

# Création du dossier d'archive si besoin
$archiveModules = "$baseModules\_archive"
$archiveResourcesModules = "$baseResourcesModules\_archive"
if (!(Test-Path $archiveModules)) { New-Item -ItemType Directory -Path $archiveModules | Out-Null }
if (!(Test-Path $archiveResourcesModules)) { New-Item -ItemType Directory -Path $archiveResourcesModules | Out-Null }

# Archivage des dossiers dans resources/modules
foreach ($section in $sections) {
    $src = Join-Path $baseResourcesModules $section
    if (Test-Path $src) {
        $dst = Join-Path $archiveResourcesModules $section
        Move-Item $src $dst -Force
        Write-Host "Archivé: $src -> $dst"
    }
}

# Archivage de tous les dossiers dans modules (sauf ceux explicitement utiles)
$modulesToKeep = @("auth","outils-externes") # À adapter selon tes besoins
Get-ChildItem -Path $baseModules -Directory | Where-Object { $_.Name -notin $modulesToKeep -and $_.Name -ne "_archive" } | ForEach-Object {
    $src = $_.FullName
    $dst = Join-Path $archiveModules $_.Name
    Move-Item $src $dst -Force
    Write-Host "Archivé: $src -> $dst"
}

Write-Host "Archivage terminé. Vérifie le fonctionnement de l'application avant toute suppression définitive !"
