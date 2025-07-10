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
    $nom_immo = isset($_POST['nom_immo']) ? validateInput($_POST['nom_immo']) : null;
    $prix_unitaire_immo = isset($_POST['prix_unitaire_immo']) ? floatval($_POST['prix_unitaire_immo']) : null;
    $raison_sortie_immo = isset($_POST['raison_sortie_immo']) ? validateInput($_POST['raison_sortie_immo']) : null;
    $date_sortie_immo = isset($_POST['date_sortie_immo']) ? $_POST['date_sortie_immo'] : null;
    $supprimer_immobilisation = isset($_POST['supprimer_immobilisation']) ? 1 : 0;

    // Si nom_immo n'est pas fourni, le récupérer depuis la base
    if (empty($nom_immo) && !empty($id_immo)) {
        $immobilisation = getImmobilisation($id_immo);
        if ($immobilisation) {
            $nom_immo = $immobilisation['nom_immo'];
        }
    }

    // Validate inputs
    $errors = [];

    if (empty($id_immo)) {
        $errors[] = "L'immobilisation est requise.";
    }

    if (empty($nom_immo)) {
        $errors[] = "Le nom de l'immobilisation est requis.";
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

    // If no errors, proceed with update or insert
    if (empty($errors)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];
            
            // Start transaction
            $bdd->beginTransaction();

            // Prepare SQL statement
            if ($id_sortie_immo) {
                // Update existing sortie_immo
                $sql = "UPDATE sortie_immo 
                        SET id_immo = :id_immo, 
                            nom_immo = :nom_immo,
                            prix_unitaire_immo = :prix_unitaire_immo,
                            raison_sortie_immo = :raison_sortie_immo,
                            date_sortie_immo = :date_sortie_immo
                        WHERE id_sortie_immo = :id_sortie_immo";

                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_sortie_immo', $id_sortie_immo, PDO::PARAM_INT);
                $stmt->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
                $stmt->bindParam(':nom_immo', $nom_immo, PDO::PARAM_STR);
                $stmt->bindParam(':prix_unitaire_immo', $prix_unitaire_immo, PDO::PARAM_STR);
                $stmt->bindParam(':raison_sortie_immo', $raison_sortie_immo, PDO::PARAM_STR);
                $stmt->bindParam(':date_sortie_immo', $date_sortie_immo, PDO::PARAM_STR);
                
                $operation = "modification";
            } else {
                // Insert new sortie_immo
                $sql = "INSERT INTO sortie_immo (id_immo, nom_immo, prix_unitaire_immo, raison_sortie_immo, date_sortie_immo) 
                        VALUES (:id_immo, :nom_immo, :prix_unitaire_immo, :raison_sortie_immo, :date_sortie_immo)";

                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
                $stmt->bindParam(':nom_immo', $nom_immo, PDO::PARAM_STR);
                $stmt->bindParam(':prix_unitaire_immo', $prix_unitaire_immo, PDO::PARAM_STR);
                $stmt->bindParam(':raison_sortie_immo', $raison_sortie_immo, PDO::PARAM_STR);
                $stmt->bindParam(':date_sortie_immo', $date_sortie_immo, PDO::PARAM_STR);
                
                $operation = "ajout";
            }

            // Execute the sortie query
            if ($stmt->execute()) {
                // Si c'est un nouvel ajout et qu'on veut supprimer l'immobilisation
                if (!$id_sortie_immo && $supprimer_immobilisation) {
                    // Supprimer l'immobilisation de la table immobilisation
                    $sql_delete = "DELETE FROM immobilisation WHERE id_immo = :id_immo";
                    $stmt_delete = $bdd->prepare($sql_delete);
                    $stmt_delete->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
                    
                    if (!$stmt_delete->execute()) {
                        throw new Exception("Erreur lors de la suppression de l'immobilisation");
                    }
                }
                
                // Commit transaction
                $bdd->commit();
                
                // Set success message
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => $operation === "modification" ? 
                        "La sortie d'immobilisation a été modifiée avec succès." : 
                        "La sortie d'immobilisation a été ajoutée avec succès." . 
                        ($supprimer_immobilisation ? " L'immobilisation a été supprimée de l'inventaire." : "")
                ];
            } else {
                // Rollback transaction
                $bdd->rollback();
                
                // Set error message if query fails
                $_SESSION['message'] = [
                    'type' => 'warning',
                    'text' => $operation === "modification" ? 
                        "Erreur lors de la modification de la sortie d'immobilisation." : 
                        "Erreur lors de l'ajout de la sortie d'immobilisation."
                ];
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            if ($bdd->inTransaction()) {
                $bdd->rollback();
            }
            
            // Handle database errors
            $_SESSION['message'] = [
                'type' => 'warning',
                'text' => "Erreur : " . $e->getMessage()
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
?>