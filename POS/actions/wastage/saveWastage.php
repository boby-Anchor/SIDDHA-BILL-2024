<?php
session_start();
require_once "../../config/db.php";

$user_id;
$shop_id;
$wastageBatchId;

try {
    if (isset($_SESSION['store_id'])) {
        $userData = $_SESSION['store_id'][0];
        $user_id = $userData['id'];
        $shop_id = $userData['shop_id'];

        $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'wastage_batches'");
        $invoiceId_row = $invoiceId_rs->fetch_assoc();
        $wastageBatchId = $invoiceId_row['AUTO_INCREMENT'];
    } else {
        echo json_encode(array(
            'status' => 'session_expired',
            'message' => 'Session expired. Wait to login again.',
        ));
        exit();
    }

    $billData = json_decode($_POST['billData'], true);
    $products = json_decode($_POST['products'], true);

    $sub_total = 0;
    $wastageDescription;
    $wastageReasonSelect;
    $currentDateTime = date("Y-m-d H:i:s");

    if (!is_null($wastageBatchId) && is_array($billData) && !empty($billData) && is_array($products) && !empty($products)) {

        foreach ($billData as $value) {
            $sub_total = $value['sub_total'];
            $wastageDescription = $value['wastageDescription'];
            $wastageReasonSelect = $value['wastageReasonSelect'];
        }

        foreach ($products as $product) {

            $code = $product['code'];
            $ucv = $product['ucv'];
            $unit_price = $product['unit_price'];
            $item_price = $product['item_price'];
            $product_name = $product['product_name'];
            $product_cost = $product['product_cost'];
            $product_qty = $product['product_qty'];
            $product_unit = $product['product_unit'];
            $product_total = $product['product_total'];

            if (
                !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($product_total)
            ) {

                $conn->query("INSERT INTO wastage_batch_items (wastage_batch_id, barcode, qty, item_price, total_price)
                        VALUES ('$wastageBatchId','$code','$product_qty','$item_price','$product_total')");

                if ($product_unit == 'kg' || $product_unit == 'l') {

                    if ($item_price == $product_cost) {
                        $product_minimum_qty = $product_qty * 1000 * $ucv;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  $product_qty) ,
                            stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost'");
                    } else {
                        $product_minimum_qty = $product_qty;
                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - $product_minimum_qty) / $ucv, 2), stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND unit_s_price = '$product_cost' ");
                    }
                } else if ($product_unit == 'pieces') {

                    $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - $product_qty)
                         WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' ");
                } else if ($product_unit == 'g' || $product_unit == 'ml') {

                    if ($item_price == $product_cost) {
                        $product_minimum_qty = $product_qty * $ucv;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  $product_qty) , stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost'");
                    } else {
                        $product_minimum_qty = $product_qty;
                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - $product_minimum_qty) / $ucv, 2), stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND unit_s_price = '$product_cost' ");
                    }
                } else if ($product_unit == 'm') {
                    if ($item_price == $product_cost) {
                        $product_minimum_qty = $product_qty * 100 * $ucv;
                        $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty -  $product_qty) , stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost'");
                    } else {
                        $product_minimum_qty = $product_qty;
                        $conn->query("UPDATE stock2 SET stock_item_qty = ROUND((stock_mu_qty - $product_minimum_qty) / $ucv, 2), stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND unit_s_price = '$product_cost' ");
                    }
                } else if ($product_unit == 'pack / bottle') {
                    $minimum_new_qty = $product_qty;
                    $conn->query("UPDATE stock2 SET 
                        stock_item_qty = (stock_item_qty - $product_qty)
                        WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost'");
                } else {
                    $qty_rs = $conn->query("SELECT * FROM stock2 WHERE stock_item_code = '$code'
                            AND stock_shop_id = '$shop_id' AND item_s_price = '$product_cost'");
                    $qty_data = $qty_rs->fetch_assoc();

                    // 700 / 7 = 100
                    $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);

                    // 700 - 50 = 650
                    $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;

                    // 650 / 100 = 6.5
                    $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;

                    $conn->query("UPDATE stock2 SET stock_item_qty = $new_stock_item_qty, stock_mu_qty = (stock_mu_qty - $product_qty)
                        WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost'");
                }
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Invalid product entry',
                ));
            }
        }

        $query = "INSERT INTO wastage_batches (wastage_reason_id, description, created_by, shop_id, created, total_value)
        VALUES ('$wastageReasonSelect', '$wastageDescription', '$user_id', '$shop_id', '$currentDateTime', '$sub_total')";

        if ($conn->query($query)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Saved Successfully!',
            ]);
        } else {
            $error = $conn->error;
            error_log("Error in inserting data: " . $error);
            echo json_encode([
                'status' => 'error',
                'message' => 'Saving error',
            ]);
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
