<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // $sql = $conn->query("SELECT SUM(gi.grn_p_qty) AS total_quantity, pm.code AS code, pm.name AS name, gi.grn_p_price AS item_price, unit_category_variation.ucv_name AS volume, medicine_unit.unit AS unit
    // FROM grn g
    // JOIN grn_item gi ON g.grn_number = gi.grn_number
    // LEFT JOIN p_medicine pm ON gi.grn_p_id = pm.code
    // INNER JOIN unit_category_variation ON pm.unit_variation = unit_category_variation.ucv_id
    // INNER JOIN medicine_unit ON unit_category_variation.p_unit_id = medicine_unit.id
    // WHERE g.grn_date BETWEEN '$start_date' AND '$end_date' AND grn_shop_id ='1'
    // GROUP BY gi.grn_p_id");
    $sql = $conn->query("SELECT
  ii.barcode AS 'barcode',
  ii.invoiceItem AS 'item name',
  ii.invoiceItem_price AS 'item price',
  SUM(CASE WHEN s.shopName = 'HUB' THEN ii.invoiceItem_qty ELSE 0 END) AS 'HUB Sales', 
  SUM(CASE WHEN s.shopName = 'Siddha.lk' THEN ii.invoiceItem_qty ELSE 0 END) AS 'Siddha Sales', 
  SUM(CASE WHEN s.shopName = 'Yakkala Ayurwedha' THEN ii.invoiceItem_qty ELSE 0 END) AS 'Yakkala Ayurwedha Sales'  
FROM
  invoices AS i
INNER JOIN
  invoiceitems AS ii ON i.invoice_id = ii.invoiceNumber 
INNER JOIN
  shop AS s ON i.shop_id = s.shopId
WHERE
  i.created BETWEEN '$start_date' AND '$end_date'
GROUP BY
  ii.invoiceItem,
  ii.invoiceItem_price  
ORDER BY `ii`.`invoiceItem` ASC");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Report | Shop Wise Items Sale</title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper">

            <!-- Main content -->

            <section class="content bg-dark">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-dark">
                            <!-- Card Header start -->
                            <div class="card-header">
                                <h1>Shop Wise Item Sale Qty</h1>
                                <div class="border-top mb-3"></div>
                                <!-- Form start -->
                                <form method="POST" id="filterForm">
                                    <div class="row g-3 accent-cyan align-items-center px-3">
                                        <div class="col-auto">
                                            <label for="start_date" class="col-form-label">Start Date:</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" id="start_date" name="start_date" class="form-control"
                                                value="<?= isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>" required>
                                        </div>
                                        <div class="col-auto">
                                            <label for="end_date" class="col-form-label">End Date:</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" id="end_date" name="end_date" class="form-control"
                                                value="<?= isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>" required>
                                        </div>
                                        <div class="ml-2">
                                            <button type="submit" class="btn btn-outline-success">Filter</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Form end -->

                            </div>
                            <!-- Card Header end -->

                        </div>
                    </div>

                    <!-- Data table start -->
                    <div class="card-body overflow-auto">
                        <table id="stockTable" class="table table-bordered table-dark table-hover">
                            <thead>
                                <!-- <tr class="bg-info">
                  <th>Barcode</th>
                  <th>Product</th>
                  <th>Volume</th>
                  <th>Qty</th>
                  <th>Item Price</th>
                  <th>Total Value</th>
                </tr> -->
                                <tr class="bg-info">
                                    <th>Barcode</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>HUB</th>
                                    <th>Siddha</th>
                                    <th>Yakkala Ayurvedha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $fullTotal = 0;

                                if (isset($_POST['start_date'])) {

                                    if (isset($sql)) {

                                        while ($row = mysqli_fetch_assoc($sql)) {
                                ?>
                                            <tr>
                                                <td> <?= $row['barcode']; ?></td>
                                                <td> <?= $row['item name']; ?></td>
                                                <td> <?= $row['item price']; ?></td>
                                                <td> <?= $row['HUB Sales']; ?></td>
                                                <td> <?= $row['Siddha Sales']; ?></td>
                                                <td> <?= $row['Yakkala Ayurwedha Sales']; ?></td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                            <!-- <tfoot>

                                <tr class="bg-blue">
                                    <td colspan="4">Total</td>
                                    <td> <?php // number_format($fullTotal, 0); ?></td>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                    <!-- Data table end -->

                </div>
            </section>
        </div>
        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->

        <!-- Alert -->
        <?php include("part/alert.php"); ?>
        <!-- Alert end -->
    </div>

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

    <!-- Page specific script -->
    <script>
        $(function() {
            $("#stockTable")
                .DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    // aaSorting: [],
                    order: [
                        [1, 'asc']
                    ],
                    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                })
                .buttons()
                .container()
                .appendTo("#stockTable_wrapper .col-md-6:eq(0)");
        });
    </script>

</body>

</html>