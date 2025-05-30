<?php

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    // Rediriger vers la page de connexion
    header("Location: ../index.php");
    exit();
}
?>