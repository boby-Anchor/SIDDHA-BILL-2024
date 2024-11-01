<?php
include('config/db.php');
session_start();

function printErrorLog($error_message)
{
    $error_log_path = $_SERVER['DOCUMENT_ROOT'] . "/s.ceylonhospitals.com/POS/error_log.txt";
    file_put_contents($error_log_path, $error_message, FILE_APPEND);
}

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $user_id = $userLoginData['id'] ?? null;
    $shop_id = $userLoginData['shop_id'] ?? null;
} else {
    echo json_encode(array(
        'status' => 'sessionExpired',
        'message' => 'Session expired. Wait to Login again.',
    ));
    exit();
}

if (isset($_POST['products'])) {

    $poArray = json_decode($_POST['products'], true);

    $grnTotalCost = 0;
    $grnTotalValue = 0;
    $newDateTime = date("Y-m-d H:i:s");
    // $grnItemErrorOccured;
    // $stockItemErrorOccured;
    $notificationMessage = "";
    // $grnItemErrorMessage = "";
    // $stockItemErrorMessage = "";

    if (is_array($poArray) && !empty($poArray)) {

        try {

            // Fetch GRN number
            $grn_number_result = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'grn'");
            $grn_number_data = $grn_number_result->fetch_assoc();
            $grn_number = "GRN-000" . $grn_number_data['AUTO_INCREMENT'];

            foreach ($poArray as $product) {

                if (
                    !empty($product['product_code']) &&
                    !empty($product['product_name']) &&
                    !empty($product['product_qty']) &&
                    // !empty($product['minimum_qty']) &&
                    // !empty($product['item_discount']) &&
                    !empty($product['item_price']) &&
                    // !empty($product['unit_s_price']) &&
                    !empty($product['total_cost']) &&
                    !empty($product['total_value'])
                    // !empty($product['free_qty'])
                ) {

                    $product_code = $product['product_code'];
                    $product_name = $product['product_name'];
                    $product_qty = $product['product_qty'];
                    $minimum_qty = $product['minimum_qty'] ?? 0;
                    $item_discount = $product['item_discount'] ?? 0;
                    $item_price = $product['item_price'];
                    $unit_s_price = $product['unit_s_price'] ?? 0;
                    $total_cost = $product['total_cost'];
                    $total_value = $product['total_value'];
                    $free_qty = $product['free_qty'] ?? 0;

                    // Add product cost to the totalcost
                    $grnTotalCost += $total_cost;
                    $grnTotalValue += $total_value;

                    // Query stock once
                    $stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                            AND item_s_price = '$item_price' AND stock_shop_id = '$shop_id'");

                    // Insert into grn_item
                    $grnItemInsertStatus = $conn->query("INSERT INTO grn_item (grn_number, grn_p_id, grn_p_qty, grn_p_cost, grn_p_price, p_plus_discount, p_free_qty)
                            VALUES ('$grn_number', '$product_code','$product_qty','$total_cost','$item_price','$item_discount','$free_qty')");

                    if (!$grnItemInsertStatus) {
                        $grnItemErrorOccured = true;
                        $error_message = "Error: " . $conn->error . "GRN item data insert failed. Date-" . $newDateTime . " GRN no-" . $grn_number . " Code-" . $product_code . ", Name-"
                            . $product_name . " Qty-" . $product_qty . " min qty-" . $minimum_qty . ' Discount-' . $item_discount . ' Unit Price-' . $unit_s_price . ' Total cost-' . $total_cost . ' Free qty-' . $free_qty . "\n";
                        printErrorLog($error_message);
                        $notificationMessage . "Failed " . $product_name . " Item insert. Check error_log.";
                    }

                    // Insert into monthly_stock
                    // $conn->query("INSERT INTO monthly_stock (item_code, item_name, qty, date_time, shop_id)
                    //     VALUES ('$product_code', '$product_name','$product_qty','$newDateTime','$shop_id')");

                    // Update or Insert into stock

                    if ($stock_result && $stock_result->num_rows > 0) {
                        $stock_data = $stock_result->fetch_assoc();
                        $update_qty = floatval($stock_data["stock_item_qty"]) + floatval($product_qty);
                        $update_minimum_qty = floatval($stock_data["stock_mu_qty"]) + floatval($minimum_qty);

                        $dataUpdateStatus = $conn->query("UPDATE stock2 SET stock_item_qty = '$update_qty', stock_mu_qty = '$update_minimum_qty', unit_s_price = '$unit_s_price'
                                WHERE stock_item_code = '$product_code' AND item_s_price='$item_price' AND stock_shop_id = '$shop_id'");
                        // echo "Stock Updated Successfully";

                        if ($dataUpdateStatus) {
                            $notificationMessage .= "Success " . $product_name . " updated.\n";
                        } else {
                            $stockItemErrorOccured = true;
                            $error_message = "Error: " . $conn->error . "Stock data update failed." . " Code-" . $product_code . ", Name-"
                                . $product_name . " Qty-" . $product_qty . " min qty-" . $minimum_qty . ' Discount-' . $item_discount . ' Unit Price-' . $unit_s_price . ' Total cost-' . $total_cost . ' Free qty-' . $free_qty . "\n";

                            printErrorLog($error_message);
                            $notificationMessage .= "Failed " . $product_name . " stock UPDATE. Check error_log.\n";
                        }
                    } else {
                        $dataInsertStatus = $conn->query("INSERT INTO stock2 (stock_item_code, stock_item_name, stock_item_qty, stock_mu_qty, unit_s_price, item_s_price, stock_shop_id)
                                VALUES ('$product_code','$product_name','$product_qty','$minimum_qty','$unit_s_price','$item_price','$shop_id')");
                        if ($dataInsertStatus) {
                            $notificationMessage .= "Success " . $product_name . " Inserted new stock.\n";
                        } else {
                            $stockItemErrorOccured = true;
                            $error_message = "Error: " . $conn->error . $grn_number . "Stock data insert failed of " . "Code-" . $product_code . ", Name-"
                                . $product_name . " qty-" . $product_qty . " min qty-" . $minimum_qty . ' discount-' . $item_discount . ' unit price-' . $unit_s_price . ' total cost-' . $total_cost . ' free qty-' . $free_qty . "\n";
                            printErrorLog($error_message);
                            $notificationMessage .= "Failed " . $product_name . " stock INSERT. Check error_log.\n";
                        }
                    }
                } else {
                    echo json_encode(array(
                        'status' => 'validationError',
                        'message' => 'Empty values passed. Re-check data'
                    ));
                }
            }

            // Insert into GRN table
            $grnInsertStatus = $conn->query("INSERT INTO grn (grn_number, grn_date, grn_shop_id, user_id, grn_sub_total, grn_total_value)
            VALUES ('$grn_number','$newDateTime', '$shop_id', '$user_id', '$grnTotalCost', '$grnTotalValue')");

            if ($grnInsertStatus) {
                $notificationMessage .= "Successfully inserted GRN.\n";
            } else {
                $grnErrorOccured = true;
                $error_message = "Error: " . $conn->error . "GRN insert failed. Date-" . $newDateTime . " GRN no-" . $grn_number . ' Shop id-' . $shop_id . ' User id-' . $user_id . ' Total cost-' . $grnTotalCost . ' Total cost-' . $grnTotalValue . "\n";
                printErrorLog($error_message);
                $notificationMessage .= "Failed. GRN insert error. Check error_log.\n";
            }
        } catch (Exception $exception) {
            $notificationMessage .= "ERROR: " . $exception->getMessage() . "\n";
        }
        echo json_encode(array(
            'status' => 'success',
            'message' => $notificationMessage
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'No products found or invalid data received.',
        ));
        exit();
        // echo "No products found or invalid data received.";
    }
} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'No Data received in the POST request.',
    ));
    exit();
    // echo "No Data received in the POST request.";
}
