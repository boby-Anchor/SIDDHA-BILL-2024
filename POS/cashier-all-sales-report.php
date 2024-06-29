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
                                           
                                        </div>
                                    </div>
                                </div>
                               <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Top Sell Product</h4>
                <form method="POST" id="filterForm">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                    <br>
                    <button type="submit">Filter</button>
                </form>
            </div>
            <div class="card-body">
                 <button class="no-print btn btn-primary" onclick="window.print()">Print Table</button>
       
                <table id="mytable" class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-info">
                            <th>Invoice Number</th>
                            <th>Patient Name</th>
                            <th>Tell</th>
                            <th>Doctor Name</th>
                            <th>REG Number</th>
                            <th>Total Amount</th>
                            <th>Payment Type</th>
                            <th>Bill Type</th>
                            <th>Chachier</th>
                            <th>SHOP</th>
                        </tr>
                    </thead>
                    <tbody id="cash-sale">
                        <?php
                        $currentDate = date("Y-m-d");
                        $sql = $conn->query("SELECT invoices.*, payment_type.payment_type, bill_type.bill_type_name ,users.name ,shop.shopName
                                             FROM invoices 
                                             INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method 
                                             INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id 
                                             INNER JOIN users ON users.id = invoices.user_id 
                         INNER JOIN shop ON shop.shopId = invoices.shop_id 
                                             WHERE DATE(`created`) = '$currentDate'    ORDER BY  created DESC");
                        $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount 
                                                                   FROM invoices 
                                                                   WHERE DATE(`created`) = '$currentDate' 
                                                                   AND user_id = '$user_id'"));
                        while ($row = mysqli_fetch_assoc($sql)) {
                        ?>
                            <tr>
                                <td><?php echo $row['invoice_id']; ?> <br><?php echo $row['created']; ?></td>
                                <td><?php echo $row['p_name']; ?></td>
                                <td><?php echo $row['contact_no']; ?></td>
                                <td><?php echo $row['d_name']; ?></td>
                                <td><?php echo $row['reg']; ?></td>
                                <td><?php echo $row['total_amount']; ?></td>
                                <td><?php echo $row['payment_type']; ?></td>
                                <td><?php echo $row['bill_type_name']; ?></td>
                                 <td><?php echo $row['name']; ?></td>
                                  <td><?php echo $row['shopName']; ?></td>
                            </tr>
                        <?php
                        } ?>
                        <tr class="bg-dark">
                            <td></td>
                            <td class="fw-bold" style="font-size:larger;">Total Sales</td>
                            <td class="fw-bold" style="font-size:larger;"><?php echo $result['total_amount']; ?> LKR</td>
                        </tr>
                    </tbody>
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
   document.getElementById('filterForm').addEventListener('submit', function (event) {
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
        url: "actions/cashier_all_sales_.php",
        method: "POST",
        data: {
            sd: JSON.stringify(soteDate),
        },
        success: function (response) {
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
        error: function (xhr, status, error) {
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
                    // console.log(totalAmount);
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