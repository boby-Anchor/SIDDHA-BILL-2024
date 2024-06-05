<?php
include('config/db.php');
session_start();

if (isset($_POST['products'])) {
    $poArray = json_decode($_POST['products'], true);


    $productsAllTotal = 0;

    if (is_array($poArray) && !empty($poArray)) {

        $newDateTimeObj = new DateTime();
        $newDateTime = $newDateTimeObj->format('Y-m-d H:i:s');

        $grn_number_result = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = 'ceylriea_ts' AND table_name = 'grn'");
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
                $minimum_qty = $product['minimum_qty'];
                $cost_input = $product['cost_input'];
                $cost_per_unit = $product['cost_per_unit'];
                $unit_s_price = $product['unit_s_price'];
                $item_discount = $product['item_discount'];
                $item_sale_price = $product['item_sale_price'];
                $free_qty = $product['free_qty'];
                $free_minimum_qty = $product['free_minimum_qty'];
                $unit_barcode = $product['unit_barcode'];
                $p_free_qty;
                if ($free_qty == '') {
                    $p_free_qty = $free_minimum_qty;
                } else {
                    $p_free_qty = $free_qty;
                }

                $oneItemCost = $cost_input / ((int)$product_qty - (int)$free_qty);
                $productsAllTotal += $cost_input;


                $stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code' AND stock_item_name = '$product_name' AND stock_item_cost = '$oneItemCost' AND added_discount = '$item_discount' ");

                if (isset($_SESSION['store_id'])) {

                    $userLoginData = $_SESSION['store_id'];

                    foreach ($userLoginData as $userData) {
                        $shop_id = $userData['shop_id'];
                        $conn->query("INSERT INTO grn_item (grn_number, grn_p_id, grn_p_qty, grn_p_cost, grn_p_price, p_plus_discount, p_free_qty) VALUES ('$grn_number', '$product_code','$product_qty','$cost_input','$item_sale_price','$item_discount','$p_free_qty')");

                        if ($stock_result && $stock_result->num_rows > 0) {

                            $stock_data = $stock_result->fetch_assoc();
                            $update_qty = $stock_data["stock_item_qty"] + $product_qty;
                            $update_minimun_unit_qty = $stock_data["stock_mu_qty"] + (int)$minimum_qty;

                            $conn->query("UPDATE stock2 SET stock_item_qty = '$update_qty' , stock_mu_qty = '$update_minimun_unit_qty', stock_shop_id = '$shop_id' WHERE stock_item_code = '$product_code' AND stock_item_name = '$product_name' AND stock_item_cost = '$oneItemCost' AND stock_shop_id = '$shop_id'");
                            echo "Stock Update Successfully !";
                        } else {

                            $conn->query("INSERT INTO stock2 (stock_item_code,stock_item_name,stock_item_qty,stock_item_cost,stock_mu_qty,unit_cost,unit_s_price,added_discount,item_s_price,stock_shop_id,stock_minimum_unit_barcode)
                                                        VALUES ('$product_code','$product_name','$product_qty','$oneItemCost','$minimum_qty','$cost_per_unit','$unit_s_price','$item_discount','$item_sale_price','$shop_id','$unit_barcode')");
                            echo "successfully insert new stock";
                            // echo $unit_barcode;
                            echo $productsAllTotal;
                        }
                    }
                }
            } else {
                echo "Invalid product entry";
            }
        }
        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            foreach ($userLoginData as $userData) {
                $shop_id = $userData['shop_id'];

                $conn->query("INSERT INTO grn (grn_number,grn_date,grn_sub_total,grn_shop_id) VALUES ('$grn_number','$newDateTime','$productsAllTotal','$shop_id')");
            }
        }
    } else {
        echo "No products found or invalid data received.";
    }
} else {
    echo "No 'products' parameter received in the POST request.";
}
