<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:../login.php");
    exit();
}

include('../../config/db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poNumber'])) {
    $poNumber = $conn->real_escape_string($_POST['poNumber']);

    $result = $conn->query("SELECT
        poinvoiceitems.*,
        p_medicine.code,
        p_medicine.name,
        p_medicine.sku,
        unit_category_variation.ucv_name AS invoiceItem_ucv,
        p_brand.name AS brand_name
        FROM poinvoiceitems
        LEFT JOIN p_medicine ON poinvoiceitems.item_code = p_medicine.code
        LEFT JOIN unit_category_variation ON p_medicine.unit_variation = unit_category_variation.ucv_id
        LEFT JOIN p_brand ON p_medicine.brand = p_brand.id
        WHERE invoiceNumber = '$poNumber'");

    if ($result) {
        $items = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            'status' => 'success',
            'items' => $items,
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to load PO items.',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'PO number missing.',
    ]);
}
