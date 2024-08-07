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

        .labInvo {
            font-weight: bold;
            color: #3E8F0C;
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
                            <h1>ALL Sales Report</h1>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            if (isset($_SESSION['store_id'])) {

                $currentDate = date('Y-m-d');

                $userLoginData = $_SESSION['store_id'];

                foreach ($userLoginData as $userData) {
                    $shop_id = $userData['shop_id'];
                    $user_id = $userData['id'];
            ?>
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="row">
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
                                        </div>
                                    </div>

                                    <div id="totalValuesFilterData" class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="card card-body bg-success">
                                                            <h2 class="text-white text-uppercase">Sell Amount</h2>
                                                            <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount
                                                                FROM invoices WHERE DATE(`created`) = '$currentDate'")); ?>
                                                            <p class="totalAmount"><?= number_format($result['total_amount'], 2); ?> LKR</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card card-body bg-info">
                                                            <h2 class="text-white text-uppercase">Cash Payments</h2>
                                                            <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(paidAmount) AS cash_amount
                                                                FROM invoices WHERE DATE(`created`) = '$currentDate'")); ?>
                                                            <p class="totalAmount"><?= number_format($result['cash_amount'], 2); ?> LKR</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card card-body bg-primary">
                                                            <h2 class="text-white text-uppercase">Card Payments</h2>
                                                            <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(cardPaidAmount) AS cardPaidAmount
                                                                FROM invoices WHERE DATE(`created`) = '$currentDate'")); ?>
                                                            <p class="totalAmount"><?= number_format($result['cardPaidAmount'], 2); ?> LKR</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card card-body bg-danger">
                                                            <h2 class="text-white text-uppercase">Cash Out</h2>
                                                            <?php $result = mysqli_fetch_assoc($conn->query("SELECT ROUND(SUM(balance), 2) AS cashout
                                                                FROM invoices WHERE DATE(`created`) = '$currentDate'")); ?>
                                                            <p class="totalAmount">-<?= $result['cashout']; ?> LKR</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <!-- <button class="no-print btn btn-primary" onclick="window.print()">Print Table</button> -->
                                                <table id="mytable" class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <th>Invoice Number</th>
                                                            <th>Patient Name</th>
                                                            <th>REG Number</th>
                                                            <th>Contact No.</th>
                                                            <th>Doctor Name</th>
                                                            <th>Total Amount</th>
                                                            <th>Payment Type</th>
                                                            <th>Bill Type</th>
                                                            <th>Chashier</th>
                                                            <th>Shop</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="cash-sale">
                                                        <?php
                                                        $currentDate = date("Y-m-d");
                                                        $sql = $conn->query("SELECT invoices.*, payment_type.payment_type,
                                                            bill_type.bill_type_name, users.name, shop.shopName
                                                            FROM invoices
                                                            INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method
                                                            INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id
                                                            INNER JOIN users ON users.id = invoices.user_id
                                                            INNER JOIN shop ON shop.shopId = invoices.shop_id
                                                            WHERE DATE(`created`) = '$currentDate'    ORDER BY  created DESC");

                                                        $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount 
                                                                   FROM invoices 
                                                                   WHERE DATE(`created`) = '$currentDate' 
                                                                   "));
                                                        while ($row = mysqli_fetch_assoc($sql)) {
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <label class="labInvo"><?= $row['invoice_id']; ?></label>
                                                                    <br><?= $row['created']; ?>
                                                                </td>
                                                                <td><?= $row['p_name']; ?></td>
                                                                <td><?= $row['reg']; ?></td>
                                                                <td><?= $row['contact_no']; ?></td>
                                                                <td><?= $row['d_name']; ?></td>
                                                                <td><?= number_format($row['total_amount']); ?></td>
                                                                <td><?= $row['payment_type']; ?></td>
                                                                <td><?= $row['bill_type_name']; ?></td>
                                                                <td><?= $row['name']; ?></td>
                                                                <td><?= $row['shopName']; ?></td>
                                                            </tr>
                                                        <?php
                                                        } ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-dark">
                                                            <td></td>
                                                            <td colspan="7" class="fw-bold" style="font-size:larger;">Total Sales</td>
                                                            <td colspan="2" class="fw-bold text-right" style="font-size:larger;"><?= number_format($result['total_amount'], 2); ?> LKR</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
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
                url: "actions/cashier_all_sales_.php",
                method: "POST",
                data: {
                    sd: JSON.stringify(soteDate)
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
                    order: [
                        [0, 'asc']
                    ],
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