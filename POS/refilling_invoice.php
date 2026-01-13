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
    <title id="titleName">Refilling | Bill</title>

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
        <?php include("part/navbar.php");
        ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <!-- Main Refilling Content Start -->
        <main class="content-wrapper bg-dark">
            <div class="row">
                <div class="col-12 text-center">
                    <label class="text-xl">Refilling Invoice</label>
                    <hr class="border border-info m-0">
                </div>

                <div class="col-12 my-2 px-4">
                    <div class="row align-items-center">

                        <!-- Batch Number Input -->
                        <div class="col-md-6">
                            <label class="form-label">Batch Number :</label>
                            <input type="text"
                                class="rounded-lg text-lg"
                                id="batch_number"
                                placeholder="YA/NI/123/26/003">
                        </div>

                        <!-- Button -->
                        <div class="col-md-6 mt-3 mt-md-0 d-flex align-items-center">
                            <button class="btn btn-primary w-25" id="proceedButton" onclick="this.disabled=true; checkout();">
                                <i class="fas fa-arrow-alt-circle-right me-1"></i> Proceed
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Source Items Selection Start -->
                <div class="col-6 px-4">
                    <div class="row text-center">
                        <div class="col-11 bg-warning rounded-lg">
                            <label class="text-lg">Source Items</label>
                        </div>
                        <div class="col-1">
                            <button class="btn btn-warning rounded-circle" data-toggle="modal" data-target="#sourceItemModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">

                        <table id="sourceItemsTable" class="table ">
                            <thead>
                                <td>Barcode</td>
                                <td>Product</td>
                                <td>Brand</td>
                                <td>Price</td>
                                <td>Qty</td>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- Source Items Selection End -->

                <!-- Converted Items Selection Start -->
                <div class="col-6">
                    <div class="row text-center">
                        <div class="col-10 bg-success rounded-lg">
                            <label class="text-lg">Refill Items</label>
                        </div>
                        <div class="col-1">
                            <button class="btn btn-success rounded-circle" data-toggle="modal" data-target="#refillItemModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-1"> </div>
                    </div>
                    <div class="row">

                        <table id="refillingItemsTable" class="table ">
                            <thead>
                                <td>Barcode</td>
                                <td>Product</td>
                                <td>Brand</td>
                                <td>Price</td>
                                <td>Qty</td>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- Converted Items Selection End -->
            </div>
        </main>
        <!-- Main Refilling Content End -->
    </div>
    <!-- Main Refilling Content End -->

    <!-- Footer -->
    <?php include "part/footer.php" ?>
    <!-- Footer End -->

    <!-- Select Source Items modal start -->
    <div class="modal fade" id="sourceItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-dark text-light">

                <!-- Header -->
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Select Source Items</h5>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div id="sourceProductGrid">
                        <div class="row p-2">
                            <div class="col-2">Barcode</div>
                            <div class="col-3">Product Name</div>
                            <div class="col-2">Volume</div>
                            <div class="col-3">Brand</div>
                            <div class="col-1">Qty</div>
                            <div class="col-1">Price</div>
                        </div>
                        <?php
                        $sourceItemsData = $conn->query("SELECT
                        pm.code AS barcode,
                        pm.name AS productName,
                        ucv.ucv_name AS volume,
                        mu.unit AS unit,
                        stock2.stock_id,
                        stock2.item_s_price AS price,
                        stock2.stock_item_qty AS qty,
                        pb.name AS brand
                        FROM stock2
                        INNER JOIN p_medicine pm
                            ON pm.code = stock2.stock_item_code
                        INNER JOIN p_brand pb
                            ON pb.id = pm.brand
                        INNER JOIN medicine_unit mu
                            ON mu.id = pm.medicine_unit_id
                        INNER JOIN unit_category_variation ucv
                            ON ucv.ucv_id = pm.unit_variation
                        WHERE ucv.ucv_name = '4.5' AND stock2.stock_shop_id =5
                        ORDER BY brand ASC");

                        while ($sourceItem = $sourceItemsData->fetch_assoc()) {
                        ?>
                            <div onclick="setSourceItem(<?= $sourceItem['stock_id'] ?>)">
                                <div class="rounded-lg bg-black border border-info py-3 px-2 mb-4">
                                    <div class="row">
                                        <div class="col-2"><?= $sourceItem['barcode'] ?></div>
                                        <div class="col-3"><?= $sourceItem['productName'] ?></div>
                                        <div class="col-2"><?= $sourceItem['volume'] ?><?= $sourceItem['unit'] ?></div>
                                        <div class="col-3"><?= $sourceItem['brand'] ?></div>
                                        <div class="col-1"><?= $sourceItem['qty'] ?></div>
                                        <div class="col-1"><?= $sourceItem['price'] ?></div>
                                        <!-- <div class="col-1">
                                        <button class="btn btn-success btn-sm rounded-circle">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div> -->
                                    </div>
                                </div>
                            </div>

                        <?php
                        }
                        ?>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Select Source Items modal end -->

    <!-- ========================================== -->
    <div id="invoice-POS" class="d-none">

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
                                            <div class="billpreviewlogo<?= $bill_data['print_paper_size'] ?>"
                                                style="background-image:url('<?= $bill_data['customize_bills_logo'] ?>');">
                                            </div>
                                            <!-- <div class="text-center">
                          <label style="font-size: large; font-weight: 100;">
                            <h3>
                              <b>
                                <?php //echo $bill_data['shopName'] 
                                ?>
                              </b>
                            </h3>
                          </label>
                        </div> -->
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="col-12 d-flex justify-content-center">
                                            <label class="contactNumber"
                                                id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                                        </div>
                                        <div class="col-12 d-flex justify-content-center text-center center">
                                            <label id="addresspreview"
                                                class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12" style="text-align: center;">
                                        <span><span class="text-left" style="font-size: 10px;"><?= $currentDate ?>
                                            </span><span class="text-right"> <?= $currentTime ?></span> </span>
                                        <br>
                                        <span><span class="invoicePatientName" id="invoicePatientName"></span> <span
                                                id="InvoiceContactNumber"></span></span>
                                        <br>
                                        <span><span class="fw-bold"><?= $user_name ?> Inv.</span> <span class="fw-bolder"
                                                style="font-size: 10px;" id="invoiceNumber"></span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
                        </div>
                        <!-- table header start -->
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
                        <!-- table header end -->
                        <div class="printInvoiceData" id="printInvoiceData">

                        </div>
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

                                            <!-- Checked by box -->
                                            <div class="col-12 d-flex justify-content-center">
                                                <div class="check-by-box">
                                                    <center>
                                                        <label style="font-weight:bold; margin-bottom:3px;">Checked
                                                            By</label>
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
    <!-- ========================================== -->

    </div>
</body>

<!-- All JS -->
<?php include "part/all-js.php" ?>
<!-- All JS end -->

<script src="dist/js/refilling-invoice.js"></script>

</html>