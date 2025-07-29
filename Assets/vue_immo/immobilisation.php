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

$types = $bdd->query("SELECT type_immo FROM immobilisation GROUP BY type_immo");
$type = $types->fetchAll(PDO::FETCH_ASSOC);

// Processus de recherche et filtrage
$searchParams = [];
if (!empty($_GET['nom_immo'])) {
  $searchParams['nom_immo'] = $_GET['nom_immo'];
}
if (!empty($_GET['categorie'])) {
  $searchParams['id_categorie_immo'] = $_GET['categorie'];
}
// Utiliser 'type_immo' comme clé pour la recherche
if (!empty($_GET['type'])) {
  $searchParams['type_immo'] = $_GET['type'];
}

// Fonction pour calculer l'amortissement linéaire
function calculateLinearDepreciation($prix_unitaire, $duree_vie, $debut_service) {
  if (empty($duree_vie) || empty($debut_service) || $duree_vie <= 0 || $prix_unitaire <= 0) {
    return null;
  }
  
  $taux_amortissement = 100 / $duree_vie;
  $amortissement_annuel = $prix_unitaire * ($taux_amortissement / 100);
  
  $debut_service_date = new DateTime($debut_service);
  $current_year = date('Y');
  $debut_year = $debut_service_date->format('Y');
  
  $tableau_amortissement = [];
  $amortissement_cumule = 0;
  
  for ($i = 0; $i < $duree_vie; $i++) {
    $annee = $debut_year + $i;
    
    // Calcul de l'amortissement pour la première année (prorata temporis)
    if ($i == 0) {
      $debut_service_month = $debut_service_date->format('n');
      $debut_service_day = $debut_service_date->format('j');
      
      // Calcul du prorata (en mois)
      $mois_restants = 12 - $debut_service_month + 1;
      if ($debut_service_day > 15) {
        $mois_restants--;
      }
      
      $amortissement_periode = $amortissement_annuel * ($mois_restants / 12);
    } else {
      $amortissement_periode = $amortissement_annuel;
    }
    
    $amortissement_cumule += $amortissement_periode;
    $valeur_comptable = $prix_unitaire - $amortissement_cumule;
    
    $tableau_amortissement[] = [
      'annee' => $annee,
      'base_amortissable' => $prix_unitaire,
      'amortissement' => $amortissement_periode,
      'amortissement_cumule' => $amortissement_cumule,
      'valeur_comptable' => $valeur_comptable
    ];
    
    // Arrêter si la valeur comptable devient nulle ou négative
    if ($valeur_comptable <= 0) {
      break;
    }
  }
  
  return $tableau_amortissement;
}

?>

