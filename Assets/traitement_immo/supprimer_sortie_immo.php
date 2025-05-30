<?php
session_start();
include_once('./function.php');

// Check if an ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_sortie_immo = intval($_GET['id']);

    try {
        // Prepare the database connection
        $bdd = $GLOBALS['bdd'];

        // Prepare SQL statement for deletion
        $sql = "DELETE FROM sortie_immo WHERE id_sortie_immo = :id_sortie_immo";
        
        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(':id_sortie_immo', $id_sortie_immo, PDO::PARAM_INT);

        // Execute the deletion
        if ($stmt->execute()) {
            // Update stock quantities
            updateStockQuantitiesImmo();
            
            // Set success message
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "La sortie d'immobilisation a été supprimée avec succès."
            ];
        } else {
            // Set error message if deletion fails
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => "Erreur lors de la suppression de la sortie d'immobilisation."
            ];
        }
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => "Erreur de base de données : " . $e->getMessage()
        ];
    }
} else {
    // If no ID is provided
    $_SESSION['message'] = [
        'type' => 'error',
        'text' => "Aucun identifiant de sortie d'immobilisation n'a été fourni."
    ];
}

// Redirect back to the sortie_immo page
header("Location: ../vue_immo/sortie_immo.php");
exit();