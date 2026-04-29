# Script PowerShell pour configurer la tâche planifiée Windows
# Exécuter en tant qu'administrateur

$taskName = "WeballAutoSync"
$scriptPath = Join-Path $PSScriptRoot "sync_daily.bat"
$description = "Synchronisation quotidienne automatique des données autorités"

# Supprimer la tâche si elle existe déjà
Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue

# Créer l'action
$action = New-ScheduledTaskAction -Execute $scriptPath

# Créer le déclencheur (tous les jours à 3h du matin)
$trigger = New-ScheduledTaskTrigger -Daily -At 3am

# Créer les paramètres
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable

# Enregistrer la tâche
Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Description $description

Write-Host "✅ Tâche planifiée '$taskName' créée avec succès !" -ForegroundColor Green
Write-Host "   Exécution quotidienne à 3h00 du matin" -ForegroundColor Cyan
Write-Host ""
Write-Host "Pour modifier l'horaire, utilisez le Planificateur de tâches Windows" -ForegroundColor Yellow
Write-Host "ou exécutez : taskschd.msc" -ForegroundColor Yellow
