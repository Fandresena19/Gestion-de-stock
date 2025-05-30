<?php
session_start();
include_once('./function.php');

// Check if an ID is provided
if (!empty($_GET['id'])) {
    $id_achat_immo = intval($_GET['id']);

    try {
        $bdd = $GLOBALS['bdd'];
        
        // Prepare SQL statement to delete the achat_immo
        $sql = "DELETE FROM achat_immo WHERE id_achat_immo = ?";
        
        $stmt = $bdd->prepare($sql);
        
        // Execute the deletion
        if ($stmt->execute([$id_achat_immo])) {
            // Update stock quantities
            updateStockQuantitiesImmo();
            
            // Set success message
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "L'achat d'immobilisation a été supprimé avec succès."
            ];
        } else {
            // Set error message if deletion fails
            $_SESSION['message'] = [
                'type' => 'warning',
                'text' => "Erreur lors de la suppression de l'achat d'immobilisation."
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
    // If no ID is provided
    $_SESSION['message'] = [
        'type' => 'warning',
        'text' => "Aucun identifiant d'achat d'immobilisation spécifié."
    ];
}

// Redirect back to the achat_immo page
header("Location: ../vue_immo/achat_immo.php");
exit();