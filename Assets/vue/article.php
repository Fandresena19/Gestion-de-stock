<?php
include('./head.php');

// Variable to store the current article for editing
$current_article = null;

// Check if an article ID is provided for editing
if (!empty($_GET['id'])) {
  $current_article = getArticle($_GET['id']);
}

// Get all categories for the dropdown
$categories = getAllCategories();

// Process search parameters
$searchParams = [];
if (!empty($_GET['nom_article'])) {
  $searchParams['nom_article'] = $_GET['nom_article'];
}
if (!empty($_GET['categorie'])) {
  $searchParams['id_categorie'] = $_GET['categorie'];
}
?>

<div class="home-content">
  <h3>Liste des articles</h3>
  <div class="overview-boxes">
    <div class="box">
      <form action="../traitement/modif_article.php" method="post">
        <!-- Hidden input for article ID during modification -->
        <input type="hidden" name="id_article" value="<?= !empty($current_article['id_article']) ? $current_article['id_article'] : '' ?>">

        <label for="nom_article">Nom de l'article</label>
        <input type="text" value="<?= !empty($current_article['nom_article']) ? htmlspecialchars($current_article['nom_article']) : '' ?>"
          name="nom_article" id="nom_article" placeholder="Saisir le nom de l'article" />

        <label for="categorie">Catégorie</label>
        <select name="categorie" id="categorie" required>
          <?php
          // If editing, show the current category as selected first
          if (!empty($current_article['id_categorie'])) {
            echo '<option value="' . $current_article['id_categorie'] . '" selected>' .
              htmlspecialchars($current_article['nom_categorie']) . '</option>';
          }

          // Populate other categories
          foreach ($categories as $category) {
            // Skip the current category to avoid duplicate
            if (empty($current_article) || $category['id_categorie'] != $current_article['id_categorie']) {
              echo '<option value="' . $category['id_categorie'] . '">' .
                htmlspecialchars($category['nom_categorie']) . '</option>';
            }
          }
          ?>
        </select>

        <label for="description">Description produit</label>
        <input type="text" value="<?= !empty($current_article['description_article']) ? htmlspecialchars($current_article['description_article']) : '' ?>"
          name="description" id="description" placeholder="Saisir la description" />
        <br /><br />

        <button type="submit" class="btn <?= !empty($current_article) ? 'btn-primary' : 'btn-success' ?>">
          <?= !empty($current_article) ? 'Modifier' : 'Ajouter' ?>
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
      <form action="" method="get" >
        <table class="mtable">
          <thead>
            <tr>
              <th>Nom article</th>
              <th>Catégorie</th> 
              <th>Actions</th>
            </tr>
          </thead>
          <tr>
            <td>
              <input type="text" name="nom_article" id="search_nom_article" 
                placeholder="Rechercher par nom" 
                value="<?= isset($_GET['nom_article']) ? htmlspecialchars($_GET['nom_article']) : '' ?>"/>
            </td>
            <td>
              <select name="categorie" id="search_categorie">
                <option value="">Toutes les catégories</option>
                <?php
                foreach ($categories as $category) {
                  $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $category['id_categorie']) ? 'selected' : '';
                  echo '<option value="' . $category['id_categorie'] . '" ' . $selected . '>' .
                    htmlspecialchars($category['nom_categorie']) . '</option>';
                }
                ?>
              </select>
            </td>
            <td>
              <button type="submit" class="btn btn-primary">
                Rechercher
              </button>
              <a href="article.php" class="btn btn-secondary">Réinitialiser</a>
            </td>
          </tr>
        </table>
      </form><br>

      <table class="mtable">
        <thead>
          <tr>
            <th>Nom article</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>

        <?php
        // Update the function.php getArticle() function to handle search parameters
        $articles = !empty($searchParams) ? getArticle(null, $searchParams) : getArticle();

        if (!empty($articles) && is_array($articles)) {
          foreach ($articles as $value) {
        ?>
            <tr>
              <td data-label="Nom article"><?= htmlspecialchars($value['nom_article']) ?></td>
              <td data-label="Catégorie"><?= htmlspecialchars($value['nom_categorie']) ?></td>
              <td data-label="Description"><?= htmlspecialchars($value['description_article']) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="article.php?id=<?= $value['id_article'] ?>" class="edit-btn" title="Modifier">
                    <i class='bx bx-edit-alt'></i>
                  </a>
                  <a href="../traitement/supprimer_article.php?id=<?= $value['id_article'] ?>"
                    class="delete-btn"
                    title="Supprimer"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
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
            <td colspan="4" class="text-center">Aucun article trouvé</td>
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