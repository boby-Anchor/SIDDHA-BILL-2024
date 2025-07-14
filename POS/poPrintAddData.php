<?php
$billData = json_decode($_POST['billData'], true);
$inArray = json_decode($_POST['products'], true);
$productsAllTotal = 0;
$balance = 0;
$sub_total;
$net_total;
$discount_percentage;

if (is_array($billData) && !empty($billData) && is_array($inArray) && !empty($inArray)) {
    foreach ($billData as $value) {
        $discount_percentage = isset($value['discount_percentage']) ? $value['discount_percentage'] : 0;
    }
?>

    <!-- set table header -->
    <div class="col-12">

        <div class="row">

            <div class="col-3">
                <span class="product_cost">U.Price</span>
            </div>
            <div class="col-3 text-center">
                <span class="product_qty">QTY</span>
            </div>
            <div class="col-3 text-center">
                <span class="productTotal">D.%</span>
            </div>
            <div class="col-3 text-center">
                <span class="productTotal">Total</span>
            </div>
        </div>
    </div>

    <?php
    foreach ($inArray as $product) {

        $product_code = isset($product['code']) ? $product['code'] : '';
        $product_name = isset($product['product_name']) ? $product['product_name'] : '';
        $product_brand = isset($product['brand']) ? $product['brand'] : '';
        $product_discount = isset($product['discount']) ? $product['discount'] : '';
        $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
        $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
        $product_unit = isset($product['product_unit']) ? $product['product_unit'] : '';

        $productTotal = doubleval($product_cost) * doubleval($product_qty);
        $productsAllTotal += $productTotal;

        $net_total = $productsAllTotal;

        if ($discount_percentage != 0) {
            $net_total = $productsAllTotal  * (1 - doubleval($discount_percentage) / 100);
        }
    ?>
        <!-- items of the invoice -->
        <div class="col-12">

            <div class="row">
                <div class="col-6">
                    <span class="product_name"><?= $product_name ?></span>
                </div>
                <div class="col-6">
                    <span class="product_cost"><?= $product_brand ?></span>
                </div>
            </div>
            <div class="row">

                <div class="col-3">
                    <span class="product_cost"><?= $product_cost ?></span>
                </div>
                <div class="col-3 text-center">
                    <span class="product_qty">
                        <?= $product_qty ?>
                    </span>
                </div>
                <div class="col-3 text-center">
                    <span class="productTotal"><?= $product_discount ?></span>
                </div>
                <div class="col-3 text-center">
                    <span class="productTotal"><?= $productTotal ?></span>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <!-- footer of total amounts -->
    <div class="col-12">
        <div class="row">
            <div>
                <div class="col-12 d-flex justify-content-end pt-2" style="border-top: #0e0e0e 0.2rem solid;">
                    <span class="productsAllTotal">Sub total : <?= $productsAllTotal ?></span>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end pt-2">
                <span class="discount">Discount : <?= $discount_percentage ?>%</span>
            </div>

            <div class="col-12 d-flex justify-content-end pt-2">
                <span class="netTotal">Net Total : <?= $net_total ?></span>
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end pt-2" style="border-top: #0e0e0e 0.2rem solid;"></div>
    </div>

<?php
} else {
    echo "No products found or invalid data received.";
}
?>