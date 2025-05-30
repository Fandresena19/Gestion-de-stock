<?php
include('./head.php');

// Variable to store the current sortie for editing
$current_sortie = null;

// Check if a sortie ID is provided for editing
if (!empty($_GET['id'])) {
  $current_sortie = getSortie($_GET['id']);
}

// Get all articles for the dropdown
$articles = getArticle();
?>

<div class="home-content">
  <h3>Sortie des articles</h3>
  <div class="overview-boxes">
    <div class="box">
      <form action="../traitement/ajout_sortie.php" method="post">
        <!-- Hidden input for sortie ID during modification -->
        <input type="hidden" name="id_sortie" value="<?= !empty($current_sortie['id_sortie']) ? $current_sortie['id_sortie'] : '' ?>">

        <label for="article">Nom de l'article</label>
        <select name="article" id="article" required>
          <?php
          // If editing, show the current article as selected first
          if (!empty($current_sortie['id_article'])) {
            echo '<option value="' . $current_sortie['id_article'] . '" selected>' .
              htmlspecialchars($current_sortie['nom_article']) . '</option>';
          }

          // Populate other articles
          foreach ($articles as $article) {
            // Skip the current article to avoid duplicate
            if (empty($current_sortie) || $article['id_article'] != $current_sortie['id_article']) {
              echo '<option value="' . $article['id_article'] . '">' .
                htmlspecialchars($article['nom_article']) . '</option>';
            }
          }
          ?>
        </select>

        <label for="quantite_sortie">Quantité sortie</label>
        <input type="number"
          name="quantite_sortie"
          id="quantite_sortie"
          placeholder="Quantité sortie"
          value="<?= !empty($current_sortie['quantite_sortie']) ? $current_sortie['quantite_sortie'] : '' ?>" />

        <!-- Correction pour prix_unitaire_sortie -->
        <label for="prix_unitaire_sortie">Prix unitaire (moyenne des prix d'acquisition)</label>
        <input type="number"
          name="prix_unitaire_sortie"
          id="prix_unitaire_sortie"
          placeholder="Prix unitaire"
          readonly
          value="<?= !empty($current_sortie['prix_unitaire_sortie']) ? $current_sortie['prix_unitaire_sortie'] : '' ?>" />

        <!-- Correction pour valeur_sortie -->
        <label for="valeur_sortie">Valeur sortie</label>
        <input type="text"
          name="valeur_sortie"
          id="valeur_sortie"
          placeholder="Valeur totale"
          readonly
          value="<?= !empty($current_sortie['valeur_sortie']) ? $current_sortie['valeur_sortie'] : '' ?>" />

        <label for="raison_sortie">Raison sortie</label>
        <input type="text"
          name="raison_sortie"
          id="raison_sortie"
          placeholder="Raison précise de sortie"
          value="<?= !empty($current_sortie['raison_sortie']) ? htmlspecialchars($current_sortie['raison_sortie']) : '' ?>" />

        <label for="date_sortie">Date sortie</label>
        <input type="date"
          name="date_sortie"
          id="date_sortie"
          value="<?= !empty($current_sortie['date_sortie']) ? $current_sortie['date_sortie'] : '' ?>" />

        <br><br>
        <button type="submit" class="btn <?= !empty($current_sortie) ? 'btn-primary' : 'btn-success' ?>">
          <?= !empty($current_sortie) ? 'Modifier' : 'Ajouter' ?>
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
            <th>Nom article</th>
            <th>Quantité sortie</th>
            <th>Prix unitaire</th>
            <th>Montant</th>
            <th>Date sortie</th>
            <th>Raison sortie</th>
            <th>Actions</th>
          </tr>
        </thead>
        <?php
        $sorties = getSortie();

        if (!empty($sorties) && is_array($sorties)) {
          foreach ($sorties as $value) {
        ?>
            <tr>
              <td><?= htmlspecialchars($value['nom_article']) ?></td>
              <td><?= $value['quantite_sortie'] ?></td>
              <td><?= $value['prix_unitaire_sortie'] ?></td>
              <td><?= $value['valeur_sortie'] ?></td>
              <td><?= date('d/m/Y', strtotime($value['date_sortie'])) ?></td>
              <td><?= htmlspecialchars($value['raison_sortie']) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="../traitement/supprimer_sortie.php?id=<?= $value['id_sortie'] ?>"
                    class="delete-btn"
                    title="Supprimer"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette sortie ?');">
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
    const articleSelect = document.getElementById('article');
    const quantiteInput = document.getElementById('quantite_sortie');
    const prixInput = document.getElementById('prix_unitaire_sortie');
    const valeurInput = document.getElementById('valeur_sortie');

    // Function to get average price via AJAX
    function getAveragePrice(articleId) {
      fetch(`../traitement/get_average_price.php?id_article=${articleId}`)
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
    articleSelect.addEventListener('change', function() {
      const selectedArticleId = this.value;
      if (selectedArticleId) {
        getAveragePrice(selectedArticleId);
      }
    });

    quantiteInput.addEventListener('input', calculateTotal);

    // Calculate initial values if editing
    if (articleSelect.value) {
      getAveragePrice(articleSelect.value);
    }
    calculateTotal();
  });
</script>

<?php
include('./foot.php');
?>