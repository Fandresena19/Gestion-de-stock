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
    $id_sortie_immo = isset($_POST['id_sortie_immo']) ? intval($_POST['id_sortie_immo']) : null;
    $id_immo = isset($_POST['immobilisation']) ? intval($_POST['immobilisation']) : null;
    $quantite_sortie_immo = isset($_POST['quantite_sortie_immo']) ? intval($_POST['quantite_sortie_immo']) : null;
    $prix_unitaire_immo = isset($_POST['prix_unitaire_immo']) ? floatval($_POST['prix_unitaire_immo']) : null;
    $valeur_sortie_immo = $quantite_sortie_immo * $prix_unitaire_immo;
    $raison_sortie_immo = isset($_POST['raison_sortie_immo']) ? validateInput($_POST['raison_sortie_immo']) : null;
    $date_sortie_immo = isset($_POST['date_sortie_immo']) ? $_POST['date_sortie_immo'] : null;

    // Validate inputs
    $errors = [];

    if (empty($id_immo)) {
        $errors[] = "L'immobilisation est requise.";
    }

    if (empty($quantite_sortie_immo) || $quantite_sortie_immo <= 0) {
        $errors[] = "La quantité sortie est requise et doit être positive.";
    }

    if (empty($prix_unitaire_immo) || $prix_unitaire_immo <= 0) {
        $errors[] = "Le prix unitaire est requis et doit être positif.";
    }

    if (empty($raison_sortie_immo)) {
        $errors[] = "La raison de sortie est requise.";
    }

    if (empty($date_sortie_immo)) {
        $errors[] = "La date de sortie est requise.";
    }

    // Vérifier si le stock est suffisant
    $immobilisation = getImmobilisation($id_immo);
    if ($immobilisation && $quantite_sortie_immo > $immobilisation['quantite_en_stock_immo']) {
        $errors[] = "Stock insuffisant. Disponible: {$immobilisation['quantite_en_stock_immo']}";
    }

    // If no errors, proceed with update or insert
    if (empty($errors)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];

            // Prepare SQL statement
            if ($id_sortie_immo) {
                // Update existing sortie_immo
                $sql = "UPDATE sortie_immo 
                        SET id_immo = :id_immo, 
                            quantite_sortie_immo = :quantite_sortie_immo,
                            prix_unitaire_immo = :prix_unitaire_immo,
                            valeur_sortie_immo = :valeur_sortie_immo,
                            raison_sortie_immo = :raison_sortie_immo,
                            date_sortie_immo = :date_sortie_immo
                        WHERE id_sortie_immo = :id_sortie_immo";

                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_sortie_immo', $id_sortie_immo, PDO::PARAM_INT);
            } else {
                // Insert new sortie_immo
                $sql = "INSERT INTO sortie_immo (id_immo, quantite_sortie_immo, prix_unitaire_immo, valeur_sortie_immo, raison_sortie_immo, date_sortie_immo) 
                        VALUES (:id_immo, :quantite_sortie_immo, :prix_unitaire_immo, :valeur_sortie_immo, :raison_sortie_immo, :date_sortie_immo)";

                $stmt = $bdd->prepare($sql);
            }
            
            // Bind parameters for both insert and update
            $stmt->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
            $stmt->bindParam(':quantite_sortie_immo', $quantite_sortie_immo, PDO::PARAM_INT);
            $stmt->bindParam(':prix_unitaire_immo', $prix_unitaire_immo, PDO::PARAM_STR);
            $stmt->bindParam(':valeur_sortie_immo', $valeur_sortie_immo, PDO::PARAM_STR);
            $stmt->bindParam(':raison_sortie_immo', $raison_sortie_immo, PDO::PARAM_STR);
            $stmt->bindParam(':date_sortie_immo', $date_sortie_immo, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {
                // Update stock quantities
                updateStockQuantitiesImmo();
                
                // Set success message
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => $id_sortie_immo ? "La sortie d'immobilisation a été modifiée avec succès." : "La sortie d'immobilisation a été ajoutée avec succès."
                ];
            } else {
                // Set error message if query fails
                $_SESSION['message'] = [
                    'type' => 'warning',
                    'text' => $id_sortie_immo ? "Erreur lors de la modification de la sortie d'immobilisation." : "Erreur lors de l'ajout de la sortie d'immobilisation."
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

// Redirect back to the sortie_immo page
header("Location: ../vue_immo/sortie_immo.php");
exit();