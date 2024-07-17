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

    <style>
        .totalAmount {
            font-size: 50px;
            font-weight: bold;
        }
        .cashier-cash , .cashier-card , .cashier-out{
           font-weight: bold;
  padding: 10px;
        }
        .cashier-cash{
            background: #4b8c9f;
        }
        .cashier-card{
            background: #0070ff;
        }
        .cashier-out{
            background: #e35151;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/ac_sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Cashier's Today's Report</h1>
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
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <?php
                                               $cashiers_rs= $conn->query("SELECT SUM(total_amount) AS total_amount , SUM(paidAmount) AS cashPayments ,
                                               SUM(cardPaidAmount) AS cardPayments ,SUM(balance) AS cashOut, users.name AS name , shop.shopName AS shopName 
                                                                FROM `invoices` 
                                                                INNER JOIN users ON users.id = invoices.user_id
                                                                INNER JOIN shop ON shop.shopId = invoices.shop_id
                                                                GROUP BY user_id;");
                                                                while($cashiers_data=$cashiers_rs->fetch_assoc()){
                                                                    ?>
                                                                      <div class="col-md-3">
                                                    <div class="card card-body text-white" style="background-color:#15b580 !important" >
                                                        <h2 class="text-white text-uppercase"><?= $cashiers_data['name'] ?></h2>
                                                       <lable><?= $cashiers_data['shopName'] ?></lable>
                                                        <p class="totalAmount"><?= $cashiers_data['total_amount'] ?> LKR</p>
                                                        <p class="cashier-cash" >Card Payments : <?= $cashiers_data['cardPayments'] ?> LKR</p>
                                                        <p class="cashier-card" >Cash Payments : <?= $cashiers_data['cashPayments'] ?> LKR</p>
                                                        <p class="cashier-out" >Cash Out : -<?= $cashiers_data['cashOut'] ?> LKR</p>
                                                    </div>
                                                </div>
                                                                    <?php
                                                                }
                                                ?>
                                              
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
                    [0, 'desc']
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
            });

            $('#mytable2').DataTable({
                // order: [[0, 'desc']],
                dom: 'Bfrtip',
                aaSorting: [],
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                "footerCallback": function(row, data, start, end, display) {
                    //Get data here
                    var totalAmount = 0;
                    for (var i = 0; i < data.length; i++) {
                        totalAmount += parseFloat(data[i][4]);
                    }
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
                    var totalAmount = 0;
                    for (var i = 0; i < data.length; i++) {
                        totalAmount += parseFloat(data[i][3]);
                    }
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
                    var totalAmount = 0;
                    for (var i = 0; i < data.length; i++) {
                        totalAmount += parseFloat(data[i][4]);
                    }
                    $("#totalReceived").text(totalAmount);
                }
            });
        });
    </script>

</body>

</html>