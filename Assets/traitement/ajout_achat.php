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
    $id_achat = isset($_POST['id_achat']) ? intval($_POST['id_achat']) : null;
    $id_article = isset($_POST['article']) ? intval($_POST['article']) : null;
    $id_fournisseur = isset($_POST['fournisseur']) ? intval($_POST['fournisseur']) : null;
    $quantite_acquis = isset($_POST['quantite_acquis']) ? intval($_POST['quantite_acquis']) : null;
    $prix_unitaire_achat = isset($_POST['prix_unitaire_achat']) ? floatval($_POST['prix_unitaire_achat']) : null;
    $valeur_achat = $quantite_acquis * $prix_unitaire_achat;
    $numero_facture = isset($_POST['numero_facture']) ? validateInput($_POST['numero_facture']) : null;
    $date_achat = isset($_POST['date_achat']) ? $_POST['date_achat'] : null;

    // Validate inputs
    $errors = [];

    if (empty($id_article)) {
        $errors[] = "L'article est requis.";
    }

    if (empty($id_fournisseur)) {
        $errors[] = "Le fournisseur est requis.";
    }

    if (empty($quantite_acquis) || $quantite_acquis <= 0) {
        $errors[] = "La quantité acquise est requise et doit être positive.";
    }
    
    if (empty($prix_unitaire_achat) || $prix_unitaire_achat <= 0) {
        $errors[] = "Le prix unitaire est requis et doit être positif.";
    }

    if (empty($numero_facture)) {
        $errors[] = "Le numéro de facture est requis.";
    }

    if (empty($date_achat)) {
        $errors[] = "La date d'achat est requise.";
    }

    // If no errors, proceed with update or insert
    if (empty($errors)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];

            // Prepare SQL statement
            if ($id_achat) {
                // Update existing achat
                $sql = "UPDATE achat 
                        SET id_article = :id_article, 
                            id_fournisseur = :id_fournisseur,
                            quantite_achete = :quantite_acquis,
                            prix_unitaire_achat = :prix_unitaire_achat,
                            valeur_achat = :valeur_achat,
                            facture = :numero_facture, 
                            date_achat = :date_achat
                        WHERE id_achat = :id_achat";

                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_achat', $id_achat, PDO::PARAM_INT);
            } else {
                // Insert new achat
                $sql = "INSERT INTO achat (id_article, id_fournisseur, quantite_achete, prix_unitaire_achat, valeur_achat, facture, date_achat) 
                        VALUES (:id_article, :id_fournisseur, :quantite_acquis, :prix_unitaire_achat, :valeur_achat, :numero_facture, :date_achat)";

                $stmt = $bdd->prepare($sql);
            }
            
            // Bind parameters for both insert and update
            $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $stmt->bindParam(':id_fournisseur', $id_fournisseur, PDO::PARAM_INT);
            $stmt->bindParam(':quantite_acquis', $quantite_acquis, PDO::PARAM_INT);
            $stmt->bindParam(':prix_unitaire_achat', $prix_unitaire_achat, PDO::PARAM_STR);
            $stmt->bindParam(':valeur_achat', $valeur_achat, PDO::PARAM_STR);
            $stmt->bindParam(':numero_facture', $numero_facture, PDO::PARAM_STR);
            $stmt->bindParam(':date_achat', $date_achat, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {
                // Update stock quantities
                updateStockQuantities();
                
                // Set success message
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => $id_achat ? "L'achat a été modifié avec succès." : "L'achat a été ajouté avec succès."
                ];
            } else {
                // Set error message if query fails
                $_SESSION['message'] = [
                    'type' => 'warning',
                    'text' => $id_achat ? "Erreur lors de la modification de l'achat." : "Erreur lors de l'ajout de l'achat."
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

// Redirect back to the achat page
header("Location: ../vue/achat.php");
exit();