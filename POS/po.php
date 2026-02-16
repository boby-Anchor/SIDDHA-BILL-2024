<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    $userData = $_SESSION['store_id'][0];
    $shop_id = $userData['shop_id'];
    $user_name = $userData['name'];
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
    <title>Home | Pharmacy</title>

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

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper bg-dark">

            <div class="row w-100">

                <div class="col-12 col-md-7">

                    <!-- Bottom amounts-->
                    <div class="col-12 total_div">

                        <div class="row">

                            <div class="col-12 p-1" style="background: #000;">
                                <div class="row">

                                    <!--id="subTotal"-->
                                    <div class="col-3   justify-content-end ">
                                        <label class="subTotal" id="subTotal"></label>
                                        <label class="subTotal">RS(ST) |</label>
                                    </div>

                                    <!--id="netTotal"  netTotal-->
                                    <div class="col-3 text-right ">
                                        <label class="subTotal" id="netTotal"></label>
                                        <label class="subTotal">RS(NT)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 p-1" style="background: #000;">
                                <div class="row" style="background: #000;">
                                    <!--id="discountPercentage"-->
                                    <div class="col-4 p-2 " id="discountField"
                                        style="color:#000 !important; background: #000;">
                                        <input type="text" placeholder="Discount %" class="form-control col-8"
                                            id="discountPercentage" name="discountPercentage" onkeyup="addDiscount()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 " style="background: #0000004a;">
                                <div class="row">
                                    <!--class="balance" id="balance"-->
                                    <div class="col-6">
                                        <!--class="balance" id="balance"-->
                                        <div class="col-12">
                                            <label class="balance" id="balance">000</label>
                                        </div>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                        <!--id="checkoutBtn"-->
                                        <button class="btn check-outBtn col-6" id="checkoutBtn">Checkout <i class="bi bi-arrow-right-circle-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom amounts end-->

                    <!--top po_shop, barcode, item price select-->
                    <div class="col-12">
                        <div class="row">
                            <div class="d-flex justify-content-evenly">
                                <div class="p-2 p-x-2">
                                    <select name="po-shop-selector" id="po-shop-selector" onchange="setPoShopOnBill(this)"
                                        class="po-shop-selector form-control rounded-5">
                                        <option value="0" selected disabled hidden>Select shop</option>
                                        <?php
                                        $shops_rs = $conn->query("SELECT shop.shopId, shop.shopName FROM shop");
                                        while ($shops_row = $shops_rs->fetch_assoc()) {
                                            if ($shop_id != $shops_row['shopId']) {
                                        ?>
                                                <option value="<?= $shops_row['shopId'] ?>">
                                                    <?= $shops_row['shopName'] ?>
                                                </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class="col-4 mb-2 p-2 p-x-2">
                                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode..."
                                    onchange="getBarcode2(this.value);">
                            </div>
                            <div class="col-4 mb-2 p-2 p-x-2">
                                <select class="form-control" name="" id="selectPrices"
                                    onchange="getBarcode3()"></select>
                            </div>

                            <!--auto-->
                            <div class="col-12" style="height: 40vh; overflow:auto;">
                                <div>
                                    <table class="table barcodeResults">
                                        <tbody id="barcodeResults"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- top end -->

                </div>

                <!--item Search List Right-->
                <div class="col-12 col-md-5">
                    <div class="card-body h-100 bg-light overflow-hidden">

                        <div class="row">
                            <!-- Company Product list -->
                            <div class="col-12" style="height: 100vh; overflow:auto; background-color: #0e0e0e;">
                                <!-- Added method attribute -->
                                <input type="search" class="form-control mt-2" onkeyup="searchProducts(this.value);" placeholder="Search...">
                                <div class="row" id="productGrid" class="productGrid">
                                </div>
                            </div>

                            <!-- Company Product list end -->
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->
        <!-- Alert -->
        <?php include("part/alert.php"); ?>
        <!-- Alert end -->

        <!-- ========================================== -->

        <div id="invoice-POS" class="d-none">

            <?php
            $currentDate = date("Y-m-d");
            $currentTime = date("H:i:s");

            $bill_data_rs = $conn->query("SELECT * FROM `customize_bills` WHERE `customize_bill_shop-id` = '$shop_id'");
            $bill_data = $bill_data_rs->fetch_assoc();
            ?>
            <div class="d-flex justify-content-center">
                <div class="col-12 p-2" style="width:<?= $bill_data['print_paper_size'] ?>mm ; background: whitesmoke;">
                    <div class="row gap-1">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <div class="col-12 d-flex justify-content-center p-2">
                                        <div class="billpreviewlogo<?= $bill_data['print_paper_size'] ?>"
                                            style="background-image:url('<?= $bill_data['customize_bills_logo'] ?>');">
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="col-12 d-flex justify-content-center">
                                        <label class="contactNumber"
                                            id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center center">
                                        <center>
                                            <label id="addresspreview"
                                                class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                                            </label>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div class="col-12">
                            <div class="row">
                                <div class="col-12" style="text-align: center;">
                                    <span style="font-size: 10px;"><?= $currentDate ?> <?= $currentTime ?></span>
                                    <br>

                                    <span>
                                        <span class="fw-bolder" style="font-size: 10px;"><?= $user_name ?>
                                            <br />
                                        </span>
                                        To-
                                        <span id="po_shop_on_bill">
                                        </span>
                                        <span class="invoiceNumber" id="invoiceNumber"> </span>
                                    </span>
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
                                            <span id="billnotepreview"
                                                style="font-size:9px;"><?= $bill_data['bill_note'] ?></span>
                                        </div>
                                        <div class="col-12 d-flex justify-content-center">
                                            <span>Thank You !</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->

    </div>
</body>

<!-- All JS -->
<?php include("part/all-js.php"); ?>
<!-- All JS end -->
<!-- Data Table JS -->
<?php include("part/data-table-js.php"); ?>
<!-- Data Table JS end -->
<!-- po JS -->
<script src="dist/js/po.js"></script>
<!-- po JS end -->

</html>