<?php
include('db.php');

function getArticle($id = null, $searchDATA = [])
{
  if (!empty($id)) {
    $sql = "SELECT a.*, c.*
        FROM article a 
        JOIN categorie c ON a.id_categorie = c.id_categorie 
        WHERE id_article = ?";

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id]);

    return $req->fetch(PDO::FETCH_ASSOC);

  } else {
    $sql = "SELECT a.*, c.*
        FROM article a 
        JOIN categorie c ON a.id_categorie = c.id_categorie";
    
    $params = [];
    $whereConditions = [];
    
    // Add search conditions
    if (!empty($searchDATA)) {
      if (!empty($searchDATA['nom_article'])) {
        $whereConditions[] = "a.nom_article LIKE ?";
        $params[] = '%' . $searchDATA['nom_article'] . '%';
      }
      
      if (!empty($searchDATA['id_categorie'])) {
        $whereConditions[] = "a.id_categorie = ?";
        $params[] = $searchDATA['id_categorie'];
      }
      
      // For stock filtering by quantity
      if (isset($searchDATA['quantite_min'])) {
        $whereConditions[] = "a.quantite_en_stock >= ?";
        $params[] = $searchDATA['quantite_min'];
      }
      
      if (isset($searchDATA['quantite_max'])) {
        $whereConditions[] = "a.quantite_en_stock <= ?";
        $params[] = $searchDATA['quantite_max'];
      }
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($whereConditions)) {
      $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add sorting if specified
    if (!empty($searchDATA['sort_by'])) {
      $sort_column = $searchDATA['sort_by'];
      $sort_dir = !empty($searchDATA['sort_dir']) ? $searchDATA['sort_dir'] : 'ASC';
      
      // Whitelist columns to prevent SQL injection
      $allowed_columns = ['nom_article', 'nom_categorie', 'quantite_en_stock'];
      $allowed_directions = ['ASC', 'DESC'];
      
      if (in_array($sort_column, $allowed_columns) && in_array($sort_dir, $allowed_directions)) {
        $sql .= " ORDER BY " . ($sort_column == 'nom_categorie' ? 'c.nom_categorie' : 'a.' . $sort_column) . " " . $sort_dir;
      }
    }

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute($params);

    return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

function getAllCategories()
{
  $sql = "SELECT * FROM categorie";

  $req = $GLOBALS['bdd']->prepare($sql);
  $req->execute();

  return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getSortie($id=null, $searchData = []){
  if(!empty($id)){
      $sql = "SELECT s.*, a.nom_article
              FROM sortie s 
              JOIN article a ON s.id_article = a.id_article 
              WHERE s.id_sortie = ? ORDER BY s.date_sortie ASC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  }else{
      $sql = "SELECT s.*, a.nom_article
              FROM sortie s 
              JOIN article a ON s.id_article = a.id_article";
      
      $params = [];
      $whereConditions = [];
      
      // Add search filters
      if (!empty($searchData)) {
        if (!empty($searchData['date_debut'])) {
          $whereConditions[] = "s.date_sortie >= ?";
          $params[] = $searchData['date_debut'];
        }
        
        if (!empty($searchData['date_fin'])) {
          $whereConditions[] = "s.date_sortie <= ?";
          $params[] = $searchData['date_fin'];
        }
        
        if (!empty($searchData['id_article'])) {
          $whereConditions[] = "s.id_article = ?";
          $params[] = $searchData['id_article'];
        }
        
        if (!empty($searchData['raison_sortie'])) {
          $whereConditions[] = "s.raison_sortie LIKE ?";
          $params[] = '%' . $searchData['raison_sortie'] . '%';
        }
      }
      
      // Add WHERE clause if conditions exist
      if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
      }
      
      // Add ORDER BY
      $sql .= " ORDER BY s.date_sortie DESC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute($params);
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

// Modification for getFournisseur to support search
function getFournisseur($id = null, $searchData = [])
{
  if (!empty($id)) {
    $sql = "SELECT * FROM fournisseur WHERE id_fournisseur = ?";

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id]);

    return $req->fetch(PDO::FETCH_ASSOC);
  } else {
    $sql = "SELECT * FROM fournisseur";
    
    $params = [];
    $whereConditions = [];
    
    if (!empty($searchData)) {
      if (!empty($searchData['nom_fournisseur'])) {
        $whereConditions[] = "nom_fournisseur LIKE ?";
        $params[] = '%' . $searchData['nom_fournisseur'] . '%';
      }
    }
    
    if (!empty($whereConditions)) {
      $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute($params);

    return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

// Modification for getAchat to support search
function getAchat($id = null, $searchData = [])
{
  if(!empty($id)){
      $sql = "SELECT a.*, ar.*, f.*
              FROM achat a 
              JOIN article ar ON a.id_article = ar.id_article 
              JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
              WHERE a.id_achat = ? 
              ORDER BY a.date_achat ASC";
      
      $req = $GLOBALS['bdd']->prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  } else {
      $sql = "SELECT a.*, ar.*, f.*
              FROM achat a 
              JOIN article ar ON a.id_article = ar.id_article 
              JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur";
      
      $params = [];
      $whereConditions = [];
      
      // Add search filters
      if (!empty($searchData)) {
        if (!empty($searchData['date_debut'])) {
          $whereConditions[] = "a.date_achat >= ?";
          $params[] = $searchData['date_debut'];
        }
        
        if (!empty($searchData['date_fin'])) {
          $whereConditions[] = "a.date_achat <= ?";
          $params[] = $searchData['date_fin'];
        }
        
        if (!empty($searchData['id_article'])) {
          $whereConditions[] = "a.id_article = ?";
          $params[] = $searchData['id_article'];
        }
        
        if (!empty($searchData['id_fournisseur'])) {
          $whereConditions[] = "a.id_fournisseur = ?";
          $params[] = $searchData['id_fournisseur'];
        }
      }
      
      // Add WHERE clause if conditions exist
      if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
      }
      
      // Add ORDER BY
      $sql .= " ORDER BY a.date_achat DESC";
      
      $req = $GLOBALS['bdd']->prepare($sql);
      $req->execute($params);
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

// Enhanced getMouvementsStock function for use in mouvement_stock.php
function getMouvementsStock($date_debut, $date_fin, $searchData = [])
{
  global $bdd;
  
  // Build WHERE conditions for both queries
  $whereClauses = ["a.date_achat BETWEEN :date_debut AND :date_fin"];
  $sortieWhereClauses = ["s.date_sortie BETWEEN :date_debut AND :date_fin"];
  $params = [
    ':date_debut' => $date_debut,
    ':date_fin' => $date_fin
  ];
  
  // Add additional search filters
  if (!empty($searchData)) {
    // Filter by article
    if (!empty($searchData['id_article'])) {
      $whereClauses[] = "a.id_article = :id_article";
      $sortieWhereClauses[] = "s.id_article = :id_article";
      $params[':id_article'] = $searchData['id_article'];
    }
    
    // Filter by fournisseur (for achats only)
    if (!empty($searchData['id_fournisseur'])) {
      $whereClauses[] = "a.id_fournisseur = :id_fournisseur";
      $params[':id_fournisseur'] = $searchData['id_fournisseur'];
    }
    
    // Filter by raison de sortie (for sorties only)
    if (!empty($searchData['raison_sortie'])) {
      $sortieWhereClauses[] = "s.raison_sortie LIKE :raison_sortie";
      $params[':raison_sortie'] = '%' . $searchData['raison_sortie'] . '%';
    }
    
    // Filter by type (entrée ou sortie)
    if (!empty($searchData['type'])) {
      // This will be handled after fetching data, but we prepare the filter flag
      $filterByType = $searchData['type'];
    }
  }

  // Récupérer les achats
  $sql_achats = "SELECT a.date_achat AS date, 'Entrée' AS type, 
                        art.nom_article AS article, a.quantite_achete AS quantite, 
                        a.prix_unitaire_achat AS prix_unitaire, a.valeur_achat AS valeur, 
                        f.nom_fournisseur AS details,
                        art.id_article, f.id_fournisseur
                FROM achat a
                JOIN article art ON a.id_article = art.id_article
                JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
                WHERE " . implode(" AND ", $whereClauses);

  $req_achats = $bdd->prepare($sql_achats);
  $req_achats->execute($params);
  $achats = $req_achats->fetchAll(PDO::FETCH_ASSOC);

  // Récupérer les sorties
  $sql_sorties = "SELECT s.date_sortie AS date, 'Sortie' AS type, 
                        art.nom_article AS article, s.quantite_sortie AS quantite,
                        s.prix_unitaire_sortie AS prix_unitaire, s.valeur_sortie AS valeur,
                        s.raison_sortie AS details,
                        art.id_article, NULL as id_fournisseur
                FROM sortie s
                JOIN article art ON s.id_article = art.id_article
                WHERE " . implode(" AND ", $sortieWhereClauses);

  $req_sorties = $bdd->prepare($sql_sorties);
  $req_sorties->execute($params);
  $sorties = $req_sorties->fetchAll(PDO::FETCH_ASSOC);

  // Combiner les deux résultats
  $mouvements = array_merge($achats, $sorties);

  // Filter by type if specified
  if (!empty($searchData['type'])) {
    $mouvements = array_filter($mouvements, function($mouvement) use ($searchData) {
      return $mouvement['type'] == $searchData['type'];
    });
  }

  // Trier par date (du plus récent au plus ancien)
  usort($mouvements, function ($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
  });

  return $mouvements;
}

function getAveragePurchasePrice($id_article) {
    $sql = "SELECT COALESCE(AVG(prix_unitaire_achat), 0) as avg_price 
            FROM achat 
            WHERE id_article = ?";
            
    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id_article]);
    
    return $req->fetch(PDO::FETCH_ASSOC)['avg_price'];
}

function updateStockQuantities() 
{
    // 1. Récupérer tous les articles
    $articles = getArticle();
    
    if (!empty($articles) && is_array($articles)) {
        foreach ($articles as $article) {
            $id_article = $article['id_article'];
            
            // 2. Calculer le total des achats pour cet article
            $sql_achats = "SELECT COALESCE(SUM(quantite_achete), 0) as total_achats 
                          FROM achat WHERE id_article = ?";
            $req_achats = $GLOBALS['bdd']->prepare($sql_achats);
            $req_achats->execute([$id_article]);
            $total_achats = $req_achats->fetch(PDO::FETCH_ASSOC)['total_achats'] ?? 0;
            
            // 3. Calculer le total des sorties pour cet article
            $sql_sorties = "SELECT COALESCE(SUM(quantite_sortie), 0) as total_sorties 
                           FROM sortie WHERE id_article = ?";
            $req_sorties = $GLOBALS['bdd']->prepare($sql_sorties);
            $req_sorties->execute([$id_article]);
            $total_sorties = $req_sorties->fetch(PDO::FETCH_ASSOC)['total_sorties'] ?? 0;
            
            // 4. Calculer la quantité en stock (achats - sorties)
            $quantite_en_stock = $total_achats - $total_sorties;
            
            // 5. Mettre à jour la quantité en stock dans la table article
            $sql_update = "UPDATE article SET quantite_en_stock = ? WHERE id_article = ?";
            $req_update = $GLOBALS['bdd']->prepare($sql_update);
            $req_update->execute([$quantite_en_stock, $id_article]);
        }
        return true;
    }
    return false;
}