<?php
session_start();
$shop_id;
$user_id;

if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    if (isset($_SESSION['store_id'])) {
        $userLoginData = $_SESSION['store_id'][0];
        $shop_id = $userLoginData['shop_id'];
        $user_id = $userLoginData['id'];
    }


    $bill_data_rs = $conn->query("SELECT * FROM `customize_bills` WHERE `customize_bill_shop-id` = '$shop_id'");
    $bill_data = $bill_data_rs->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Ensure start_date is not after end_date
        if (strtotime($start_date) > strtotime($end_date)) {
            $temp = $start_date;
            $start_date = $end_date;
            $end_date = $temp;
        }
    } else {
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Purchase Orders Between <?= $start_date ?> And <?= $end_date ?></title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-dark">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <h3>
                                        View Purchase Orders Between <?= $start_date ?> And <?= $end_date ?>
                                    </h3>
                                    <div class="border-top mb-3"></div>
                                    <!-- Form start -->
                                    <form method="POST" id="filterForm">
                                        <div class="row g-3 accent-cyan align-items-center px-3">
                                            <div class="col-auto">
                                                <label for="start_date" class="col-form-label">Start Date:</label>
                                            </div>
                                            <div class="col-auto">
                                                <input type="date" id="start_date" name="start_date" class="form-control"
                                                    value="<?= $start_date ?>" required>
                                            </div>
                                            <div class="col-auto">
                                                <label for="end_date" class="col-form-label">End Date:</label>
                                            </div>
                                            <div class="col-auto">
                                                <input type="date" id="end_date" name="end_date" class="form-control"
                                                    value="<?= $end_date ?>" required>
                                            </div>
                                            <div class="ml-2">
                                                <button type="submit" class="btn btn-outline-success">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form end -->
                                </div>
                                <div class="card-body">

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <th class="adThText">Order Number</th>
                                                <th class="adThText">Order From</th>
                                                <th class="adThText">Order To</th>
                                                <th class="adThText">Placed By</th>
                                                <th class="adThText">Items</th>
                                                <th class="adThText">Order Placed Date</th>
                                                <th class="adThText">Sub Total</th>
                                                <th class="adThText">Discount %</th>
                                                <th class="adThText">Net Total</th>
                                                <th class='adThText'>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($shop_id == "1") {
                                                $query = "SELECT
                                                            poinvoices.invoice_id AS invoice_id,
                                                            poinvoices.shop_id AS shop_id,
                                                            users.name AS user_name,
                                                            shop1.shopName AS shop_name,
                                                            shop2.shopName AS po_shop_name,
                                                            poinvoices.created AS po_date,
                                                            Round(poinvoices.sub_total,2) AS sub_total,
                                                            poinvoices.discount_percentage AS discount,
                                                            poinvoices.net_total AS net_total,
                                                            poinvoices.transferred AS status
                                                            FROM poinvoices
                                                            INNER JOIN users ON users.id = poinvoices.user_id
                                                            INNER JOIN shop AS shop1 ON shop1.shopId = poinvoices.shop_id
                                                            INNER JOIN shop AS shop2 ON shop2.shopId = poinvoices.po_shop_id
                                                            WHERE poinvoices.created BETWEEN '" . date("Y-m-d 00:00:00", strtotime($start_date)) . "' AND '" . date("Y-m-d 23:59:59", strtotime($end_date)) . "'
                                                            ORDER BY po_date DESC
                                                            ";
                                                $hub_order_details_result = $conn->query($query);
                                            } else {
                                                $query = "SELECT
                                                            poinvoices.invoice_id AS invoice_id,
                                                            poinvoices.shop_id AS shop_id,
                                                            users.name AS user_name,
                                                            shop1.shopName AS shop_name,
                                                            shop2.shopName AS po_shop_name,
                                                            poinvoices.created AS po_date,
                                                            Round(poinvoices.sub_total,2) AS sub_total,
                                                            poinvoices.discount_percentage AS discount,
                                                            poinvoices.net_total AS net_total,
                                                            poinvoices.transferred AS status
                                                            FROM poinvoices
                                                            INNER JOIN users ON users.id = poinvoices.user_id
                                                            INNER JOIN shop AS shop1 ON shop1.shopId = poinvoices.shop_id
                                                            INNER JOIN shop AS shop2 ON shop2.shopId = poinvoices.po_shop_id
                                                            WHERE shop2.shopId = '$shop_id' AND poinvoices.created BETWEEN '" . date("Y-m-d 00:00:00", strtotime($start_date)) . "' AND '" . date("Y-m-d 23:59:59", strtotime($end_date)) . "'
                                                            ORDER BY po_date DESC
                                                            ";
                                                $hub_order_details_result = $conn->query($query);
                                            }

                                            if ($hub_order_details_result && $hub_order_details_result->num_rows > 0) {
                                                while ($hub_order_details_data = $hub_order_details_result->fetch_assoc()) {
                                            ?>
                                                    <tr>
                                                        <th><?= $hub_order_details_data["invoice_id"] ?></th>
                                                        <th><?= $hub_order_details_data["shop_name"] ?></th>
                                                        <th><?= $hub_order_details_data["po_shop_name"] ?></th>
                                                        <th><?= $hub_order_details_data["user_name"] ?></th>
                                                        <th>
                                                            <?php
                                                            $itemCount_result = $conn->query("SELECT COUNT(invoiceNumber) AS itemCount
                                                                    FROM poinvoiceitems WHERE invoiceNumber = '" . $hub_order_details_data['invoice_id'] . "'");

                                                            $itemCount_data = $itemCount_result->fetch_assoc();
                                                            ?>
                                                            <button class="btn fa fa-eye badge badge-info p-2 text-white" type="button"
                                                                onclick='viewPoItems(
                                                                    <?= json_encode($hub_order_details_data["invoice_id"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["shop_name"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["po_shop_name"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["user_name"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["po_date"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["sub_total"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["discount"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["net_total"]) ?>
                                                                )'>
                                                                <?= $itemCount_data['itemCount'] ?>
                                                            </button>
                                                        </th>
                                                        <th><?= $hub_order_details_data["po_date"] ?></th>
                                                        <th><?= number_format($hub_order_details_data["sub_total"], 0) ?></th>
                                                        <th><?= number_format($hub_order_details_data["discount"], 0) ?></th>
                                                        <th><?= number_format($hub_order_details_data["net_total"], 0) ?></th>
                                                        <th><?php echo ($hub_order_details_data["status"] == 1) ? "Transferred" : "No"; ?></th>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="10" class="text-center">
                                                        No PO Data Found
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- PO items Modal start -->
        <div class="modal fade" id="po-items-data-modal" tabindex="-1" aria-labelledby="poItemsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content bg-dark">
                    <div class="modal-header">
                        <h5 class="modal-title" id="poItemsModalLabel">Purchase Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Order Number:</strong> <span id="po_modal_order_number"></span></p>
                                <p><strong>Order From:</strong> <span id="po_modal_shop_name"></span></p>
                                <p><strong>Order To:</strong> <span id="po_modal_po_shop_name"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Placed By:</strong> <span id="po_modal_user_name"></span></p>
                                <p><strong>Order Date:</strong> <span id="po_modal_po_date"></span></p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="po_items_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Barcode</th>
                                        <th>Item Name</th>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>Item Price</th>
                                        <th>Qty</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody id="po_items_table_body"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning" type="button" onclick="handlePrint()"><i class="nav-icon fas fa-print"></i> Print</button>
                        <button class="btn btn-primary" type="button" onclick="handleBillPrint()"><i class="nav-icon fas fa-receipt"></i> Print Bill</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- PO items Modal end -->

        <!-- ========================================== -->

        <div id="invoice-POS" class="d-none">

            <?php
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
                                    <span id="po_bill_date" style="font-size: 10px;"></span>
                                    <br>

                                    <span>
                                        <span class="fw-bolder" style="font-size: 10px;"><span id="bill_user_name"></span>
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
                                            <span style="font-size:9px;">This is a re-printed PO Bill</span>
                                        </div>
                                        <div class="col-12 d-flex justify-content-center">
                                            <span style="font-size: 10px;">Print Time - <?= date("Y-m-d") ?> <?= date("H:i:s") ?></span>
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

    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->
</body>

<!-- PO_View JS -->
<script src="dist/js/po/po_view.js"></script>
<!-- All JS end -->

</html>