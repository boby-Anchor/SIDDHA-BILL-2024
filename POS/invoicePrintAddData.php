<?php
include('config/db.php');
session_start();

$itemData = isset($_POST['itemData']) ? json_decode($_POST['itemData'], true) : [];
$dMData = isset($_POST['dMData']) ? json_decode($_POST['dMData'], true) : [];
$billData = isset($_POST['billData']) ? json_decode($_POST['billData'], true) : [];

if (is_array($dMData) && !empty($dMData)) {
    foreach ($dMData as $product) {
        $product_name = isset($product['product_name']) ? $product['product_name'] : '';
        $item_price = isset($product['item_price']) ? $product['item_price'] : '';
?>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <span class="product_name"><?= $product_name ?></span>
                </div>
                <div class="col-4">
                    <span class="product_cost"><?= $item_price ?></span>
                </div>
                <div class="col-4 text-center">
                    <span class="product_qty">
                        1
                    </span>
                </div>
                <div class="col-4 text-center">
                    <span class="productTotal"><?= $item_price ?></span>
                </div>
            </div>
        </div>
        <?php
    }
}

if (is_array($itemData) && !empty($itemData)) {
    foreach ($itemData as $product) {

        $isPaththu = $product['isPaththu'];
        // $product_code = isset($product['code']) ? $product['code'] : '';
        $product_name = isset($product['product_name']) ? $product['product_name'] : '';
        $product_brand = isset($product['product_brand']) ? $product['product_brand'] : '';
        $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
        $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
        $product_unit = isset($product['product_unit']) ? $product['product_unit'] : '';
        $productTotal = isset($product['productTotal']) ? $product['productTotal'] : '';

        if ($isPaththu == false) {
        ?>
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <span class="product_name"><?= $product_name ?></span>
                        <br/>
                        <span class="product_brand"><?= $product_brand ?></span>
                    </div>
                    <div class="col-4">
                        <span class="product_cost"><?= $product_cost ?></span>
                    </div>
                    <div class="col-4 text-center">
                        <span class="product_qty"> <?= $product_qty ?> </span>
                    </div>
                    <div class="col-4 text-center">
                        <span class="productTotal"><?= $productTotal ?></span>
                    </div>
                </div>
            </div>
    <?php
        }
    }
}

if (is_array($billData) && !empty($billData)) {
    // $product = $billData;
    foreach ($billData as $product) {

        $balance = isset($product['balance']) ? $product['balance'] : 0;
        $subTotal = isset($product['subTotal']) ? $product['subTotal'] : 0;
        $netTotal = isset($product['netTotal']) ? $product['netTotal'] : 0;
        $discountPercentage = isset($product['discountPercentage']) ? $product['discountPercentage'] : 0;
        $deliveryCharges = isset($product['deliveryCharges']) ? $product['deliveryCharges'] : 0;
        $valueAddedServices = isset($product['valueAddedServices']) ? $product['valueAddedServices'] : 0;
        $cashAmount = isset($product['cashAmount']) ? $product['cashAmount'] : 0;
        $cardAmount = isset($product['cardAmount']) ? $product['cardAmount'] : 0;

        $vas_delivery = doubleval($valueAddedServices) + doubleval($deliveryCharges);

        if ($cardAmount == "") {
            $cardAmount = 0;
        }
        if ($discountPercentage == "") {
            $discountPercentage = 0;
        }
    }
    ?>
    <!-- total amount tika set krnwa -->
    <div class="col-12">
        <div class="row">
            <div>
                <div class="col-12 d-flex justify-content-start pt-2" style="border-top: #0e0e0e 0.2rem solid;">
                    <span class="productsAllTotal">Sub total : <?= $subTotal ?></span>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-start pt-2">
                <span class="productsAllTotal">Discount %: <?= $discountPercentage ?></span>
            </div>

            <!-- <div class="col-12 d-flex justify-content-start pt-2">
                <span class="productsAllTotal">VAS & delivery: <?= $vas_delivery ?></span>
            </div> -->

            <div class="col-12 d-flex justify-content-start pt-2">
                <span class="productsAllTotal">Net Total : <?= $netTotal ?></span>
            </div>

            <div class="col-12 d-flex justify-content-start pt-2">
                <span class="enterAmountFiled">Cash Amount :<?= $cashAmount ?></span>
            </div>
            <div class="col-12 d-flex justify-content-start pt-2" style="border-bottom: #0e0e0e 0.2rem solid;">
                <span class="enterAmountFiled">Card Amount :<?= $cardAmount ?></span>
            </div>

            <div class="col-12 d-flex justify-content-start pt-2" style="border-bottom: #0e0e0e 0.2rem solid;">
                <span class="balance">Balance : <?= $balance ?></span>
            </div>
        </div>
    </div>
<?php
}
