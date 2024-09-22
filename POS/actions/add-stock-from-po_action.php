<?php
session_start();
include('config/db.php');

$poItemData = isset($_POST['poItemData']) ? json_decode($_POST['poItemData'], true) : [];

//  check if session is started
if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];

    $userId = $userData['id'];
    $shop_id = $userData['shop_id'];

    $poShopId;
    $product_code;
    $product_name;
    $ucv;
    $product_qty;
    $item_price;
    $manual_qty;

    if (is_array($poItemData) && !empty($poItemData)) {
        foreach ($poItemData as $product) {
            $poShopId = $product['poShopId'];
            $product_code = $product['product_code'];
            $product_name = $product['product_name'];
            $ucv = $product['ucv'];
            $product_qty = $product['product_qty'];
            $item_price = $product['item_price'];
            $manual_qty = $product['manual_qty'];

            if (
                !empty($product_code) && !empty($ucv) && !empty($item_price)
                && !empty($product_name) && is_numeric($product_qty)
            ) {

                $stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                AND item_s_price = '$item_price' AND stock_shop_id = '$poShopId'");

                $update_qty;
                $update_minimum_qty;

                if ($stock_result && $stock_result->num_rows > 0) {
                    $stock_data = $stock_result->fetch_assoc();
                    if ($stock_data["stock_item_qty"] < 1) {
                        $update_qty = $manual_qty;
                        $update_minimum_qty = $manual_qty * $ucv;
                    } else {
                        $minimum_qty = $manual_qty * $ucv;
                        $update_qty = $stock_data["stock_item_qty"] + $manual_qty;
                        $update_minimum_qty = $stock_data["stock_mu_qty"] + (int) $minimum_qty;
                    }
                    $conn->query("UPDATE stock2 SET stock_item_qty = '$update_qty', stock_mu_qty = '$update_minimum_qty'
                    WHERE stock_item_code = '$product_code' AND stock_shop_id = '$poShopId'");
                    echo "Stock Updated Successfully";
                } else {

                    $po_stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                    AND item_s_price = '$item_price' AND stock_shop_id = '$shop_id'");

                    $po_stock_data = $po_stock_result->fetch_assoc();

                    $cost_input = $po_stock_data["stock_item_cost"];
                    $cost_per_unit = $po_stock_data["unit_cost"];
                    $unit_s_price = $po_stock_data["unit_s_price"];

                    $item_discount = $po_stock_data["added_discount"];


                    $minimum_qty = $manual_qty * $ucv;

                    $conn->query("INSERT INTO stock2 (
                    stock_item_code, stock_item_name, stock_item_qty, stock_item_cost, stock_mu_qty, unit_cost, unit_s_price, added_discount, item_s_price, stock_shop_id)VALUES 
                    ('$product_code','$product_name','$manual_qty','$cost_input','$minimum_qty','$cost_per_unit','$unit_s_price','$item_discount','$item_price','$poShopId')");
                    echo "Successfully inserted new stock";
                }


                // if ($product_unit == 'kg' || $product_unit == 'l') {

                //     if ($item_price == $product_cost) {

                //         $total_qty = $product_qty * 1000;
                //         $sell_p_qty = ($total_qty / $ucv);

                //         $conn->query("UPDATE stock2 SET
                //         stock_item_qty = (stock_item_qty - $product_qty),
                //         stock_mu_qty = (stock_mu_qty - $total_qty)
                //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code'
                //         OR stock_minimum_unit_barcode = '$code')
                //         AND (item_s_price = '$product_cost'
                //         OR unit_s_price = '$product_cost')");
                //     } else {
                //         $conn->query("UPDATE stock2 SET
                //         stock_item_qty = (stock_item_qty - ROUND($product_qty / ($ucv * 1000), 3)),
                //         stock_mu_qty = (stock_mu_qty - $product_qty)
                //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' 
                //         OR stock_minimum_unit_barcode = '$code')
                //         AND unit_s_price = '$product_cost'");
                //     }
                // } else if ($product_unit == 'pieces') {
                //     $conn->query("UPDATE stock2 SET stock_item_qty = (stock_item_qty - $product_qty)
                //     WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost'");
                // } else if ($product_unit == 'g' || $product_unit == 'ml') {

                //     if ($item_price == $product_cost) {
                //         $total_qty = $product_qty * $ucv;
                //         $sell_p_qty = ($total_qty / $ucv);

                //         $conn->query("UPDATE stock2 SET
                //         stock_item_qty = (stock_item_qty - $sell_p_qty),
                //         stock_mu_qty = (stock_mu_qty - $total_qty)
                //         WHERE stock_shop_id = '$shop_id'
                //         AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                //         AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
                //     } else {
                //         $total_qty = $product_qty * 1000;
                //         $sell_p_qty = ($total_qty / $ucv);

                //         $conn->query("UPDATE stock2 SET
                //         stock_item_qty = (stock_item_qty - ROUND($product_qty / $ucv, 3)),
                //         stock_mu_qty = (stock_mu_qty - $product_qty)
                //         WHERE stock_shop_id = '$shop_id' 
                //         AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                //         AND unit_s_price = '$product_cost'");
                //     }
                // } else if ($product_unit == 'm') {

                //     if ($item_price == $product_cost) {
                //         $product_minimum_qty = $product_qty * 100;
                //         $conn->query("UPDATE stock2 SET
                //         stock_item_qty = (stock_item_qty -  $product_qty), 
                //         stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                //         WHERE stock_shop_id = '$shop_id'
                //         AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                //         AND item_s_price = '$product_cost'");
                //     } else {
                //         $product_minimum_qty = $product_qty;
                //         $conn->query("UPDATE stock2 SET
                //         stock_item_qty = ROUND((stock_mu_qty - $product_minimum_qty) / 100, 2), 
                //         stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                //         WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                //         AND unit_s_price = '$product_cost'");
                //     }
                // } else if ($product_unit == 'pack / bottle') {

                //     $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' 
                //     OR stock_minimum_unit_barcode = '$code')
                //     AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' 
                //     OR item_s_price = '$product_cost')");
                //     $qty_data = $qty_rs->fetch_assoc();
                //     $qd = $qty_data['stock_mu_qty'];
                //     $si = $qty_data['stock_item_qty'];
                //     $minimum_new_qty = $product_qty;

                //     $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $minimum_new_qty)
                //     WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' 
                //     OR stock_minimum_unit_barcode = '$code')
                //     AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
                // }
                // else {
                //     $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' 
                // OR stock_minimum_unit_barcode = '$code')
                // AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' 
                // OR item_s_price = '$product_cost')");

                //     $qty_data = $qty_rs->fetch_assoc();

                //     $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                //     echo $minimum_new_qty;

                //     $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                //     echo $new_minimum_qty;

                //     $new_stock_item_qty = ROUND($new_minimum_qty / $minimum_new_qty, 2);
                //     echo $new_stock_item_qty;

                //     $conn->query("UPDATE stock2 SET stock_item_qty = $new_stock_item_qty, 
                // stock_mu_qty = (stock_mu_qty - $product_qty)
                // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' 
                // OR stock_minimum_unit_barcode = '$code')
                // AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
                // }

            } // close data check if()
        }  // close for-each $itemData
    }  // poItemData[] end

}
