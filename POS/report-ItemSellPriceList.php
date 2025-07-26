<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // $shop_id = $_POST['shop_id'];
    // $end_date = $_POST['end_date'];
    $shop_id = isset($_POST['shop_id']) ? $_POST['shop_id'] : 'select shop';

    $shop_name;
    $shop_name_data = $conn->query("SELECT shopName FROM shop WHERE shopId = '$shop_id'");
    if ($shop_row = $shop_name_data->fetch_assoc()) {
        $shop_name = $shop_row['shopName'];
    }

    $sellPriceData = $conn->query("SELECT
    p_medicine.name AS p_name,
    p_medicine.code AS code,
    p_medicine.img AS img,
    p_medicine_category.name AS category, p_brand.name AS brand,
    medicine_unit.unit AS unit, unit_category_variation.ucv_name,
    stock2.stock_id AS stock_id,
    stock2.item_s_price AS itemSprice,
    stock2.unit_s_price AS unitSprice
    FROM stock2
    INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
    INNER JOIN p_medicine_category ON p_medicine.category = p_medicine_category.id
    INNER JOIN p_brand ON p_brand.id = p_medicine.brand
    INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
    INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
    WHERE stock2.stock_shop_id = '$shop_id'
    ");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        <?php
        if (isset($_POST['shop_id'])) {
            echo "Items Sell Prices List of " . htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8');
        } else {
            echo "Item Selling prices report";
        }
        ?>
    </title>

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
                        <!-- Card start -->
                        <div class="card bg-dark">
                            <div class="card-header">
                                <?php
                                if (isset($_POST['shop_id'])) {
                                    echo "<h1>Items Sell Prices List of " . htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') . "</h1>";
                                } else {
                                    echo "<h1>Item Selling prices report</h1>";
                                }
                                ?>
                                <div class="border-top mb-3"></div>
                                <!-- Form start -->
                                <form method="POST" id="filterForm">
                                    <div class="row g-3 accent-cyan align-items-center px-3">

                                        <div class="col-auto">
                                            <label for="end_date" class="col-form-label">Shop:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select name="shop_id" id="shop_id" class="form-control" required>
                                                <option value="select_shop" disabled selected hidden>Select Shop</option>
                                                <?php
                                                $shops_rs = $conn->query("SELECT shop.shopId, shop.shopName FROM shop");
                                                while ($shops_row = $shops_rs->fetch_assoc()) {
                                                ?>
                                                    <option value="<?= $shops_row['shopId'] ?>">
                                                        <?= $shops_row['shopName'] ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="ml-2">
                                            <button type="submit" class="btn btn-outline-success">Filter</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Form end -->

                            </div>
                        </div>
                        <!-- Card end -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <!-- Data table start -->
                        <div class="card-body overflow-auto">
                            <table id="sellPriceTable" class="table table-bordered table-dark table-hover">
                                <thead>
                                    <tr class="bg-info">
                                        <th>Barcode</th>
                                        <th>Item Name</th>
                                        <th>Brand</th>
                                        <th>Volume</th>
                                        <th>Category</th>
                                        <th>Item Price</th>
                                        <th>Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_POST['shop_id'])) {
                                        if (isset($sellPriceData)) {
                                            while ($row = mysqli_fetch_assoc($sellPriceData)) {
                                    ?>
                                                <tr>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['code']; ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['p_name']; ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['brand']; ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['ucv_name'] . $row['unit']; ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['category']; ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['itemSprice']; ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <?= $row['unitSprice']; ?>
                                                    </td>
                                                </tr>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Data table end -->
                    </div>
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

    <script>
        $(function() {
            $("#sellPriceTable")
                .DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    // aaSorting: [],
                    order: [
                        [1, 'asc']
                    ],
                    searching: true,
                    // buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    buttons: ["excel", "pdf", "print", "colvis"],
                })
                .buttons()
                .container()
                .appendTo("#sellPriceTable_wrapper .col-md-6:eq(0)");
        });
    </script>

</body>

</html>