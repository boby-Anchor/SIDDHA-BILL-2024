<?php
session_start();
include('config/db.php');

$poArray = json_decode($_POST['products'], true);

$productsAllTotal = 0;

if (is_array($poArray) && !empty($poArray)) {

    $invoiceNumber;
    $userId;
    $shop_id;
    $currentDateTime;
    $selectBillType;
    $paymentmethodselector;
    $productsAllTotal;
    $discountPercentage;
    $deliveryCharges;
    $valueAddedServices;
    $cashAmount;
    $cardAmount;
    $balance;

    foreach ($poArray as $product) {

        $code = $product['code'];
        $ucv = $product['ucv'];
        $unit_price = $product['unit_price'];
        $item_price = $product['item_price'];
        $product_name = $product['product_name'];
        $product_cost = $product['product_cost'];
        $product_qty = $product['product_qty'];
        $product_unit = $product['product_unit'];
        $productTotal = $product['productTotal'];
        $invoiceNumber = $product['invoiceNumber'];

        $patientName = $product['patientName'];
        $contactNo = $product['contactNo'];
        $doctorName = $product['doctorName'];
        $regNo = $product['regNo'];

        $balance = $product['balance'];
        $discountPercentage = $product['discountPercentage'];
        $deliveryCharges = $product['deliveryCharges'];
        $valueAddedServices = $product['valueAddedServices'];
        $cashAmount = $product['cashAmount'];
        $cardAmount = $product['cardAmount'];
        $paymentmethodselector = $product['paymentmethodselector'];
        $selectBillType = $product['selectBillType'];
        $currentDateTime = date("Y-m-d H:i:s");

        $conn->query("INSERT INTO test (c1, c2) VALUES('$selectBillType', '1')");

        switch ($selectBillType) {

                // Normal Invoice
            case '1':
                $query = "SELECT invoice_id  FROM `invoices` WHERE invoice_id = '$invoiceNumber'";
                $cm = runQuery($query);

                if (empty($cm)) {
                    if (
                        !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                        is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($productTotal) && !empty($invoiceNumber)
                    ) {

                        if (isset($_SESSION['store_id'])) {

                            $userLoginData = $_SESSION['store_id'];

                            foreach ($userLoginData as $userData) {

                                $userId = $userData['id'];
                                $shop_id = $userData['shop_id'];
                                $productsAllTotal += $productTotal;


                                $conn->query("INSERT INTO invoiceitems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total)
                            VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");


                                if ($product_unit == 'kg' || $product_unit == 'l') {

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 1000 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                    stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                        // stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                        //                     $conn->query("UPDATE stock23 SET

                                        // stock_item_qty = CASE WHEN stock_item_qty >= :'$product_qty' THEN stock_item_qty - : '$product_qty' ELSE  '0' END,

                                        // stock_mu_qty = CASE WHEN stock_mu_qty >= :'$product_minimum_qty' THEN stock_mu_qty - : '$product_minimum_qty' ELSE  '0' END

                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //                         AND item_s_price = '$product_cost'");


                                    } else { //unit s price
                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        //  $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pieces') {

                                    //stock_item_qty = (stock_item_qty -  '$product_qty')
                                    $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                 WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");

                                } else if ($product_unit == 'g' || $product_unit == 'ml') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_unit', '1')");

                                    if ($item_price == $product_cost) { // item s price


                                        $product_minimum_qty = $product_qty * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        //stock_item_qty = (stock_item_qty -  '$product_qty')
                                        //  $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                        //  VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '3')");

                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                            AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'cm') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '2')");

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 100 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pack / bottle') {
                                    //stock_item_qty = (stock_item_qty -  '$product_qty')



                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();
                                    $qd = $qty_data['stock_mu_qty'];
                                    $si = $qty_data['stock_item_qty'];

                                    //   $conn->query("INSERT INTO `test`(`id`, `c1`, `c2`) VALUES ('$product_unit','$qd','$si')");


                                    $minimum_new_qty =  $product_qty;
                                    $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                                stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                 WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                    //                 stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    //                 AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");                


                                } else {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '4')");

                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();

                                    // 700 / 7 = 100
                                    $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                                    echo $minimum_new_qty;

                                    // 700 - 50 = 650
                                    $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                                    echo $new_minimum_qty;

                                    // 650 / 100 = 6.5
                                    $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;
                                    echo $new_stock_item_qty;

                                    $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");

                                    //   $conn->query("UPDATE stock23 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                    //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                }
                            }
                        }
                    } else {
                        echo "Invalid product entry";
                        exit;
                    }
                } else {
                    echo "DD";
                    exit;
                }
                break;

                // Online

                // Online Invoice
            case '2':
                $query = "SELECT invoice_id  FROM `onlineinvoices` WHERE invoice_id = '$invoiceNumber'";
                $cm = runQuery($query);

                if (empty($cm)) {
                    if (
                        !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                        is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($productTotal) && !empty($invoiceNumber)
                    ) {

                        if (isset($_SESSION['store_id'])) {

                            $userLoginData = $_SESSION['store_id'];

                            foreach ($userLoginData as $userData) {

                                $userId = $userData['id'];
                                $shop_id = $userData['shop_id'];
                                $productsAllTotal += $productTotal;


                                $conn->query("INSERT INTO onlineinvoiceitems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total)
                            VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");


                                if ($product_unit == 'kg' || $product_unit == 'l') {

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 1000 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                    stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                        // stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                        //                     $conn->query("UPDATE stock23 SET

                                        // stock_item_qty = CASE WHEN stock_item_qty >= :'$product_qty' THEN stock_item_qty - : '$product_qty' ELSE  '0' END,

                                        // stock_mu_qty = CASE WHEN stock_mu_qty >= :'$product_minimum_qty' THEN stock_mu_qty - : '$product_minimum_qty' ELSE  '0' END

                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //                         AND item_s_price = '$product_cost'");


                                    } else { //unit s price
                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        //  $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pieces') {

                                    //stock_item_qty = (stock_item_qty -  '$product_qty')
                                    $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                 WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");

                                } else if ($product_unit == 'g' || $product_unit == 'ml') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_unit', '1')");

                                    if ($item_price == $product_cost) { // item s price


                                        $product_minimum_qty = $product_qty * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        //stock_item_qty = (stock_item_qty -  '$product_qty')
                                        //  $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                        //  VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '3')");

                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                            AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'cm') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '2')");

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 100 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pack / bottle') {
                                    //stock_item_qty = (stock_item_qty -  '$product_qty')



                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();
                                    $qd = $qty_data['stock_mu_qty'];
                                    $si = $qty_data['stock_item_qty'];

                                    //   $conn->query("INSERT INTO `test`(`id`, `c1`, `c2`) VALUES ('$product_unit','$qd','$si')");


                                    $minimum_new_qty =  $product_qty;
                                    $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                                stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                 WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                    //                 stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    //                 AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");                


                                } else {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '4')");

                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();

                                    // 700 / 7 = 100
                                    $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                                    echo $minimum_new_qty;

                                    // 700 - 50 = 650
                                    $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                                    echo $new_minimum_qty;

                                    // 650 / 100 = 6.5
                                    $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;
                                    echo $new_stock_item_qty;

                                    $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");

                                    //   $conn->query("UPDATE stock23 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                    //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                }
                            }
                        }
                    } else {
                        echo "Invalid product entry";
                        exit;
                    }
                } else {
                    echo "DD";
                    exit;
                }
                break;


                // PO

                // PO Invoice
            case '3':
                $query = "SELECT invoice_id  FROM `poinvoices` WHERE invoice_id = '$invoiceNumber'";
                $cm = runQuery($query);

                if (empty($cm)) {
                    if (
                        !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                        is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($productTotal) && !empty($invoiceNumber)
                    ) {

                        if (isset($_SESSION['store_id'])) {

                            $userLoginData = $_SESSION['store_id'];

                            foreach ($userLoginData as $userData) {

                                $userId = $userData['id'];
                                $shop_id = $userData['shop_id'];
                                $productsAllTotal += $productTotal;


                                $conn->query("INSERT INTO poinvoiceitems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total)
                            VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");


                                if ($product_unit == 'kg' || $product_unit == 'l') {

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 1000 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                    stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                        // stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                        //                     $conn->query("UPDATE stock23 SET

                                        // stock_item_qty = CASE WHEN stock_item_qty >= :'$product_qty' THEN stock_item_qty - : '$product_qty' ELSE  '0' END,

                                        // stock_mu_qty = CASE WHEN stock_mu_qty >= :'$product_minimum_qty' THEN stock_mu_qty - : '$product_minimum_qty' ELSE  '0' END

                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //                         AND item_s_price = '$product_cost'");


                                    } else { //unit s price
                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        //  $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pieces') {

                                    //stock_item_qty = (stock_item_qty -  '$product_qty')
                                    $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                 WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");

                                } else if ($product_unit == 'g' || $product_unit == 'ml') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_unit', '1')");

                                    if ($item_price == $product_cost) { // item s price


                                        $product_minimum_qty = $product_qty * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        //stock_item_qty = (stock_item_qty -  '$product_qty')
                                        //  $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                        //  VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '3')");

                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                            AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'cm') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '2')");

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 100 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pack / bottle') {
                                    //stock_item_qty = (stock_item_qty -  '$product_qty')



                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();
                                    $qd = $qty_data['stock_mu_qty'];
                                    $si = $qty_data['stock_item_qty'];

                                    //   $conn->query("INSERT INTO `test`(`id`, `c1`, `c2`) VALUES ('$product_unit','$qd','$si')");


                                    $minimum_new_qty =  $product_qty;
                                    $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                                stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                 WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                    //                 stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    //                 AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");                


                                } else {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '4')");

                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();

                                    // 700 / 7 = 100
                                    $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                                    echo $minimum_new_qty;

                                    // 700 - 50 = 650
                                    $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                                    echo $new_minimum_qty;

                                    // 650 / 100 = 6.5
                                    $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;
                                    echo $new_stock_item_qty;

                                    $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");

                                    //   $conn->query("UPDATE stock23 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                    //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                }
                            }
                        }
                    } else {
                        echo "Invalid product entry";
                        exit;
                    }
                } else {
                    echo "DD";
                    exit;
                }
                break;

                // CH Medicine

                // CH Invoice
            case '4':
                $query = "SELECT invoice_id  FROM `chinvoices` WHERE invoice_id = '$invoiceNumber'";
                $cm = runQuery($query);

                if (empty($cm)) {
                    if (
                        !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                        is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($productTotal) && !empty($invoiceNumber)
                    ) {

                        if (isset($_SESSION['store_id'])) {

                            $userLoginData = $_SESSION['store_id'];

                            foreach ($userLoginData as $userData) {

                                $userId = $userData['id'];
                                $shop_id = $userData['shop_id'];
                                $productsAllTotal += $productTotal;


                                $conn->query("INSERT INTO chinvoiceitems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total)
                            VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");


                                if ($product_unit == 'kg' || $product_unit == 'l') {

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 1000 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                    stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                                        // stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                        //                     $conn->query("UPDATE stock23 SET

                                        // stock_item_qty = CASE WHEN stock_item_qty >= :'$product_qty' THEN stock_item_qty - : '$product_qty' ELSE  '0' END,

                                        // stock_mu_qty = CASE WHEN stock_mu_qty >= :'$product_minimum_qty' THEN stock_mu_qty - : '$product_minimum_qty' ELSE  '0' END

                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //                         AND item_s_price = '$product_cost'");


                                    } else { //unit s price
                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        //  $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pieces') {

                                    //stock_item_qty = (stock_item_qty -  '$product_qty')
                                    $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                 WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty - '$product_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");

                                } else if ($product_unit == 'g' || $product_unit == 'ml') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_unit', '1')");

                                    if ($item_price == $product_cost) { // item s price


                                        $product_minimum_qty = $product_qty * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        //stock_item_qty = (stock_item_qty -  '$product_qty')
                                        //  $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                        //  VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '3')");

                                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                            AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'cm') {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '2')");

                                    if ($item_price == $product_cost) { // item s price
                                        $product_minimum_qty = $product_qty * 100 * $ucv;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        //     AND item_s_price = '$product_cost'");

                                    } else { //unit s price
                                        $product_minimum_qty = $product_qty;
                                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");

                                        // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        // AND unit_s_price = '$product_cost' ");

                                    }
                                } else if ($product_unit == 'pack / bottle') {
                                    //stock_item_qty = (stock_item_qty -  '$product_qty')



                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();
                                    $qd = $qty_data['stock_mu_qty'];
                                    $si = $qty_data['stock_item_qty'];

                                    //   $conn->query("INSERT INTO `test`(`id`, `c1`, `c2`) VALUES ('$product_unit','$qd','$si')");


                                    $minimum_new_qty =  $product_qty;
                                    $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                                stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                 WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                                AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                    // $conn->query("UPDATE stock23 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                    //                 stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                    //  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    //                 AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");                


                                } else {

                                    // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                                    // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '4')");

                                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                                    $qty_data = $qty_rs->fetch_assoc();

                                    // 700 / 7 = 100
                                    $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                                    echo $minimum_new_qty;

                                    // 700 - 50 = 650
                                    $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                                    echo $new_minimum_qty;

                                    // 650 / 100 = 6.5
                                    $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;
                                    echo $new_stock_item_qty;

                                    $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");

                                    //   $conn->query("UPDATE stock23 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                                    //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


                                }
                            }
                        }
                    } else {
                        echo "Invalid product entry";
                        exit;
                    }
                } else {
                    echo "DD";
                    exit;
                }
                break;
        }

        // $query = "SELECT invoice_id  FROM `invoices` WHERE invoice_id = '$invoiceNumber'";
        // $cm = runQuery($query);

        // if (empty($cm)) {
        //     if (
        //         !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
        //         is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($productTotal) && !empty($invoiceNumber)
        //     ) {

        //         if (isset($_SESSION['store_id'])) {

        //             $userLoginData = $_SESSION['store_id'];

        //             foreach ($userLoginData as $userData) {

        //                 $userId = $userData['id'];
        //                 $shop_id = $userData['shop_id'];
        //                 $productsAllTotal += $productTotal;


        //                 $conn->query("INSERT INTO invoiceitems (invoiceNumber,invoiceDate,invoiceItem,invoiceItem_qty,invoiceItem_unit,invoiceItem_price,invoiceItem_total)
        //             VALUES ('$invoiceNumber','$currentDateTime','$product_name','$product_qty','$product_unit','$product_cost','$productTotal')");


        //                 if ($product_unit == 'kg' || $product_unit == 'l') {

        //                     if ($item_price == $product_cost) { // item s price
        //                         $product_minimum_qty = $product_qty * 1000 * $ucv;
        //                         $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
        //                     stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         AND item_s_price = '$product_cost'");

        //                         // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
        //                         // stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         //     AND item_s_price = '$product_cost'");

        //                         //                     $conn->query("UPDATE stock23 SET

        //                         // stock_item_qty = CASE WHEN stock_item_qty >= :'$product_qty' THEN stock_item_qty - : '$product_qty' ELSE  '0' END,

        //                         // stock_mu_qty = CASE WHEN stock_mu_qty >= :'$product_minimum_qty' THEN stock_mu_qty - : '$product_minimum_qty' ELSE  '0' END

        //                         // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         //                         AND item_s_price = '$product_cost'");


        //                     } else { //unit s price
        //                         // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
        //                         $product_minimum_qty = $product_qty;
        //                         $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                     AND unit_s_price = '$product_cost' ");

        //                         //  $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         // AND unit_s_price = '$product_cost' ");

        //                     }
        //                 } else if ($product_unit == 'pieces') {

        //                     //stock_item_qty = (stock_item_qty -  '$product_qty')
        //                     $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - '$product_qty')
        //                  WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");


        //                     // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty - '$product_qty')
        //                     //  WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");

        //                 } else if ($product_unit == 'g' || $product_unit == 'ml') {

        //                     // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
        //                     // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_unit', '1')");

        //                     if ($item_price == $product_cost) { // item s price


        //                         $product_minimum_qty = $product_qty * $ucv;
        //                         $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                                 WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                                 AND item_s_price = '$product_cost'");

        //                         // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         //     AND item_s_price = '$product_cost'");

        //                     } else { //unit s price
        //                         //stock_item_qty = (stock_item_qty -  '$product_qty')
        //                         //  $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
        //                         //  VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '3')");

        //                         // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
        //                         $product_minimum_qty = $product_qty;
        //                         $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                             WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                             AND unit_s_price = '$product_cost' ");

        //                         // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         // AND unit_s_price = '$product_cost' ");

        //                     }
        //                 } else if ($product_unit == 'cm') {

        //                     // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
        //                     // VALUES('$ucv', '$product_qty', '$shop_id', '$code', '$product_cost', '2')");

        //                     if ($item_price == $product_cost) { // item s price
        //                         $product_minimum_qty = $product_qty * 100 * $ucv;
        //                         $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         AND item_s_price = '$product_cost'");

        //                         // $conn->query("UPDATE stock23 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         //     AND item_s_price = '$product_cost'");

        //                     } else { //unit s price
        //                         $product_minimum_qty = $product_qty;
        //                         $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                     AND unit_s_price = '$product_cost' ");

        //                         // $conn->query("UPDATE stock23 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
        //                         // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                         // AND unit_s_price = '$product_cost' ");

        //                     }
        //                 } else if ($product_unit == 'pack / bottle') {
        //                     //stock_item_qty = (stock_item_qty -  '$product_qty')



        //                     $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                 AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
        //                     $qty_data = $qty_rs->fetch_assoc();
        //                     $qd = $qty_data['stock_mu_qty'];
        //                     $si = $qty_data['stock_item_qty'];

        //                     //   $conn->query("INSERT INTO `test`(`id`, `c1`, `c2`) VALUES ('$product_unit','$qd','$si')");


        //                     $minimum_new_qty =  $product_qty;
        //                     $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
        //                                 stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
        //                  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                                 AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


        //                     // $conn->query("UPDATE stock23 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
        //                     //                 stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
        //                     //  WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                     //                 AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");                


        //                 } else {

        //                     // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
        //                     // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '4')");

        //                     $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
        //                 AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
        //                     $qty_data = $qty_rs->fetch_assoc();

        //                     // 700 / 7 = 100
        //                     $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
        //                     echo $minimum_new_qty;

        //                     // 700 - 50 = 650
        //                     $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
        //                     echo $new_minimum_qty;

        //                     // 650 / 100 = 6.5
        //                     $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;
        //                     echo $new_stock_item_qty;

        //                     $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
        //                 WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");

        //                     //   $conn->query("UPDATE stock23 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
        //                     //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");


        //                 }
        //             }
        //         }
        //     } else {
        //         echo "Invalid product entry";
        //         exit;
        //     }
        // } else {
        //     echo "DD";
        //     exit;
        // }


    }  // close for-each $poArrary 


    switch ($selectBillType) {

            // Normal Invoice
        case '1':
            $query = "SELECT invoice_id  FROM `invoices` WHERE invoice_id = '$invoiceNumber'";
            $cm = runQuery($query);

            if (empty($cm)) {

                $conn->query("INSERT INTO invoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
        VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentmethodselector', '$productsAllTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount','$cardAmount', '$balance')");

                // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '003')");
            } else {
                echo "DD2";
                exit;
            }
            break;

            // online Invoice
        case '2':
            $query = "SELECT invoice_id  FROM `onlineinvoices` WHERE invoice_id = '$invoiceNumber'";
            $cm = runQuery($query);

            if (empty($cm)) {

                $conn->query("INSERT INTO onlineinvoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
    VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentmethodselector', '$productsAllTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount','$cardAmount', '$balance')");

                // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '003')");
            } else {
                echo "DD2";
                exit;
            }
            break;

            // PO Invoice
        case '3':
            $query = "SELECT invoice_id  FROM `poinvoices` WHERE invoice_id = '$invoiceNumber'";
            $cm = runQuery($query);

            if (empty($cm)) {

                $conn->query("INSERT INTO poinvoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
        VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentmethodselector', '$productsAllTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount','$cardAmount', '$balance')");

                // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '003')");
            } else {
                echo "DD2";
                exit;
            }
            break;

            // CH Invoice
        case '4':
            $query = "SELECT invoice_id  FROM `chinvoices` WHERE invoice_id = '$invoiceNumber'";
            $cm = runQuery($query);

            if (empty($cm)) {

                $conn->query("INSERT INTO chinvoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
        VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentmethodselector', '$productsAllTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount','$cardAmount', '$balance')");

                // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
                // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '003')");
            } else {
                echo "DD2";
                exit;
            }
            break;
    }

    // $query = "SELECT invoice_id  FROM `invoices` WHERE invoice_id = '$invoiceNumber'";
    // $cm = runQuery($query);

    // if (empty($cm)) {

    //     $conn->query("INSERT INTO invoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
    // VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentmethodselector', '$productsAllTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount','$cardAmount', '$balance')");

    //     // $conn->query("INSERT INTO test_table (col1, col2, col3, col4, col5, col6)
    //     // VALUES('$ucv', '$product_unit', '$item_price', '$product_qty', '$product_minimum_qty', '003')");
    // } else {
    //     echo "DD2";
    //     exit;
    // }

} else {

    echo "No products found or invalid data received.";
}
