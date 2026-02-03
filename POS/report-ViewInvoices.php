<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$start_date = '';
$end_date = '';
$shop_id = '';
$invoices = [];
$totalPrice = 0;
$totalValue = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $shop_id = $_POST['shop_id'];

    $result = $conn->query("SELECT invoices.*, users.name AS cashier
    FROM invoices
    INNER JOIN users ON invoices.user_id = users.id
    WHERE
    Date(created) BETWEEN '$start_date' AND '$end_date'
    AND invoices.shop_id = '$shop_id'
    ");
    $invoices = $result->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "Invoices Between " . $start_date . " and " . $end_date;
        } else {
            echo "View Invoices";
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

        <div class="content-wrapper bg-dark">

            <!-- Main content -->

            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Card start -->
                        <div class="card bg-dark">
                            <div class="card-header">
                                <h1>
                                    Invoices
                                    <?php
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        echo "Between " . $start_date . " and " . $end_date;
                                    }
                                    ?>
                                </h1>
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

                                        <div class="col-auto">
                                            <label for="end_date" class="col-form-label">Shop:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select name="shop_id" id="shop_id" class="form-control" required>
                                                <option value="" disabled selected hidden>Select Shop</option>
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

                    <!-- Data table start -->
                    <div class="card-body overflow-auto">
                        <table id="invoicesTable" class="table table-bordered table-dark table-hover">
                            <thead>
                                <tr class="bg-info">
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Doctor</th>
                                    <th>Total Amount</th>
                                    <th>Discount Percentages</th>
                                    <th>Cash Paid</th>
                                    <th>Card Paid</th>
                                    <th>Balance</th>
                                    <th>Cashier</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($invoices)) {
                                    foreach ($invoices as $invoice) {
                                ?>
                                        <tr>
                                            <td> <?= $invoice['invoice_id']; ?> <br> <?= $invoice['created']; ?></td>
                                            <td> <?= $invoice['p_name']; ?></td>
                                            <td> <?= $invoice['d_name']; ?></td>
                                            <td> <?= $invoice['total_amount']; ?></td>
                                            <td> <?= $invoice['discount_percentage']; ?></td>
                                            <td> <?= $invoice['paidAmount']; ?></td>
                                            <td> <?= $invoice['cardPaidAmount']; ?></td>
                                            <td> <?= $invoice['balance']; ?></td>
                                            <td> <?= $invoice['cashier']; ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
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
            $("#invoicesTable")
                .DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    // aaSorting: [],
                    order: [
                        [0, 'asc']
                    ],
                    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                })
                .buttons()
                .container()
                .appendTo("#invoicesTable_wrapper .col-md-6:eq(0)");
        });
    </script>

</body>

</html>