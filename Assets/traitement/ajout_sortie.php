<?php
session_start();
include_once('./function.php');

// Validate and sanitize input
function validateInput($input)
{
    return htmlspecialchars(trim($input));
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $id_sortie = isset($_POST['id_sortie']) ? intval($_POST['id_sortie']) : null;
    $id_article = isset($_POST['article']) ? intval($_POST['article']) : null;
    $quantite_sortie = isset($_POST['quantite_sortie']) ? intval($_POST['quantite_sortie']) : null;
    $prix_unitaire_sortie = isset($_POST['prix_unitaire_sortie']) ? floatval($_POST['prix_unitaire_sortie']) : null;
    $valeur_sortie = $quantite_sortie * $prix_unitaire_sortie;
    $raison_sortie = isset($_POST['raison_sortie']) ? validateInput($_POST['raison_sortie']) : null;
    $date_sortie = isset($_POST['date_sortie']) ? $_POST['date_sortie'] : null;

    // Validate inputs
    $errors = [];

    if (empty($id_article)) {
        $errors[] = "L'article est requis.";
    }

    if (empty($quantite_sortie) || $quantite_sortie <= 0) {
        $errors[] = "La quantité sortie est requise et doit être positive.";
    }

    if (empty($prix_unitaire_sortie) || $prix_unitaire_sortie <= 0) {
        $errors[] = "Le prix unitaire est requis et doit être positif.";
    }

    if (empty($raison_sortie)) {
        $errors[] = "La raison de sortie est requise.";
    }

    if (empty($date_sortie)) {
        $errors[] = "La date de sortie est requise.";
    }

    // Vérifier si le stock est suffisant
    $article = getArticle($id_article);
    if ($article && $quantite_sortie > $article['quantite_en_stock']) {
        $errors[] = "Stock insuffisant. Disponible: {$article['quantite_en_stock']}";
    }

    // If no errors, proceed with update or insert
    if (empty($errors)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];

            // Prepare SQL statement
            if ($id_sortie) {
                // Update existing sortie
                $sql = "UPDATE sortie 
                        SET id_article = :id_article, 
                            quantite_sortie = :quantite_sortie,
                            prix_unitaire_sortie = :prix_unitaire_sortie,
                            valeur_sortie = :valeur_sortie,
                            raison_sortie = :raison_sortie,
                            date_sortie = :date_sortie
                        WHERE id_sortie = :id_sortie";

                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_sortie', $id_sortie, PDO::PARAM_INT);
            } else {
                // Insert new sortie
                $sql = "INSERT INTO sortie (id_article, quantite_sortie, prix_unitaire_sortie, valeur_sortie, raison_sortie, date_sortie) 
                        VALUES (:id_article, :quantite_sortie, :prix_unitaire_sortie, :valeur_sortie, :raison_sortie, :date_sortie)";

                $stmt = $bdd->prepare($sql);
            }
            
            // Bind parameters for both insert and update
            $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $stmt->bindParam(':quantite_sortie', $quantite_sortie, PDO::PARAM_INT);
            $stmt->bindParam(':prix_unitaire_sortie', $prix_unitaire_sortie, PDO::PARAM_STR);
            $stmt->bindParam(':valeur_sortie', $valeur_sortie, PDO::PARAM_STR);
            $stmt->bindParam(':raison_sortie', $raison_sortie, PDO::PARAM_STR);
            $stmt->bindParam(':date_sortie', $date_sortie, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {
                // Update stock quantities
                updateStockQuantities();
                
                // Set success message
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => $id_sortie ? "La sortie a été modifiée avec succès." : "La sortie a été ajoutée avec succès."
                ];
            } else {
                // Set error message if query fails
                $_SESSION['message'] = [
                    'type' => 'warning',
                    'text' => $id_sortie ? "Erreur lors de la modification de la sortie." : "Erreur lors de l'ajout de la sortie."
                ];
            }
        } catch (PDOException $e) {
            // Handle database errors
            $_SESSION['message'] = [
                'type' => 'warning',
                'text' => "Erreur de base de données : " . $e->getMessage()
            ];
        }
    } else {
        // If there are validation errors
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => implode('<br>', $errors)
        ];
    }
} else {
    // If not a POST request
    $_SESSION['message'] = [
        'type' => 'warning',
        'text' => "Méthode de requête invalide."
    ];
}

// Redirect back to the sortie page
header("Location: ../vue/sortie.php");
exit();