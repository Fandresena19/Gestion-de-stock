<?php
include('./db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom_art = $_POST['nom_article'];
    $id_categorie = $_POST['categorie'];
    $description = $_POST['description'];


    if (!empty($nom_art) && !empty($id_categorie) && !empty($description)) {

        $sql = 'INSERT INTO article (nom_article, id_categorie, description_article)
 VALUES (?, ?, ?)';

        $req = $bdd->prepare($sql);
        $req->execute([
            $nom_art,
            $id_categorie,
            $description
        ]);

        if ($req->rowCount() != 0) {
            $_SESSION['message']['text'] = 'Article ajouté avec succé';
            $_SESSION['message']['type'] = 'success';
        } else {
            $_SESSION['message']['text'] =  'Une erreur s\'est produite lors de l\'ajout de \'article';
            $_SESSION['message']['type'] = 'danger';
        }
    } else {
        $_SESSION['message']['text'] =  'Une information obligatoire non renseignée';
        $_SESSION['message']['type'] = 'warning';
    }


    header('location: ../vue/article.php');
}
