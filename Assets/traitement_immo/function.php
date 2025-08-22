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
    $sql = "SELECT i.*, c.nom_categorie_immo
        FROM immobilisation i 
        JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo 
        WHERE i.id_immo = ?";

    $req = $GLOBALS['bdd']->prepare($sql);
    $req->execute([$id]);

    return $req->fetch(PDO::FETCH_ASSOC);
  } else {
    $sql = "SELECT i.*, c.nom_categorie_immo
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
    
    // Add type_immo filter
    if (!empty($searchParams['type_immo'])) {
      $conditions[] = "i.type_immo = ?";
      $params[] = $searchParams['type_immo'];
    }
    
    // Add WHERE clause if there are conditions
    if (!empty($conditions)) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY i.nom_immo ASC";

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
      $sql = "SELECT s.*, 
              CASE 
                WHEN i.nom_immo IS NOT NULL THEN i.nom_immo 
                ELSE CONCAT('Immobilisation supprimée (ID: ', s.id_immo, ')')
              END as nom_immo,
              i.id_immo as immo_exists
              FROM sortie_immo s
              LEFT JOIN immobilisation i ON s.id_immo = i.id_immo
              WHERE s.id_sortie_immo = ? 
              ORDER BY s.date_sortie_immo ASC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  }else{
      $sql = "SELECT s.*, 
              CASE 
                WHEN i.nom_immo IS NOT NULL THEN i.nom_immo 
                ELSE CONCAT('Immobilisation supprimée (ID: ', s.id_immo, ')')
              END as nom_immo,
              i.id_immo as immo_exists
              FROM sortie_immo s
              LEFT JOIN immobilisation i ON s.id_immo = i.id_immo
              ORDER BY s.date_sortie_immo DESC";
      
      $req = $GLOBALS['bdd'] -> prepare($sql);
      $req->execute();
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

function getAchatImmo($id = null)
{
  if(!empty($id)){
      $sql = "SELECT a.*, 
              CASE 
                WHEN i.nom_immo IS NOT NULL THEN i.nom_immo 
                ELSE CONCAT('Immobilisation supprimée (ID: ', a.id_immo, ')')
              END as nom_immo,
              CASE 
                WHEN i.type_immo IS NOT NULL THEN i.type_immo 
                ELSE 'Type inconnu'
              END as type_immo,
              CASE 
                WHEN c.nom_categorie_immo IS NOT NULL THEN c.nom_categorie_immo 
                ELSE 'Catégorie inconnue'
              END as nom_categorie_immo,
              f.nom_fournisseur,
              i.id_immo as immo_exists,
              i.description_immo,
              i.duree_vie,
              i.debut_service
              FROM achat_immo a 
              LEFT JOIN immobilisation i ON a.id_immo = i.id_immo 
              LEFT JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo
              LEFT JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
              WHERE a.id_achat_immo = ? 
              ORDER BY a.date_achat_immo ASC";
      
      $req = $GLOBALS['bdd']->prepare($sql);
      $req->execute([$id]);
      
      return $req->fetch(PDO::FETCH_ASSOC);
  
  } else {
      $sql = "SELECT a.*, 
              CASE 
                WHEN i.nom_immo IS NOT NULL THEN i.nom_immo 
                ELSE CONCAT('Immobilisation supprimée (ID: ', a.id_immo, ')')
              END as nom_immo,
              CASE 
                WHEN i.type_immo IS NOT NULL THEN i.type_immo 
                ELSE 'Type inconnu'
              END as type_immo,
              CASE 
                WHEN c.nom_categorie_immo IS NOT NULL THEN c.nom_categorie_immo 
                ELSE 'Catégorie inconnue'
              END as nom_categorie_immo,
              f.nom_fournisseur,
              i.id_immo as immo_exists
              FROM achat_immo a 
              LEFT JOIN immobilisation i ON a.id_immo = i.id_immo 
              LEFT JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo
              LEFT JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
              ORDER BY a.date_achat_immo DESC";
      
      $req = $GLOBALS['bdd']->prepare($sql);
      $req->execute();
      
      return $req->fetchAll(PDO::FETCH_ASSOC);
  }
}

/**
 * Fonction pour supprimer une immobilisation de l'inventaire
 * @param int $id_immo L'ID de l'immobilisation à supprimer
 * @return bool True si la suppression a réussi, false sinon
 */
function supprimerImmobilisation($id_immo) 
{
    try {
        $sql = "DELETE FROM immobilisation WHERE id_immo = ?";
        $req = $GLOBALS['bdd']->prepare($sql);
        return $req->execute([$id_immo]);
    } catch (Exception $e) {
        error_log("Erreur lors de la suppression de l'immobilisation : " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour vérifier si une immobilisation existe
 * @param int $id_immo L'ID de l'immobilisation à vérifier
 * @return bool True si l'immobilisation existe, false sinon
 */
function immobilisationExiste($id_immo)
{
    try {
        $sql = "SELECT COUNT(*) FROM immobilisation WHERE id_immo = ?";
        $req = $GLOBALS['bdd']->prepare($sql);
        $req->execute([$id_immo]);
        return $req->fetchColumn() > 0;
    } catch (Exception $e) {
        error_log("Erreur lors de la vérification de l'immobilisation : " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour obtenir les informations d'une immobilisation par son ID
 * @param int $id_immo L'ID de l'immobilisation
 * @return array|false Les informations de l'immobilisation ou false si non trouvée
 */
function getImmobilisationById($id_immo)
{
    try {
        $sql = "SELECT i.*, c.nom_categorie_immo 
                FROM immobilisation i 
                JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo 
                WHERE i.id_immo = ?";
        $req = $GLOBALS['bdd']->prepare($sql);
        $req->execute([$id_immo]);
        return $req->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération de l'immobilisation : " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour supprimer un achat d'immobilisation et optionnellement l'immobilisation associée
 * @param int $id_achat_immo L'ID de l'achat à supprimer
 * @param bool $supprimer_immo Si true, supprime aussi l'immobilisation associée
 * @return bool True si la suppression a réussi, false sinon
 */
function supprimerAchatImmo($id_achat_immo, $supprimer_immo = false)
{
    try {
        $bdd = $GLOBALS['bdd'];
        $bdd->beginTransaction();

        // Récupérer l'ID de l'immobilisation avant de supprimer l'achat
        $achat = getAchatImmo($id_achat_immo);
        if (!$achat) {
            throw new Exception("Achat d'immobilisation non trouvé.");
        }
        
        $id_immo = $achat['id_immo'];

        // Supprimer l'achat
        $sql = "DELETE FROM achat_immo WHERE id_achat_immo = ?";
        $req = $bdd->prepare($sql);
        if (!$req->execute([$id_achat_immo])) {
            throw new Exception("Erreur lors de la suppression de l'achat.");
        }

        // Supprimer l'immobilisation si demandé
        if ($supprimer_immo) {
            $sql_immo = "DELETE FROM immobilisation WHERE id_immo = ?";
            $req_immo = $bdd->prepare($sql_immo);
            if (!$req_immo->execute([$id_immo])) {
                throw new Exception("Erreur lors de la suppression de l'immobilisation.");
            }
        }

        $bdd->commit();
        return true;
    } catch (Exception $e) {
        if ($bdd->inTransaction()) {
            $bdd->rollback();
        }
        error_log("Erreur lors de la suppression de l'achat d'immobilisation : " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour obtenir toutes les immobilisations disponibles (non sorties)
 * @return array Liste des immobilisations disponibles
 */
function getImmobilisationsDisponibles()
{
    try {
        $sql = "SELECT i.*, c.nom_categorie_immo
                FROM immobilisation i 
                JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo
                WHERE i.id_immo NOT IN (
                    SELECT DISTINCT id_immo FROM sortie_immo WHERE id_immo IS NOT NULL
                )
                ORDER BY i.nom_immo ASC";
        
        $req = $GLOBALS['bdd']->prepare($sql);
        $req->execute();
        
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des immobilisations disponibles : " . $e->getMessage());
        return [];
    }
}

/**
 * Fonction pour obtenir toutes les immobilisations disponibles pour les sorties
 * Inclut seulement les immobilisations qui existent encore
 * @return array Liste des immobilisations disponibles
 */
function getImmobilisationsForSortie()
{
    try {
        $sql = "SELECT i.*, c.nom_categorie_immo
                FROM immobilisation i 
                JOIN categorie_immo c ON i.id_categorie_immo = c.id_categorie_immo
                WHERE i.id_immo NOT IN (
                    SELECT DISTINCT id_immo FROM sortie_immo WHERE id_immo IS NOT NULL
                )
                ORDER BY i.nom_immo ASC";
        
        $req = $GLOBALS['bdd']->prepare($sql);
        $req->execute();
        
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des immobilisations pour sortie : " . $e->getMessage());
        return [];
    }
}
?>