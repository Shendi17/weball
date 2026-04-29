@echo off
REM Script de synchronisation quotidienne des autorités
REM À exécuter via Windows Task Scheduler

cd /d "%~dp0.."
php scripts/sync_autorites.php --all --force > scripts/logs/cron_last_run.log 2>&1

REM Envoyer un email de notification (optionnel)
REM php scripts/send_sync_notification.php
