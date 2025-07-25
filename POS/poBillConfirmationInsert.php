<?php
session_start();
include('config/db.php');

$user_id;
$shop_id;

try {
    if (isset($_SESSION['store_id'])) {
        $userData = $_SESSION['store_id'][0];
        $user_id = $userData['id'];
        $shop_id = $userData['shop_id'];
    } else {
        echo json_encode(array(
            'status' => 'session_expired',
            'message' => 'Session expired. Wait to login again.',
        ));
        exit();
    }

    $invoice_number = isset($_POST['invoice_number']) && !empty($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
    $billData = json_decode($_POST['billData'], true);
    $poArray = json_decode($_POST['products'], true);

    // if (runQuery("SELECT invoice_id  FROM `poinvoices` WHERE invoice_id = '$invoice_number'")) {
    //     echo json_encode(array(
    //         'status' => 'error',
    //         'message' => 'Invoice ID already exists.',
    //     ));
    //     exit();
    // }

    $productsAllTotal = 0;
    $po_shop_id;
    $sub_total;
    $discount_percentage;
    $net_total;
    $currentDateTime = date("Y-m-d H:i:s");

    if (!is_null($invoice_number) && is_array($billData) && !empty($billData) && is_array($poArray) && !empty($poArray)) {

        foreach ($billData as $value) {
            $po_shop_id = $value['po_shop_id'];
            $sub_total = $value['sub_total'];
            $discount_percentage = $value['discount_percentage'];
            $net_total = $value['net_total'];
        }

        foreach ($poArray as $product) {

            $code = $product['code'];
            $ucv = $product['ucv'];
            $unit_price = $product['unit_price'];
            $item_price = $product['item_price'];
            $product_name = $product['product_name'];
            $product_cost = $product['product_cost'];
            $product_qty = $product['product_qty'];
            $product_unit = $product['product_unit'];
            $product_total = $product['product_total'];

            $discount_percentage = isset($discount_percentage) ? $discount_percentage : 0;

            if (
                !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($product_total) && !empty($invoice_number)
            ) {

                $productsAllTotal += $product_total;

                $conn->query("INSERT INTO poinvoiceitems (invoiceNumber,  item_code, invoiceItem, invoiceItem_qty, invoiceItem_unit, invoiceItem_price, invoiceItem_total)
                        VALUES ('$invoice_number','$code','$product_name','$product_qty','$product_unit','$product_cost','$product_total')");

                if ($product_unit == 'kg' || $product_unit == 'l') {

                    if ($item_price == $product_cost) { // item s price
                        $product_minimum_qty = $product_qty * 1000 * $ucv;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') ,
                            stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND item_s_price = '$product_cost'");
                    } else { //unit s price
                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                        $product_minimum_qty = $product_qty;
                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                            AND unit_s_price = '$product_cost' ");
                    }
                } else if ($product_unit == 'pieces') {

                    //stock_item_qty = (stock_item_qty -  '$product_qty')
                    $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - '$product_qty')
                         WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");
                } else if ($product_unit == 'g' || $product_unit == 'ml') {

                    if ($item_price == $product_cost) { // item s price

                        $product_minimum_qty = $product_qty * $ucv;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost'");
                    } else {

                        // ((stock_mu_qty - '$product_minimum_qty') / '$ucv')
                        $product_minimum_qty = $product_qty;
                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty') / '$ucv', 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost' ");
                    }
                } else if ($product_unit == 'm') {

                    if ($item_price == $product_cost) { // item s price
                        $product_minimum_qty = $product_qty * 100 * $ucv;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  '$product_qty') , stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                AND item_s_price = '$product_cost'");
                    } else { //unit s price
                        $product_minimum_qty = $product_qty;
                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - '$product_minimum_qty'), 2), stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                            AND unit_s_price = '$product_cost' ");
                    }
                } else if ($product_unit == 'pack / bottle') {
                    //stock_item_qty = (stock_item_qty -  '$product_qty')

                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                            AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                    $qty_data = $qty_rs->fetch_assoc();
                    $qd = $qty_data['stock_mu_qty'];
                    $si = $qty_data['stock_item_qty'];

                    $minimum_new_qty = $product_qty;
                    $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty) ,
                                        stock_mu_qty = (stock_mu_qty - '$minimum_new_qty')
                                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                        AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");
                } else {

                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                            AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost' )");
                    $qty_data = $qty_rs->fetch_assoc();

                    // 700 / 7 = 100
                    $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);

                    // 700 - 50 = 650
                    $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;

                    // 650 / 100 = 6.5
                    $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;

                    $conn->query("UPDATE stock2 SET stock_item_qty = '$new_stock_item_qty' , stock_mu_qty = (stock_mu_qty - '$product_qty')
                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code') AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost' ");
                }
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Invalid product entry',
                ));
            }
        }  // close for-each $poArray

        $query = "INSERT INTO poinvoices (invoice_id, user_id, shop_id, po_shop_id, created, sub_total, discount_percentage, net_total)
        VALUES ('$invoice_number', '$user_id', '$shop_id', '$po_shop_id', '$currentDateTime', '$sub_total', '$discount_percentage', '$net_total')";

        if ($conn->query($query)) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Order Placed Successfully!',
            ));
        } else {
            $error = $conn->error;
            error_log("Error in inserting invoice: " . $error);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Invoice saving error',
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'No products found or invalid data received.',
        ));
        exit();
    }
} catch (Throwable $th) {
    echo json_encode(array(
        'status' => 'error',
        'message' => $th->getMessage(),
    ));
}