<?php
include('config/db.php');
session_start();
$inArray = $_POST['products'];
// print_r($inArray);
$productsAllTotal = 0;
$balance = 0; // Initialize balance outside of the loop
$discount_percentage = 0;
$cardAmount = 0;
$invoiceNumber = "";

if (is_array($inArray) && !empty($inArray)) {
?>

    <!--//table header eka set krnwa-->
    <div class="col-12">

        <div class="row">

            <div class="col-3">
                <span class="product_cost">U.Price</span>
            </div>
            <div class="col-3 text-center">
                <span class="product_qty">
                    QTY
                </span>
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

        $sub_total = isset($product['sub_total']) ? $product['sub_total'] : 0;
        $discount_percentage = isset($product['discount_percentage']) ? $product['discount_percentage'] : 0;
        $net_total = isset($product['net_total']) ? $product['net_total'] : 0;

        $productTotal = doubleval($product_cost) * doubleval($product_qty);
        $productsAllTotal += $productTotal;

        if ($discount_percentage != 0) {
            $net_total = $productsAllTotal  * (1 - doubleval($discount_percentage) / 100);
        }
        if ($discount_percentage == "") {
            $discount_percentage = 0;
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
    <!-- total amount tika set krnwa -->
    <!-- footer for amounts -->
    <div class="col-12">
        <div class="row">
            <div>
                <div class="col-12 d-flex justify-content-end pt-2" style="border-top: #0e0e0e 0.2rem solid;">
                    <span class="productsAllTotal">Sub total : <?= $productsAllTotal ?></span>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end pt-2">
                <span class="productsAllTotal">Discount %: <?= $discount_percentage ?></span>
            </div>

            <div class="col-12 d-flex justify-content-end pt-2">
                <span class="productsAllTotal">Net Total : <?= $net_total ?></span>
            </div>
        </div>
    </div>

<?php
} else {
    echo "No products found or invalid data received.";
}
?>