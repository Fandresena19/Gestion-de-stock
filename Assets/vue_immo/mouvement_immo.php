<?php
include('./head.php');

// Définir les dates par défaut pour le filtre
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01'); // Premier jour du mois
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-t'); // Dernier jour du mois
?>

<div class="home-content">
  <h3>Mouvement Immobilisation</h3>
  <div class="overview-boxes">
    <div class="box" style="font-size: 12px;">
      <h2>Filtrage par date</h2>

      <!-- Formulaire de filtre par date -->
      <form method="GET" action="" class="filter-form" style="margin-bottom: 20px;">
        <div>
          <div>
            <label for="date_debut">Date début:</label>
            <input type="date" id="date_debut" name="date_debut" value="<?= $date_debut ?>">
          </div>
          <div>
            <label for="date_fin">Date fin:</label>
            <input type="date" id="date_fin" name="date_fin" value="<?= $date_fin ?>">
          </div>
          <button type="submit" class="btn btn-primary">Filtrer</button>
        </div>
      </form>
    </div>


    <div class="box">
      <table class="mtable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Immobilisation</th>
            <th>Prix unitaire</th>
            <th>Fournisseur/Raison</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Fonction pour récupérer les mouvements d'immobilisations
          function getMouvementsImmo($date_debut, $date_fin)
          {
            global $bdd;

            // Récupérer les achats d'immobilisations
            $sql_achats_immo = "SELECT ai.date_achat_immo AS date, 'Entrée' AS type, 
                                   immo.nom_immo AS article, 
                                   ai.prix_unitaire_immo AS prix_unitaire,
                                   f.nom_fournisseur AS details
                            FROM achat_immo ai
                            JOIN immobilisation immo ON ai.id_immo = immo.id_immo
                            JOIN fournisseur f ON ai.id_fournisseur = f.id_fournisseur
                            WHERE ai.date_achat_immo BETWEEN :date_debut AND :date_fin";

            $req_achats_immo = $bdd->prepare($sql_achats_immo);
            $req_achats_immo->execute([
              ':date_debut' => $date_debut,
              ':date_fin' => $date_fin
            ]);
            $achats_immo = $req_achats_immo->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les sorties d'immobilisations
            $sql_sorties_immo = "SELECT si.date_sortie_immo AS date, 'Sortie' AS type, 
                                    si.prix_unitaire_immo AS prix_unitaire,
                                    si.raison_sortie_immo AS details,
                                    si.nom_immo AS article
                            FROM sortie_immo si
                            WHERE si.date_sortie_immo BETWEEN :date_debut AND :date_fin";

            $req_sorties_immo = $bdd->prepare($sql_sorties_immo);
            $req_sorties_immo->execute([
              ':date_debut' => $date_debut,
              ':date_fin' => $date_fin
            ]);
            $sorties_immo = $req_sorties_immo->fetchAll(PDO::FETCH_ASSOC);

            // Combiner les deux résultats
            $mouvements = array_merge($achats_immo, $sorties_immo);

            // Trier par date (du plus récent au plus ancien)
            usort($mouvements, function ($a, $b) {
              return strtotime($b['date']) - strtotime($a['date']);
            });

            return $mouvements;
          }

          // Récupérer les mouvements
          $mouvements = getMouvementsImmo($date_debut, $date_fin);

          // Afficher les mouvements
          if (!empty($mouvements)) {
            foreach ($mouvements as $mouvement) {
              $color_class = $mouvement['type'] == 'Entrée' ? 'text-success' : 'text-danger';
          ?>
              <tr>
                <td data-label="Date"><?= date('d/m/Y', strtotime($mouvement['date'])) ?></td>
                <td class="<?= $color_class ?>" data-label="Type"><?= htmlspecialchars($mouvement['type']) ?></td>
                <td><?= htmlspecialchars($mouvement['article']) ?></td>
                <td data-label="Prix"><?= $mouvement['prix_unitaire'] ?> MGA</td>
                <td data-label="Detail"><?= htmlspecialchars($mouvement['details']) ?></td>
              </tr>
            <?php
            }
          } else {
            ?>
            <tr>
              <td colspan="7" class="text-center">Aucun mouvement d'immobilisation trouvé pour cette période</td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
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
  }
</style>

<?php
include('./foot.php');
?>