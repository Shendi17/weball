<?php
$pageTitle = "Paramètres - WebAllOne";
require_once __DIR__ . '/../../includes/template.php';
?>

<div class="container mt-4">
    <h1>Paramètres</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Préférences</h5>
            <form>
                <div class="mb-3">
                    <label class="form-label">Thème</label>
                    <select class="form-select">
                        <option value="light">Clair</option>
                        <option value="dark">Sombre</option>
                        <option value="system">Système</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notifications</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="emailNotif">
                        <label class="form-check-label" for="emailNotif">
                            Recevoir les notifications par email
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer les préférences</button>
            </form>
        </div>
    </div>
</div>
