<?php
session_start();
include_once('./function.php');

// Check if an ID is provided
if (!empty($_GET['id'])) {
    $id_achat = intval($_GET['id']);

    try {
        $bdd = $GLOBALS['bdd'];
        
        // Prepare SQL statement to delete the achat
        $sql = "DELETE FROM achat WHERE id_achat = ?";
        
        $stmt = $bdd->prepare($sql);
        
        // Execute the deletion
        if ($stmt->execute([$id_achat])) {
            // Set success message
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "L'achat a été supprimé avec succès."
            ];
        } else {
            // Set error message if deletion fails
            $_SESSION['message'] = [
                'type' => 'warning',
                'text' => "Erreur lors de la suppression de l'achat."
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
        'text' => "Aucun identifiant d'achat spécifié."
    ];
}

// Redirect back to the achat page
header("Location: ../vue/achat.php");
exit();
?>