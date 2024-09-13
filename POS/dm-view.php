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
    <title>Home | Pharmacy</title>

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
                                    <select class="form-control mr-2 bg-dark" id="doctorName" name="doctorName" required>
                                        <option value="" selected>Select a doctor</option>
                                        <option value="Dr. Buddhika">Dr. Buddhika</option>
                                        <option value="Dr. Daya">Dr. Daya</option>
                                        <option value="Dr. Devinda">Dr. Devinda</option>
                                        <option value="Dr. Fathima">Dr. Fathima</option>
                                        <option value="Dr. Kusal">Dr. Kusal</option>
                                        <option value="Dr. Mithula">Dr. Mithula</option>
                                        <option value="Dr. Padmasiri">Dr. Padmasiri</option>
                                        <option value="Dr. Parakrama">Dr. Parakrama</option>
                                        <option value="Dr. Prasanga">Dr. Prasanga</option>
                                        <option value="Dr. Tharindu">Dr. Tharindu</option>
                                        <option value="Dr. Thilanka">Dr. Thilanka</option>
                                        <option value="Dr. Yashodara">Dr. Yashodara</option>
                                    </select>
                                    <input type="date" id="date" name="date" class="form-control col-8 bg-dark" value="<?php echo htmlspecialchars($selected_date); ?>" required>
                                    <button type="submit" class="form-control col-2 btn btn-success ml-2"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="col-2">
                        </div>

                        <div class="col-6 row">
                            <div class="col-6">
                                <label>Total Value:</label>
                                <label id="totalValueLabel"><?= htmlspecialchars($totalValue) ?></label>
                            </div>
                            <div class="col-6">
                                <label>Total Price:</label>
                                <label id="totalPriceLabel"><?= htmlspecialchars($totalPrice) ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row p-4">
                        <div class="col-12">
                            <table class="table table-dark table-striped table-bordered table-hover">
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

                    <script>
                        document.getElementById('totalPriceLabel').textContent = "<?= htmlspecialchars($totalPrice) ?>";
                        document.getElementById('totalValueLabel').textContent = "<?= htmlspecialchars($totalValue) ?>";
                    </script>

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

</body>

<script>
    $(document).on("click", "#searchPoNumber", function() {

        var poNumber = $("#poNumber").val();
        $("#poNumber").val("");

        $.ajax({
            url: "add-stock-from-po-actions.php",
            method: "POST",
            data: {
                poNumber: poNumber,
            },
            success: function(response) {
                $("#poItemTable").html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });
</script>

</html>