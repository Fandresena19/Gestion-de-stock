<?php
session_start();
include_once('./function.php');

// Check if an ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_immo = intval($_GET['id']);

    try {
        // Prepare the database connection
        $bdd = $GLOBALS['bdd'];

        // Prepare SQL statement for deletion
        $sql = "DELETE FROM immobilisation WHERE id_immo = :id_immo";
        
        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(':id_immo', $id_immo, PDO::PARAM_INT);

        // Execute the deletion
        if ($stmt->execute()) {
            // Set success message
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "L'immobilisation a été supprimée avec succès."
            ];
        } else {
            // Set error message if deletion fails
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => "Erreur lors de la suppression de l'immobilisation."
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
        'text' => "Aucun identifiant d'immobilisation n'a été fourni."
    ];
}

// Redirect back to the immobilisation page
header("Location: ../vue_immo/immobilisation.php");
exit();
?>