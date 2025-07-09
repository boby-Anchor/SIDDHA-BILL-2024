<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$selected_date = '';
$invoices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_date = $_POST['date'];
    $doctor = $_POST['doctorName'];

    $result = $conn->query("SELECT * 
    FROM dm_items 
    INNER JOIN invoices ON dm_items.invoice_id = invoices.invoice_id 
    WHERE DATE(invoices.created) = '$selected_date' AND invoices.d_name ='$doctor';
    ");

    $invoices = $result->fetch_all(MYSQLI_ASSOC);
}
$totalPrice = 0;
$totalValue = 0;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report | Doctor Medicine</title>

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Product -->
    <link rel="stylesheet" href="dist/css/product.css">
    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body class="hold-transition layout-fixed bg-dark">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-dark">

            <div class="row">
                <div class="card-body overflow-hidden">
                    <div class="row px-4">
                        <div class="col-4">
                            <form action="" method="post">
                                <div class="input-group">
                                    <select class="form-control select2" id="doctorName" name="doctorName">
                                        <option value="" disabled selected hidden>Select a doctor</option>
                                        <?php
                                        $doctors_rs = $conn->query("SELECT * FROM doctors ORDER BY name ASC");
                                        while ($doctors_row = $doctors_rs->fetch_assoc()) {
                                        ?>
                                            <option value="<?= $doctors_row['name'] ?>">
                                                <?= $doctors_row['name'] ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="date" id="date" name="date" class="form-control col-8 bg-dark" value="<?php echo htmlspecialchars($selected_date); ?>" required>
                                    <button type="submit" class="form-control col-2 btn btn-success ml-2"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row p-4">
                        <div class="col-12">
                            <table id="dm_data_table" class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <th>Invoice ID</th>
                                    <th>Doc. Name</th>
                                    <th>Patient Name</th>
                                    <th>Value</th>
                                    <th>Price</th>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($invoices)) {
                                        foreach ($invoices as $invoice):
                                            $totalValue += $invoice['itemPrice'];
                                            $totalPrice += $invoice['totalPrice'];
                                    ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($invoice['invoice_id']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['d_name']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['p_name']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['itemPrice']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['totalPrice']); ?></td>
                                            </tr>
                                        <?php endforeach;
                                        ?>
                                        <tr>
                                            <td class=""><strong>Date:</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $selected_date; ?></td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Total Value:</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $totalValue; ?></td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Total Price:</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $totalPrice; ?></td>
                                        </tr>
                                    <?php

                                    } else {
                                    ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No Doctor Medicine Data</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->

    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

</body>

<script>
    $(function() {
        $("#dm_data_table")
            .DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                searching: false,
                // aaSorting: [],
                order: [
                    [1, 'asc']
                ],
                buttons: ["pdf", "print", "colvis"],
            })
            .buttons()
            .container()
            .appendTo("#dm_data_table_wrapper .col-md-6:eq(0)");
    });
</script>
</html>