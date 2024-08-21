<?php
include('config/db.php');
session_start();
$inArray = $_POST['products'];
// print_r($inArray);
// $productsAllTotal = 0;
$balance = 0;
$discountPercentage = 0;
$deliveryCharges = 0;
$valueAddedServices = 0;
$cashAmount = 0;
$cardAmount = 0;
$productTotal;
$subTotal;
$netTotal;


if (is_array($inArray) && !empty($inArray)) {
    ?>
    
     <!--//table header eka set krnwa-->
            <div class="col-12">
                    
                    <div class="row">
                        
                        <div class="col-4">
                            <span class="product_cost">U.Price</span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="product_qty">
                                QTY
                            </span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="productTotal">Total</span>
                        </div>
                    </div>
                </div>
    
    <?php
    foreach ($inArray as $product) {
        // if (isset($_SESSION['store_id'])) {
            // $userLoginData = $_SESSION['store_id'];

   //time tika set krn eka
            // foreach ($userLoginData as $userData) {
            //     $userId = $userData['id'];
            //     $shop_id = $userData['shop_id'];

                // $isPaththu = false;
                
                $isPaththu = $product['isPaththu'];
                $product_code = isset($product['code']) ? $product['code'] : '';
                $product_name = isset($product['product_name']) ? $product['product_name'] : '';
                $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
                $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
                $product_unit = isset($product['product_unit']) ? $product['product_unit'] : '';
                $productTotal = isset($product['productTotal']) ? $product['productTotal'] : '';

                $balance = isset($product['balance']) ? $product['balance'] : 0;
                $subTotal = isset($product['subTotal']) ? $product['subTotal'] : 0;
                $netTotal = isset($product['netTotal']) ? $product['netTotal'] : 0;
                $discountPercentage = isset($product['discountPercentage']) ? $product['discountPercentage'] : 0;
                $deliveryCharges = isset($product['deliveryCharges']) ? $product['deliveryCharges'] : 0;
                $valueAddedServices = isset($product['valueAddedServices']) ? $product['valueAddedServices'] : 0;
                $cashAmount = isset($product['cashAmount']) ? $product['cashAmount'] : 0;
                $cardAmount = isset($product['cardAmount']) ? $product['cardAmount'] : 0;
                // $invoiceNumber = isset($product['invoiceNumber']) ? $product['invoiceNumber'] : '';

                if($isPaththu== "false"){
?>
                <div class="col-12">
                    
                    <div class="row">
                        <div class="col-12">
                            <span class="product_name"><?= $product_name ?></span>
                        </div>
                        <div class="col-4">
                            <span class="product_cost"><?= $product_cost ?></span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="product_qty">
                                <?= $product_qty ?>
                            <!-- <?= $product_unit ?> -->
                        </span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="productTotal"><?= $productTotal ?></span>
                        </div>
                    </div>
                </div>
    <?php
    
}
            // }
        // } else {
        //     echo "Session Expired !";
        // }
    }

    $vas_delivery = doubleval($valueAddedServices) + doubleval($deliveryCharges);

    // $productTotal = doubleval($product_cost) * doubleval($product_qty);
    // $productsAllTotal += $productTotal;

    // if ($discountPercentage == 0) {
        // $discountPercentage = 0;
        // $net_total = $subTotal + doubleval($vas_delivery);
    // } else {
        // $net_total = $subTotal  * (1 - doubleval($discountPercentage) / 100);
        // $net_total += doubleval($vas_delivery);
    // }

    if ($cardAmount == "") {
        $cardAmount = 0;
    }
    if ($discountPercentage == "") {
        $discountPercentage = 0;
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

    <!-- <div class="col-12 pt-2">
        <div class="row">
            <div class="col-12 d-flex justify-content-center text-center">
                <span style="font-size:9px;">*Kindly note that once the Medication has been taken away. The hospital cannot be held responsible for any issues.</span>
            </div>
            <div class="col-12 d-flex justify-content-center">
                <span>Thank You !</span>
            </div>
        </div>
    </div> -->

<?php
} else {
    echo "No products found or invalid data received.";
}
?>