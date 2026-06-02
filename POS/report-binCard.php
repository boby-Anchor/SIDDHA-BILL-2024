<?php
session_start();

if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    if (isset($_SESSION['store_id'])) {
        $userLoginData = $_SESSION['store_id'][0];
        $shop_id = $userLoginData['shop_id'];
        $user_id = $userLoginData['id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Item Flow Data</title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-dark">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <!-- <h3>
                                        View Purchase Orders Between And 
                                    </h3> -->
                                    <div class="mb-4"></div>
                                    <!-- Form start -->
                                    <div class="row px-3">
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
                                        <div class="col-auto">
                                            <label for="barcode" class="col-form-label">Barcode:</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="text" id="barcode" name="barcode" class="form-control" placeholder="Enter barcode">
                                        </div>

                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <!-- Card 1 -->
                                        <div class="col-md-6">
                                            <div class="card border border-success p-2 bg-dark">
                                                <div class="card-header text-white d-flex align-items-center">
                                                    <h5 class="mb-0 pr-2">GRN Items Flow</h5>
                                                    <button class="btn btn-sm btn-primary" id="grnSearchButton"><i class="fas fa-search"></i></button>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table id="grnTable" class="table table-bordered mb-0">
                                                        <thead>
                                                            <tr class="bg-info">
                                                                <th>GRN No.</th>
                                                                <th>Date</th>
                                                                <th>Invoice No.</th>
                                                                <th>Supplier</th>
                                                                <th>Qty</th>
                                                                <th>Price</th>
                                                                <th>Cost</th>
                                                                <!-- <th>Placed By</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card 2 -->
                                        <div class="col-md-6">
                                            <div class="card border border-warning p-2 bg-dark">
                                                <div class="card-header text-white d-flex align-items-center">
                                                    <h5 class="mb-0 pr-2">PO Items Flow</h5>
                                                    <button class="btn btn-sm btn-primary" id="poSearchButton"><i class="fas fa-search"></i></button>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table id="poTable" class="table table-bordered mb-0">
                                                        <thead>
                                                            <tr class="bg-info">
                                                                <th>Order Number</th>
                                                                <th>Date</th>
                                                                <th>To</th>
                                                                <th>Qty</th>
                                                                <th>Price</th>
                                                                <!-- <th>Placed By</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->
</body>

<!--  JS -->
<!-- <script src="dist/js/po/po_view.js"></script> -->


<script>
    function dataValidation(barcode) {
        if (!barcode) {
            ErrorMessageDisplay("Enter barcode.");
            return false;
        }
        InfoMessageDisplay("Loading recent data...!");
        return true;
    }

    $("#grnSearchButton").click(function() {
        var startDate = $("#start_date").val() || null;
        var endDate = $("#end_date").val() || null;
        var barcode = $("#barcode").val() || null;

        if (!dataValidation(barcode)) {
            return;
        }

        $.ajax({
            type: "POST",
            url: "actions/bin_card/getGrn.php",
            data: {
                startDate,
                endDate,
                barcode,
            },
            dataType: "json",
            success: function(response) {
                switch (response.status) {
                    case "success":
                        setGRNDetails(response.details);
                        break;

                    case "empty":
                        $("#grnTableBody").html("<td colspan='5' style='color: white;'>" + response.message + ".</td>");
                        break;

                    case "error":
                        ErrorMessageDisplay(response.message);
                        break;

                    case "sessionExpired":
                        handleExpiredSession(response.message);
                        break;
                }
            },
        });
    });

    function setGRNDetails(details) {

        let table = $("#grnTable").DataTable();

        table.clear();

        details.forEach(function(item) {
            table.row.add([
                item.grn_number,
                item.date,
                item.invoice_number,
                item.supplier,
                item.qty,
                item.price,
                item.cost
            ]);
        });

        table.draw();
    }

    $("#poSearchButton").click(function() {
        var startDate = $("#start_date").val() || null;
        var endDate = $("#end_date").val() || null;
        var barcode = $("#barcode").val() || null;

        dataValidation(barcode);

        let poTable = $("#poTable").DataTable();

        // poTable.clear();

        $.ajax({
            type: "POST",
            url: "actions/bin_card/getPo.php",
            data: {
                startDate,
                endDate,
                barcode,
            },
            dataType: "json",
            success: function(response) {

                switch (response.status) {
                    case "success":
                        setPODetails(poTable, response.details);
                        break;

                    case "empty":
                        $("#poTableBody").html("<td colspan='5' style='color: white;'>" + response.message + ".</td>");
                        break;

                    case "error":
                        ErrorMessageDisplay(response.message);
                        break;

                    case "sessionExpired":
                        handleExpiredSession(response.message);
                        break;
                }
            },
        });
    });

    function setPODetails(poTable, details) {

        details.forEach(function(item) {
            poTable.row.add([
                item.PO_Number,
                item.date,
                item.PO_Shop,
                item.qty,
                item.price,
            ]);
        });
        poTable.draw();
    }

    $(function() {
        $("#grnTable")
            .DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                order: [
                    [1, 'desc']
                ],
            })
            .buttons()
            .container()
            .appendTo("#grnTable_wrapper .col-md-6:eq(0)");
    });
    $(function() {
        $("#poTable")
            .DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                order: [
                    [1, 'desc']
                ],
            })
            .buttons()
            .container()
            .appendTo("#poTable_wrapper .col-md-6:eq(0)");
    });
</script>


<!-- JS end -->

</html>