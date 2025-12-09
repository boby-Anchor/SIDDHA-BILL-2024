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

        <div class="content-wrapper bg-dark">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>My Today Sale Report</h1>
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
                                        <div class="card-body bg-dark">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-success">
                                                        <h2 class="text-white text-uppercase">Sell Amount</h2>
                                                        <?php $currentDate = date('Y-m-d');
                                                        $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount"><?= number_format($result['total_amount'], 2); ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-info">
                                                        <h2 class="text-white text-uppercase">Cash Payments</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(paidAmount) AS cash_amount FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount"><?= number_format($result['cash_amount'], 2); ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-primary">
                                                        <h2 class="text-white text-uppercase">Card Payments</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(cardPaidAmount) AS cardPaidAmount FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount"><?= number_format($result['cardPaidAmount'], 2); ?> LKR</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-body bg-danger">
                                                        <h2 class="text-white text-uppercase">Cash Out</h2>
                                                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT ROUND(SUM(balance), 2) AS cashout FROM invoices WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'")); ?>
                                                        <p class="totalAmount">-<?= $result['cashout']; ?> LKR</p>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row w-100">
                                    <div class="col-12">
                                        <div class="card">

                                            <div class="card-body bg-dark">

                                                <table id="SalesTable" class="table table-bordered">
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <td>#</td>
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
                                                        $sql = $conn->query("SELECT * FROM invoices
                                                        INNER JOIN payment_type
                                                        ON payment_type.payment_type_id = invoices.payment_method
                                                        INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id
                                                        WHERE DATE(`created`) = '$currentDate'
                                                        AND user_id = '$user_id'
                                                        ");
                                                        $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount
                                                        FROM invoices WHERE DATE(`created`) = '$currentDate'
                                                        AND user_id = '$user_id'
                                                        "));
                                                        $rowNo = 0;
                                                        while ($row = mysqli_fetch_assoc($sql)) {
                                                            $rowNo++;
                                                        ?>
                                                            <tr>
                                                                <td><?= $rowNo; ?></td>
                                                                <td><?= $row['invoice_id']; ?><br><?= $row['p_name']; ?></td>
                                                                <td>
                                                                    <?= number_format($row['total_amount']); ?>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                            <!-- <span class="caret"></span> -->
                                                                        </button>
                                                                        <ul class="dropdown-menu bg-dark">
                                                                            <table class="table" id="poItemsTable<?= $row['invoice_id']; ?>">
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
                                                                                    $poItems_query = "
                                                                                    SELECT invoiceitems.* FROM invoiceitems INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber
                                                                                    WHERE   invoices.invoice_id = '" . $row['invoice_id'] . "' ";
                                                                                    $rowCount = 0;
                                                                                    $poItems_result = $conn->query($poItems_query);
                                                                                    while ($poItems_data = $poItems_result->fetch_assoc()) {
                                                                                        $rowCount++;
                                                                                    ?>
                                                                                        <tr>
                                                                                            <td><?= $rowCount; ?></td>
                                                                                            <td><?= $poItems_data['invoiceNumber']; ?></td>
                                                                                            <td><?= $poItems_data['invoiceItem']; ?></td>
                                                                                            <td><?= $poItems_data['invoiceItem_qty']; ?></td>
                                                                                            <td><?= number_format($poItems_data['invoiceItem_price'], 0); ?></td>
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
                                                                <td><?= number_format($row['paidAmount'], 0); ?></td>
                                                                <td><?= number_format((float)$row['cardPaidAmount'], 0); ?></td>
                                                                <td><?= number_format($row['balance'], 0); ?></td>
                                                                <td><?= $row['payment_type']; ?></td>
                                                                <td><?= $row['bill_type_name']; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-gray">
                                                            <td></td>
                                                            <td colspan="5" class="fw-bold fs-1" style="font-size: larger;">Total Sales</td>
                                                            <td colspan="2" class="fw-bold text-right" style="font-size: larger;"><?= number_format($result['total_amount'], 0); ?> LKR</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="col-6">
                                        <div class="card bg-dark">
                                            <div class="card-header">
                                                <h4>Cash Received</h4>
                                            </div>
                                            <div class="card-body">
                                                <table id="CashTable" class="table table-bordered">
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
                                    </div> -->
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

            //     $('#SalesTable').DataTable({
            //         order: [
            //             [0, 'asc']
            //         ],
            //         // pageLength : 3,
            //         dom: 'Bfrtip',
            //         aaSorting: [],
            //         buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            //         "footerCallback": function(row, data, start, end, display) {
            //             var totalAmount = 0;
            //             for (var i = 0; i < data.length; i++) {
            //                 totalAmount += parseFloat(data[i][4]);
            //             }
            //             // console.log(totalAmount);
            //             $("#totalSales").text(totalAmount);
            //         }
            //     });

            $('#SalesTable').DataTable({
                    order: [
                        [0, 'asc']
                    ],
                    // pageLength : 10,
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    buttons: ['copy', 'csv', 'pdf', 'print', 'colvis']
                }).buttons()
                .container()
                .appendTo("#SalesTable_wrapper .col-md-6:eq(0)");

            // $('#CashTable').DataTable({
            //     // order: [[0, 'desc']],
            //     dom: 'Bfrtip',
            //     aaSorting: [],
            //     buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            //     "footerCallback": function(row, data, start, end, display) {
            //         //Get data here
            //         // console.log(data);
            //         var totalAmount = 0;
            //         for (var i = 0; i < data.length; i++) {
            //             totalAmount += parseFloat(data[i][4]);
            //         }
            //         // console.log(totalAmount);
            //         $("#totalReceived").text(totalAmount);
            //     }
            // });
        });
    </script>

</body>

</html>