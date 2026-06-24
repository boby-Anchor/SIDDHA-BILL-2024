<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired. Wait to Login again.'
    ]);
    exit();
}

require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invoiceNumber'])) {
    $invoiceNumber = $conn->real_escape_string($_POST['invoiceNumber']);

    $dmResult = $conn->query("SELECT *
        FROM dm_items
        WHERE invoice_id = '$invoiceNumber'");

    $result = $conn->query("SELECT
        invoiceitems.*,
        p_medicine.code,
        p_medicine.name,
        p_medicine.sku,
        medicine_unit.unit AS unit,
        unit_category_variation.ucv_name,
        p_brand.name AS brand_name
        FROM invoiceitems
        LEFT JOIN p_medicine ON invoiceitems.barcode = p_medicine.code
        LEFT JOIN medicine_unit
            ON p_medicine.medicine_unit_id = medicine_unit.id
        LEFT JOIN unit_category_variation
            ON unit_category_variation.ucv_id = p_medicine.unit_variation
        LEFT JOIN p_brand
            ON p_medicine.brand = p_brand.id
        WHERE invoiceNumber = '$invoiceNumber'");

    if ($result || $dmResult) {
        echo json_encode([
            'status' => 'success',
            'items' => $result->fetch_all(MYSQLI_ASSOC),
            'dmItems' => $dmResult->fetch_all(MYSQLI_ASSOC),
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to load details.',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invoice number missing.',
    ]);
}
