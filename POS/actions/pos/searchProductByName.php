<?php
session_start();
include('../../config/db.php');

$searchName = $_POST['searchName'];
$shop_id = 0;

// Session check of login and values assign
if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];
    $user_id = $userData['id'];
    $shop_id = $userData['shop_id'];

    if ($user_id == 0 or $shop_id == 0) {
        echo json_encode([
            'status' => 'sessionExpired',
            'message' => 'Session error. Wait to login again.'
        ]);
        exit();
    }
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session Expired! Wait to login again.',
    ]);
    exit();
}


if (isset($_POST['searchName'])) {
    $searchName = $_POST['searchName'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Barcode data not received.',
    ]);
    exit();
}

try {

    $itemsData = $conn->query("SELECT stock2.*, p_brand.name AS bName, p_medicine.code AS code, p_medicine.name AS name,
    medicine_unit.unit AS unit2 , unit_category_variation.ucv_name AS ucv_name2 
    FROM stock2
    INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
    INNER JOIN p_brand ON p_brand.id = p_medicine.brand
    INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
    INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
    WHERE stock2.stock_shop_id = '$shop_id'
    AND p_medicine.name LIKE '%$searchName%'
    ORDER BY bName ASC
    ");

    if ($itemsData && $itemsData->num_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $itemsData->fetch_all(MYSQLI_ASSOC),
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'empty',
            'message' => 'Product not found with ' . $searchName,
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
