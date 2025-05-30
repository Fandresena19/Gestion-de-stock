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
if (isset($_GET['quantite_min']) && $_GET['quantite_min'] !== '') {
  $searchParams['quantite_min'] = (int)$_GET['quantite_min'];
}
if (isset($_GET['quantite_max']) && $_GET['quantite_max'] !== '') {
  $searchParams['quantite_max'] = (int)$_GET['quantite_max'];
}

// Sorting parameters
if (!empty($_GET['sort_by'])) {
  $searchParams['sort_by'] = $_GET['sort_by'];
  $searchParams['sort_dir'] = !empty($_GET['sort_dir']) ? $_GET['sort_dir'] : 'ASC';
}

// Mettre à jour les quantités en stock
updateStockQuantities();
?>

<div class="home-content">
  <h3>Stock</h3>
  <div class="overview-boxes">
    <div class="box">
      <h3>Filtrer les articles en stock</h3>
      
      <form action="" method="get" class="filter-form">
        <div class="filter-row">
          <div class="filter-group">
            <label for="nom_article">Nom article:</label>
            <input type="text" id="nom_article" name="nom_article" 
                   value="<?= isset($_GET['nom_article']) ? htmlspecialchars($_GET['nom_article']) : '' ?>" 
                   placeholder="Filtrer par nom">
          </div>
          
          <div class="filter-group">
            <label for="categorie">Catégorie:</label>
            <select name="categorie" id="categorie">
              <option value="">Toutes les catégories</option>
              <?php
              foreach ($categories as $category) {
                $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $category['id_categorie']) ? 'selected' : '';
                echo '<option value="' . $category['id_categorie'] . '" ' . $selected . '>' .
                  htmlspecialchars($category['nom_categorie']) . '</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="quantite_min">Quantité min:</label>
            <input type="number" id="quantite_min" name="quantite_min" 
                   value="<?= isset($_GET['quantite_min']) ? htmlspecialchars($_GET['quantite_min']) : '' ?>" 
                   placeholder="Min">
          </div>
          
          <div class="filter-group">
            <label for="quantite_max">Quantité max:</label>
            <input type="number" id="quantite_max" name="quantite_max" 
                   value="<?= isset($_GET['quantite_max']) ? htmlspecialchars($_GET['quantite_max']) : '' ?>" 
                   placeholder="Max">
          </div>
          
          <div class="filter-group">
            <label for="sort_by">Trier par:</label>
            <select name="sort_by" id="sort_by">
              <option value="">-- Choisir --</option>
              <option value="nom_article" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] == 'nom_article') ? 'selected' : '' ?>>Nom article</option>
              <option value="nom_categorie" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] == 'nom_categorie') ? 'selected' : '' ?>>Catégorie</option>
              <option value="quantite_en_stock" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] == 'quantite_en_stock') ? 'selected' : '' ?>>Quantité</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="sort_dir">Ordre:</label>
            <select name="sort_dir" id="sort_dir">
              <option value="ASC" <?= (isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'ASC') ? 'selected' : '' ?>>Croissant</option>
              <option value="DESC" <?= (isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'DESC') ? 'selected' : '' ?>>Décroissant</option>
            </select>
          </div>
        </div>
        
        <div class="filter-buttons">
          <button type="submit" class="btn btn-primary">Filtrer</button>
          <a href="stock.php" class="btn btn-secondary">Réinitialiser</a>
        </div>
      </form>
    </div>
      <div class="box">
      <table class="mtable">
        <thead>
          <tr>
            <th>Nom article</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Quantité en stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $articles = !empty($searchParams) ? getArticle(null, $searchParams) : getArticle();

        if(!empty($articles) && is_array($articles)){
          foreach($articles as $value){
            ?>
            <tr>
              <td data-label="Nom article"><?= htmlspecialchars($value['nom_article'])?></td>
              <td data-label="Catégorie"><?= htmlspecialchars($value['nom_categorie'])?></td>
              <td data-label="Description"><?= htmlspecialchars($value['description_article'])?></td>
              <td data-label="Quantité"><?= $value['quantite_en_stock'] ?? 0 ?></td>
              <td>
                <div class="action-buttons">
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
            <td colspan="5" class="text-center">Aucun article trouvé</td>
          </tr>
          <?php
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .filter-form {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
  }
  
  .filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
  }
  
  .filter-group {
    flex: 1;
    min-width: 180px;
  }
  
  .filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  .filter-group input,
  .filter-group select {
    width: 100%;
    padding: 6px;
    border: 1px solid #ced4da;
    border-radius: 4px;
  }
  
  .filter-buttons {
    display: flex;
    gap: 10px;
  }
  
  .btn-secondary {
    background-color: #6c757d;
    color: white;
    padding: 5px 10px;
    border-radius: 3px;
    text-decoration: none;
    display: inline-block;
  }
  
  .text-center {
    text-align: center;
  }
</style>

<?php
include('./foot.php');
?>