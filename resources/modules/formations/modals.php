<?php
// Vérifier si le fichier est appelé directement
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}
?>

<!-- Modal pour les détails -->
<div class="modal fade" id="formationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h4 id="formationTitle" class="fw-bold"></h4>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold">Description</h6>
                    <p id="formationDescription" class="text-muted"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <h6 class="fw-bold">Niveau</h6>
                        <p id="formationNiveau"></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="fw-bold">Catégorie</h6>
                        <p id="formationCategorie"></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="fw-bold">Durée</h6>
                        <p id="formationDuree"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Date de début</h6>
                        <p id="formationDateDebut"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Date de fin</h6>
                        <p id="formationDateFin"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour l'ajout -->
<div class="modal fade" id="addFormationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addFormationForm">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau" name="niveau">
                                <option value="debutant">Débutant</option>
                                <option value="intermediaire">Intermédiaire</option>
                                <option value="avance">Avancé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="duree" class="form-label">Durée</label>
                            <input type="text" class="form-control" id="duree" name="duree" placeholder="Ex: 40 heures">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut">
                        </div>
                        <div class="col-md-6">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <select class="form-select" id="categorie" name="categorie">
                            <option value="development">Développement</option>
                            <option value="design">Design</option>
                            <option value="marketing">Marketing</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary" form="addFormationForm">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour la modification -->
<div class="modal fade" id="editFormationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editFormationForm">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="edit_titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_niveau" class="form-label">Niveau</label>
                            <select class="form-select" id="edit_niveau" name="niveau">
                                <option value="debutant">Débutant</option>
                                <option value="intermediaire">Intermédiaire</option>
                                <option value="avance">Avancé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_duree" class="form-label">Durée</label>
                            <input type="text" class="form-control" id="edit_duree" name="duree" placeholder="Ex: 40 heures">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_date_debut" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="edit_date_debut" name="date_debut">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_date_fin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="edit_date_fin" name="date_fin">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_categorie" class="form-label">Catégorie</label>
                        <select class="form-select" id="edit_categorie" name="categorie">
                            <option value="development">Développement</option>
                            <option value="design">Design</option>
                            <option value="marketing">Marketing</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary" form="editFormationForm">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
