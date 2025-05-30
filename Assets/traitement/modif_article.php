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
    $id_article = isset($_POST['id_article']) ? intval($_POST['id_article']) : null;
    $nom_article = isset($_POST['nom_article']) ? validateInput($_POST['nom_article']) : '';
    $categorie = isset($_POST['categorie']) ? intval($_POST['categorie']) : null;
    $description = isset($_POST['description']) ? validateInput($_POST['description']) : '';

    // Validate inputs
    $warning = [];

    if (empty($nom_article)) {
        $warning[] = "Le nom de l'article est requis.";
    }

    if (empty($categorie)) {
        $warning[] = "La catégorie est requise.";
    }

    // If no warning$warning, proceed with update
    if (empty($warning)) {
        try {
            // Prepare the database connection
            $bdd = $GLOBALS['bdd'];

            // Prepare SQL statement
            if ($id_article) {
                // Update existing article
                $sql = "UPDATE article 
                        SET nom_article = :nom_article, 
                            description_article = :description, 
                            id_categorie = :categorie 
                        WHERE id_article = :id_article";
                
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
                $stmt->bindParam(':nom_article', $nom_article, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':categorie', $categorie, PDO::PARAM_INT);

                // Execute the update
                if ($stmt->execute()) {
                    // Set success message
                    $_SESSION['message'] = [
                        'type' => 'success',
                        'text' => "L'article a été modifié avec succès."
                    ];
                } else {
                    // Set error message if update fails
                    $_SESSION['message'] = [
                        'type' => 'danger',
                        'text' => "Erreur lors de la modification de l'article."
                    ];
                }
            } else {
                // Insert new article if no ID is provided
                $sql = "INSERT INTO article (nom_article, description_article, id_categorie) 
                        VALUES (:nom_article, :description, :categorie)";
                
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':nom_article', $nom_article, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':categorie', $categorie, PDO::PARAM_INT);

                // Execute the insert
                if ($stmt->execute()) {
                    // Set success message
                    $_SESSION['message'] = [
                        'type' => 'success',
                        'text' => "L'article a été ajouté avec succès."
                    ];
                } else {
                    // Set error message if insert fails
                    $_SESSION['message'] = [
                        'type' => 'danger',
                        'text' => "Erreur lors de l'ajout de l'article."
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

// Redirect back to the article page
header("Location: ../vue/article.php");
exit();
?>