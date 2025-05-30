<?php
include('head.php');

//REcuperation des immobilisations trié par quantité en stock (décroissant)
$sql_immo = "SELECT i.*, c.nom_categorie_immo
            FROM immobilisation i
            LEFT JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo
            ORDER BY i.quantite_en_stock_immo DESC
            LIMIT 4";

$stmt_immo = $bdd->prepare($sql_immo);
$stmt_immo->execute();

//Récuperation des sorties récentes
$sql_sorties = "SELECT s.*, i.nom_immo
                FROM sortie_immo s
                JOIN immobilisation i ON s.id_immo = i.id_immo
                ORDER BY s.date_sortie_immo DESC
                LIMIT 4";

$stmt_sorties = $bdd->prepare($sql_sorties);
$stmt_sorties->execute();
$sorties = $stmt_sorties->fetchAll(PDO::FETCH_ASSOC);

//Recuperation des achats récents
$sql_achats = "SELECT a.*, i.nom_immo, f.nom_fournisseur
               FROM achat_immo a
               JOIN immobilisation i ON a.id_immo = i.id_immo
               JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
               ORDER BY a.date_achat_immo DESC
               LIMIT 5";

$stmt_achats = $bdd->prepare($sql_achats);
$stmt_achats->execute();
?>

<div class="home-content">
  <div class="summary-cards">
    <?php while ($immo = $stmt_immo -> fetch(PDO::FETCH_ASSOC)): ?>
      <div class="card">
        <div class="card-info">
          <div class="card-title"><?= htmlspecialchars($immo['nom_immo'])?></div>
          <div class="card-value"><?= htmlspecialchars($immo['quantite_en_stock_immo'])?></div>
          <div class="card-badge">
            <i class="bx bx-package"></i>
            <span class="badge-text"><?= htmlspecialchars($immo['nom_categorie_immo'])?></span>
          </div>
        </div>
        <i class="bx bx-box inventory-icon"></i>
      </div>
      <?php endwhile; ?>
  </div>

  <div class="data-containers">
    <div class="output-history card-container">
      <div class="section-heading">Sorties récentes</div>
      <div class="data-grid">

        <ul class="data-column">
          <div class="column-headeer">
            <li class="column-header">Date</li>
            <?php foreach($sorties as $sortie) :?>
              <li><a href="#"><?= date('d M Y', strtotime($sortie['date_sortie_immo']))?></a></li>
            <?php endforeach;?>
          </div>
        </ul>

        <ul class="data-column">
          <li class="column-header">Immobilisation</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= htmlspecialchars($sortie['nom_immo']) ?></a></li>
          <?php endforeach; ?>
        </ul>

        <ul class="data-column">
          <li class="column-header">Quantité</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= htmlspecialchars($sortie['quantite_sortie_immo']) ?></a></li>
          <?php endforeach; ?>
        </ul>

        <ul class="data-column">
          <li class="column-header">Raison</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= htmlspecialchars($sortie['raison_sortie_immo']) ?></a></li>
          <?php endforeach; ?>
        </ul>

      </div>
      <div class="action-btn">
        <a href="./sortie_immo.php">Voir Tout</a>
      </div>
    </div>


    <div class="input-history card-container">
      <div class="section-heading">Entrées récents</div>
      <ul class="input-list">
        <?php while ($achat = $stmt_achats->fetch(PDO::FETCH_ASSOC)): ?>
          <li>
            <a href="#">
              <span class="item-name"><?= htmlspecialchars($achat['nom_immo']) ?></span>
            </a>
            <span class="item-quantity">
              <?= number_format($achat['quantite_achete_immo'])?> unités
            </span>
          </li>
          <?php endwhile; ?>
      </ul><br>

      <div class="action-btn">
        <a href="./achat_immo.php">Voir détail</a>
      </div>
    </div>
  </div>

</div>

<?php
include('foot.php')
?>