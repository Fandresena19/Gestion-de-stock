<?php
include('./db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom_immo = $_POST['nom_immo'];
    $id_categorie = $_POST['categorie'];
    $description = $_POST['description'];

    if (!empty($nom_immo) && !empty($id_categorie) && !empty($description)) {

        $sql = 'INSERT INTO immobilisation (nom_immo, id_categorie_immo, description_immo)
                VALUES (?, ?, ?)';

        $req = $bdd->prepare($sql);
        $req->execute([
            $nom_immo,
            $id_categorie,
            $description,
        ]);

        if ($req->rowCount() != 0) {
            $_SESSION['message']['text'] = 'Immobilisation ajoutée avec succès';
            $_SESSION['message']['type'] = 'success';
        } else {
            $_SESSION['message']['text'] = 'Une erreur s\'est produite lors de l\'ajout de l\'immobilisation';
            $_SESSION['message']['type'] = 'danger';
        }
    } else {
        $_SESSION['message']['text'] = 'Une information obligatoire non renseignée';
        $_SESSION['message']['type'] = 'warning';
    }

    header('location: ../vue_immo/immobilisation.php');
}
?>