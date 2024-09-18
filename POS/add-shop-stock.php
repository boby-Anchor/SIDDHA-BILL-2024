<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$invoiceNumber = '';
$items = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['invoiceNumber'])) {
        $invoiceNumber = $_POST['invoiceNumber'];

        $invResult = $conn->query("SELECT poinvoices.*, 
               users.name AS cashier, 
               shop1.shopName AS shop, 
               shop2.shopName AS poShop
        FROM poinvoices
        INNER JOIN users ON poinvoices.user_id = users.id
        INNER JOIN shop shop1 ON poinvoices.shop_id = shop1.shopId
        INNER JOIN shop shop2 ON poinvoices.po_shop_id = shop2.shopId
        WHERE invoice_id = '$invoiceNumber';
    ");

        $invoiceData = $invResult->fetch_all(MYSQLI_ASSOC);

        $result = $conn->query("SELECT *
        FROM poinvoiceitems
        WHERE invoiceNumber = '$invoiceNumber';
        ");
        $items = $result->fetch_all(MYSQLI_ASSOC);
    }
}
$totalPrice = 0;
$totalValue = 0;

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
    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body class="hold-transition layout-fixed bg-dark">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-dark">

            <div class="row">
                <div class="card-body overflow-hidden">
                    <div class="row px-4">
                        <div class="col-4">
                            <form action="" method="post">
                                <div class="input-group">
                                    <label for="invoiceNumber" class="form-control bg-dark">Inv. No:</label>
                                    <input type="text" id="invoiceNumber" name="invoiceNumber"
                                        class="form-control col-8 bg-dark"
                                        value="<?php echo htmlspecialchars($invoiceNumber); ?>" required>
                                    <button type="submit" class="form-control col-2 btn btn-success ml-2"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <?php
                        if (!empty($invoiceData)) {
                            foreach ($invoiceData as $invData):
                                ?>

                                <div class="col-12 row">
                                    <div class="col-2">
                                        <label>Cashier: <?= $invData['cashier'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Shop: <?= $invData['shop'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Sent to: <?= $invData['poShop'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Date: <?= $invData['created'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Sub total: <?= $invData['sub_total'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Discount: <?= $invData['discount_percentage'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Net total: <?= $invData['net_total'] ?></label>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        }
                        ?>
                    </div>

                    <div class="row p-4">
                        <div class="col-12">
                            <table class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Item Total</th>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($items)) {
                                        $rowNo = 0;
                                        foreach ($items as $item):
                                            $rowNo++
                                                ?>
                                            <tr>
                                                <td><?= $rowNo ?></td>
                                                <td><?= htmlspecialchars($item['invoiceItem']); ?></td>
                                                <td><?= htmlspecialchars($item['invoiceItem_qty']); ?></td>
                                                <td><?= htmlspecialchars($item['invoiceItem_unit']); ?></td>
                                                <td><?= number_format(htmlspecialchars($item['invoiceItem_price']), 0); ?></td>
                                                <td><?= number_format(htmlspecialchars($item['invoiceItem_total']), 0); ?></td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No data</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        document.getElementById('totalPriceLabel').textContent = "<?= htmlspecialchars($totalPrice) ?>";
                        document.getElementById('totalValueLabel').textContent = "<?= htmlspecialchars($totalValue) ?>";
                    </script>

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

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

</body>

</html>