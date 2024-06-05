<?php
session_start();
include('config/db.php');

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

        $discountPercentage = $product['discountPercentage'];
        $deliveryCharges = $product['deliveryCharges'];
        $valueAddedServices = $product['valueAddedServices'];
        $cashAmount = $product['cashAmount'];
        $cardAmount = $product['cardAmount'];

        $paymentmethodselector = $product['paymentmethodselector'];
        $selectBillType = $product['selectBillType'];
        $currentDateTime = date("Y-m-d H:i:s");



        if (!empty($code) && !empty($product_name) && is_numeric($product_cost) && is_numeric($product_qty) && !empty($product_unit) && is_numeric($productTotal) && !empty($invoiceNumber)) {

            if (isset($_SESSION['store_id'])) {

                $userLoginData = $_SESSION['store_id'];

                foreach ($userLoginData as $userData) {
                    $userId = $userData['id'];
                    $shop_id = $userData['shop_id'];
                    $productsAllTotal += $productTotal;
                    $conn->query("INSERT INTO invoiceitems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total) VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");

                    if ($product_unit == 'kg' || $product_unit == 'l') {

                        $product_minimum_qty = $product_qty * 1000;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");
                    } else if ($product_unit == 'm') {

                        $product_minimum_qty = $product_qty * 100;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");
                    } else if ($product_unit == 'pack / bottle') {

                        $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                        $qty_data = $qty_rs->fetch_assoc();

                        $minimum_new_qty =  ($qty_data['stock_mu_qty'] / $qty_data['stock_item_qty']) * $product_qty;

                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");
                    } else {

                        $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                        $qty_data = $qty_rs->fetch_assoc();

                        // 700 / 7 = 100
                        $minimum_new_qty =  (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                        echo $minimum_new_qty;

                        // 700 - 50 = 650
                        $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                        echo $new_minimum_qty;

                        // 650 / 100 = 6.5
                        $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;
                        echo $new_stock_item_qty;

                        $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");
                    }
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
            $conn->query("INSERT INTO invoices (invoice_id, user_id, shop_id, created, bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance) VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$selectBillType', '$paymentmethodselector', '$productsAllTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount','$cardAmount', '$balance')");
        }
    }
} else {

    echo "No products found or invalid data received.";
}
