<?php
session_start();
require "../../config/db.php";

$stock_id;

if (isset($_POST['stock_id'])) {
    $stock_id = $_POST['stock_id'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data not received.',
    ]);
    exit();
}

try {
    $barcodeResult = $conn->query("SELECT 
    stock2.item_s_price AS item_s_price,
    stock2.unit_s_price AS unit_s_price,
    p_medicine.id AS id,
    p_medicine.code AS code,
    p_medicine.name AS name,
    unit_category_variation.ucv_name AS ucv_name,
    p_brand.name AS brand,
    medicine_unit.unit AS unit
    FROM stock2
    INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
    INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
    INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
    INNER JOIN p_brand ON p_medicine.brand = p_brand.id
    WHERE stock_id ='$stock_id' ");

    if ($barcodeResult && $barcodeResult->num_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $barcodeResult->fetch_all(MYSQLI_ASSOC),
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No data found!',
        ]);
        exit();
    }
} catch (Exception $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage(),
    ]);
    exit();
}
