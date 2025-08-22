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
    
    // Informations de l'immobilisation
    $nom_immo = isset($_POST['nom_immo']) ? validateInput($_POST['nom_immo']) : null;
    $id_categorie = isset($_POST['id_categorie']) ? intval($_POST['id_categorie']) : null;
    $description = isset($_POST['description']) ? validateInput($_POST['description']) : null;
    $type_immo = isset($_POST['type_immo']) ? validateInput($_POST['type_immo']) : null;
    $duree_vie = isset($_POST['duree_vie']) ? intval($_POST['duree_vie']) : null; // Variable manquante ajoutée
    $debut_service = isset($_POST['debut_service']) ? $_POST['debut_service'] : null;
    
    // Informations d'achat
    $id_fournisseur = isset($_POST['fournisseur']) ? intval($_POST['fournisseur']) : null;
    $prix_unitaire_immo = isset($_POST['prix_unitaire_immo']) ? floatval($_POST['prix_unitaire_immo']) : null;
    $facture = isset($_POST['facture']) ? validateInput($_POST['facture']) : null;
    $date_achat_immo = isset($_POST['date_achat_immo']) ? $_POST['date_achat_immo'] : null;

    // Validate inputs
    $errors = [];

    // Validation des informations d'immobilisation
    if (empty($nom_immo)) {
        $errors[] = "Le nom de l'immobilisation est requis.";
    }

    if (empty($id_categorie)) {
        $errors[] = "La catégorie est requise.";
    }

    if (empty($type_immo)) {
        $errors[] = "Le type d'immobilisation est requis.";
    }

    // Validation des informations d'achat
    if (empty($id_fournisseur)) {
        $errors[] = "Le fournisseur est requis.";
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
            
            // Start transaction
            $bdd->beginTransaction();

            $id_immo = null;

            // Format date if not empty
            if (!empty($debut_service)) {
                $debut_service = date('Y-m-d', strtotime($debut_service));
            } else {
                $debut_service = null;
            }

            if ($id_achat_immo) {
                // Mode modification - récupérer l'ID de l'immobilisation existante
                $achat_existant = getAchatImmo($id_achat_immo);
                if (!$achat_existant) {
                    throw new Exception("Achat d'immobilisation non trouvé.");
                }
                $id_immo = $achat_existant['id_immo'];

                // Mettre à jour l'immobilisation
                $sql_immo = "UPDATE immobilisation 
                            SET nom_immo = :nom_immo,
                                id_categorie_immo = :id_categorie,
                                description_immo = :description,
                                type_immo = :type_immo,
                                duree_vie = :duree_vie,
                                debut_service = :debut_service
                            WHERE id_immo = :id_immo";

                $stmt_immo = $bdd->prepare($sql_immo);
                $stmt_immo->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
                $stmt_immo->bindParam(':nom_immo', $nom_immo, PDO::PARAM_STR);
                $stmt_immo->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);
                $stmt_immo->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt_immo->bindParam(':type_immo', $type_immo, PDO::PARAM_STR);
                $stmt_immo->bindParam(':duree_vie', $duree_vie, PDO::PARAM_INT);
                $stmt_immo->bindParam(':debut_service', $debut_service, PDO::PARAM_STR);

                if (!$stmt_immo->execute()) {
                    throw new Exception("Erreur lors de la mise à jour de l'immobilisation.");
                }

                // Mettre à jour l'achat
                $sql_achat = "UPDATE achat_immo 
                             SET id_fournisseur = :id_fournisseur,
                                 prix_unitaire_immo = :prix_unitaire_immo,
                                 facture = :facture,
                                 date_achat_immo = :date_achat_immo
                             WHERE id_achat_immo = :id_achat_immo";

                $stmt_achat = $bdd->prepare($sql_achat);
                $stmt_achat->bindParam(':id_achat_immo', $id_achat_immo, PDO::PARAM_INT);
                $stmt_achat->bindParam(':id_fournisseur', $id_fournisseur, PDO::PARAM_INT);
                $stmt_achat->bindParam(':prix_unitaire_immo', $prix_unitaire_immo, PDO::PARAM_STR);
                $stmt_achat->bindParam(':facture', $facture, PDO::PARAM_STR);
                $stmt_achat->bindParam(':date_achat_immo', $date_achat_immo, PDO::PARAM_STR);

                if (!$stmt_achat->execute()) {
                    throw new Exception("Erreur lors de la mise à jour de l'achat.");
                }

                $operation = "modification";
            } else {
                // Mode ajout - créer une nouvelle immobilisation
                $sql_immo = "INSERT INTO immobilisation (nom_immo, id_categorie_immo, description_immo, type_immo, duree_vie, debut_service) 
                            VALUES (:nom_immo, :id_categorie, :description, :type_immo, :duree_vie, :debut_service)";

                $stmt_immo = $bdd->prepare($sql_immo);
                $stmt_immo->bindParam(':nom_immo', $nom_immo, PDO::PARAM_STR);
                $stmt_immo->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);
                $stmt_immo->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt_immo->bindParam(':type_immo', $type_immo, PDO::PARAM_STR);
                $stmt_immo->bindParam(':duree_vie', $duree_vie, PDO::PARAM_INT);
                $stmt_immo->bindParam(':debut_service', $debut_service, PDO::PARAM_STR);

                if (!$stmt_immo->execute()) {
                    throw new Exception("Erreur lors de l'ajout de l'immobilisation.");
                }

                // Récupérer l'ID de l'immobilisation nouvellement créée
                $id_immo = $bdd->lastInsertId();

                // Insérer l'achat
                $sql_achat = "INSERT INTO achat_immo (id_immo, id_fournisseur, prix_unitaire_immo, facture, date_achat_immo) 
                             VALUES (:id_immo, :id_fournisseur, :prix_unitaire_immo, :facture, :date_achat_immo)";

                $stmt_achat = $bdd->prepare($sql_achat);
                $stmt_achat->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);
                $stmt_achat->bindParam(':id_fournisseur', $id_fournisseur, PDO::PARAM_INT);
                $stmt_achat->bindParam(':prix_unitaire_immo', $prix_unitaire_immo, PDO::PARAM_STR);
                $stmt_achat->bindParam(':facture', $facture, PDO::PARAM_STR);
                $stmt_achat->bindParam(':date_achat_immo', $date_achat_immo, PDO::PARAM_STR);

                if (!$stmt_achat->execute()) {
                    throw new Exception("Erreur lors de l'ajout de l'achat.");
                }

                $operation = "ajout";
            }

            // Commit transaction
            $bdd->commit();
            
            // Set success message
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => $operation === "modification" ? 
                    "L'achat d'immobilisation a été modifié avec succès." : 
                    "L'achat d'immobilisation a été ajouté avec succès. Immobilisation créée avec l'ID: " . $id_immo
            ];

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

// Redirect back to the achat_immo page
header("Location: ../vue_immo/achat_immo.php");
exit();
?>