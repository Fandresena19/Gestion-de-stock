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
                <!-- Hidden input for achat_immo ID during modification -->
                <input type="hidden" name="id_achat_immo" value="<?= !empty($current_achat_immo['id_achat_immo']) ? $current_achat_immo['id_achat_immo'] : '' ?>">

                <!-- Informations de l'immobilisation - Toujours nouvelles -->
                <h4>Informations de l'immobilisation</h4>
                
                <label for="nom_immo">Nom de l'immobilisation</label>
                <input type="text" 
                    name="nom_immo" 
                    id="nom_immo" 
                    placeholder="Nom de l'immobilisation"
                    value="<?= !empty($current_achat_immo['nom_immo']) ? htmlspecialchars($current_achat_immo['nom_immo']) : '' ?>"
                    required>

                <label for="id_categorie">Catégorie</label>
                <select name="id_categorie" id="id_categorie" required>
                    <option value="">Choisir une catégorie</option>
                    <?php
                    foreach ($categories as $categorie) {
                        $selected = (!empty($current_achat_immo['id_categorie_immo']) && $current_achat_immo['id_categorie_immo'] == $categorie['id_categorie_immo']) ? 'selected' : '';
                        echo '<option value="' . $categorie['id_categorie_immo'] . '" ' . $selected . '>' .
                            htmlspecialchars($categorie['nom_categorie_immo']) . '</option>';
                    }
                    ?>
                </select>

                <label for="description">Description</label>
                <textarea name="description" 
                    id="description" 
                    placeholder="Description de l'immobilisation"><?= !empty($current_achat_immo['description']) ? htmlspecialchars($current_achat_immo['description']) : '' ?></textarea>

                <label for="type_immo">Type d'immobilisation</label>
                <input type="text" 
                    name="type_immo" 
                    id="type_immo" 
                    placeholder="Type d'immobilisation"
                    value="<?= !empty($current_achat_immo['type_immo']) ? htmlspecialchars($current_achat_immo['type_immo']) : '' ?>"
                    required>

                <label for="duree_vie">Durée de vie</label>
                <input type="number"
                name="duree_vie"
                id="duree_vie"
                placeholder="Entrez la durée de vie en années">

                <label for="debut_service">Date de début de service</label>
                <input type="date" 
                    name="debut_service" 
                    id="debut_service"
                    value="<?= !empty($current_achat_immo['debut_service']) ? $current_achat_immo['debut_service'] : '' ?>">

                <hr>
                <h4>Informations d'achat</h4>

                <label for="fournisseur">Fournisseur</label>
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
                <button type="submit" class="btn <?= !empty($current_achat_immo) ? 'btn-primary' : 'btn-success' ?>">
                    <?= !empty($current_achat_immo) ? 'Modifier' : 'Ajouter' ?>
                </button>

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $achats_immo = getAchatImmo();

                    if (!empty($achats_immo) && is_array($achats_immo)) {
                        foreach ($achats_immo as $value) {
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($value['nom_immo']) ?></td>
                                <td><?= htmlspecialchars($value['nom_categorie_immo']) ?></td>
                                <td><?= htmlspecialchars($value['type_immo']) ?></td>
                                <td><?= htmlspecialchars($value['nom_fournisseur']) ?></td>
                                <td><?= number_format($value['prix_unitaire_immo'], 2) ?> MGA</td>
                                <td><?= date('d/m/Y', strtotime($value['date_achat_immo'])) ?></td>
                                <td><?= htmlspecialchars($value['facture']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="achat_immo.php?id=<?= $value['id_achat_immo'] ?>"
                                            class="edit-btn"
                                            title="Modifier">
                                            <i class='bx bx-edit'></i>
                                        </a>
                                        <a href="../traitement_immo/supprimer_achat_immo.php?id=<?= $value['id_achat_immo'] ?>"
                                            class="delete-btn"
                                            title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat d\'immobilisation ?');">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" class="text-center">Aucun achat d'immobilisation trouvé</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include('./foot.php');
?>