<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}
// include('actions/cart-pos.php');
//   include('actions/cart.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home | Inv 1</title>

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Product -->
    <link rel="stylesheet" href="dist/css/product.css">
    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->
    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

    <!-- bootstrap icon link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dist/css/customize_bill.css">

</head>

<body class="hold-transition sidebar-mini layout-fixed" onload="focus_filed();">
    <div id="invoice-POS">

        <?php
        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            $currentDate = date("Y-m-d");
            $currentTime = date("H:i:s");

            foreach ($userLoginData as $userData) {
                $shop_id = $userData['shop_id'];
                $user_name = $userData['name'];

                $bill_data_rs = $conn->query("SELECT shop.shopName AS shopName, customize_bills.*
                FROM `customize_bills`
                INNER JOIN shop ON shopId = customize_bills.`customize_bill_shop-id`
                WHERE `customize_bill_shop-id` = '$shop_id'
                ");
                $bill_data = $bill_data_rs->fetch_assoc();
        ?>
                <div class="d-flex justify-content-center">
                    <div class="col-12 p-2" style="width:<?= $bill_data['print_paper_size'] ?>mm ; background: whitesmoke;">
                        <div class="row gap-1">
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
                                        <div class="col-12 d-flex justify-content-center text-center center">
                                            <label id="addresspreview" class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12" style="text-align: center;">
                                        <span style="font-size: 10px;"><?= $currentDate ?> <?= $currentTime ?></span> <br>

                                        <span><span class="fw-bolder" style="font-size: 10px;"><?= $user_name ?> NO - </span> <span class="invoiceNumber" id="invoiceNumber"></span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
                        </div>
                        <div class="printInvoiceData" id="printInvoiceData"> </div>
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
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</body>