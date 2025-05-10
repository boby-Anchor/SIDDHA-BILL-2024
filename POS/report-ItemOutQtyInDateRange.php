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
    $shop_id = isset($_POST['shop_id']) ? $_POST['shop_id'] : 'select shop';

    // Extract the day from the input range
    $startDay = date("d", strtotime($start_date));
    $endDay = date("d", strtotime($end_date));

    // Calculate the previous month's range
    $previousMonthStart = date("Y-m-$startDay", strtotime("first day of last month", strtotime($start_date)));
    $previousMonthEnd = date("Y-m-$endDay", strtotime("first day of last month", strtotime($end_date)));

    // Calculate the range for two months ago
    $twoMonthsAgoStart = date("Y-m-$startDay", strtotime("first day of -2 months", strtotime($start_date)));
    $twoMonthsAgoEnd = date("Y-m-$endDay", strtotime("first day of -2 months", strtotime($end_date)));

    // Calculate the range for three months ago
    $threeMonthsAgoStart = date("Y-m-$startDay", strtotime("first day of -3 months", strtotime($start_date)));
    $threeMonthsAgoEnd = date("Y-m-$endDay", strtotime("first day of -3 months", strtotime($end_date)));

    $thisMonthData = $conn->query("SELECT invoiceItem, invoiceItem_unit, invoiceItem_price,
        SUM(invoiceItem_qty) AS this_month_total_qty
        FROM
        invoiceitems
        INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber
        WHERE DATE(invoiceDate) BETWEEN '$start_date' AND '$end_date'
        AND invoices.shop_id = '$shop_id'
        GROUP BY invoiceItem, invoiceItem_price
        ORDER BY 'invoiceItem' ASC
      ");
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
                <div class="row">
                    <div class="col-12">
                        <!-- Card start -->
                        <div class="card bg-dark">
                            <div class="card-header">
                                <?php
                                if (isset($_POST['start_date'])) {
                                    $shop_name = '';
                                    $shop_name_data = $conn->query("SELECT shopName FROM shop WHERE shopId = '$shop_id'");
                                    if ($shop_row = $shop_name_data->fetch_assoc()) {
                                        $shop_name = $shop_row['shopName'];
                                    }
                                    echo "<h1>Total Item Sales Qty of " . htmlspecialchars($shop_name, ENT_QUOTES, 'UTF-8') . "</h1>";
                                } else {
                                    echo "<h1>Total Item Sales Qty</h1>";
                                }
                                ?>
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
                    <div class="col-7">
                        <!-- Data table start -->
                        <div class="card-body overflow-auto">
                            <table id="stockTable" class="table table-bordered table-dark table-hover">
                                <thead>
                                    <tr class="bg-info">
                                        <th>Invoice Item</th>
                                        <th>Brand</th>
                                        <?php
                                        if (isset($_POST['start_date'])) {
                                        ?>
                                            <th><?php echo "$start_date - $end_date" ?></th>
                                            <th><?php echo "$previousMonthStart - $previousMonthEnd" ?></th>
                                            <th><?php echo "$twoMonthsAgoStart - $twoMonthsAgoEnd" ?></th>
                                            <th><?php echo "$threeMonthsAgoStart - $threeMonthsAgoEnd" ?></th>
                                            <th>Details</th>

                                        <?php
                                        } else {
                                        ?>
                                            <th>This Month Total Qty</th>
                                            <th>Previous Month Total Qty</th>
                                            <th>Two Months Ago Total Qty</th>
                                            <th>Three Months Ago Total Qty</th>
                                            <th>Details</th>
                                        <?php
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_POST['start_date'])) {
                                        if (isset($thisMonthData)) {
                                            while ($row = mysqli_fetch_assoc($thisMonthData)) {
                                                $invoiceItemName =  $row['invoiceItem'];
                                                $invoiceItemUnit =  $row['invoiceItem_unit'];
                                                $invoiceItemPrice =  $row['invoiceItem_price'];
                                    ?>
                                                <tr>
                                                    <td>
                                                        <?php echo "$invoiceItemName <br/>";

                                                        $barcodeData = $conn->query("SELECT stock_item_code
                                                        FROM stock2
                                                        WHERE stock2.stock_item_name = '$invoiceItemName'
                                                        AND (stock2.item_s_price=$invoiceItemPrice OR stock2.unit_s_price = $invoiceItemPrice)
                                                        GROUP BY stock_item_code
                                                        ORDER BY stock_item_code DESC
                                                        LIMIT 1
                                                        ");
                                                        if ($barcodeData) {
                                                            if ($barcodeRow = mysqli_fetch_assoc($barcodeData)) {
                                                                $barcode = $barcodeRow['stock_item_code'];

                                                                $unitData = $conn->query("SELECT medicine_unit.unit AS unit, unit_category_variation.ucv_name AS volume, p_brand.name as brand
                                                                    FROM p_medicine
                                                                    JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                                                                    JOIN medicine_unit ON medicine_unit.id = unit_category_variation.p_unit_id
                                                                    JOIN p_brand ON p_medicine.brand = p_brand.id
                                                                    WHERE p_medicine.name = '$invoiceItemName'
                                                                    AND p_medicine.code = '$barcode'
                                                                    ");

                                                                if ($unitData) {
                                                                    while ($unitRow = mysqli_fetch_assoc($unitData)) {
                                                                        echo "(" . $unitRow ? $unitRow['volume'] . $unitRow['unit']  . ")<br/>" : "No unit <br/>";
                                                                        $brand = $unitRow['brand'];
                                                                    }
                                                                }
                                                                echo "
                                                                Price:-" . $invoiceItemPrice;
                                                            }
                                                        }

                                                        // $unitData = $conn->query("SELECT medicine_unit.unit AS unit, unit_category_variation.ucv_name AS volume FROM stock2
                                                        // JOIN p_medicine ON p_medicine.code = p_medicine.unit_variation
                                                        // JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                                                        // JOIN medicine_unit ON medicine_unit.id = unit_category_variation.p_unit_id
                                                        // WHERE stock2.stock_item_name = '$invoiceItemName'
                                                        // AND stock2.item_s_price = '$invoiceItemPrice'
                                                        // AND stock2.stock_shop_id = '$shop_id'
                                                        // ");
                                                        // if ($unitData) {
                                                        //     while ($unitRow = mysqli_fetch_assoc($unitData)) {
                                                        //         echo "(" . $unitRow['volume'];
                                                        //         echo $unitRow['unit'] . ")";
                                                        //     }
                                                        // }
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?= $brand; ?>
                                                    </td>
                                                    <td id="this_month_qty"><?= $row['this_month_total_qty']; ?></td>
                                                    <td id="last_month_qty">
                                                        <?php
                                                        $previousMonthData = $conn->query("SELECT SUM(invoiceItem_qty) AS prev_month_total_qty
                                                        FROM invoiceitems
                                                        INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber
                                                        WHERE invoices.shop_id = '$shop_id'
                                                        AND invoiceItem='$invoiceItemName'
                                                        AND invoiceItem_unit ='$invoiceItemUnit'
                                                        AND invoiceItem_price = '$invoiceItemPrice'
                                                        AND DATE(invoiceDate) BETWEEN '$previousMonthStart' AND '$previousMonthEnd'
                                                        ");

                                                        if (isset($previousMonthData)) {
                                                            while ($prevMonthCell = mysqli_fetch_assoc($previousMonthData)) {
                                                                echo $prevMonthCell['prev_month_total_qty'];
                                                            }
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td id="two_months_ago_qty">
                                                        <?php
                                                        $twoMonthsAgoData = $conn->query("SELECT SUM(invoiceItem_qty) AS two_months_ago_total_qty
                                                        FROM invoiceitems
                                                        INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber
                                                        WHERE invoices.shop_id = '$shop_id'
                                                        AND invoiceItem='$invoiceItemName'
                                                        AND invoiceItem_unit ='$invoiceItemUnit'
                                                        AND invoiceItem_price = '$invoiceItemPrice'
                                                        AND DATE(invoiceDate) BETWEEN '$twoMonthsAgoStart' AND '$twoMonthsAgoEnd'
                                                        ");

                                                        if (isset($twoMonthsAgoData)) {
                                                            while ($twoMonthsAgoCell = mysqli_fetch_assoc($twoMonthsAgoData)) {
                                                                echo $twoMonthsAgoCell['two_months_ago_total_qty'];
                                                            }
                                                        } else {
                                                            echo "0";
                                                        }

                                                        ?>
                                                    </td>
                                                    <td id="three_months_ago_qty">
                                                        <?php
                                                        $threeMonthsAgoData = $conn->query("SELECT SUM(invoiceItem_qty) AS three_months_ago_total_qty
                                                        FROM invoiceitems
                                                        INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber
                                                        WHERE invoices.shop_id = '$shop_id'
                                                        AND invoiceItem='$invoiceItemName'
                                                        AND invoiceItem_unit ='$invoiceItemUnit'
                                                        AND invoiceItem_price = '$invoiceItemPrice'
                                                        AND DATE(invoiceDate) BETWEEN '$threeMonthsAgoStart' AND '$threeMonthsAgoEnd'
                                                        ");

                                                        if (isset($threeMonthsAgoData)) {
                                                            while ($threeMonthsAgoCell = mysqli_fetch_assoc($threeMonthsAgoData)) {
                                                                echo $threeMonthsAgoCell['three_months_ago_total_qty'];
                                                            }
                                                        } else {
                                                            echo "0";
                                                        }

                                                        ?>
                                                    </td>
                                                    <td class="align-items-center align-content-center">
                                                        <button id="updateCharButton" class="button bg-dark border rounded border-success" onclick="updateChartData(this)">
                                                            <i class="nav-icon fas fa-eye p-2"></i>
                                                        </button>
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

                    <div class="col-5">
                        <canvas id="myBarChart" width="400" height="200"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const chartElement = document.getElementById('myBarChart').getContext('2d');
        const myBarChart = new Chart(chartElement, {
            type: 'bar',
            data: {
                labels: ['This Month', 'Last Month', 'Two Months Ago', 'Three Months Ago'],
                datasets: [{
                    label: 'Items Qty',
                    data: [0, 0, 0],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        $(document).on("click", "#updateCharButton", function() {
            var this_month_qty = $(this).closest("tr").find("#this_month_qty").text();
            var last_month_qty = $(this).closest("tr").find("#last_month_qty").text();
            var two_months_ago_qty = $(this).closest("tr").find("#two_months_ago_qty").text();
            var three_months_ago_qty = $(this).closest("tr").find("#three_months_ago_qty").text();

            // alert(this_month_qty + " " + last_month_qty + " " + two_months_ago_qty);
            const newData = [this_month_qty, last_month_qty, two_months_ago_qty, three_months_ago_qty];
            myBarChart.data.datasets[0].data = newData;
            myBarChart.update();

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
                        [2, 'desc']
                    ],
                    searching: true,
                    // buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    buttons: ["excel", "pdf", "print", "colvis"],
                })
                .buttons()
                .container()
                .appendTo("#stockTable_wrapper .col-md-6:eq(0)");
        });
    </script>

</body>

</html>