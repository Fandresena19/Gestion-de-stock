<?php
include('function.php');

header('Content-Type: application/json');

if(isset($_GET['id_immo'])) {
    $id_immo = $_GET['id_immo'];
    $average_price = getAveragePurchasePriceImmo($id_immo);
    
    echo json_encode(['average_price' => $average_price]);
} else {
    echo json_encode(['error' => 'Immobilisation ID not provided']);
}