<?php
include('function.php');

header('Content-Type: application/json');

if(isset($_GET['id_article'])) {
    $id_article = $_GET['id_article'];
    $average_price = getAveragePurchasePrice($id_article);
    
    echo json_encode(['average_price' => $average_price]);
} else {
    echo json_encode(['error' => 'Article ID not provided']);
}
