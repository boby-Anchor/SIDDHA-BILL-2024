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
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-success">
                                                        <h2 class="text-white text-uppercase">Sell Amount</h2>
                                                        <?php $currentDate = date('Y-m-d'); ?>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount FROM invoices WHERE DATE(`created`) = '$currentDate' ")); ?>
                                                        <p class="totalAmount"><?php echo $result['total_amount']; ?> LKR</p>
                                                    </div>
                                                </div>

                                                <?php
                                                // AND user_id = '$user_id'
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
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(paidAmount) AS cash_amount FROM invoices WHERE DATE(`created`) = '$currentDate' ")); ?>
                                                        <p class="totalAmount"><?php echo $result['cash_amount']; ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-primary">
                                                        <h2 class="text-white text-uppercase">Card Payments</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(cardPaidAmount) AS cardPaidAmount FROM invoices WHERE DATE(`created`) = '$currentDate' ")); ?>
                                                        <p class="totalAmount"><?php echo $result['cardPaidAmount']; ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-danger">
                                                        <h2 class="text-white text-uppercase">Cash Out</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT ROUND(SUM(balance), 2) AS cashout FROM invoices WHERE DATE(`created`) = '$currentDate' ")); ?>
                                                        <p class="totalAmount">-<?php echo $result['cashout']; ?> LKR</p>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Top Sell Product</h4>
                                            </div>
                                            <div class="card-body">
                                                
                                                
                                                
                        <table id="mytable" class="table table-bordered table-hover">
    <thead>
        <tr class="bg-info">
            <th>Invoice Number</th>
            <th>Total Amount <span class="caret"></span></th>
            <th>Cash Amount</th>
            <th>Card Amount</th>
            <th>Balance</th>
            <th>Payment Type</th>
            <th>Bill Type</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = $conn->query("SELECT * FROM invoices INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id WHERE DATE(`created`) = '$currentDate' ");
        $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount FROM invoices WHERE DATE(`created`) = '$currentDate' "));
        while ($row = mysqli_fetch_assoc($sql)) {
        ?>
        <tr>
            <td><?php echo $row['invoice_id']; ?><br><?php echo $row['p_name']; ?></td>
            <td>
                <?php echo $row['total_amount']; ?>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <table class="table" id="poItemsTable<?php echo $row['invoice_id']; ?>">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Item Code</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $currentDate = date("d-m-Y");
                                
                                // SELECT `invoiceItemId`, `invoiceNumber`, `invoiceDate`, `invoiceItem`, `invoiceItem_qty`, `invoiceItem_unit`, `invoiceItem_price`, `invoiceItem_total` 
                                // FROM `invoiceitems` WHERE 1
                                
                                $poItems_query = "
                                    SELECT invoiceitems.* FROM invoiceitems INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber 
                                    WHERE   invoices.invoice_id = '" . $row['invoice_id'] . "' ";
                                $poItems_result = $conn->query($poItems_query);
                                while ($poItems_data = $poItems_result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $poItems_data['invoiceItem']; ?></td>
                                    <td><?php echo $poItems_data['invoiceItem_qty']; ?></td>
                                    <td><?php echo $poItems_data['invoiceItem_unit']; ?></td>
                                    <td><?php echo $poItems_data['invoiceItem_price']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?php echo $row['invoice_id']; ?>');">
                            <i class="nav-icon fas fa-copy"></i> PRINT
                        </button>
                    </ul>
                </div>
            </td>
            <td><?php echo $row['paidAmount']; ?></td>
            <td><?php echo $row['cardPaidAmount']; ?></td>
            <td><?php echo $row['balance']; ?></td>
            <td><?php echo $row['payment_type']; ?></td>
            <td><?php echo $row['bill_type_name']; ?></td>
        </tr>
        <?php } ?>
        <tr class="bg-dark">
            <td></td>
            <td class="fw-bold" style="font-size: larger;">Total Sales</td>
            <td class="fw-bold" style="font-size: larger;"><?php echo $result['total_amount']; ?> LKR</td>
        </tr>
    </tbody>
</table>

                                                
                                        
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">

                                    </div>
                                    <div class="col-6">

                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Cash Received</h4>
                                            </div>
                                            <div class="card-body">
                                                <table id="mytable4" class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Customer/Supplier</th>
                                                            <th>Payment Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>

                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" class="text-right"><strong>Total (Rs): </strong> </td>
                                                            <td> <strong id="totalReceived"> 0.00 </strong> </td>
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