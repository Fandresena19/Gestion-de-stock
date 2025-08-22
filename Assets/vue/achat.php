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

<div id="addFournisseurModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h4>Ajouter un nouveau Fournisseur</h4>
        <form action="../traitement/add_fournisseur.php" method="post" id="addFournisseurForm">
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