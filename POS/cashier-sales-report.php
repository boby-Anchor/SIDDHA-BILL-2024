<?php
date_default_timezone_set("Asia/Colombo");
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

    <style>
        .totalAmount {
            font-size: 50px;
            font-weight: bold;
        }
    </style>

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
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Cashier's Sales Report</h1>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            if (isset($_SESSION['store_id'])) {

                $userLoginData = $_SESSION['store_id'];

                foreach ($userLoginData as $userData) {
                    $shop_id = $userData['shop_id'];
                    $user_id = $userData['id'];
            ?>
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div id="totalValuesFilterData" class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-success">
                                                        <h2 class="text-white text-uppercase">Sell Amount</h2>
                                                        <?php $currentDate = date('Y-m-d'); ?>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount"><?= number_format($result['total_amount'], 0); ?> LKR</p>
                                                    </div>
                                                </div>

                                                <?php
                                                // $invoiceItemQty_rs = $conn->query("SELECT * FROM invoiceitems
                                                //     INNER JOIN p_medicine ON  invoiceitems.invoiceItem = p_medicine.name
                                                //         WHERE DATE(`invoiceDate`) = '$currentDate'");

                                                // $total_profit = 0; // Initialize total profit
                                                // $total_cost = 0;

                                                // while ($invoiceItemQty_data = $invoiceItemQty_rs->fetch_assoc()) {
                                                //     $stock_price_rs = $conn->query("SELECT * FROM stock2 WHERE stock2.stock_item_id = '" . $invoiceItemQty_data['code'] . "'");
                                                //     $stock_price_data = $stock_price_rs->fetch_assoc();

                                                //     if ($stock_price_data !== null) {
                                                //         $stock_cost = $stock_price_data['stock_item_cost'];
                                                //         $total_cost += $stock_cost * $invoiceItemQty_data['invoiceItem_qty']; // Add today's selling cost to total cost
                                                //     }

                                                //     // Check if $stock_price_data is not null before accessing its elements
                                                //     if ($stock_price_data !== null) {
                                                //         $stock_profit = $stock_price_data['stock_s_price'] - $stock_price_data['stock_item_cost'];
                                                //         $today_profit = $stock_profit * $invoiceItemQty_data['invoiceItem_qty'];
                                                //         $total_profit += $today_profit; // Add today's profit to total profit


                                                //     } else {
                                                //         // Handle the case where $stock_price_data is null (optional)
                                                //         // For example, you can display an error message or skip this item
                                                //         echo "Error: Stock data not found for item with code " . $invoiceItemQty_data['code'];
                                                //     }
                                                // }


                                                ?>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-info">
                                                        <h2 class="text-white text-uppercase">Cash Payments</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(paidAmount) AS cash_amount FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount"><?= number_format($result['cash_amount'], 0); ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-primary">
                                                        <h2 class="text-white text-uppercase">Card Payments</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(cardPaidAmount) AS cardPaidAmount FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount"><?= number_format($result['cardPaidAmount'], 0); ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-danger">
                                                        <h2 class="text-white text-uppercase">Cash Out</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT ROUND(SUM(balance), 2) AS cashout FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount">-<?= number_format($result['cashout'], 0); ?> LKR</p>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">

                                            <form method="POST" id="filterForm">
                                                <label class="ml-2" for="start_date">Start Date:</label>
                                                <input type="date" class="mr-5" id="start_date" name="start_date" required>
                                                <label for="end_date">End Date:</label>
                                                <input type="date" class="mr-4" id="end_date" name="end_date" required>
                                                <button type="submit" class="btn btn-outline-dark">Filter</button>
                                            </form>
                                        </div>
                                        <div class="card-body">
                                            <table id="mytable" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="bg-info">
                                                        <th>REG Number</th>
                                                        <th>Invoice Number</th>
                                                        <th>Patient Name</th>
                                                        <th>Contact No.</th>
                                                        <th>Doctor Name</th>
                                                        <th>Total Amount</th>
                                                        <th>Payment Type</th>
                                                        <th>Bill Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cash-sale">
                                                    <?php
                                                    $currentDate = date("Y-m-d");
                                                    $sql = $conn->query("SELECT invoices.*, payment_type.payment_type, bill_type.bill_type_name 
                                             FROM invoices 
                                             INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method 
                                             INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id 
                                             WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'
                                             ");
                                                    $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount 
                                                                   FROM invoices 
                                                                   WHERE DATE(`created`) = '$currentDate' 
                                                                   AND user_id = '$user_id'"));
                                                    while ($row = mysqli_fetch_assoc($sql)) {
                                                    ?>
                                                        <tr>
                                                            <td><?= $row['reg']; ?></td>
                                                            <td><?= $row['invoice_id']; ?></td>
                                                            <td><?= $row['p_name']; ?></td>
                                                            <td><?= $row['contact_no']; ?></td>
                                                            <td><?= $row['d_name']; ?></td>
                                                            <td>
                                                                <?= number_format($row['total_amount'], 0); ?>
                                                            </td>
                                                            <td><?= $row['payment_type']; ?></td>
                                                            <td><?= $row['bill_type_name']; ?></td>
                                                        </tr>
                                                    <?php
                                                    } ?>
                                                    <tr class="bg-dark">
                                                        <td></td>
                                                        <td colspan="5" class="fw-bold" style="font-size:larger;">Total Sales</td>
                                                        <td colspan="2" class="fw-bold text-right" style="font-size:larger;"><?= number_format($result['total_amount'], 0); ?> LKR</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                    </section>
            <?php
                }
            }
            ?>

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

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

    <!-- send data SearchSalesFilterDate -->
    <script>
        document.getElementById('filterForm').addEventListener('submit', function(event) {
            event.preventDefault();
            SearchSalesFilterDate();
        });

        function SearchSalesFilterDate() {
            var STDATE = $("#start_date").val();
            var ENDDATE = $("#end_date").val();

            var soteDate = {
                STDATE: STDATE,
                ENDDATE: ENDDATE
            };

            $.ajax({
                url: "actions/cashier_total_sale_values.php",
                method: "POST",
                data: {
                    sd: JSON.stringify(soteDate)
                },
                success: function(data) {
                    document.getElementById("totalValuesFilterData").innerHTML = data;
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: "error",
                        title: "Error: Something went wrong!" + xhr.responseText,
                    });
                },
            });
            $.ajax({
                url: "actions/cashier_sales_.php",
                method: "POST",
                data: {
                    sd: JSON.stringify(soteDate),
                },
                success: function(response) {
                    Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: "success",
                        title: "Success: Filtered!",
                    });

                    document.getElementById("cash-sale").innerHTML = response;
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: "error",
                        title: "Error: Something went wrong!" + xhr.responseText,
                    });
                },
            });
        }
    </script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $(".select2").select2();

            $(".select2bs4").select2({
                theme: "bootstrap4",
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#mytable').DataTable({
                    // order: [
                    //     [0, 'desc']
                    // ],
                    // pageLength : 3,
                    dom: 'Bfrtip',
                    aaSorting: [],
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                    "footerCallback": function(row, data, start, end, display) {
                        var totalAmount = 0;
                        for (var i = 0; i < data.length; i++) {
                            totalAmount += parseFloat(data[i][4]);
                        }
                        $("#totalSales").text(totalAmount);
                    }
                }).buttons()
                .container()
                .appendTo("#mytable_wrapper .col-md-6:eq(0)");

            $('#mytable2').DataTable({
                // order: [[0, 'desc']],
                dom: 'Bfrtip',
                aaSorting: [],
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                "footerCallback": function(row, data, start, end, display) {
                    //Get data here
                    // console.log(data);
                    var totalAmount = 0;
                    for (var i = 0; i < data.length; i++) {
                        totalAmount += parseFloat(data[i][4]);
                    }
                    // console.log(totalAmount);
                    $("#totalExpense").text(totalAmount);
                }
            });

            $('#mytable3').DataTable({
                // order: [[0, 'desc']],
                dom: 'Bfrtip',
                aaSorting: [],
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                "footerCallback": function(row, data, start, end, display) {
                    //Get data here
                    // console.log(data);
                    var totalAmount = 0;
                    for (var i = 0; i < data.length; i++) {
                        totalAmount += parseFloat(data[i][3]);
                    }
                    // console.log(totalAmount);
                    $("#totalPurchase").text(totalAmount);
                }
            });

            $('#mytable4').DataTable({
                // order: [[0, 'desc']],
                dom: 'Bfrtip',
                aaSorting: [],
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                "footerCallback": function(row, data, start, end, display) {
                    //Get data here
                    // console.log(data);
                    var totalAmount = 0;
                    for (var i = 0; i < data.length; i++) {
                        totalAmount += parseFloat(data[i][4]);
                    }
                    // console.log(totalAmount);
                    $("#totalReceived").text(totalAmount);
                }
            });
        });
    </script>

</body>

</html>