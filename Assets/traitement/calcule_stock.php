<?php
function updateStockQuantities() {
    try {
        $bdd = $GLOBALS['bdd'];
        
        // Récupérer tous les articles
        $articles_query = "SELECT id_article FROM article";
        $articles_stmt = $bdd->query($articles_query);
        $articles = $articles_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($articles as $article) {
            $id_article = $article['id_article'];
            
            // Calculer la somme des achats pour cet article
            $achats_query = "SELECT COALESCE(SUM(quantite_acquis), 0) as total_achats 
                            FROM achat 
                            WHERE id_article = :id_article";
            $achats_stmt = $bdd->prepare($achats_query);
            $achats_stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $achats_stmt->execute();
            $achats_result = $achats_stmt->fetch(PDO::FETCH_ASSOC);
            $total_achats = $achats_result['total_achats'];
            
            // Calculer la somme des sorties pour cet article
            $sorties_query = "SELECT COALESCE(SUM(quantite_sortie), 0) as total_sorties 
                             FROM sortie 
                             WHERE id_article = :id_article";
            $sorties_stmt = $bdd->prepare($sorties_query);
            $sorties_stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $sorties_stmt->execute();
            $sorties_result = $sorties_stmt->fetch(PDO::FETCH_ASSOC);
            $total_sorties = $sorties_result['total_sorties'];
            
            // Calculer la quantité en stock
            $quantite_en_stock = $total_achats - $total_sorties;
            
            // Mettre à jour la quantité en stock dans la table article
            $update_query = "UPDATE article 
                            SET quantite_en_stock = :quantite_en_stock 
                            WHERE id_article = :id_article";
            $update_stmt = $bdd->prepare($update_query);
            $update_stmt->bindParam(':quantite_en_stock', $quantite_en_stock, PDO::PARAM_INT);
            $update_stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $update_stmt->execute();
        }
    } catch (PDOException $e) {
        // Journaliser l'erreur ou la gérer selon vos besoins
        error_log("Erreur lors de la mise à jour des quantités en stock: " . $e->getMessage());
    }
}
?>