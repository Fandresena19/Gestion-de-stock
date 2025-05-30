<?php
include('./head.php');

// Get all articles for dropdown
$articles = getArticle();

// Get all fournisseurs for dropdown
$fournisseurs = getFournisseur();

// Définir les dates par défaut pour le filtre
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01'); // Premier jour du mois
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-t'); // Dernier jour du mois

// Préparer les paramètres de recherche
$searchParams = [
  'date_debut' => $date_debut,
  'date_fin' => $date_fin
];

// Ajouter d'autres paramètres de recherche s'ils existent
if (!empty($_GET['id_article'])) {
  $searchParams['id_article'] = $_GET['id_article'];
}

if (!empty($_GET['id_fournisseur'])) {
  $searchParams['id_fournisseur'] = $_GET['id_fournisseur'];
}

if (!empty($_GET['type'])) {
  $searchParams['type'] = $_GET['type'];
}

if (!empty($_GET['raison_sortie'])) {
  $searchParams['raison_sortie'] = $_GET['raison_sortie'];
}
?>

<div class="home-content">
  <h3>Mouvement de stock</h3>
  <div class="overview-boxes">
    <div class="box" style="width: 100%;">
      <h2>Filtrage des mouvements de stock</h2>

      <!-- Formulaire de filtre avancé -->
      <form method="GET" action="" class="filter-form">
        <div class="filter-row">
          <div class="filter-group">
            <label for="date_debut">Date début:</label>
            <input type="date" id="date_debut" name="date_debut" value="<?= $date_debut ?>">
          </div>
          
          <div class="filter-group">
            <label for="date_fin">Date fin:</label>
            <input type="date" id="date_fin" name="date_fin" value="<?= $date_fin ?>">
          </div>
          
          <div class="filter-group">
            <label for="type">Type de mouvement:</label>
            <select name="type" id="type">
              <option value="">Tous</option>
              <option value="Entrée" <?= (isset($_GET['type']) && $_GET['type'] == 'Entrée') ? 'selected' : '' ?>>Entrée</option>
              <option value="Sortie" <?= (isset($_GET['type']) && $_GET['type'] == 'Sortie') ? 'selected' : '' ?>>Sortie</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="id_article">Article:</label>
            <select name="id_article" id="id_article">
              <option value="">Tous les articles</option>
              <?php
              if (!empty($articles) && is_array($articles)) {
                foreach ($articles as $article) {
                  $selected = (isset($_GET['id_article']) && $_GET['id_article'] == $article['id_article']) ? 'selected' : '';
                  echo '<option value="' . $article['id_article'] . '" ' . $selected . '>' . 
                    htmlspecialchars($article['nom_article']) . '</option>';
                }
              }
              ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="id_fournisseur">Fournisseur:</label>
            <select name="id_fournisseur" id="id_fournisseur">
              <option value="">Tous les fournisseurs</option>
              <?php
              if (!empty($fournisseurs) && is_array($fournisseurs)) {
                foreach ($fournisseurs as $fournisseur) {
                  $selected = (isset($_GET['id_fournisseur']) && $_GET['id_fournisseur'] == $fournisseur['id_fournisseur']) ? 'selected' : '';
                  echo '<option value="' . $fournisseur['id_fournisseur'] . '" ' . $selected . '>' .
                    htmlspecialchars($fournisseur['nom_fournisseur']) . '</option>';
                }
              }
              ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="raison_sortie">Raison de sortie:</label>
            <input type="text" id="raison_sortie" name="raison_sortie" 
                  value="<?= isset($_GET['raison_sortie']) ? htmlspecialchars($_GET['raison_sortie']) : '' ?>" 
                  placeholder="Rechercher une raison">
          </div>
        </div>
        
        <div class="filter-buttons">
          <button type="submit" class="btn btn-primary">Filtrer</button>
          <a href="mouvement_stock.php" class="btn btn-secondary">Réinitialiser</a>
        </div>
      </form>
    </div>


    <div class="box">
      <table class="mtable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Article</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Valeur</th>
            <th>Fournisseur/Raison</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Récupérer les mouvements avec les filtres appliqués
          $mouvements = getMouvementsStock($date_debut, $date_fin, $searchParams);

          // Afficher les mouvements
          if (!empty($mouvements)) {
            foreach ($mouvements as $mouvement) {
              $color_class = $mouvement['type'] == 'Entrée' ? 'text-success' : 'text-danger';
              $quantite_affichage = $mouvement['type'] == 'Entrée' ? '+' . $mouvement['quantite'] : '-' . $mouvement['quantite'];
          ?>
              <tr>
                <td data-label="Date"><?= date('d/m/Y', strtotime($mouvement['date'])) ?></td>
                <td class="<?= $color_class ?>" data-label="Type"><?= htmlspecialchars($mouvement['type']) ?></td>
                <td data-label="Article"><?= htmlspecialchars($mouvement['article']) ?></td>
                <td class="<?= $color_class ?>" data-label="Quantité"><?= $quantite_affichage ?></td>
                <td data-label="Prix"><?= number_format($mouvement['prix_unitaire'] ?? 0, 2) ?> MGA</td>
                <td data-label="Montant"><?= number_format($mouvement['valeur'] ?? 0, 2) ?> MGA</td>
                <td data-label="Detail"><?= htmlspecialchars($mouvement['details']) ?></td>
              </tr>
            <?php
            }
          } else {
            ?>
            <tr>
              <td colspan="7" class="text-center">Aucun mouvement trouvé pour cette période</td>
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
  .text-success {
    color: green;
  }

  .text-danger {
    color: red;
  }

  .text-center {
    text-align: center;
  }

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
</style>

<?php
include('./foot.php');
?>