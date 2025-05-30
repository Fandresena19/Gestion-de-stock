<?php
include('./head.php');

// Variable to store the current immobilisation for editing
$current_immobilisation = null;

// Check if an immobilisation ID is provided for editing
if (!empty($_GET['id'])) {
  $current_immobilisation = getImmobilisation($_GET['id']);
}

// Get all categories for the dropdown
$categories = getAllCategoriesImmo();

// Process search parameters
$searchParams = [];
if (!empty($_GET['nom_immo'])) {
  $searchParams['nom_immo'] = $_GET['nom_immo'];
}
if (!empty($_GET['categorie'])) {
  $searchParams['id_categorie_immo'] = $_GET['categorie'];
}
?>

<div class="home-content">
  <h3>Liste des Immobilisations</h3>
  <div class="overview-boxes">
    <div class="box">
      <form action="../traitement_immo/modif_immobilisation.php" method="post">
        <!-- Hidden input for immobilisation ID during modification -->
        <input type="hidden" name="id_immo" value="<?= !empty($current_immobilisation['id_immo']) ? $current_immobilisation['id_immo'] : '' ?>">

        <label for="nom_immo">Nom de l'immobilisation</label>
        <input type="text" value="<?= !empty($current_immobilisation['nom_immo']) ? htmlspecialchars($current_immobilisation['nom_immo']) : '' ?>"
          name="nom_immo" id="nom_immo" placeholder="Saisir le nom de l'immobilisation" />

        <label for="categorie">Catégorie</label>
        <select name="categorie" id="categorie" required>
          <?php
          // If editing, show the current category as selected first
          if (!empty($current_immobilisation['id_categorie_immo'])) {
            echo '<option value="' . $current_immobilisation['id_categorie_immo'] . '" selected>' .
              htmlspecialchars($current_immobilisation['nom_categorie_immo']) . '</option>';
          }

          // Populate other categories
          foreach ($categories as $category) {
            // Skip the current category to avoid duplicate
            if (empty($current_immobilisation) || $category['id_categorie_immo'] != $current_immobilisation['id_categorie_immo']) {
              echo '<option value="' . $category['id_categorie_immo'] . '">' .
                htmlspecialchars($category['nom_categorie_immo']) . '</option>';
            }
          }
          ?>
        </select>

        <label for="description">Description immobilisation</label>
        <input type="text" value="<?= !empty($current_immobilisation['description_immo']) ? htmlspecialchars($current_immobilisation['description_immo']) : '' ?>"
          name="description" id="description" placeholder="Saisir la description" />

          <br><br>
        <button type="submit" class="btn <?= !empty($current_immobilisation) ? 'btn-primary' : 'btn-success' ?>">
          <?= !empty($current_immobilisation) ? 'Modifier' : 'Ajouter' ?>
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
      <h3>Recherche et filtrage</h3>
      <form action="" method="get">
        <table class="mtable">
          <thead>
            <tr>
              <th>Nom immobilisation</th>
              <th>Catégorie</th> 
              <th>Actions</th>
            </tr>
          </thead>
          <tr>
            <td>
              <input type="text" name="nom_immo" id="search_nom_immo" 
                placeholder="Rechercher par nom" 
                value="<?= isset($_GET['nom_immo']) ? htmlspecialchars($_GET['nom_immo']) : '' ?>"/>
            </td>
            <td>
              <select name="categorie" id="search_categorie">
                <option value="">Toutes les catégories</option>
                <?php
                foreach ($categories as $category) {
                  $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $category['id_categorie_immo']) ? 'selected' : '';
                  echo '<option value="' . $category['id_categorie_immo'] . '" ' . $selected . '>' .
                    htmlspecialchars($category['nom_categorie_immo']) . '</option>';
                }
                ?>
              </select>
            </td>
            <td>
              <button type="submit" class="btn btn-primary">
                Rechercher
              </button>
              <a href="immobilisation.php" class="btn btn-secondary">Réinitialiser</a>
            </td>
          </tr>
        </table>
      </form><br>

      <table class="mtable">
        <thead>
          <tr>
            <th>Nom immobilisation</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Actions</th>
          </tr>
        </thead>

        <?php
        // Get immobilisations with search parameters
        $immobilisations = !empty($searchParams) ? getImmobilisation(null, $searchParams) : getImmobilisation();

        if (!empty($immobilisations) && is_array($immobilisations)) {
          foreach ($immobilisations as $value) {
        ?>
            <tr>
              <td data-label="Nom immobilisation"><?= htmlspecialchars($value['nom_immo']) ?></td>
              <td data-label="Catégorie"><?= htmlspecialchars($value['nom_categorie_immo']) ?></td>
              <td data-label="Description"><?= htmlspecialchars($value['description_immo']) ?></td>
              <td data-label="Quantité"><?= htmlspecialchars($value['quantite_en_stock_immo']) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="immobilisation.php?id=<?= $value['id_immo'] ?>" class="edit-btn" title="Modifier">
                    <i class='bx bx-edit-alt'></i>
                  </a>
                  <a href="../traitement/supprimer_immobilisation.php?id=<?= $value['id_immo'] ?>"
                    class="delete-btn"
                    title="Supprimer"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette immobilisation ?');">
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
            <td colspan="5" class="text-center">Aucune immobilisation trouvée</td>
          </tr>
        <?php
        }
        ?>
      </table>
    </div>
  </div>
</div>

<style>
  .text-center {
    text-align: center;
  }
  
  .btn-secondary {
    background-color: #6c757d;
    color: white;
    padding: 5px 10px;
    border-radius: 3px;
    text-decoration: none;
    display: inline-block;
    margin-left: 5px;
  }
</style>

<?php
include('./foot.php');
?>