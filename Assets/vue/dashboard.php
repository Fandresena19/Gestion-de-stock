<?php
include('head.php');

// Récupération des articles triés par quantité en stock (décroissant)
$sql_articles = "SELECT a.*, c.nom_categorie 
                FROM article a 
                LEFT JOIN categorie c ON a.id_categorie = c.id_categorie 
                ORDER BY a.quantite_en_stock DESC 
                LIMIT 4";

$stmt_articles = $bdd->prepare($sql_articles);
$stmt_articles->execute();

// Récupération des sorties récentes
$sql_sorties = "SELECT s.*, a.nom_article 
               FROM sortie s 
               JOIN article a ON s.id_article = a.id_article 
               ORDER BY s.date_sortie DESC 
               LIMIT 4";

$stmt_sorties = $bdd->prepare($sql_sorties);
$stmt_sorties->execute();
$sorties = $stmt_sorties->fetchAll(PDO::FETCH_ASSOC);

// Récupération des achats récents
$sql_achats = "SELECT ac.*, a.nom_article, f.nom_fournisseur 
              FROM achat ac 
              JOIN article a ON ac.id_article = a.id_article 
              JOIN fournisseur f ON ac.id_fournisseur = f.id_fournisseur 
              ORDER BY ac.date_achat DESC 
              LIMIT 5";

$stmt_achats = $bdd->prepare($sql_achats);
$stmt_achats->execute();
?>

<div class="home-content">
  <div class="summary-cards">
    <?php while ($article = $stmt_articles->fetch(PDO::FETCH_ASSOC)): ?>
      <div class="card">
        <div class="card-info">
          <div class="card-title"><?= htmlspecialchars($article['nom_article']) ?></div>
          <div class="card-value"><?= number_format($article['quantite_en_stock']) ?></div>
          <div class="card-badge">
            <i class="bx bx-package"></i>
            <span class="badge-text"><?= htmlspecialchars($article['nom_categorie']) ?></span>
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
          <li class="column-header">Date</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= date('d M Y', strtotime($sortie['date_sortie'])) ?></a></li>
          <?php endforeach; ?>
        </ul>

        <ul class="data-column">
          <li class="column-header">Article</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= htmlspecialchars($sortie['nom_article']) ?></a></li>
          <?php endforeach; ?>
        </ul>

        <ul class="data-column">
          <li class="column-header">Quantité</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= number_format($sortie['quantite_sortie']) ?></a></li>
          <?php endforeach; ?>
        </ul>

        <ul class="data-column">
          <li class="column-header">Raison</li>
          <?php foreach ($sorties as $sortie): ?>
            <li><a href="#"><?= htmlspecialchars(substr($sortie['raison_sortie'], 0, 15)) ?></a></li>
          <?php endforeach; ?>
        </ul>

      </div>
      <div class="action-btn">
        <a href="./sortie.php">Voir Tout</a>
      </div>
    </div>
    <div class="input-history card-container">
      <div class="section-heading">Entrées récentes</div>
      <ul class="input-list">
        <?php while ($achat = $stmt_achats->fetch(PDO::FETCH_ASSOC)): ?>
          <li>
            <a href="#">
              <span class="item-name"><?= htmlspecialchars($achat['nom_article']) ?></span>
            </a>
            <span class="item-quantity">
              <?= number_format($achat['quantite_achete']) ?> unités
            </span>
          </li>
        <?php endwhile; ?>
      </ul><br>

      <div class="action-btn">
        <a href="./achat">Voir Détail</a>
      </div>
    </div>
  </div>
</div>

<?php
// Fermeture des curseurs et connexion
$stmt_articles = null;
$stmt_sorties = null;
$stmt_achats = null;
$bdd = null;

include('foot.php');
?>