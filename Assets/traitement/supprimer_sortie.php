<?php
session_start();
include_once('./function.php');

// Check if an ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_sortie = intval($_GET['id']);

    try {
        // Prepare the database connection
        $bdd = $GLOBALS['bdd'];

        // Prepare SQL statement for deletion
        $sql = "DELETE FROM sortie WHERE id_sortie = :id_sortie";
        
        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(':id_sortie', $id_sortie, PDO::PARAM_INT);

        // Execute the deletion
        if ($stmt->execute()) {
            // Set success message
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "La sortie a été supprimée avec succès."
            ];
        } else {
            // Set error message if deletion fails
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => "Erreur lors de la suppression de la sortie."
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
        'text' => "Aucun identifiant de sortie n'a été fourni."
    ];
}

// Redirect back to the sortie page
header("Location: ../vue/sortie.php");
exit();
?>