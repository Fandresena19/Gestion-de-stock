<?php
include('db.php');
function getAllCategories()
{
  $sql = "SELECT * FROM categorie_immo";

  $req = $GLOBALS['bdd']->prepare($sql);
  $req->execute();

  return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getSortie($id=null){
  if(!empty($id)){
      $sql = "SELECT s.*, a.nom_article
              FROM sortie s 
              JOIN article a ON s.id_article = a.id_article 
              WHERE s.id_sortie = ? ORDER BY s.date_sorte ASC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  }else{
      $sql = "SELECT s.*, a.nom_article
              FROM sortie s 
              JOIN article a ON s.id_article = a.id_article";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute();
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

function getFournisseur($id = null)
{
  if (!empty($id)) {
    $sql = "SELECT * FROM fournisseur WHERE id_fournisseur = ?";

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id]);

    return $req->fetch(PDO::FETCH_ASSOC);
  } else {
    $sql = "SELECT * FROM fournisseur";

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute();

    return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

// Fonctions pour la gestion des immobilisations

function getImmobilisation($id = null, $searchParams = [])
{
  if (!empty($id)) {
    $sql = "SELECT i.*, c.*
        FROM immobilisation i 
        JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo 
        WHERE id_immo = ?";

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id]);

    return $req->fetch(PDO::FETCH_ASSOC);
  } else {
    $sql = "SELECT i.*, c.*
        FROM immobilisation i 
        JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo";
    
    $params = [];
    $conditions = [];
    
    // Add search conditions
    if (!empty($searchParams['nom_immo'])) {
      $conditions[] = "i.nom_immo LIKE ?";
      $params[] = '%' . $searchParams['nom_immo'] . '%';
    }
    
    if (!empty($searchParams['id_categorie_immo'])) {
      $conditions[] = "i.id_categorie_immo = ?";
      $params[] = $searchParams['id_categorie_immo'];
    }
    
    // Add WHERE clause if there are conditions
    if (!empty($conditions)) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute($params);

    return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

function getAllCategoriesImmo()
{
  $sql = "SELECT * FROM categorie_immo";

  $req = $GLOBALS['bdd']->prepare($sql);
  $req->execute();

  return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getSortieImmo($id=null){
  if(!empty($id)){
      $sql = "SELECT s.*, i.nom_immo
              FROM sortie_immo s 
              JOIN immobilisation i ON s.id_immo = i.id_immo 
              WHERE s.id_sortie_immo = ? ORDER BY s.date_sortie_immo ASC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  }else{
      $sql = "SELECT s.*, i.nom_immo
              FROM sortie_immo s 
              JOIN immobilisation i ON s.id_immo = i.id_immo
              ORDER BY s.date_sortie_immo DESC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute();
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

function getAchatImmo($id = null)
{
  if(!empty($id)){
      $sql = "SELECT a.*, i.*, f.*
              FROM achat_immo a 
              JOIN immobilisation i ON a.id_immo = i.id_immo 
              JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
              WHERE a.id_achat_immo = ? 
              ORDER BY a.date_achat_immo ASC";
      
      $req = $GLOBALS['bdd']->prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  } else {
      $sql = "SELECT a.*, i.*, f.*
              FROM achat_immo a 
              JOIN immobilisation i ON a.id_immo = i.id_immo 
              JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
              ORDER BY a.date_achat_immo DESC";
      
      $req = $GLOBALS['bdd']->prepare($sql);
      $req->execute();
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

function getAveragePurchasePriceImmo($id_immo) {
    $sql = "SELECT COALESCE(AVG(prix_unitaire_immo), 0) as avg_price 
            FROM achat_immo 
            WHERE id_immo = ?";
            
    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id_immo]);
    
    return $req->fetch(PDO::FETCH_ASSOC)['avg_price'];
}

function updateStockQuantitiesImmo() 
{
    // 1. Récupérer toutes les immobilisations
    $immobilisations = getImmobilisation();
    
    if (!empty($immobilisations) && is_array($immobilisations)) {
        foreach ($immobilisations as $immobilisation) {
            $id_immo = $immobilisation['id_immo'];
            
            // 2. Calculer le total des achats pour cette immobilisation
            $sql_achats = "SELECT COALESCE(SUM(quantite_achete_immo), 0) as total_achats 
                          FROM achat_immo WHERE id_immo = ?";
            $req_achats = $GLOBALS['bdd']->prepare($sql_achats);
            $req_achats->execute([$id_immo]);
            $total_achats = $req_achats->fetch(PDO::FETCH_ASSOC)['total_achats'] ?? 0;
            
            // 3. Calculer le total des sorties pour cette immobilisation
            $sql_sorties = "SELECT COALESCE(SUM(quantite_sortie_immo), 0) as total_sorties 
                           FROM sortie_immo WHERE id_immo = ?";
            $req_sorties = $GLOBALS['bdd']->prepare($sql_sorties);
            $req_sorties->execute([$id_immo]);
            $total_sorties = $req_sorties->fetch(PDO::FETCH_ASSOC)['total_sorties'] ?? 0;
            
            // 4. Calculer la quantité en stock (achats - sorties)
            $quantite_en_stock = $total_achats - $total_sorties;
            
            // 5. Mettre à jour la quantité en stock dans la table immobilisation
            $sql_update = "UPDATE immobilisation SET quantite_en_stock_immo = ? WHERE id_immo = ?";
            $req_update = $GLOBALS['bdd']->prepare($sql_update);
            $req_update->execute([$quantite_en_stock, $id_immo]);
        }
        return true;
    }
    return false;
}