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
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <form method="POST" id="filterForm">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-auto">
                                                        <label for="start_date" class="col-form-label">Start Date:</label>
                                                    </div>
                                                    <div class="col-auto">
                                                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                                                    </div>

                                                    <div class="col-auto">
                                                        <label for="end_date" class="col-form-label">End Date:</label>
                                                    </div>
                                                    <div class="col-auto">
                                                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-outline-dark">Filter</button>
                                                    </div>
                                                </div>
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
                                                        <p id="totalSale" class="totalAmount"></p>
                                                        <p class="totalAmount">LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-info">
                                                        <h2 class="text-white text-uppercase">Cash Payments</h2>
                                                        <p id="totalCash" class="totalAmount"></p>
                                                        <p class="totalAmount"> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-primary">
                                                        <h2 class="text-white text-uppercase">Card Payments</h2>
                                                        <p id="totalCard" class="totalAmount"></p>
                                                        <p class="totalAmount"> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-danger">
                                                        <h2 class="text-white text-uppercase">Cash Out</h2>
                                                        <p id="totalOut" class="totalAmount"></p>
                                                        <p class="totalAmount"> LKR</p>
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
                                            <table id="table" class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice Number</th>
                                                        <th>Patient Name</th>
                                                        <th>REG Number</th>
                                                        <th>Contact No.</th>
                                                        <th>Doctor Name</th>
                                                        <th>Total Amount</th>
                                                        <th>Cash Amount</th>
                                                        <th>Card Paid Amount</th>
                                                        <th>Balance</th>
                                                        <th>Payment Type</th>
                                                        <th>Bill Type</th>
                                                        <th>Chashier</th>
                                                        <th>Shop</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="saleInvoiceData">
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-dark">
                                                        <td colspan="8" class="fw-bold" style="font-size:larger;">Total Sales</td>
                                                        <td colspan="4" id="totalSales" class="fw-bold text-right" style="font-size:larger;"><?php //number_format($result['total_amount'], 2); 
                                                                                                                                                ?> LKR</td>
                                                    </tr>
                                                </tfoot>
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

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Data Table JS -->
    <?php //include("part/data-table-js.php"); 
    ?>

    <!-- send data SearchSalesFilterDate -->
    <script>
        window.onload = function() {
            var todayDate = new Date().toISOString().split('T')[0]
            filterData(todayDate, todayDate);
        };

        document.getElementById('filterForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            filterData(startDate, endDate);
        });

        function filterData(startDate, endDate) {

            var sortDates = {
                startDate: startDate,
                endDate: endDate
            };

            $.ajax({
                url: "actions/cashier_total_sale_values.php",
                method: "POST",
                data: {
                    sortDates: JSON.stringify(sortDates),
                },
                success: function(response) {

                    var result = JSON.parse(response);

                    if (result.status === 'success') {

                        var item_total = 0;
                        var card_total = 0;
                        var cash_total = 0;
                        var balance_total = 0;


                        $("#totalSale").text(result.sellAmount);
                        // $("#totalSales").text(result.sellAmount);
                        $("#totalCash").text(result.cashpayment);
                        $("#totalCard").text(result.cardPayment);
                        $("#totalOut").text(result.cashOut);

                        $('#saleInvoiceData').empty();

                        result.tableData.forEach(function(item) {

                            item_total += parseFloat(item.total_amount) || 0;
                            card_total += parseFloat(item.cardPaidAmount) || 0;
                            cash_total += parseFloat(item.paidAmount) || 0;
                            balance_total += parseFloat(item.balance) || 0;

                            var row = '<tr>' +
                                '<td><lable class="labInvo">' + item.invoice_id + '</lable> <br> ' + item.created + '</td>' +
                                '<td>' + item.p_name + '</td>' +
                                '<td>' + item.reg + '</td>' +
                                '<td>' + item.contact_no + '</td>' +
                                '<td>' + item.d_name + '</td>' +
                                '<td>' + item.total_amount + '</td>' +
                                '<td>' + item.paidAmount + '</td>' +
                                '<td>' + item.cardPaidAmount + '</td>' +
                                '<td>' + item.balance + '</td>' +
                                '<td>' + item.payment_type + '</td>' +
                                '<td>' + item.bill_type_name + '</td>' +
                                '<td>' + item.name + '</td>' +
                                '<td>' + item.shopName + '</td>' +
                                '</tr>';
                            // $('#saleInvoiceData').append(row);
                            document.getElementById('saleInvoiceData').insertAdjacentHTML('beforeend', row);
                        });

                        $("#table tfoot").append(
                            '<tr>' +
                            '<td>' + item_total + '</td>' +
                            '<td>' + card_total + '</td>' +
                            '<td>' + cash_total + '</td>' +
                            '<td>' + balance_total + '</td>' +
                            '</tr>'
                        )

                    } else {
                        alert('response failed');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
    <!-- Page specific script -->

</body>

</html>