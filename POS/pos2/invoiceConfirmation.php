<?php
session_start();
include('../config/db.php');

$inArray = json_decode($_POST['products'], true);
// print_r($inArray);
$productsAllTotal = 0;
$balance = 0; // Initialize balance outside of the loop
$discountPercentage = 0;
$deliveryCharges = 0;
$valueAddedServices = 0;
$cashAmount = 0;
$cardAmount = 0;
$invoiceNumber = "";

$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    // user data from session
    foreach ($userLoginData as $userData) {
        $userId = $userData['id'];
        $shop_id = $userData['shop_id'];
        $user_name = $userData['name'];

        $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'invoices'");
        $invoiceId_row = $invoiceId_rs->fetch_assoc();
        $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
        $invoiceNumber = "000" . $userId . $shop_id . $invoiceId;

        $bill_data_rs = $conn->query("SELECT shop.shopName AS shopName, customize_bills.*
        FROM `customize_bills`
        INNER JOIN shop ON shopId = customize_bills.`customize_bill_shop-id`
        WHERE `customize_bill_shop-id` = '$shop_id'
        ");
        $bill_data = $bill_data_rs->fetch_assoc();
?>
        <!-- Invoice Start -->
        <div class="d-flex justify-content-center">
            <div class="col-12 p-2" style="width:<?= $bill_data['print_paper_size'] ?>mm ; background: whitesmoke;">

                <!-- Bill header -->
                <div class="row gap-1">

                    <!-- Header (shop name) -->
                    <table>
                        <tr>
                            <td colspan="3">
                                <div class="col-12 d-flex justify-content-center p-2">
                                    <div class="">
                                        <label style="font-size: large; font-weight: 100;">
                                            <h3>
                                                <b>
                                                    <?= $bill_data['shopName'] ?>
                                                </b>
                                            </h3>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="col-12 d-flex justify-content-center">
                                    <label class="contactNumber" id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                                </div>
                                <div class="col-12 d-flex justify-content-center center">
                                    <center>
                                        <label id="addresspreview" class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                                        </label>
                                    </center>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <!-- Header (shop name) end -->

                    <!-- Cashier name, date time -->
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12" style="text-align: center;">
                                <span style="font-size: 10px;"><?= $currentDate ?> <?= $currentTime ?></span> <br>

                                <span><span class="fw-bolder" style="font-size: 10px;"><?= $user_name ?> NO - </span> <span class="invoiceNumber" id="invoiceNumber"><?= $invoiceNumber ?></span></span>
                            </div>
                        </div>
                    </div>
                    <!-- Username, date time end -->

                    <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
                </div>
                <!-- Bill header end -->

                <?php

                if (is_array($inArray) && !empty($inArray)) {
                ?>

                    <!-- item data table header (u.Price, QTY, Total)-->
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
                    <!-- item data header end -->

                    <?php
                    // item for each
                    foreach ($inArray as $product) {
                        $product_code = isset($product['code']) ? $product['code'] : '';
                        $product_name = isset($product['product_name']) ? $product['product_name'] : '';
                        $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
                        $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
                        $product_unit = isset($product['product_unit']) ? $product['product_unit'] : '';

                        $balance = isset($product['balance']) ? $product['balance'] : 0;
                        $discountPercentage = isset($product['discountPercentage']) ? $product['discountPercentage'] : 0;
                        $deliveryCharges = isset($product['deliveryCharges']) ? $product['deliveryCharges'] : 0;
                        $valueAddedServices = isset($product['valueAddedServices']) ? $product['valueAddedServices'] : 0;
                        $cashAmount = isset($product['cashAmount']) ? $product['cashAmount'] : 0;
                        $cardAmount = isset($product['cardAmount']) ? $product['cardAmount'] : 0;

                        $vas_delivery = doubleval($valueAddedServices) + doubleval($deliveryCharges);

                        $productTotal = doubleval($product_cost) * doubleval($product_qty);
                        $productsAllTotal += $productTotal;

                        if ($discountPercentage == 0) {
                            $discountPercentage = 0;
                            $net_total = $productsAllTotal + doubleval($vas_delivery);
                        } else {
                            $net_total = $productsAllTotal  * (1 - doubleval($discountPercentage) / 100);
                            $net_total += doubleval($vas_delivery);
                        }

                        if ($cardAmount == "") {
                            $cardAmount = 0;
                        }
                        if ($discountPercentage == "") {
                            $discountPercentage = 0;
                        }
                    ?>
                        <!-- Items details (Name, Price, Item total price-->
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
                        <!-- Items details end -->
                    <?php

                    }
                    // item for each end
                    ?>
                    <!-- Footer total amounts -->
                    <div class="col-12">
                        <div class="row">
                            <div>
                                <div class="col-12 d-flex justify-content-end pt-2" style="border-top: #0e0e0e 0.2rem solid;">
                                    <span class="productsAllTotal">Sub total : <?= $productsAllTotal ?></span>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-2">
                                <span class="productsAllTotal">Discount %: <?= $discountPercentage ?></span>
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-2">
                                <span class="productsAllTotal">VAS & delivery: <?= $vas_delivery ?></span>
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-2">
                                <span class="productsAllTotal">Net Total : <?= $net_total ?></span>
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-2">
                                <span class="enterAmountFiled">Cash Amount :<?= $cashAmount ?></span>
                            </div>
                            <div class="col-12 d-flex justify-content-end pt-2" style="border-bottom: #0e0e0e 0.2rem solid;">
                                <span class="enterAmountFiled">Card Amount :<?= $cardAmount ?></span>
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-2" style="border-bottom: #0e0e0e 0.2rem solid;">
                                <span class="balance">Balance : <?= $balance ?></span>
                            </div>
                        </div>
                    </div>
                    <!-- Footer total amounts end -->

                    <!-- Footer (checked by) -->
                    <table>
                        <tr style="font-weight: 600;">
                            <td>
                                <div class="col-12 pt-2">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-center text-center">
                                            <span id="billnotepreview" style="font-size:9px;"><?= $bill_data['bill_note'] ?></span>
                                        </div>
                                        <div class="col-12 d-flex justify-content-center">
                                            <span>Thank You !</span>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center">
                                            <div class="check-by-box">
                                                <center>
                                                    <label style="font-weight:bold; margin-bottom:3px;">Check By</label>
                                                </center>

                                                <label for="date">Date: <?= $currentDate ?><?= $currentTime ?></label>

                                                <label for="emp-no">EMP No:.............................</label>

                                                <label for="signature">Signature:..........................</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <!-- Footer (checked by) end -->
            </div>
        </div>
        <!-- Invoice end -->
<?php
                //  if (is_array($inArray) close
                } else {
                    echo "No products found or invalid data received.";
                } //  if (is_array($inArray) else close
            }
        } else {
            echo "Session Expired !"; // user data from session. end
        }
