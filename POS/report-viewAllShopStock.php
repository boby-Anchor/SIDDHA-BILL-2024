<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
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
    <title>View All Shop Stock</title>

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

        <div class="content-wrapper bg-dark">

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <h3 class="card-title">Products Stock</h3>
                                </div>
                                <div class="card-body overflow-auto">

                                    <table id="stockTable" class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <th>Barcode</th>
                                                <th>Product</th>
                                                <th>Brand</th>
                                                <th>Volume</th>
                                                <th>Price</th>
                                                <th>Hub</th>
                                                <th>Pharmacy</th>
                                                <th>YA</th>
                                                <th>Refilling</th>
                                                <th>Online</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($_SESSION['store_id'])) {
                                                $userLoginData = $_SESSION['store_id'];

                                                $sql = $conn->query("SELECT stock_item_code, stock_item_name, item_s_price, p_brand.name AS brand,
                                                unit_category_variation.ucv_name, medicine_unit.unit,
                                                SUM(CASE WHEN stock_shop_id = 1 THEN stock_item_qty ELSE 0 END) AS hub_qty,
                                                SUM(CASE WHEN stock_shop_id = 2 THEN stock_item_qty ELSE 0 END) AS pharmacy_qty,
                                                SUM(CASE WHEN stock_shop_id = 9 THEN stock_item_qty ELSE 0 END) AS YA_qty,
                                                SUM(CASE WHEN stock_shop_id = 5 THEN stock_item_qty ELSE 0 END) AS RF_qty,
                                                SUM(CASE WHEN stock_shop_id = 10 THEN stock_item_qty ELSE 0 END) AS OS_qty
                                                FROM stock2
                                                INNER JOIN p_medicine ON stock2.stock_item_code = p_medicine.code
                                                INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                                                INNER JOIN unit_category_variation ON p_medicine.unit_variation = unit_category_variation.ucv_id
                                                INNER JOIN medicine_unit ON unit_category_variation.p_unit_id = medicine_unit.id
                                                GROUP BY stock_item_code, item_s_price
                                                ");

                                                while ($row = mysqli_fetch_assoc($sql)) {
                                                    $hub_qty = $row['hub_qty'] < 0 ? 0 : $row['hub_qty'];
                                                    $pharmacy_qty = $row['pharmacy_qty'] < 0 ? 0 : $row['pharmacy_qty'];
                                                    $YA_qty = $row['YA_qty'] < 0 ? 0 : $row['YA_qty'];
                                            ?>
                                                    <tr>
                                                        <td> <?= $row['stock_item_code']; ?> </td>
                                                        <td><?= $row['stock_item_name']; ?></td>
                                                        <td><?= $row['brand']; ?></td>
                                                        <td><?= $row['ucv_name']; ?><?= $row['unit']; ?></td>
                                                        <td><?= $row['item_s_price']; ?></td>
                                                        <td><?= $hub_qty; ?></td>
                                                        <td><?= $pharmacy_qty ?></td>
                                                        <td><?= $YA_qty ?></td>
                                                        <td><?= $row['RF_qty'] ?></td>
                                                        <td><?= $row['OS_qty'] ?></td>
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

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->
    <!-- Page specific script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Initialize Select2 Elements
            $(".select2bs4").select2({
                theme: "bootstrap4",
            });
        });
    </script>

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