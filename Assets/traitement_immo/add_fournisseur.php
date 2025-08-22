<?php
session_start();
include('./db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_fournisseur = trim($_POST['nom_fournisseur']);

    if (!empty($nom_fournisseur)) {
        try {
            // Check if supplier already exists
            $stmt_check = $bdd->prepare("SELECT COUNT(*) FROM fournisseur WHERE nom_fournisseur = ?");
            $stmt_check->execute([$nom_fournisseur]);
            $count = $stmt_check->fetchColumn();

            if ($count > 0) {
                $_SESSION['message'] = [
                    'text' => "Erreur : Ce fournisseur existe déjà.",
                    'type' => "alert-danger"
                ];
            } else {
                $stmt = $bdd->prepare("INSERT INTO fournisseur (nom_fournisseur) VALUES (?)");
                $stmt->execute([$nom_fournisseur]);
                
                $_SESSION['message'] = [
                    'text' => "Fournisseur '{$nom_fournisseur}' ajouté avec succès.",
                    'type' => "alert-success"
                ];
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = [
                'text' => "Erreur d'ajout du fournisseur : " . $e->getMessage(),
                'type' => "alert-danger"
            ];
        }
    } else {
        $_SESSION['message'] = [
            'text' => "Erreur : Le nom du fournisseur ne peut pas être vide.",
            'type' => "alert-danger"
        ];
    }
} 

// Redirect back to the acquisition page
header("Location: ../vue_immo/achat_immo.php");
exit();
?>