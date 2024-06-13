<?php
session_start();
include ('config/db.php');

$poArray = json_decode($_POST['products'], true);

$productsAllTotal = 0;
if (is_array($poArray) && !empty($poArray)) {
    foreach ($poArray as $product) {

        $code = $product['code'];
        $product_name = $product['product_name'];
        $product_cost = $product['product_cost'];
        $product_qty = $product['product_qty'];
        $product_unit = $product['product_unit'];
        $productTotal = $product['productTotal'];
        $invoiceNumber = $product['invoiceNumber'];
        $balance = $product['balance'];
        $enterAmountFiled = $product['enterAmountFiled'];
        $currentDateTime = date("Y-m-d H:i:s");


        if (!empty($code) && !empty($product_name) && is_numeric($product_cost) && is_numeric($product_qty) && !empty($product_unit) && is_numeric($productTotal) && !empty($invoiceNumber)) {

            if (isset($_SESSION['store_id'])) {

                $userLoginData = $_SESSION['store_id'];

                foreach ($userLoginData as $userData) {
                    $userId = $userData['id'];
                    $shop_id = $userData['shop_id'];
                    $productsAllTotal += $productTotal;
                    $conn->query("INSERT INTO invoiceItems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total) VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");
                    $conn->query("UPDATE stock2 SET stock_qty = stock_qty -  '$product_qty' WHERE stock_shop_id = '$shop_id' AND stock_item_id = '$code'");
                }
            }
        } else {

            echo "Invalid product entry";
            exit;
        }
    }

    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {
            $userId = $userData['id'];
            $shop_id = $userData['shop_id'];
            $conn->query("INSERT INTO invoices (invoice_id,user_id,total_amount,payment_method,created,balance,paidAmount) VALUES ('$invoiceNumber','$userId','$productsAllTotal','1','$currentDateTime','$balance','$enterAmountFiled')");
        }

    }
} else {

    echo "No products found or invalid data received.";
}
