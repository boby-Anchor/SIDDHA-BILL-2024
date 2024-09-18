<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {

            $shop_id = $userData['shop_id'];
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="utf-8" />

                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <title>Pharmacy</title>

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
                    <div class="content-wrapper">
                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Purchase Orders</h3>
                                            </div>
                                            <div class="card-body">

                                                <table class="table table-bordered table-hover">
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if ($shop_id == "1") {
                                                            $hub_order_details_result = $conn->query("SELECT 
                                                            poinvoices.invoice_id AS invoice_id,
                                                            poinvoices.shop_id AS shop_id,
                                                            users.name AS user_name,
                                                            shop1.shopName AS shop_name,
                                                            shop2.shopName AS po_shop_name,
                                                            poinvoices.created AS po_date,
                                                            Round(poinvoices.sub_total,2) AS sub_total,
                                                            poinvoices.discount_percentage AS discount,
                                                            poinvoices.net_total AS net_total
                                                            FROM poinvoices
                                                            INNER JOIN users ON users.id = poinvoices.user_id
                                                            INNER JOIN shop AS shop1 ON shop1.shopId = poinvoices.shop_id
                                                            INNER JOIN shop AS shop2 ON shop2.shopId = poinvoices.po_shop_id
                                                            ");
                                                        } else {
                                                            $hub_order_details_result = $conn->query("SELECT 
                                                            poinvoices.invoice_id AS invoice_id,
                                                            poinvoices.shop_id AS shop_id,
                                                            users.name AS user_name,
                                                            shop1.shopName AS shop_name,
                                                            shop2.shopName AS po_shop_name,
                                                            poinvoices.created AS po_date,
                                                            Round(poinvoices.sub_total,2) AS sub_total,
                                                            poinvoices.discount_percentage AS discount,
                                                            poinvoices.net_total AS net_total
                                                            FROM poinvoices
                                                            INNER JOIN users ON users.id = poinvoices.user_id
                                                            INNER JOIN shop AS shop1 ON shop1.shopId = poinvoices.shop_id
                                                            INNER JOIN shop AS shop2 ON shop2.shopId = poinvoices.po_shop_id
                                                            WHERE shop1.shopId = '$shop_id'");
                                                        }

                                                        while ($hub_order_details_data = $hub_order_details_result->fetch_assoc()) {
                                                        ?>
                                                            <tr>
                                                                <th><?= $hub_order_details_data["invoice_id"] ?></th>
                                                                <td><?= $hub_order_details_data["shop_name"] ?></td>
                                                                <td><?= $hub_order_details_data["po_shop_name"] ?></td>
                                                                <td><?= $hub_order_details_data["user_name"] ?></td>
                                                                <td>
                                                                    <?php
                                                                    $itemCount_result = $conn->query("SELECT COUNT(invoiceNumber) AS itemCount
                                                                    FROM poinvoiceitems WHERE invoiceNumber = '" . $hub_order_details_data['invoice_id'] . "'");

                                                                    $itemCount_data = $itemCount_result->fetch_assoc();
                                                                    ?>
                                                                    <button class="btn dropdown-toggle badge badge-info " type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom-start">
                                                                        <?= $itemCount_data['itemCount'] ?>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <table class="table" id="poItemsTable<?= $hub_order_details_data['invoice_id'] ?>">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col">#</th>
                                                                                    <th scope="col">Barcode</th>
                                                                                    <th scope="col">Item Name</th>
                                                                                    <th scope="col">Item Price</th>
                                                                                    <th scope="col">Qty</th>
                                                                                    <th scope="col">Cost</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php

                                                                                // $poItems_result = $conn->query("SELECT * FROM hub_order INNER JOIN p_medicine ON p_medicine.code = hub_order.HO_item WHERE HO_number = '" . $hub_order_details_data['hub_order_number'] . "'");

                                                                                $poItems_result = $conn->query("SELECT * FROM poinvoiceitems 
                                                                                INNER JOIN p_medicine ON p_medicine.code = poinvoiceitems.item_code
                                                                                WHERE invoiceNumber = '" . $hub_order_details_data['invoice_id'] . "'  ");
                                                                                $rowNo = 0;
                                                                                while ($poItems_data = $poItems_result->fetch_array()) {
                                                                                    $rowNo++
                                                                                ?>
                                                                                    <tr>
                                                                                        <th scope="row"><?= $rowNo ?></th>
                                                                                        <td><?= $poItems_data["code"] ?></td>
                                                                                        <td><?= $poItems_data["name"] ?></td>
                                                                                        <td><?= number_format($poItems_data["invoiceItem_price"], 0) ?></td>
                                                                                        <td><?= number_format($poItems_data["invoiceItem_qty"], 0) ?></td>
                                                                                        <td><?= number_format($poItems_data["invoiceItem_total"], 0) ?></td>
                                                                                    </tr>
                                                                                <?php
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table>
                                                                        <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?= $hub_order_details_data['invoice_id'] ?>');"> <i class="nav-icon fas fa-copy"></i> PRINT</button>
                                                                    </ul>

                                                                </td>
                                                                <td><?= $hub_order_details_data['po_date'] ?></td>
                                                                <td><?= number_format($hub_order_details_data["sub_total"], 0) ?></td>
                                                                <td><?= number_format($hub_order_details_data['discount'], 0) ?></td>
                                                                <td><?= number_format($hub_order_details_data['net_total'], 0) ?></td>

                                                            </tr>
                                                    <?php
                                                        }
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
                    <!-- Footer -->
                    <?php include("part/footer.php"); ?>
                    <!-- Footer End -->
                </div>

                <!-- Alert -->
                <?php include("part/alert.php"); ?>
                <!-- Alert end -->

                <!-- All JS -->
                <?php include("part/all-js.php"); ?>
                <!-- All JS end -->



                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var dropdownButtonList = [].slice.call(document.querySelectorAll('.btn.dropdown-toggle'));
                        dropdownButtonList.map(function(button) {
                            // Check if the button has already been initialized
                            if (!button.classList.contains('dropdown-initialized')) {
                                new bootstrap.Dropdown(button);
                                // Mark the button as initialized to prevent re-initialization
                                button.classList.add('dropdown-initialized');
                            }
                        });
                    });
                </script>
                <script>
                    function printTable(orderNumber) {
                        var printWindow = window.open('', '_blank');
                        printWindow.document.write('<html><head><title>Print Preview</title>');
                        // Include Bootstrap CSS
                        printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
                        printWindow.document.write('</head><body>');
                        printWindow.document.write('<div class="container">');
                        printWindow.document.write('<h2 class="text-center bg-success text-light" style="margin-top:5px;padding:3px;">ORDER DETAILS</h2>');
                        printWindow.document.write('<div class="col-12" style="margin-top: 50px;margin-bottom: 20px;font-family: monospace;">');
                        printWindow.document.write('<div class="row">');
                        printWindow.document.write('<div class="col-12" style="text-align: start;">');
                        printWindow.document.write('<h5>ORDER NUMBER : ' + orderNumber + '</h5>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('<div class="col-12" style="text-align: start;">');
                        printWindow.document.write('<h6>ORDER DATE : <?= date('Y-m-d', strtotime($itemCount_data['orderDate'])) ?></h6>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('<div class="col-12" style="text-align: start;">');
                        printWindow.document.write('<h6>ORDER TIME : <?= date('H:i:s', strtotime($itemCount_data['orderDate'])) ?></h6>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('</div>');
                        printWindow.document.write(document.getElementById('poItemsTable' + orderNumber).outerHTML);
                        printWindow.document.write('</div>');
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.focus();
                        printWindow.print();

                    }
                </script>

            </body>

            </html>
    <?php
    }
}

    ?>