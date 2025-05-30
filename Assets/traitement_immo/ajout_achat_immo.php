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
    $id_achat_immo = isset($_POST['id_achat_immo']) ? intval($_POST['id_achat_immo']) : null;
    $id_immo = isset($_POST['immobilisation']) ? intval($_POST['immobilisation']) : null;
    $id_fournisseur = isset($_POST['fournisseur']) ? intval($_POST['fournisseur']) : null;
    $quantite_achete_immo = isset($_POST['quantite_achete_immo']) ? intval($_POST['quantite_achete_immo']) : null;
    $prix_unitaire_immo = isset($_POST['prix_unitaire_immo']) ? floatval($_POST['prix_unitaire_immo']) : null;
    $valeur_achat_immo = $quantite_achete_immo * $prix_unitaire_immo;
    $facture = isset($_POST['facture']) ? validateInput($_POST['facture']) : null;
    $date_achat_immo = isset($_POST['date_achat_immo']) ? $_POST['date_achat_immo'] : null;

    // Validate inputs
    $errors = [];

    if (empty($id_immo)) {
        $errors[] = "L'immobilisation est requise.";
    }

    if (empty($id_fournisseur)) {
        $errors[] = "Le fournisseur est requis.";
    }

    if (empty($quantite_achete_immo) || $quantite_achete_immo <= 0) {
        $errors[] = "La quantité achetée est requise et doit être positive.";
    }
    
    if (empty($prix_unitaire_immo) || $prix_unitaire_immo <= 0) {
        $errors[] = "Le prix unitaire est requis et doit être positif.";
    }

    if (empty($facture)) {
        $errors[] = "Le numéro de facture est requis.";
    }

    if (empty($date_achat_immo)) {
        $errors[] = "La date d'achat est requise.";
    }

    // If no errors, proceed with update or insert
    if (empty($errors)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];

            // Prepare SQL statement
            if ($id_achat_immo) {
                // Update existing achat_immo
                $sql = "UPDATE achat_immo 
                        SET id_immo = :id_immo, 
                            id_fournisseur = :id_fournisseur,
                            quantite_achete_immo = :quantite_achete_immo,
                            prix_unitaire_immo = :prix_unitaire_immo,
                            valeur_achat_immo = :valeur_achat_immo,
                            facture = :facture, 
                            date_achat_immo = :date_achat_immo
                        WHERE id_achat_immo = :id_achat_immo";

                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_achat_immo', $id_achat_immo, PDO::PARAM_INT);
            } else {
                // Insert new achat_immo
                $sql = "INSERT INTO achat_immo (id_immo, id_fournisseur, quantite_achete_immo, prix_unitaire_immo, valeur_achat_immo, facture, date_achat_immo) 
                        VALUES (:id_immo, :id_fournisseur, :quantite_achete_immo, :prix_unitaire_immo, :valeur_achat_immo, :facture, :date_achat_immo)";

                $stmt = $bdd->prepare($sql);
            }
            
            // Bind parameters for both insert and update
            $stmt->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
            $stmt->bindParam(':id_fournisseur', $id_fournisseur, PDO::PARAM_INT);
            $stmt->bindParam(':quantite_achete_immo', $quantite_achete_immo, PDO::PARAM_INT);
            $stmt->bindParam(':prix_unitaire_immo', $prix_unitaire_immo, PDO::PARAM_STR);
            $stmt->bindParam(':valeur_achat_immo', $valeur_achat_immo, PDO::PARAM_STR);
            $stmt->bindParam(':facture', $facture, PDO::PARAM_STR);
            $stmt->bindParam(':date_achat_immo', $date_achat_immo, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {
                // Update stock quantities
                updateStockQuantitiesImmo();
                
                // Set success message
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => $id_achat_immo ? "L'achat d'immobilisation a été modifié avec succès." : "L'achat d'immobilisation a été ajouté avec succès."
                ];
            } else {
                // Set error message if query fails
                $_SESSION['message'] = [
                    'type' => 'warning',
                    'text' => $id_achat_immo ? "Erreur lors de la modification de l'achat d'immobilisation." : "Erreur lors de l'ajout de l'achat d'immobilisation."
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

// Redirect back to the achat_immo page
header("Location: ../vue_immo/achat_immo.php");
exit();