<?php
include('./head.php');

// Variable to store the current achat_immo for editing
$current_achat_immo = null;

// Check if an achat_immo ID is provided for editing
if (!empty($_GET['id'])) {
    $current_achat_immo = getAchatImmo($_GET['id']);
}

// Get all fournisseurs for the dropdown
$fournisseurs = getFournisseur();

// Get all categories for the dropdown
$categories = getAllCategoriesImmo();
?>

<div class="home-content">
    <h3>Acquisition Immobilisation</h3>
    <div class="overview-boxes">
        <div class="box">
            <form action="../traitement_immo/ajout_achat_immo.php" method="post">
                <input type="hidden" name="id_achat_immo" value="<?= !empty($current_achat_immo['id_achat_immo']) ? $current_achat_immo['id_achat_immo'] : '' ?>">

                <h4>Informations de l'immobilisation</h4>
                
                <label for="nom_immo">Nom de l'immobilisation</label>
                <input type="text" 
                    name="nom_immo" 
                    id="nom_immo" 
                    placeholder="Nom de l'immobilisation"
                    value="<?= !empty($current_achat_immo['nom_immo']) && !empty($current_achat_immo['immo_exists']) ? htmlspecialchars($current_achat_immo['nom_immo']) : '' ?>"
                    <?= !empty($current_achat_immo) && empty($current_achat_immo['immo_exists']) ? 'readonly' : '' ?>
                    required>

                <?php if (!empty($current_achat_immo) && empty($current_achat_immo['immo_exists'])): ?>
                    <small style="color: red;">⚠️ Cette immobilisation a été supprimée de l'inventaire</small>
                <?php endif; ?>

                <label for="id_categorie">Catégorie</label>
                <select name="id_categorie" id="id_categorie" <?= !empty($current_achat_immo) && empty($current_achat_immo['immo_exists']) ? 'disabled' : 'required' ?>>
                    <option value="">Choisir une catégorie</option>
                    <?php
                    foreach ($categories as $categorie) {
                        $selected = '';
                        if (!empty($current_achat_immo)) {
                            // Pour les immobilisations supprimées, on compare avec le nom de catégorie
                            if (empty($current_achat_immo['immo_exists'])) {
                                $selected = ($current_achat_immo['nom_categorie_immo'] == $categorie['nom_categorie_immo']) ? 'selected' : '';
                            } else {
                                $selected = ($current_achat_immo['id_categorie_immo'] == $categorie['id_categorie_immo']) ? 'selected' : '';
                            }
                        }
                        echo '<option value="' . $categorie['id_categorie_immo'] . '" ' . $selected . '>' .
                            htmlspecialchars($categorie['nom_categorie_immo']) . '</option>';
                    }
                    ?>
                </select>

                <label for="description">Description</label>
                <textarea name="description" 
                    id="description" 
                    placeholder="Description de l'immobilisation"
                    <?= !empty($current_achat_immo) && empty($current_achat_immo['immo_exists']) ? 'readonly' : '' ?>><?= !empty($current_achat_immo['description']) && !empty($current_achat_immo['immo_exists']) ? htmlspecialchars($current_achat_immo['description']) : '' ?></textarea>

                <label for="type_immo">Type d'immobilisation</label>
                <input type="text" 
                    name="type_immo" 
                    id="type_immo" 
                    placeholder="Type d'immobilisation"
                    value="<?= !empty($current_achat_immo['type_immo']) ? htmlspecialchars($current_achat_immo['type_immo']) : '' ?>"
                    <?= !empty($current_achat_immo) && empty($current_achat_immo['immo_exists']) ? 'readonly' : '' ?>
                    required>

                <label for="duree_vie">Durée de vie</label>
                <input type="number"
                    name="duree_vie"
                    id="duree_vie"
                    placeholder="Entrez la durée de vie en années"
                    value="<?= !empty($current_achat_immo['duree_vie']) && !empty($current_achat_immo['immo_exists']) ? $current_achat_immo['duree_vie'] : '' ?>"
                    <?= !empty($current_achat_immo) && empty($current_achat_immo['immo_exists']) ? 'readonly' : '' ?>>

                <label for="debut_service">Date de début de service</label>
                <input type="date" 
                    name="debut_service" 
                    id="debut_service"
                    value="<?= !empty($current_achat_immo['debut_service']) && !empty($current_achat_immo['immo_exists']) ? $current_achat_immo['debut_service'] : '' ?>"
                    <?= !empty($current_achat_immo) && empty($current_achat_immo['immo_exists']) ? 'readonly' : '' ?>>

                <hr>
                <h4>Informations d'achat</h4>

                <label for="fournisseur">Fournisseur</label>
                <div class="fournisseur-select-container">
                    <select name="fournisseur" id="fournisseur" required>
                        <option value="">Choisir un fournisseur</option>
                        <?php
                        foreach ($fournisseurs as $fournisseur) {
                            $selected = (!empty($current_achat_immo['id_fournisseur']) && $current_achat_immo['id_fournisseur'] == $fournisseur['id_fournisseur']) ? 'selected' : '';
                            echo '<option value="' . $fournisseur['id_fournisseur'] . '" ' . $selected . '>' .
                                htmlspecialchars($fournisseur['nom_fournisseur']) . '</option>';
                        }
                        ?>
                    </select>
                    <button type="button" class="btn-add-fournisseur" id="openModalBtn">
                        <i class='bx bx-plus'></i>
                    </button>
                </div>


                <label for="prix_unitaire_immo">Prix unitaire</label>
                <input type="number"
                    name="prix_unitaire_immo"
                    id="prix_unitaire_immo"
                    placeholder="Prix unitaire"
                    step="0.01"
                    min="0"
                    value="<?= !empty($current_achat_immo['prix_unitaire_immo']) ? $current_achat_immo['prix_unitaire_immo'] : '' ?>"
                    required />

                <label for="facture">Numéro de Facture</label>
                <input type="text"
                    name="facture"
                    id="facture"
                    placeholder="Numéro de facture"
                    value="<?= !empty($current_achat_immo['facture']) ? htmlspecialchars($current_achat_immo['facture']) : '' ?>"
                    required />

                <label for="date_achat_immo">Date achat</label>
                <input type="date"
                    name="date_achat_immo"
                    id="date_achat_immo"
                    value="<?= !empty($current_achat_immo['date_achat_immo']) ? $current_achat_immo['date_achat_immo'] : '' ?>"
                    required />

                <br><br>
                <?php if (empty($current_achat_immo) || !empty($current_achat_immo['immo_exists'])): ?>
                    <button type="submit" class="btn <?= !empty($current_achat_immo) ? 'btn-primary' : 'btn-success' ?>">
                        <?= !empty($current_achat_immo) ? 'Modifier' : 'Ajouter' ?>
                    </button>
                <?php else: ?>
                    <div class="alert alert-warning">
                        ⚠️ Modification impossible : L'immobilisation associée a été supprimée de l'inventaire.
                        <br>Vous pouvez seulement consulter les informations de cet achat.
                    </div>
                    <a href="achat_immo.php" class="btn btn-secondary">Retour</a>
                <?php endif; ?>

                <?php
                if (!empty($_SESSION['message']['text'])) {
                ?>
                    <div class="alert <?= $_SESSION['message']['type'] ?>">
                        <?= $_SESSION['message']['text'] ?>
                    </div>
                <?php
                    // Clear the message after displaying
                    unset($_SESSION['message']);
                }
                ?>
            </form>
        </div>

        <div class="box">
            <table class="mtable" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th>Nom immobilisation</th>
                        <th>Catégorie</th>
                        <th>Type</th>
                        <th>Fournisseur</th>
                        <th>Prix unitaire</th>
                        <th>Date achat</th>
                        <th>Facture</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $achats_immo = getAchatImmo();

                    if (!empty($achats_immo) && is_array($achats_immo)) {
                        foreach ($achats_immo as $value) {
                    ?>
                            <tr <?= empty($value['immo_exists']) ? 'style="background-color: #fff2f2;"' : '' ?>>
                                <td>
                                    <?= htmlspecialchars($value['nom_immo']) ?>
                                </td>
                                <td><?= htmlspecialchars($value['nom_categorie_immo']) ?></td>
                                <td><?= htmlspecialchars($value['type_immo']) ?></td>
                                <td><?= htmlspecialchars($value['nom_fournisseur']) ?></td>
                                <td><?= number_format($value['prix_unitaire_immo'], 2) ?> MGA</td>
                                <td><?= date('d/m/Y', strtotime($value['date_achat_immo'])) ?></td>
                                <td><?= htmlspecialchars($value['facture']) ?></td>
                                <td>
                                    <?php if (!empty($value['immo_exists'])): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Supprimée</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="achat_immo.php?id=<?= $value['id_achat_immo'] ?>"
                                            class="edit-btn"
                                            title="<?= !empty($value['immo_exists']) ? 'Modifier' : 'Consulter' ?>">
                                            <i class='bx <?= !empty($value['immo_exists']) ? 'bx-edit' : 'bx-show' ?>'></i>
                                        </a>
                                        <?php if (!empty($value['immo_exists'])): ?>
                                            <a href="../traitement_immo/supprimer_achat_immo.php?id=<?= $value['id_achat_immo'] ?>"
                                               class="delete-btn"
                                               title="Supprimer"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat d\'immobilisation ?');">
                                                <i class='bx bx-trash'></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9" class="text-center">Aucun achat d'immobilisation trouvé</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addFournisseurModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h4>Ajouter un nouveau Fournisseur</h4>
        <form action="../traitement_immo/add_fournisseur.php" method="post" id="addFournisseurForm">
            <label for="nom_fournisseur">Nom du Fournisseur</label>
            <input type="text" name="nom_fournisseur" id="nom_fournisseur" required>
            <button type="submit" class="btn btn-success">Enregistrer le Fournisseur</button>
        </form>
    </div>
</div>

<style>
/* CSS for the modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 8px;
    position: relative;
}

.close-btn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close-btn:hover,
.close-btn:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.fournisseur-select-container {
    display: flex;
    align-items: center;
}

.fournisseur-select-container select {
    flex-grow: 1;
    margin-right: 10px;
}

.btn-add-fournisseur {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-add-fournisseur:hover {
    background-color: #218838;
}

.badge {
    display: inline-block;
    font-weight: bold;
    text-align: center;
    border-radius: 4px;
    font-size: 10px;
    padding: 2px 6px;
}

.badge-success {
    color: green;
    background-color: #d4edda;
}

.badge-danger {
    color: red;
    background-color: #f8d7da;
}

.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
}
</style>

<script>
    // Get the modal and buttons
    var modal = document.getElementById("addFournisseurModal");
    var openBtn = document.getElementById("openModalBtn");
    var closeBtn = document.getElementsByClassName("close-btn")[0];

    // When the user clicks the button, open the modal
    openBtn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php
include('./foot.php');
?>