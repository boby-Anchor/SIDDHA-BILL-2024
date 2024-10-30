<?php
include('config/db.php');
session_start();

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $userId = $userData['id'];
        $shop_id = $userData['shop_id'];

        if (isset($_POST['products'])) {
            $poArray = json_decode($_POST['products'], true);

            $productsAllTotal = 0;

            if (is_array($poArray) && !empty($poArray)) {

                $newDateTime = date("Y-m-d H:i:s");
                $grnTotalCost;
                $grnItemErrorOccured;
                $stockItemErrorOccured;
                $notificationMessage = "";
                $notificationErrorMessage = "";

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
                        !empty($product['total_cost'])
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
                        $free_qty = $product['free_qty'] ?? 0;

                        // Add product cost to the totalcost
                        $grnTotalCost += $total_cost;

                        // Query stock once
                        $stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                            AND item_s_price = '$item_price' AND stock_shop_id = '$shop_id'");

                        // Insert into grn_item
                        $result = $conn->query("INSERT INTO grn_item (grn_number, grn_p_id, grn_p_qty, grn_p_cost, grn_p_price, p_plus_discount, p_free_qty)
                            VALUES ('$grn_number', '$product_code','$product_qty','$total_cost','$item_price','$item_discount','$free_qty')");



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


                        } else {
                            $dataInsertStatus = $conn->query("INSERT INTO stock2 (stock_item_code, stock_item_name, stock_item_qty, stock_mu_qty, unit_s_price, item_s_price, stock_shop_id)
                                VALUES ('$product_code','$product_name','$product_qty','$minimum_qty','$unit_s_price','$item_price','$shop_id')");
                            // echo "Successfully inserted new stock";

                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'badu na yako.',
                        ));
                        // echo "Invalid product entry";
                    }
                }

                // Insert into GRN table
                $conn->query("INSERT INTO grn (grn_number, grn_date, grn_shop_id, user_id, grn_sub_total) 
                     VALUES ('$grn_number','$newDateTime', '$shop_id', '$user_id', '$productsAllTotal')");
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'No products found or invalid data received.',

                ));
                // echo "No products found or invalid data received.";
            }
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'No Data received in the POST request.',

            ));
            // echo "No Data received in the POST request.";
        }
    }
}
