<?php
include('config/db.php');
session_start();

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];

        if (isset($_POST['products'])) {
            $poArray = json_decode($_POST['products'], true);

            $productsAllTotal = 0;

            if (is_array($poArray) && !empty($poArray)) {

                $newDateTime = date("Y-m-d H:i:s");

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
                        !empty($product['cost_input'])
                        // !empty($product['cost_per_unit']) &&
                        // !empty($product['unit_s_price']) &&
                        // !empty($product['item_discount']) &&
                        // !empty($product['item_sale_price'])
                    ) {

                        $product_code = $product['product_code'];
                        $product_name = $product['product_name'];
                        $product_qty = $product['product_qty'];
                        $minimum_qty = $product['minimum_qty'] ?? 0;
                        $cost_input = $product['cost_input'];
                        $cost_per_unit = $product['cost_per_unit'] ?? 0;
                        $unit_s_price = $product['unit_s_price'] ?? 0;
                        $item_discount = $product['item_discount'] ?? 0;
                        $item_sale_price = $product['item_sale_price'] ?? 0;
                        $free_qty = $product['free_qty'] ?? 0;
                        $free_minimum_qty = $product['free_minimum_qty'] ?? 0;
                        $unit_barcode = $product['unit_barcode'] ?? '';

                        // Determine free quantity
                        $p_free_qty = $free_qty !== '' ? $free_qty : $free_minimum_qty;

                        // Add product cost to total
                        $productsAllTotal += $cost_input;

                        // Query stock once
                        $stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                            AND stock_item_cost = '$cost_input' AND added_discount = '$item_discount' AND stock_shop_id = '$shop_id'");

                        // Insert into grn_item
                        $conn->query("INSERT INTO grn_item (grn_number, grn_p_id, grn_p_qty, grn_p_cost, grn_p_price, p_plus_discount, p_free_qty)
                            VALUES ('$grn_number', '$product_code','$product_qty','$cost_input','$item_sale_price','$item_discount','$p_free_qty')");

                        // Insert into monthly_stock
                        $conn->query("INSERT INTO monthly_stock (item_code, item_name, qty, date_time, shop_id)
                            VALUES ('$product_code', '$product_name','$product_qty','$newDateTime','$shop_id')");

                        // Update or Insert into stock
                        if ($stock_result && $stock_result->num_rows > 0) {
                            $stock_data = $stock_result->fetch_assoc();
                            $update_qty = $stock_data["stock_item_qty"] + $product_qty;
                            $update_minimum_qty = $stock_data["stock_mu_qty"] + (int)$minimum_qty;

                            $conn->query("UPDATE stock2 SET stock_item_qty = '$update_qty', stock_mu_qty = '$update_minimum_qty'
                                WHERE stock_item_code = '$product_code' AND stock_shop_id = '$shop_id'");
                            echo "Stock Updated Successfully";
                        } else {
                            $conn->query("INSERT INTO stock2 (stock_item_code, stock_item_name, stock_item_qty, stock_item_cost, stock_mu_qty, unit_cost, unit_s_price, added_discount, item_s_price, stock_shop_id, stock_minimum_unit_barcode)
                                VALUES ('$product_code','$product_name','$product_qty','$cost_input','$minimum_qty','$cost_per_unit','$unit_s_price','$item_discount','$item_sale_price','$shop_id','$unit_barcode')");
                            echo "Successfully inserted new stock";
                        }
                    } else {
                        echo "Invalid product entry";
                    }
                }

                // Insert into GRN table
                $conn->query("INSERT INTO grn (grn_number,grn_date,grn_sub_total,grn_shop_id) 
                    VALUES ('$grn_number','$newDateTime','$productsAllTotal','$shop_id')");
            } else {
                echo "No products found or invalid data received.";
            }
        } else {
            echo "No 'products' parameter received in the POST request.";
        }
    }
}