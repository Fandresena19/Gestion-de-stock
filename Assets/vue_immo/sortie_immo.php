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
        <select name="immobilisation" id="immobilisation" required onchange="updateImmoDetails()">
          <option value="">-- Sélectionner une immobilisation --</option>
          <?php
          // If editing, show the current immobilisation as selected first
          if (!empty($current_sortie_immo['id_immo'])) {
            echo '<option value="' . $current_sortie_immo['id_immo'] . '" selected data-nom="' . 
              htmlspecialchars($current_sortie_immo['nom_immo']) . '">' .
              htmlspecialchars($current_sortie_immo['nom_immo']) . '</option>';
          }

          // Populate other immobilisations
          foreach ($immobilisations as $immobilisation) {
            // Skip the current immobilisation to avoid duplicate
            if (empty($current_sortie_immo) || $immobilisation['id_immo'] != $current_sortie_immo['id_immo']) {
              echo '<option value="' . $immobilisation['id_immo'] . '" data-nom="' . 
                htmlspecialchars($immobilisation['nom_immo']) . '">' .
                htmlspecialchars($immobilisation['nom_immo']) . '</option>';
            }
          }
          ?>
        </select>

        <!-- Hidden input to store the nom_immo -->
        <input type="hidden" name="nom_immo" id="nom_immo" value="<?= !empty($current_sortie_immo['nom_immo']) ? htmlspecialchars($current_sortie_immo['nom_immo']) : '' ?>">

        <label for="prix_unitaire_immo">Prix unitaire</label>
        <input type="number" 
               step="0.01"
               name="prix_unitaire_immo"
               id="prix_unitaire_immo"
               placeholder="Prix en MGA"
               value="<?= !empty($current_sortie_immo['prix_unitaire_immo']) ? $current_sortie_immo['prix_unitaire_immo'] : '' ?>"
               required />

        <label for="raison_sortie_immo">Raison sortie</label>
        <input type="text"
               name="raison_sortie_immo"
               id="raison_sortie_immo"
               placeholder="Raison précise de sortie"
               value="<?= !empty($current_sortie_immo['raison_sortie_immo']) ? htmlspecialchars($current_sortie_immo['raison_sortie_immo']) : '' ?>"
               required />

        <label for="date_sortie_immo">Date sortie</label>
        <input type="date"
               name="date_sortie_immo"
               id="date_sortie_immo"
               value="<?= !empty($current_sortie_immo['date_sortie_immo']) ? $current_sortie_immo['date_sortie_immo'] : date('Y-m-d') ?>"
               required />

        <div class="form-group">
          <input type="checkbox" 
                 name="supprimer_immobilisation" 
                 id="supprimer_immobilisation" 
                 value="1"
                 <?= !empty($current_sortie_immo) ? '' : 'checked' ?>>
          <label for="supprimer_immobilisation">Supprimer définitivement l'immobilisation de l'inventaire</label>
        </div>

        <br><br>
        <button type="submit" class="btn <?= !empty($current_sortie_immo) ? 'btn-primary' : 'btn-success' ?>">
          <?= !empty($current_sortie_immo) ? 'Modifier' : 'Ajouter' ?>
        </button>

        <?php if (!empty($current_sortie_immo)): ?>
          <a href="sortie_immo.php" class="btn btn-secondary">Annuler</a>
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
      <h4>Liste des sorties d'immobilisations</h4>
      <table class="mtable">
        <thead>
          <tr>
            <th>Nom immobilisation</th>
            <th>Prix unitaire</th>
            <th>Date sortie</th>
            <th>Raison sortie</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $sorties_immo = getSortieImmo();

        if (!empty($sorties_immo) && is_array($sorties_immo)) {
          foreach ($sorties_immo as $value) {
        ?>
            <tr>
              <td><?= htmlspecialchars($value['nom_immo']) ?></td>
              <td><?= number_format($value['prix_unitaire_immo'], 2, ',', ' ') ?> MGA</td>
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
            <td colspan="5" class="text-center">Aucune sortie d'immobilisation trouvée</td>
          </tr>
        <?php
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function updateImmoDetails() {
  const select = document.getElementById('immobilisation');
  const nomImmoField = document.getElementById('nom_immo');
  
  if (select.value) {
    const selectedOption = select.options[select.selectedIndex];
    const nomImmo = selectedOption.getAttribute('data-nom');
    nomImmoField.value = nomImmo;
  } else {
    nomImmoField.value = '';
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  updateImmoDetails();
});
</script>

<?php
include('./foot.php');
?>