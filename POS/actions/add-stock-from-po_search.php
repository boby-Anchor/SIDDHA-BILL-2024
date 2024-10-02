<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('../config/db.php');
}

$poNumber = '';
$items = [];
$totalPrice = 0;
$totalValue = 0;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['poNumber'])) {
        $poNumber = $_POST['poNumber'];

        $invResult = $conn->query("SELECT poinvoices.*,
               users.name AS stockKeeper,
               shop1.shopName AS shop,
               shop2.shopName AS poShop
        FROM poinvoices
        INNER JOIN users ON poinvoices.user_id = users.id
        INNER JOIN shop shop1 ON poinvoices.shop_id = shop1.shopId
        INNER JOIN shop shop2 ON poinvoices.po_shop_id = shop2.shopId
        WHERE invoice_id = '$poNumber';
        ");

        $invoiceData = $invResult->fetch_all(MYSQLI_ASSOC);

        $result = $conn->query("SELECT poinvoiceitems.*, unit_category_variation.ucv_name AS invoiceItem_ucv        
        FROM poinvoiceitems
        INNER JOIN p_medicine ON poinvoiceitems.item_code = p_medicine.code
        INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
        WHERE invoiceNumber = '$poNumber';
        ");
        $items = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode(
            array(
                'status' => 'success',
                'invoiceData' => $invoiceData,
                'items' => $items,
            )
        );
    } else {
        echo json_encode(
            array(
                'status' => 'error',
            )
        );
    }
}