<div class="home-content">
  <h3>Liste des Immobilisations</h3>
  <div class="overview-boxes">
    
    <!-- Formulaire de modification -->
    <div class="box">
      <h3><?= !empty($current_immobilisation) ? 'Modifier l\'immobilisation' : 'Sélectionner une immobilisation à modifier' ?></h3>
      <form action="../traitement_immo/modif_immobilisation.php" method="post">
        <!-- Hidden input for immobilisation ID during modification -->
        <input type="hidden" name="id_immo" value="<?= !empty($current_immobilisation['id_immo']) ? $current_immobilisation['id_immo'] : '' ?>">

        <label for="nom_immo">Nom de l'immobilisation</label>
        <input type="text" value="<?= !empty($current_immobilisation['nom_immo']) ? htmlspecialchars($current_immobilisation['nom_immo']) : '' ?>"
          name="nom_immo" id="nom_immo" placeholder="Saisir le nom de l'immobilisation" 
          <?= empty($current_immobilisation) ? 'readonly' : '' ?> required />

        <label for="categorie">Catégorie</label>
        <select name="categorie" id="categorie" <?= empty($current_immobilisation) ? 'disabled' : '' ?> required>
          <option value="">Sélectionnez une catégorie</option>
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

        <label for="type">Type immobilisation</label>
        <select name="type" id="type" class="type" onchange="HandleTypeChange()" <?= empty($current_immobilisation) ? 'disabled' : '' ?>>
          <option value="">Sélectionnez ou ajoutez en</option>
          <?php
          // If editing, show the current type as selected first
          if (!empty($current_immobilisation['type_immo'])) {
            echo '<option value="' . htmlspecialchars($current_immobilisation['type_immo']) . '" selected>' .
              htmlspecialchars($current_immobilisation['type_immo']) . '</option>';
          }
          // Populate other types
          foreach ($type as $t) {
            // Skip the current type to avoid duplicate
            if (empty($current_immobilisation) || $t['type_immo'] != $current_immobilisation['type_immo']) {
              echo '<option value="' . htmlspecialchars($t['type_immo']) . '">' .
                htmlspecialchars($t['type_immo']) . '</option>';
            }
          }
          ?>
          <option value="new">+ Ajouter une nouvelle type</option>
        </select>
        <input type="text" id="new_type" name="new_type" placeholder="Ajouter un nouveau type" style="display:none;" 
               <?= empty($current_immobilisation) ? 'readonly' : '' ?> />
        
        <label for="duree_vie">Durée de vie (en années)</label>
        <input type="number" value="<?= !empty($current_immobilisation['duree_vie']) ? $current_immobilisation['duree_vie'] : '' ?>"
          name="duree_vie" id="duree_vie" placeholder="Saisir la durée de vie en années" 
          <?= empty($current_immobilisation) ? 'readonly' : '' ?> />

        <label for="description">Description immobilisation</label>
        <input type="text" value="<?= !empty($current_immobilisation['description_immo']) ? htmlspecialchars($current_immobilisation['description_immo']) : '' ?>"
          name="description" id="description" placeholder="Saisir la description" 
          <?= empty($current_immobilisation) ? 'readonly' : '' ?> required />

        <label for="debut_service">Date de début de service</label>
        <input type="date" value="<?= !empty($current_immobilisation['debut_service']) ? $current_immobilisation['debut_service'] : '' ?>"
          name="debut_service" id="debut_service" <?= empty($current_immobilisation) ? 'readonly' : '' ?> />

        <br><br>
        <button type="submit" class="btn btn-primary" <?= empty($current_immobilisation) ? 'disabled' : '' ?>>
          Modifier
        </button>
        <?php if (!empty($current_immobilisation)): ?>
          <a href="immobilisation.php" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
      </form>
    </div>

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

    <div class="box">
      <h3>Recherche et filtrage</h3>
      <form action="" method="get">
        <table class="mtable">
          <thead>
            <tr>
              <th>Nom immobilisation</th>
              <th>Catégorie</th> 
              <th>Type</th>
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
              <select name="type" id="search_type">
                <option value="">Tous les types</option>
                <?php
                foreach ($type as $t) {
                  $selected = (isset($_GET['type']) && $_GET['type'] == $t['type_immo']) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($t['type_immo']) . '" ' . $selected . '>' .
                    htmlspecialchars($t['type_immo']) . '</option>';
                }
                ?>
              </select>
            </td>
            <td>
              <button type="submit" class="btn btn-primary" style="width: auto; height: 30px;">
                Rechercher
              </button>
              <a href="immobilisation.php" class="btn btn-secondary" style="width: auto; height:30px;">Réinitialiser</a>
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
            <th>Type</th>
            <th>Durée de vie</th>
            <th>Date début service</th>
            <th>Actions</th>
          </tr>
        </thead>

        <?php
        // Get immobilisations with search parameters
        $immobilisations = !empty($searchParams) ? getImmobilisation(null, $searchParams) : getImmobilisation();

        if (!empty($immobilisations) && is_array($immobilisations)) {
          foreach ($immobilisations as $index => $value) {
            // Récupérer les données d'achat pour calculer l'amortissement
            $achat_info = null;
            if (!empty($value['id_immo'])) {
              $stmt = $bdd->prepare("SELECT prix_unitaire_immo FROM achat_immo WHERE id_immo = ?");
              $stmt->execute([$value['id_immo']]);
              $achat_info = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        ?>
            <tr>
              <td data-label="Nom immobilisation"><?= htmlspecialchars($value['nom_immo']) ?></td>
              <td data-label="Catégorie"><?= htmlspecialchars($value['nom_categorie_immo']) ?></td>
              <td data-label="Description"><?= htmlspecialchars($value['description_immo']) ?></td>
              <td data-label="Type"><?= htmlspecialchars($value['type_immo']) ?></td>
              <td data-label="Duree"><?= !empty($value['duree_vie']) ? $value['duree_vie'] : 'Non définie'?></td>
              <td data-label="Date début service"><?= !empty($value['debut_service']) ? date('d/m/Y', strtotime($value['debut_service'])) : 'Non définie' ?></td>
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
                  
                  <!-- Bouton More pour l'amortissement -->
                  <button class="more-btn" title="Voir amortissement" onclick="toggleAmortissement(<?= $index ?>)">
                    <i class='bx bx-dots-vertical-rounded'></i>
                  </button>
                </div>
              </td>
            </tr>
            
            <!-- Ligne cachée pour le tableau d'amortissement -->
            <tr id="amortissement-<?= $index ?>" class="amortissement-row" style="display: none;">
              <td colspan="6">
                <div class="amortissement-container">
                  <h4>Tableau d'amortissement - <?= htmlspecialchars($value['nom_immo']) ?></h4>
                  
                  <?php
                  // Vérifier si on a les données nécessaires pour calculer l'amortissement
                  if (empty($value['duree_vie']) || empty($value['debut_service']) || empty($achat_info['prix_unitaire_immo'])) {
                  ?>
                    <div class="alert alert-warning">
                      <i class='bx bx-warning'></i>
                      Durée de vie et date début service obligatoire pour voir amortissement
                      <?php if (empty($achat_info['prix_unitaire_immo'])): ?>
                        <br>Prix unitaire manquant dans les données d'achat
                      <?php endif; ?>
                    </div>
                  <?php
                  } else {
                    // Calculer l'amortissement
                    $tableau_amortissement = calculateLinearDepreciation(
                      $achat_info['prix_unitaire_immo'],
                      $value['duree_vie'],
                      $value['debut_service']
                    );
                    
                    if (!empty($tableau_amortissement)) {
                      $taux_amortissement = 100 / $value['duree_vie'];
                  ?>
                    <div class="amortissement-info">
                      <p><strong>Base amortissable:</strong> <?= number_format($achat_info['prix_unitaire_immo'], 2, ',', ' ') ?> MGA</p>
                      <p><strong>Durée de vie:</strong> <?= $value['duree_vie'] ?> ans</p>
                      <p><strong>Taux d'amortissement:</strong> <?= number_format($taux_amortissement, 2, ',', ' ') ?>%</p>
                      <p><strong>Date de début de service:</strong> <?= date('d/m/Y', strtotime($value['debut_service'])) ?></p>
                    </div>
                    
                    <table class="amortissement-table">
                      <thead>
                        <tr>
                          <th>Année</th>
                          <th>Base amortissable</th>
                          <th>Amortissement</th>
                          <th>Amortissement cumulé</th>
                          <th>Valeur comptable</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($tableau_amortissement as $ligne): ?>
                        <tr>
                          <td><?= $ligne['annee'] ?></td>
                          <td><?= number_format($ligne['base_amortissable'], 2, ',', ' ') ?> MGA</td>
                          <td><?= number_format($ligne['amortissement'], 2, ',', ' ') ?> MGA</td>
                          <td><?= number_format($ligne['amortissement_cumule'], 2, ',', ' ') ?> MGA</td>
                          <td><?= number_format($ligne['valeur_comptable'], 2, ',', ' ') ?> MGA</td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php
                    } else {
                  ?>
                    <div class="alert alert-error">
                      Erreur dans le calcul de l'amortissement
                    </div>
                  <?php
                    }
                  }
                  ?>
                </div>
              </td>
            </tr>
        <?php
          }
        } else {
        ?>
          <tr>
            <td colspan="6" class="text-center">Aucune immobilisation trouvée</td>
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
  
  .btn:disabled {
    background-color: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
  }
  
  input[readonly], select[disabled] {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
  }
  
  .more-btn {
    background-color: transparent;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    width: 15px;
  }

  .more-btn i {
    font-size: 20px;
    vertical-align: middle;
    color: black;

  }
  
  .amortissement-row {
    background-color: #f8f9fa;
  }
  
  .amortissement-container {
    padding: 20px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    margin: 10px 0;
  }
  
  .amortissement-info {
    background-color: #e9ecef;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
  }
  
  .amortissement-info p {
    margin: 5px 0;
  }
  
  .amortissement-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  
  .amortissement-table th,
  .amortissement-table td {
    border: 1px solid #dee2e6;
    padding: 8px;
    text-align: center;
  }
  
  .amortissement-table th {
    background-color: #007bff;
    color: white;
  }
  
  .amortissement-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }
  
  .amortissement-table tbody tr:hover {
    background-color: #e9ecef;
  }
  
  .alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
  }
  
  .alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
  }
  
  .alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
  }
  
  .alert i {
    margin-right: 8px;
  }
  
  .action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 2px !important;
  }
</style>

<script>
  function HandleTypeChange() {
    var typeSelect = document.getElementById('type');
    var newTypeInput = document.getElementById('new_type');

    if (typeSelect.value === 'new') {
      newTypeInput.style.display = 'block';
      newTypeInput.required = true;
    } else {
      newTypeInput.style.display = 'none';
      newTypeInput.required = false;
    }
  }

  function toggleAmortissement(index) {
    var row = document.getElementById('amortissement-' + index);
    if (row.style.display === 'none') {
      row.style.display = 'table-row';
    } else {
      row.style.display = 'none';
    }
  }

  // Initialize the type input visibility on page load
  document.addEventListener('DOMContentLoaded', function() {
    HandleTypeChange();
  });
</script>

<?php
include('./foot.php');
?>