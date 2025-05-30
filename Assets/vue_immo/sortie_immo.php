<?php
include('./head.php');

// Variable to store the current sortie_immo for editing
$current_sortie_immo = null;

// Check if a sortie_immo ID is provided for editing
if (!empty($_GET['id'])) {
  $current_sortie_immo = getSortieImmo($_GET['id']);
}

// Get all immobilisations for the dropdown
$immobilisations = getImmobilisation();
?>

<div class="home-content">
  <h3>Sortie Immobilisation</h3>
  <div class="overview-boxes">
    <div class="box">
      <form action="../traitement_immo/ajout_sortie_immo.php" method="post">
        <!-- Hidden input for sortie_immo ID during modification -->
        <input type="hidden" name="id_sortie_immo" value="<?= !empty($current_sortie_immo['id_sortie_immo']) ? $current_sortie_immo['id_sortie_immo'] : '' ?>">

        <label for="immobilisation">Nom de l'immobilisation</label>
        <select name="immobilisation" id="immobilisation" required>
          <?php
          // If editing, show the current immobilisation as selected first
          if (!empty($current_sortie_immo['id_immo'])) {
            echo '<option value="' . $current_sortie_immo['id_immo'] . '" selected>' .
              htmlspecialchars($current_sortie_immo['nom_immo']) . '</option>';
          }

          // Populate other immobilisations
          foreach ($immobilisations as $immobilisation) {
            // Skip the current immobilisation to avoid duplicate
            if (empty($current_sortie_immo) || $immobilisation['id_immo'] != $current_sortie_immo['id_immo']) {
              echo '<option value="' . $immobilisation['id_immo'] . '">' .
                htmlspecialchars($immobilisation['nom_immo']) . '</option>';
            }
          }
          ?>
        </select>

        <label for="quantite_sortie_immo">Quantité sortie</label>
        <input type="number"
          name="quantite_sortie_immo"
          id="quantite_sortie_immo"
          placeholder="Quantité sortie"
          value="<?= !empty($current_sortie_immo['quantite_sortie_immo']) ? $current_sortie_immo['quantite_sortie_immo'] : '' ?>" />

        <label for="prix_unitaire_immo">Prix unitaire (moyenne des prix d'acquisition)</label>
        <input type="number"
          name="prix_unitaire_immo"
          id="prix_unitaire_immo"
          placeholder="Prix unitaire"
          readonly
          value="<?= !empty($current_sortie_immo['prix_unitaire_immo']) ? $current_sortie_immo['prix_unitaire_immo'] : '' ?>" />

        <label for="valeur_sortie_immo">Valeur sortie</label>
        <input type="text"
          name="valeur_sortie_immo"
          id="valeur_sortie_immo"
          placeholder="Valeur totale"
          readonly
          value="<?= !empty($current_sortie_immo['valeur_sortie_immo']) ? $current_sortie_immo['valeur_sortie_immo'] : '' ?>" />

        <label for="raison_sortie_immo">Raison sortie</label>
        <input type="text"
          name="raison_sortie_immo"
          id="raison_sortie_immo"
          placeholder="Raison précise de sortie"
          value="<?= !empty($current_sortie_immo['raison_sortie_immo']) ? htmlspecialchars($current_sortie_immo['raison_sortie_immo']) : '' ?>" />

        <label for="date_sortie_immo">Date sortie</label>
        <input type="date"
          name="date_sortie_immo"
          id="date_sortie_immo"
          value="<?= !empty($current_sortie_immo['date_sortie_immo']) ? $current_sortie_immo['date_sortie_immo'] : '' ?>" />

        <br><br>
        <button type="submit" class="btn <?= !empty($current_sortie_immo) ? 'btn-primary' : 'btn-success' ?>">
          <?= !empty($current_sortie_immo) ? 'Modifier' : 'Ajouter' ?>
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
      <table class="mtable">
        <thead>
          <tr>
            <th>Nom immobilisation</th>
            <th>Quantité sortie</th>
            <th>Prix unitaire</th>
            <th>Montant</th>
            <th>Date sortie</th>
            <th>Raison sortie</th>
            <th>Actions</th>
          </tr>
        </thead>
        <?php
        $sorties_immo = getSortieImmo();

        if (!empty($sorties_immo) && is_array($sorties_immo)) {
          foreach ($sorties_immo as $value) {
        ?>
            <tr>
              <td><?= htmlspecialchars($value['nom_immo']) ?></td>
              <td><?= $value['quantite_sortie_immo'] ?></td>
              <td><?= $value['prix_unitaire_immo'] ?> MGA</td>
              <td><?= $value['valeur_sortie_immo'] ?> MGA</td>
              <td><?= date('d/m/Y', strtotime($value['date_sortie_immo'])) ?></td>
              <td><?= htmlspecialchars($value['raison_sortie_immo']) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="sortie_immo.php?id=<?= $value['id_sortie_immo'] ?>"
                    class="edit-btn"
                    title="Modifier">
                    <i class='bx bx-edit'></i>
                  </a>
                  <a href="../traitement_immo/supprimer_sortie_immo.php?id=<?= $value['id_sortie_immo'] ?>"
                    class="delete-btn"
                    title="Supprimer"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette sortie d\'immobilisation ?');">
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
              <td colspan="7" class="text-center">Aucune sortie d'immobilisation trouvée</td>
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
    const immobilisationSelect = document.getElementById('immobilisation');
    const quantiteInput = document.getElementById('quantite_sortie_immo');
    const prixInput = document.getElementById('prix_unitaire_immo');
    const valeurInput = document.getElementById('valeur_sortie_immo');

    // Function to get average price via AJAX
    function getAveragePriceImmo(immoId) {
      fetch(`../traitement_immo/get_average_price_immo.php?id_immo=${immoId}`)
        .then(response => response.json())
        .then(data => {
          prixInput.value = parseFloat(data.average_price).toFixed(2);
          calculateTotal();
        })
        .catch(error => console.error('Error:', error));
    }

    function calculateTotal() {
      const quantite = parseFloat(quantiteInput.value) || 0;
      const prix = parseFloat(prixInput.value) || 0;
      valeurInput.value = (quantite * prix).toFixed(2);
    }

    // Event listeners
    immobilisationSelect.addEventListener('change', function() {
      const selectedImmoId = this.value;
      if (selectedImmoId) {
        getAveragePriceImmo(selectedImmoId);
      }
    });

    quantiteInput.addEventListener('input', calculateTotal);

    // Calculate initial values if editing
    if (immobilisationSelect.value) {
      getAveragePriceImmo(immobilisationSelect.value);
    }
    calculateTotal();
  });
</script>

<?php
include('./foot.php');
?>