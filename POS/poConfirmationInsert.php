<?php
session_start();
include('config/db.php');
$poArray = json_decode($_POST['products'], true);
$productsAllTotal = 0;

echo "Length of poArray: " . count($poArray) . "<br>";

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {

        $shop_id = $userData['shop_id'];

        if (is_array($poArray) && !empty($poArray)) {
            foreach ($poArray as $product) {

                if (!empty($product['product_code']) && !empty($product['product_name']) && !empty($product['product_cost']) && !empty($product['product_qty']) && !empty($product['qty_unit'])) {

                    $product_code = $product['product_code'];
                    $product_name = $product['product_name'];
                    $product_cost = floatval($product['product_cost']);
                    $product_qty = intval($product['product_qty']);
                    $unit = $product['qty_unit'];
                    $orderNumber = $product["orderNumber"];
                    $orderDate = $product["orderDate"];
                    $orderTime = $product["orderTime"];

                    $orderDateTime = $product["orderDate"] . ' ' . $product["orderTime"];
                    $orderDateTimeObj = new DateTime($orderDateTime, new DateTimeZone('Asia/Colombo'));
                    $dateTimeFormatted = $orderDateTimeObj->format('Y-m-d H:i:s');

                    $productTotal = $product_cost * $product_qty;

                    $productsAllTotal += $productTotal;
                    $conn->query("INSERT INTO hub_order (HO_number, HO_date, HO_item, HO_qty, hub_order_unit, HO_price, HO_total,HO_shopId) VALUES ('$orderNumber', '$dateTimeFormatted', '$product_code', '$product_qty', '$unit', '$product_cost', '$productTotal','$shop_id')");
                    echo "success";
                } else {
                    echo "Invalid product entry";
                }
            }
            $conn->query("INSERT INTO hub_order_details (hub_order_number,hub_order_subTotal,hub_order_status,hub_order_paymentType,hub_order_paymentStatus) VALUES ('$orderNumber','$productsAllTotal','1','1','1')");
        } else {
            echo "No products found or invalid data received.";
        }
    }
}
