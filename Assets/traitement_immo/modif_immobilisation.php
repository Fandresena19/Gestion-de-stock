<?php
session_start();
include_once('./function.php');

// Validate and sanitize input
function validateInput($input) {
    return htmlspecialchars(trim($input));
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $id_immo = isset($_POST['id_immo']) ? intval($_POST['id_immo']) : null;
    $nom_immo = isset($_POST['nom_immo']) ? validateInput($_POST['nom_immo']) : '';
    $categorie = isset($_POST['categorie']) ? intval($_POST['categorie']) : null;
    $description = isset($_POST['description']) ? validateInput($_POST['description']) : '';
    $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 0;

    // Validate inputs
    $warning = [];

    if (empty($nom_immo)) {
        $warning[] = "Le nom de l'immobilisation est requis.";
    }

    if (empty($categorie)) {
        $warning[] = "La catégorie est requise.";
    }

    // If no warning, proceed with update
    if (empty($warning)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];

            // Prepare SQL statement
            if ($id_immo) {
                // Update existing immobilisation
                $sql = "UPDATE immobilisation 
                        SET nom_immo = :nom_immo, 
                            description_immo = :description, 
                            id_categorie = :categorie,
                            quantite_en_stock_immo = :quantite 
                        WHERE id_immo = :id_immo";
                
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
                $stmt->bindParam(':nom_immo', $nom_immo, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':categorie', $categorie, PDO::PARAM_INT);
                $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);

                // Execute the update
                if ($stmt->execute()) {
                    // Set success message
                    $_SESSION['message'] = [
                        'type' => 'success',
                        'text' => "L'immobilisation a été modifiée avec succès."
                    ];
                } else {
                    // Set error message if update fails
                    $_SESSION['message'] = [
                        'type' => 'danger',
                        'text' => "Erreur lors de la modification de l'immobilisation."
                    ];
                }
            } else {
                // Insert new immobilisation if no ID is provided
                $sql = "INSERT INTO immobilisation (nom_immo, description_immo, id_categorie_immo, quantite_en_stock_immo) 
                        VALUES (:nom_immo, :description, :categorie, :quantite)";
                
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':nom_immo', $nom_immo, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':categorie', $categorie, PDO::PARAM_INT);
                $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);

                // Execute the insert
                if ($stmt->execute()) {
                    // Set success message
                    $_SESSION['message'] = [
                        'type' => 'success',
                        'text' => "L'immobilisation a été ajoutée avec succès."
                    ];
                } else {
                    // Set error message if insert fails
                    $_SESSION['message'] = [
                        'type' => 'danger',
                        'text' => "Erreur lors de l'ajout de l'immobilisation."
                    ];
                }
            }
        } catch (PDOException $e) {
            // Handle database errors
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Erreur de base de données : " . $e->getMessage()
            ];
        }
    } else {
        // If there are validation errors
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => implode('<br>', $warning)
        ];
    }
} else {
    // If not a POST request
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "Méthode de requête invalide."
    ];
}

// Redirect back to the immobilisation page
header("Location: ../vue_immo/immobilisation.php");
exit();
?>