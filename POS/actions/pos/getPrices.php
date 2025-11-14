<?php
session_start();
require "../../config/db.php";

$shop_id;
$barcode;

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

if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Barcode data not received.',
    ]);
    exit();
}

try {
    $barcodeResult = $conn->query("SELECT stock2.stock_id,
    stock2.unit_s_price AS unit_price,
    stock2.item_s_price AS item_price
    FROM stock2
    WHERE stock_shop_id = '$shop_id'
    AND stock_item_code = '$barcode'
    -- AND stock2.stock_item_qty > '0'
    ");

    if ($barcodeResult && $barcodeResult->num_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $barcodeResult->fetch_all(MYSQLI_ASSOC),
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Prices not found!',
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
