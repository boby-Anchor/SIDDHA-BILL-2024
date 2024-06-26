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



                $product_code = $product['product_code'];
                $product_name = $product['product_name'];
                $product_brand = $product['product_brand'];
                $product_cost = floatval($product['product_cost']);
                $product_qty = intval($product['product_qty']);
                $unit = $product['qty_unit'];
                $orderNumber = $product["orderNumber"];
                $orderDate = $product["orderDate"];
                $orderTime = $product["orderTime"];

                // $conn->query("INSERT INTO test (c1,c2)VALUES ('$product_code,'$product_name')");


                if (
                    !empty($product['product_code']) && !empty($product['product_name'])   // && !empty($product['product_brand']) 
                    && !empty($product['product_cost']) && !empty($product['product_qty']) && !empty($product['qty_unit'])
                ) {


                    $orderDateTime = $product["orderDate"] . ' ' . $product["orderTime"];


                    $productTotal = $product_cost * $product_qty;

                    $productsAllTotal += $productTotal;


                    $conn->query("INSERT INTO hub_order (HO_number, HO_item, HO_brand,HO_qty, hub_order_unit, HO_price, HO_total,HO_shopId) 
                    VALUES ('$orderNumber', '$product_code', '$product_brand','$product_qty', '$unit', '$product_cost', '$productTotal','$shop_id')");
                    echo "success";
                } else {
                    echo "Invalid product entry ";
                }
            }


            $orderDateTimeObj = new DateTime($orderDateTime, new DateTimeZone('Asia/Colombo'));
            $dateTimeFormatted = $orderDateTimeObj->format('Y-m-d H:i:s');

            $conn->query("INSERT INTO hub_order_details (hub_order_number,hub_order_subTotal,hub_order_status,hub_order_paymentType,hub_order_paymentStatus,HO_date)
            VALUES ('$orderNumber','$productsAllTotal','1','1','1','$dateTimeFormatted')");
        } else {

            echo "No products found or invalid data received.";
        }
    }
}
