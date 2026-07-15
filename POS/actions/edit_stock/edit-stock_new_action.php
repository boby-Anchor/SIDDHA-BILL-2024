<?php
include('../../config/db.php');
session_start();

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $user_id = $userLoginData['id'] ?? null;
    $shop_id = $userLoginData['shop_id'] ?? null;
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired. Wait to Login again.',
    ]);
    exit();
}

if (!isset($_POST['products'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No Data received in the POST request.',
    ]);
    exit();
    // No Data received in the POST request.
}

$poArray = json_decode($_POST['products'], true);

$errorOccurred = false;
$notificationMessage = "";

if (!is_array($poArray) || empty($poArray)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No products found or invalid data received.',
    ]);
    exit();
    // No products found or invalid data received.
}

try {

    foreach ($poArray as $product) {

        if (
            !empty($product['product_code']) &&
            !empty($product['product_name']) &&
            !empty($product['product_qty']) &&
            !empty($product['item_price'])
        ) {

            $product_code = $product['product_code'];
            $product_name = $product['product_name'];
            $product_qty = $product['product_qty'];
            $minimum_qty = $product['minimum_qty'] ?? 0;
            $unit_s_price = 0;
            $item_price = $product['item_price'];

            // Insert into stock

            $stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                    AND item_s_price = '$item_price' AND stock_shop_id = '$shop_id'");

            if ($stock_result && $stock_result->num_rows > 0) {
                try {
                    $stock_data = $stock_result->fetch_assoc();

                    // if ($stock_data["stock_item_qty"] < 0) {
                    $updated_qty = floatval($product_qty);
                    $updated_minimum_qty = floatval($minimum_qty);
                    // } else {
                    //     $updated_qty = floatval($product_qty) + floatval($stock_data["stock_item_qty"]);
                    //     $updated_minimum_qty = floatval($minimum_qty) + floatval($stock_data["stock_mu_qty"]);
                    // }

                    // $updated_qty = floatval($stock_data["stock_item_qty"]) + floatval($product_qty);
                    // $updated_minimum_qty = floatval($stock_data["stock_mu_qty"]) + floatval($minimum_qty);

                    $conn->query("UPDATE stock2 SET stock_item_qty = '$updated_qty', stock_mu_qty = '$updated_minimum_qty', is_updated = '1'
                            WHERE stock_item_code = '$product_code' AND item_s_price='$item_price' AND stock_shop_id = '$shop_id'");
                    $notificationMessage .= "Success " . $product_name . " updated.\n";
                } catch (Exception $exception) {
                    $errorOccurred = true;
                    error_log($exception->getMessage());
                    $notificationMessage .= "Failed " . $product_name . " stock UPDATE. Check error_log.\n";
                }
            } else {
                try {
                    $conn->query("INSERT INTO stock2 (stock_item_code, stock_item_name, stock_item_qty, stock_mu_qty, item_s_price, unit_s_price, stock_shop_id, is_updated)
                                VALUES ('$product_code','$product_name','$product_qty','$minimum_qty', '$item_price', '$unit_s_price', '$shop_id', '1')");
                    $notificationMessage .= "Success " . $product_name . " Inserted new stock.\n";
                } catch (Exception $exception) {
                    $errorOccurred = true;
                    $error_message = "Error: " . $conn->error . " " . $exception->getMessage() . " " . "Stock data insert failed of Code-" . $product_code . ", Name-"
                        . $product_name . " qty-" . $product_qty . " min qty-" . $minimum_qty . ' unit price-' . $unit_s_price . ' total cost-' . $total_cost . "\n";
                    error_log($error_message);
                    $notificationMessage .= "Failed " . $product_name . " stock INSERT. Check error_log.\n";
                }
            }
        }
    }
} catch (Exception $exception) {
    $errorOccurred = true;
    $error_message = "ERROR: " . $exception->getMessage() . "\n";
    error_log($error_message);
    $notificationMessage .= $exception->getMessage();
} finally {
    if ($errorOccurred) {
        echo json_encode([
            'status' => 'error',
            'message' => $error_message
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => $notificationMessage
        ]);
    }
}
