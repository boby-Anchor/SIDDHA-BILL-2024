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
    <title>Pharmacy</title>

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
                                                <th>Image</th>
                                                <th>Product</th>
                                                <th>Brand</th>
                                                <th>Cost</th>
                                                <th>Price</th>
                                                <?php
                                                // Fetch all shops
                                                $shop_rs = $conn->query("SELECT `shopId`, `shopName` FROM shop");
                                                while ($shop_row = $shop_rs->fetch_assoc()) {
                                                ?>
                                                    <th><?= $shop_row['shopName'] ?></th>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($_SESSION['store_id'])) {
                                                $userLoginData = $_SESSION['store_id'];

                                                // Fetch data from the stock table
                                                $sql = $conn->query("
                                                SELECT stock2.*, shop.*, p_medicine.*, p_brand.name AS bName
                                                FROM stock2
                                                INNER JOIN shop ON shop.shopId = stock2.stock_shop_id
                                                INNER JOIN p_medicine ON stock2.stock_item_code = p_medicine.code
                                                INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                                                ORDER BY p_medicine.name DESC
                                                ");

                                                // Initialize an array to store product data indexed by product ID
                                                $products = array();

                                                // Group products by product ID
                                                while ($row = mysqli_fetch_assoc($sql)) {
                                                    $product_id = $row['stock_id'];

                                                    // Initialize product data if not exists
                                                    if (!isset($products[$product_id])) {
                                                        $products[$product_id] = array(
                                                            'name' => $row['name'],
                                                            'bName' => $row['bName'],
                                                            'cost' => $row['stock_item_cost'],
                                                            'prices' => array(),
                                                            'qty_by_shop' => array(),
                                                            'img' => $row['img']
                                                        );
                                                    }

                                                    // Store selling prices for the product
                                                    $products[$product_id]['prices'][$row['item_s_price']] = $row['item_s_price'];

                                                    // Store product availability by shop
                                                    $products[$product_id]['qty_by_shop'][$row['item_s_price']][$row['shopId']] = $row['stock_item_qty'];
                                                }

                                                // Loop through products and display them
                                                foreach ($products as $product_id => $product) {
                                                    // Loop through unique prices for the product
                                                    foreach ($product['prices'] as $price) {
                                            ?>
                                                        <tr>
                                                            <td style="padding:5px" class="text-center">
                                                                <img src="dist/img/product/<?php echo $product['img']; ?>" width="50" alt="Image">
                                                            </td>
                                                            <td><?php echo $product['name']; ?></td>
                                                            <td><?php echo $product['bName']; ?></td>
                                                            <td><?php echo $product['cost']; ?></td>
                                                            <td><?php echo $price; ?></td>
                                                            <?php
                                                            // Loop through all shops and display product quantity for the price
                                                            $shop_rs = $conn->query("SELECT `shopId` FROM shop");
                                                            while ($shop_row = $shop_rs->fetch_assoc()) {
                                                                $shop_id = $shop_row['shopId'];
                                                                $qty = isset($product['qty_by_shop'][$price][$shop_id]) ? $product['qty_by_shop'][$price][$shop_id] : '-';
                                                            ?>
                                                                <td><?php echo $qty; ?></td>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tr>
                                            <?php
                                                    }
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
                        [5, 'desc']
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