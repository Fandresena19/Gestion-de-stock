<?php
include('./db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_immo = $_POST['id_immo'];
    $nom_immo = $_POST['nom_immo'];
    $id_categorie = $_POST['categorie'];
    $description = $_POST['description'];
    $debut_service = $_POST['debut_service'];
    $duree_vie = $_POST['duree_vie'];
    
    // Gestion du type d'immobilisation
    $type_immo = '';
    if (!empty($_POST['type']) && $_POST['type'] !== 'new') {
        $type_immo = $_POST['type'];
    } elseif (!empty($_POST['new_type'])) {
        $type_immo = $_POST['new_type'];
    }

    // Validation des champs requis
    if (!empty($id_immo) && !empty($nom_immo) && !empty($id_categorie) && !empty($description)) {

        // Préparer la requête SQL
        $sql = 'UPDATE immobilisation 
                SET nom_immo = ?, 
                    id_categorie_immo = ?, 
                    description_immo = ?, 
                    type_immo = ?,
                    duree_vie = ?, 
                    debut_service = ?
                WHERE id_immo = ?';

        $req = $bdd->prepare($sql);
        $req->execute([
            $nom_immo,
            $id_categorie,
            $description,
            $type_immo,
            $duree_vie,
            !empty($debut_service) ? $debut_service : null,
            $id_immo
        ]);

        if ($req->rowCount() != 0) {
            $_SESSION['message']['text'] = 'Immobilisation modifiée avec succès';
            $_SESSION['message']['type'] = 'success';
        } else {
            $_SESSION['message']['text'] = 'Aucune modification effectuée ou erreur lors de la modification';
            $_SESSION['message']['type'] = 'warning';
        }
    } else {
        $_SESSION['message']['text'] = 'Une information obligatoire non renseignée';
        $_SESSION['message']['type'] = 'danger';
    }

    // Redirection vers la page d'immobilisation
    header('location: ../vue_immo/immobilisation.php' . (!empty($id_immo) ? '?id=' . $id_immo : ''));
    exit();
}
?>