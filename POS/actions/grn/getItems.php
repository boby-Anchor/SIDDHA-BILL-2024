<?php
session_start();
require_once "../../config/db.php";

$shop_id;
$grn_number;

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

if (isset($_POST['grn_number'])) {
    $grn_number = $_POST['grn_number'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'GRN data not received.',
    ]);
    exit();
}

try {
    $grnItemsResult = $conn->query("SELECT
    grn_item.*,
    p_medicine.name AS item_name,
    p_medicine.sku,
    p_brand.name AS brand
    FROM grn_item
    INNER JOIN p_medicine ON grn_item.grn_p_id = p_medicine.code
    INNER JOIN p_brand ON p_medicine.brand = p_brand.id
    WHERE grn_number = '$grn_number'
    ORDER BY grn_item_id ASC
    ");

    if ($grnItemsResult && $grnItemsResult->num_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $grnItemsResult->fetch_all(MYSQLI_ASSOC),
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'GRN items not found!',
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
