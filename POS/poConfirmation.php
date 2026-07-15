<?php

$poArray = $_POST['products'];
$productsAllTotal = 0;

if (is_array($poArray) && !empty($poArray)) {
    foreach ($poArray as $product) {

        $product_code = isset($product['product_code']) ? $product['product_code'] : '';
        $product_name = isset($product['product_name']) ? $product['product_name'] : '';
        $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
        $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
        $unit = isset($product['unit']) ? $product['unit'] : '';

        $productTotal = intval($product_cost) * intval($product_qty);

        $productsAllTotal += $productTotal;

?>
        <tr>
            <th scope="row" class="product_code"><?= $product_code ?></th>
            <td class="product_name"><?= $product_name ?></td>
            <td class="product_cost"><?= $product_cost ?></td>
            <td style="width:60px;"><span class="product_qty"><?= $product_qty ?></span><span class="qty_unit"><?= $unit ?></span></td>
            <td class="productTotal"><?= $productTotal ?>.00</td>
        </tr>

    <?php
    }
    ?>
    <tr>
        <td colspan="4"></td>
        <td class="order-confirmation-total"><?= $productsAllTotal ?>.00</td>
    </tr>
<?php
} else {
    echo "No products found or invalid data received.";
}
