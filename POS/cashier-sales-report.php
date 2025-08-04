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

        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .card {
            border-radius: 0.5rem;
            border: none;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .totalAmount {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .table-responsive {
            overflow-x: auto;
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
                    <section class="content py-4">
                        <div class="container-fluid">
                            <!-- Filter Card -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
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
                            </div>

                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <!-- Sell Amount -->
                                                <div class="col-md-6 col-lg-2">
                                                    <div class="card bg-success text-white h-100 shadow">
                                                        <div class="card-body text-center py-4">
                                                            <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                                                            <h5 class="card-title text-uppercase mb-3">Sell Amount</h5>
                                                            <h2 id="totalSale" class="mb-1 fw-bold">0.00</h2>
                                                            <small class="opacity-75">LKR</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Invoice Total -->
                                                <div class="col-md-6 col-lg-2">
                                                    <div class="card bg-purple text-white h-100 shadow">
                                                        <div class="card-body text-center py-4">
                                                            <i class="fas fa-file-invoice-dollar fa-2x mb-3"></i>
                                                            <h5 class="card-title text-uppercase mb-3">Invoice Total</h5>
                                                            <h2 id="totalInvoice" class="mb-1 fw-bold">0.00</h2>
                                                            <small class="opacity-75">LKR</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Cash Payments -->
                                                <div class="col-md-6 col-lg-2">
                                                    <div class="card bg-info text-white h-100 shadow">
                                                        <div class="card-body text-center py-4">
                                                            <i class="fas fa-money-bill-wave fa-2x mb-3"></i>
                                                            <h5 class="card-title text-uppercase mb-3">Cash Payments</h5>
                                                            <h2 id="totalCash" class="mb-1 fw-bold">0.00</h2>
                                                            <small class="opacity-75">LKR</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card Payments -->
                                                <div class="col-md-6 col-lg-2">
                                                    <div class="card bg-primary text-white h-100 shadow">
                                                        <div class="card-body text-center py-4">
                                                            <i class="fas fa-credit-card fa-2x mb-3"></i>
                                                            <h5 class="card-title text-uppercase mb-3">Card Payments</h5>
                                                            <h2 id="totalCard" class="mb-1 fw-bold">0.00</h2>
                                                            <small class="opacity-75">LKR</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Cash Out -->
                                                <div class="col-md-6 col-lg-2">
                                                    <div class="card bg-danger text-white h-100 shadow">
                                                        <div class="card-body text-center py-4">
                                                            <i class="fas fa-sign-out-alt fa-2x mb-3"></i>
                                                            <h5 class="card-title text-uppercase mb-3">Cash Out</h5>
                                                            <h2 id="totalOut" class="mb-1 fw-bold">0.00</h2>
                                                            <small class="opacity-75">LKR</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0 text-dark">
                                                <i class="fas fa-table me-2"></i>Sales Details
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped table-bordered">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Invoice #</th>
                                                            <th>Patient Name</th>
                                                            <th>REG Number</th>
                                                            <th>Contact No.</th>
                                                            <th>Doctor Name</th>
                                                            <th>Total Amount</th>
                                                            <th>Paid Amount</th>
                                                            <th>Payment Type</th>
                                                            <th>Bill Type</th>
                                                            <th>Cashier</th>
                                                            <th>Shop</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="saleInvoiceData" class="align-middle">
                                                        <!-- Data will be inserted here -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-dark text-white">
                                                            <td colspan="8" class="fw-bold fs-5">TOTAL SALES</td>
                                                            <td colspan="3" id="totalSales" class="fw-bold fs-5 text-end">0.00 LKR</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <button class="btn btn-outline-primary me-2">
                                                <i class="fas fa-file-excel me-2"></i>Export to Excel
                                            </button>
                                            <button class="btn btn-outline-secondary" onclick="window.print()">
                                                <i class="fas fa-print me-2"></i>Print Report
                                            </button>
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
                    var invoice_total = 0;

                    var result = JSON.parse(response);

                    if (result.status === 'success') {

                        $('#saleInvoiceData').empty();

                        result.tableData.forEach(function(item) {

                            invoice_total += (1 - (item.discount_percentage / 100)) * item.total_amount;

                            var row = '<tr>' +
                                '<td><lable class="labInvo">' + item.invoice_id + '</lable> <br> ' + item.created + '</td>' +
                                '<td>' + item.p_name + '</td>' +
                                '<td>' + item.reg + '</td>' +
                                '<td>' + item.contact_no + '</td>' +
                                '<td>' + item.d_name + '</td>' +
                                '<td>' + item.total_amount + '</td>' +
                                '<td>' + item.paidAmount + '</td>' +
                                '<td>' + item.payment_type + '</td>' +
                                '<td>' + item.bill_type_name + '</td>' +
                                '<td>' + item.name + '</td>' +
                                '<td>' + item.shopName + '</td>' +
                                '</tr>';
                            // $('#saleInvoiceData').append(row);
                            document.getElementById('saleInvoiceData').insertAdjacentHTML('beforeend', row);
                        });

                        $("#totalSale").text(result.sellAmount);
                        // $("#totalSales").text(result.sellAmount);
                        $("#totalCash").text(result.cashpayment);
                        $("#totalCard").text(result.cardPayment);
                        $("#totalOut").text(result.cashOut);
                        $("#totalInvoice").text(Number(invoice_total).toLocaleString());

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