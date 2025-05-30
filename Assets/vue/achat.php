<?php
include('./head.php');

// Variable to store the current achat for editing
$current_achat = null;

// Check if an achat ID is provided for editing
if (!empty($_GET['id'])) {
    $current_achat = getAchat($_GET['id']);
}

// Get all articles for the dropdown
$articles = getArticle();

// Get all fournisseurs for the dropdown
$fournisseurs = getFournisseur();
?>

<div class="home-content">
    <h3>Achat Articles</h3>
    <div class="overview-boxes">
        <div class="box">
            <form action="../traitement/ajout_achat.php" method="post">
                <!-- Hidden input for achat ID during modification -->
                <input type="hidden" name="id_achat" value="<?= !empty($current_achat['id_achat']) ? $current_achat['id_achat'] : '' ?>">

                <label for="article">Nom de l'article</label>
                <select name="article" id="article" required>
                    <?php
                    // If editing, show the current article as selected first
                    if (!empty($current_achat['id_article'])) {
                        echo '<option value="' . $current_achat['id_article'] . '" selected>' .
                            htmlspecialchars($current_achat['nom_article']) . '</option>';
                    }

                    // Populate other articles
                    foreach ($articles as $article) {
                        // Skip the current article to avoid duplicate
                        if (empty($current_achat) || $article['id_article'] != $current_achat['id_article']) {
                            echo '<option value="' . $article['id_article'] . '">' .
                                htmlspecialchars($article['nom_article']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <label for="fournisseur">Fournisseur</label>
                <select name="fournisseur" id="fournisseur" required>
                    <?php
                    // If editing, show the current fournisseur as selected first
                    if (!empty($current_achat['id_fournisseur'])) {
                        echo '<option value="' . $current_achat['id_fournisseur'] . '" selected>' .
                            htmlspecialchars($current_achat['nom_fournisseur']) . '</option>';
                    }

                    // Populate other fournisseurs
                    foreach ($fournisseurs as $fournisseur) {
                        // Skip the current fournisseur to avoid duplicate
                        if (empty($current_achat) || $fournisseur['id_fournisseur'] != $current_achat['id_fournisseur']) {
                            echo '<option value="' . $fournisseur['id_fournisseur'] . '">' .
                                htmlspecialchars($fournisseur['nom_fournisseur']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <label for="quantite_acquis">Quantité acquise</label>
                <input type="number"
                    name="quantite_acquis"
                    id="quantite_acquis"
                    placeholder="Quantité acquise"
                    value="<?= !empty($current_achat['quantite_acquis']) ? $current_achat['quantite_acquis'] : '' ?>" />

                <!-- Après le champ quantité acquise -->
                <label for="prix_unitaire_achat">Prix unitaire</label>
                <input type="number"
                    name="prix_unitaire_achat"
                    id="prix_unitaire_achat"
                    placeholder="Prix unitaire"
                    step="0.01"
                    value="<?= !empty($current_achat['prix_unitaire_achat']) ? $current_achat['prix_unitaire_achat'] : '' ?>" />

                <label for="valeur_achat">Valeur totale</label>
                <input type="text"
                    name="valeur_achat"
                    id="valeur_achat"
                    placeholder="Valeur totale"
                    readonly
                    value="<?= !empty($current_achat['valeur_achat']) ? $current_achat['valeur_achat'] : '' ?>" />

                <label for="numero_facture">Numéro de Facture</label>
                <input type="text"
                    name="numero_facture"
                    id="numero_facture"
                    placeholder="Numéro de facture"
                    value="<?= !empty($current_achat['numero_facture']) ? htmlspecialchars($current_achat['numero_facture']) : '' ?>" />

                <label for="date_achat">Date achat</label>
                <input type="date"
                    name="date_achat"
                    id="date_achat"
                    value="<?= !empty($current_achat['date_achat']) ? $current_achat['date_achat'] : '' ?>" />


                <br><br>
                <button type="submit" class="btn <?= !empty($current_achat) ? 'btn-primary' : 'btn-success' ?>">
                    <?= !empty($current_achat) ? 'Modifier' : 'Ajouter' ?>
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
                        <th>Nom article</th>
                        <th>Fournisseur</th>
                        <th>Quantité acquise</th>
                        <th>Date achat</th>
                        <th>Facture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <?php
                $achats = getAchat();

                if (!empty($achats) && is_array($achats)) {
                    foreach ($achats as $value) {
                ?>
                        <tr>
                            <td><?= htmlspecialchars($value['nom_article']) ?></td>
                            <td><?= htmlspecialchars($value['nom_fournisseur']) ?></td>
                            <td><?= $value['quantite_achete'] ?></td>
                            <td><?= date('d/m/Y', strtotime($value['date_achat'])) ?></td>
                            <td><?= htmlspecialchars($value['facture']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../traitement/supprimer_achat.php?id=<?= $value['id_achat'] ?>"
                                        class="delete-btn"
                                        title="Supprimer"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat ?');">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantiteInput = document.getElementById('quantite_acquis');
        const prixInput = document.getElementById('prix_unitaire_achat');
        const valeurInput = document.getElementById('valeur_achat');

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