<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['store_id'])) {
    header("location: login.php");
    exit();
} else {
    include('config/db.php');
}
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
            <section class="content bg-dark">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <h3 class="card-title">View Goods Receipt Notes</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <th class="adThText">GRN Number</th>
                                                <th class="adThText">Invoice Number</th>
                                                <th class="adThText">Items</th>
                                                <th class="adThText">GRN Added Date</th>
                                                <th class="adThText">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            if (isset($_SESSION['store_id'])) {

                                                $userLoginData = $_SESSION['store_id'];

                                                foreach ($userLoginData as $userData) {
                                                    $shop_id = $userData['shop_id'];
                                                    $grn_details_result = $conn->query("SELECT * FROM `grn` WHERE grn_shop_id = '$shop_id' ORDER BY grn_date DESC LIMIT 100");

                                            ?>
                                                    <?php while ($grn_details_data = $grn_details_result->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?= $grn_details_data["grn_number"] ?></td>
                                                            <td><?= $grn_details_data["invoice_number"] ?></td>
                                                            <td>
                                                                <?php
                                                                $itemCount_result = $conn->query("SELECT COUNT(grn_number) AS grnItemsCount FROM grn_item WHERE grn_item.grn_number = '" . $grn_details_data["grn_number"] . "'");
                                                                $itemCount_data = $itemCount_result->fetch_assoc();
                                                                ?>
                                                                <button class="btn dropdown-toggle badge badge-info " type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom-start"> <?= $itemCount_data['grnItemsCount'] ?> </button>
                                                                <ul class="dropdown-menu">
                                                                    <table class="table" id="poItemsTable<?= $grn_details_data["grn_number"] ?>">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">#</th>
                                                                                <th scope="col">Item Code</th>
                                                                                <th scope="col">Item Name</th>
                                                                                <th scope="col">Qty</th>
                                                                                <th scope="col">Free Qty</th>
                                                                                <th scope="col">Item Price</th>
                                                                                <th scope="col">Total Value</th>
                                                                                <th scope="col">Item Discount</th>
                                                                                <th scope="col">Total Cost</th>
                                                                                <th scope="col">Unit Cost</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $itemCount = 1;
                                                                            $poItems_result = $conn->query("SELECT * FROM grn_item INNER JOIN p_medicine ON grn_item.grn_p_id = p_medicine.code WHERE grn_number = '" . $grn_details_data["grn_number"] . "'");
                                                                            while ($poItems_data = $poItems_result->fetch_array()) { ?>
                                                                                <tr>
                                                                                    <td><?= $itemCount++ ?></td>
                                                                                    <td><?= $poItems_data["grn_p_id"] ?></td>
                                                                                    <td><?= $poItems_data["name"] ?></td>
                                                                                    <td><?= $poItems_data["grn_p_qty"] ?></td>
                                                                                    <td><?= $poItems_data["p_free_qty"] ?></td>
                                                                                    <td><?= number_format($poItems_data["grn_p_price"], 0) ?></td>
                                                                                    <td><?= number_format($poItems_data["grn_p_qty"] * $poItems_data["grn_p_price"], 0) ?></td>
                                                                                    <td><?= $poItems_data["p_plus_discount"] ?></td>
                                                                                    <td><?= number_format($poItems_data["grn_p_cost"], 0) ?></td>
                                                                                    <td><?= number_format($poItems_data["grn_u_cost"], 0) ?></td>
                                                                                </tr>
                                                                            <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?= $grn_details_data['grn_number'] ?>','<?= $grn_details_data['invoice_number'] ?>','<?= $grn_details_data['grn_date'] ?>');"> <i class="nav-icon fas fa-copy"></i> PRINT</button>
                                                                </ul>
                                                            </td>
                                                            <td><?= $grn_details_data["grn_date"] ?></td>
                                                            <td><?= number_format($grn_details_data["grn_sub_total"], 0) ?></td>
                                                        </tr>
                                                    <?php } ?>
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
        function printTable(grnNumber, invoice_number, grnDate) {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Preview</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="container">');
            printWindow.document.write('<h2 class="text-center bg-success text-light" style="margin-top:5px;padding:3px;">GOODS RECEIPT NOTES</h2>');
            printWindow.document.write('<div class="col-12" style="margin-top: 50px;margin-bottom: 20px;font-family: monospace;">');
            printWindow.document.write('<div class="row">');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h5>GRN Number : ' + grnNumber + '</h5>');
            printWindow.document.write('</div>');
            if (invoice_number) {
                printWindow.document.write('<div class="col-12" style="text-align: start;">');
                printWindow.document.write('<h5>Invoice Number : ' + invoice_number + '</h5>');
                printWindow.document.write('</div>');
            }
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h6>Added : ' + grnDate + '</h6>');
            printWindow.document.write('</div>');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h6>Printed : <?= date('Y-m-d H:i:s') ?></h6>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
            printWindow.document.write(document.getElementById('poItemsTable' + grnNumber).outerHTML);
            printWindow.document.write('</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
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
</body>

</html>