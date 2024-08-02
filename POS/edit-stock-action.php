<?php
session_start();
include('config/db.php');

$poArray = json_decode($_POST['products'], true);
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");
$currentdatetime = $currentDate . " " . $currentTime;

$productsAllTotal = 0;

if (is_array($poArray) && !empty($poArray)) {

    foreach ($poArray as $product) {

        $stock_id = $product['stock_id'];
        $stock_shop_id = $product['stock_shop_id'];
        $product_code = $product['product_code'];

        $old_unit_cost = $product['old_unit_cost'];
        $old_added_discount = $product['old_added_discount'];
        $old_item_s_price = $product['old_item_s_price'];

        $product_name = $product['product_name'];
        $product_qty = $product['product_qty'];
        $minimum_qty = $product['minimum_qty'];
        $cost_input = $product['cost_input'];

        $item_discount = $product['item_discount'];
        $item_sale_price = $product['item_sale_price'];
        $cost_per_unit = $product['cost_per_unit'];
        $unit_s_price = $product['unit_s_price'];

        $item_discount = isset($item_discount) ? $item_discount : 0;
        $unit_s_price = isset($unit_s_price) ? $unit_s_price : 0;

        $stock_item_cost = (($cost_input * (100 - $item_discount)) / 100) / $product_qty;

        $conn->query("DELETE FROM stock2
        WHERE stock_item_code='$product_code'
        AND item_s_price= '$old_item_s_price'
        AND stock_shop_id = '$stock_shop_id'
        And stock_id != '$stock_id'
        ;
        ");

        $conn->query("UPDATE stock2 SET
        stock_item_name ='$product_name',
        stock_item_qty = '$product_qty',
        stock_mu_qty = '$minimum_qty',
        stock_item_cost = '$stock_item_cost',
        unit_cost = '$cost_per_unit',
        unit_s_price = '$unit_s_price',
        added_discount = '$item_discount',
        item_s_price = '$item_sale_price'
        WHERE
        stock_id = '$stock_id'
        ");

        $conn->query("INSERT INTO monthly_stock (stock_id, item_code, item_name, qty, date_time)
        VALUES ('$stock_id', '$product_code','$product_name','$product_qty','$currentdatetime')");
    }  // close for-each $poArrary 

} else {
    echo "No products found or invalid data received.";
}
