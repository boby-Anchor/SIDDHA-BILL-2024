<?php

$poArray = $_POST['products'];
$productsAllTotal = 0;

if (is_array($poArray) && !empty($poArray)) {
    foreach ($poArray as $product) {

        $product_code = isset($product['product_code']) ? $product['product_code'] : '';
        $product_name = isset($product['product_name']) ? $product['product_name'] : '';
        $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
        $minimum_qty = isset($product['minimum_qty']) ? $product['minimum_qty'] : '';
        $cost_input = isset($product['cost_input']) ? $product['cost_input'] : '';
        $cost_per_unit = isset($product['cost_per_unit']) ? $product['cost_per_unit'] : 0.0;
        $unit_s_price = isset($product['unit_s_price']) ? doubleval($product['unit_s_price']) : 0.0;

        $item_discount = isset($product['item_discount']) ? $product['item_discount'] : '';
        $item_sale_price = isset($product['item_sale_price']) ? doubleval($product['item_sale_price']) : '';

        $free_qty = isset($product['free_qty']) ? $product['free_qty'] : '';
        $free_minimum_qty = isset($product['free_minimum_qty']) ? $product['free_minimum_qty'] : '';

        $manual_unit_input = isset($product['manual_unit_input']) ? $product['manual_unit_input'] : '';
        $free_manual_unit_input = isset($product['free_manual_unit_input']) ? $product['free_manual_unit_input'] : '';
        $unit_barcode = isset($product['unit_barcode']) ? $product['unit_barcode'] : '';

        $p_qty = (int)$product_qty + (int)$free_qty;

?>
        <tr>
            <th scope="row" class="product_code"><?= $product_code ?></th>
            <td class="product_name"><?= $product_name ?></td>
            <td class="product_qty"><?= $p_qty ?></td>
            <!-- <td class="minimum_qty"><?= $p_minimum_qty ?></td> -->
            <td class="minimum_qty">
                <?php
                if ($minimum_qty == '') {
                    $p_manual_minimum_qty = (int)$manual_unit_input + (int)$free_manual_unit_input;
                    echo $p_manual_minimum_qty;
                } else {
                    $p_minimum_qty = (int)$minimum_qty + (int)$free_minimum_qty;
                    echo $p_minimum_qty;
                }
                ?>
            </td>
            <td class="cost_input"><?= $cost_input ?></td>
            <td class="cost_per_unit"><?= $cost_per_unit ?></td>
            <td class="unit_s_price"><?= $unit_s_price ?></td>
            <td class="item_discount"><?= $item_discount ?></td>
            <td class="item_sale_price"><?= $item_sale_price ?></td>
            <td class="free_qty d-none"><?= $free_qty ?></td>
            <td class="unit_barcode d-none"><?= $unit_barcode ?></td>
        </tr>

    <?php
    }
    ?>

<?php
} else {
    echo "No products found or invalid data received.";
}
