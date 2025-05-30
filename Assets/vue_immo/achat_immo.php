<?php
include('./head.php');

// Variable to store the current achat_immo for editing
$current_achat_immo = null;

// Check if an achat_immo ID is provided for editing
if (!empty($_GET['id'])) {
    $current_achat_immo = getAchatImmo($_GET['id']);
}

// Get all immobilisations for the dropdown
$immobilisations = getImmobilisation();

// Get all fournisseurs for the dropdown
$fournisseurs = getFournisseur();
?>

<div class="home-content">
    <h3>Acquisition Immobilisation</h3>
    <div class="overview-boxes">
        <div class="box">
            <form action="../traitement_immo/ajout_achat_immo.php" method="post">
                <!-- Hidden input for achat_immo ID during modification -->
                <input type="hidden" name="id_achat_immo" value="<?= !empty($current_achat_immo['id_achat_immo']) ? $current_achat_immo['id_achat_immo'] : '' ?>">

                <label for="immobilisation">Nom de l'immobilisation</label>
                <select name="immobilisation" id="immobilisation" required>
                    <?php
                    // If editing, show the current immobilisation as selected first
                    if (!empty($current_achat_immo['id_immo'])) {
                        echo '<option value="' . $current_achat_immo['id_immo'] . '" selected>' .
                            htmlspecialchars($current_achat_immo['nom_immo']) . '</option>';
                    }

                    // Populate other immobilisations
                    foreach ($immobilisations as $immobilisation) {
                        // Skip the current immobilisation to avoid duplicate
                        if (empty($current_achat_immo) || $immobilisation['id_immo'] != $current_achat_immo['id_immo']) {
                            echo '<option value="' . $immobilisation['id_immo'] . '">' .
                                htmlspecialchars($immobilisation['nom_immo']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <label for="fournisseur">Fournisseur</label>
                <select name="fournisseur" id="fournisseur" required>
                    <?php
                    // If editing, show the current fournisseur as selected first
                    if (!empty($current_achat_immo['id_fournisseur'])) {
                        echo '<option value="' . $current_achat_immo['id_fournisseur'] . '" selected>' .
                            htmlspecialchars($current_achat_immo['nom_fournisseur']) . '</option>';
                    }

                    // Populate other fournisseurs
                    foreach ($fournisseurs as $fournisseur) {
                        // Skip the current fournisseur to avoid duplicate
                        if (empty($current_achat_immo) || $fournisseur['id_fournisseur'] != $current_achat_immo['id_fournisseur']) {
                            echo '<option value="' . $fournisseur['id_fournisseur'] . '">' .
                                htmlspecialchars($fournisseur['nom_fournisseur']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <label for="quantite_achete_immo">Quantité acquise</label>
                <input type="number"
                    name="quantite_achete_immo"
                    id="quantite_achete_immo"
                    placeholder="Quantité acquise"
                    value="<?= !empty($current_achat_immo['quantite_achete_immo']) ? $current_achat_immo['quantite_achete_immo'] : '' ?>" />

                <label for="prix_unitaire_immo">Prix unitaire</label>
                <input type="number"
                    name="prix_unitaire_immo"
                    id="prix_unitaire_immo"
                    placeholder="Prix unitaire"
                    step="0.01"
                    value="<?= !empty($current_achat_immo['prix_unitaire_immo']) ? $current_achat_immo['prix_unitaire_immo'] : '' ?>" />

                <label for="valeur_achat_immo">Valeur totale</label>
                <input type="text"
                    name="valeur_achat_immo"
                    id="valeur_achat_immo"
                    placeholder="Valeur totale"
                    readonly
                    value="<?= !empty($current_achat_immo['valeur_achat_immo']) ? $current_achat_immo['valeur_achat_immo'] : '' ?>" />

                <label for="facture">Numéro de Facture</label>
                <input type="text"
                    name="facture"
                    id="facture"
                    placeholder="Numéro de facture"
                    value="<?= !empty($current_achat_immo['facture']) ? htmlspecialchars($current_achat_immo['facture']) : '' ?>" />

                <label for="date_achat_immo">Date achat</label>
                <input type="date"
                    name="date_achat_immo"
                    id="date_achat_immo"
                    value="<?= !empty($current_achat_immo['date_achat_immo']) ? $current_achat_immo['date_achat_immo'] : '' ?>" />

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
                        <th>Fournisseur</th>
                        <th>Quantité acquise</th>
                        <th>Prix unitaire</th>
                        <th>Valeur totale</th>
                        <th>Date achat</th>
                        <th>Facture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <?php
                $achats_immo = getAchatImmo();

                if (!empty($achats_immo) && is_array($achats_immo)) {
                    foreach ($achats_immo as $value) {
                ?>
                        <tr>
                            <td><?= htmlspecialchars($value['nom_immo']) ?></td>
                            <td><?= htmlspecialchars($value['nom_fournisseur']) ?></td>
                            <td><?= $value['quantite_achete_immo'] ?></td>
                            <td><?= $value['prix_unitaire_immo'] ?> MGA</td>
                            <td><?= $value['valeur_achat_immo'] ?> MGA</td>
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
                        <td colspan="8" class="text-center">Aucune immobilisation trouvée</td>
                      </tr>
                    <?php
                    }
                ?>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantiteInput = document.getElementById('quantite_achete_immo');
        const prixInput = document.getElementById('prix_unitaire_immo');
        const valeurInput = document.getElementById('valeur_achat_immo');

        function calculateTotal() {
            const quantite = parseFloat(quantiteInput.value) || 0;
            const prix = parseFloat(prixInput.value) || 0;
            valeurInput.value = (quantite * prix).toFixed(2);
        }

        quantiteInput.addEventListener('input', calculateTotal);
        prixInput.addEventListener('input', calculateTotal);

        // Calculate initial value if editing
        calculateTotal();
    });
</script>
<?php
include('./foot.php');
?>