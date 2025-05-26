<?php
session_start();
include('../config/db.php');

$poItemData = isset($_POST['poItemData']) ? json_decode($_POST['poItemData'], true) : [];
$poNumber = isset($_POST['poNumber']) ? $_POST['poNumber'] : 0;

//  check if session is started
if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];

    $userId = $userData['id'];
    $shop_id = $userData['shop_id'];

    $poShopId;
    $product_code;
    $product_name;
    $ucv;
    $type;
    $product_qty;
    $item_price;
    $manual_qty;

    if (is_array($poItemData) && !empty($poItemData)) {
        foreach ($poItemData as $product) {
            $poShopId = $product['poShopId'];
            $product_code = $product['product_code'];
            $product_name = $product['product_name'];
            $ucv = $product['ucv'];
            $type = $product['type'];
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

                    if ($type == 'pieces' || $type == 'pack / bottle') {
                        $minimum_qty = 0;
                    } else if ($type == 'kg' || $type == 'l') {
                        $minimum_qty = $manual_qty * $ucv * 1000;
                    } else if ($type == 'g' || $type == 'ml') {
                        $minimum_qty = $manual_qty * $ucv;
                    } else if ($type == 'm') {
                        $minimum_qty = $manual_qty * $ucv * 100;
                    }

                    $po_stock_result = $conn->query("SELECT * FROM  stock2 WHERE stock_item_code = '$product_code'
                    AND item_s_price = '$item_price' AND stock_shop_id = '$shop_id'");

                    $po_stock_data = $po_stock_result->fetch_assoc();

                    $cost_input = $po_stock_data["stock_item_cost"];
                    $cost_per_unit = $po_stock_data["unit_cost"];
                    $unit_s_price = $po_stock_data["unit_s_price"];
                    $item_discount = $po_stock_data["added_discount"];

                    $conn->query("INSERT INTO stock2 (
                    stock_item_code, stock_item_name, stock_item_qty, stock_item_cost, stock_mu_qty, unit_cost, unit_s_price, added_discount, item_s_price, stock_shop_id)VALUES 
                    ('$product_code','$product_name','$manual_qty','$cost_input','$minimum_qty','$cost_per_unit','$unit_s_price','$item_discount','$item_price','$poShopId')");
                    echo "Successfully inserted new stock";
                }
            } // close data check if()
        }  // close for-each $itemData

        $conn->query("UPDATE poinvoices SET transferred = '1'
        WHERE invoice_id = '$poNumber'");
    }  // poItemData[] end

}
