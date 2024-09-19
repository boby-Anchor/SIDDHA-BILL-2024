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
                            <div class="input-group">
                                <label for="poNumber" class="form-control bg-dark">Inv. No:</label>
                                <input type="text" id="poNumber" name="poNumber"
                                    class="form-control col-8 bg-dark"
                                    value="">
                                <button id="searchPo" class="form-control col-2 btn btn-success ml-2"><i
                                        class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <div class="col-12 row card-body">
                            <div class="col">
                                <label>Stock Keeper: <label id="stockKeeper"></label> </label>
                            </div>
                            <div class="col">
                                <label>From: <label id="shopName"></label></label>
                            </div>
                            <div class="col">
                                <label>Sent to: <label id="poShopName"></label></label>
                            </div>
                            <div class="col">
                                <label>Date: <label id="date"></label></label>
                            </div>
                            <div class="col">
                                <label>Sub total: <label id="subtotal"></label></label>
                            </div>
                            <div class="col">
                                <label>Discount: <label id="discount"></label></label>
                            </div>
                            <div class="col">
                                <label>Net total: <label id="nettotal"></label></label>
                            </div>
                        </div>
                    </div>

                    <div class="row p-4">
                        <div class="col-12">
                            <h1 id="results"></h1>
                            <table class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <th>#</th>
                                    <th>code</th>
                                    <th>ucv</th>
                                    <th>Qty</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>itemData Total</th>
                                    <th>Correct Qty</th>
                                </thead>
                                <tbody id="itemDataTable">

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

</body>

<script>
    $(document).on("click", "#searchPo", function() {

        var poNumber = $("#poNumber").val();

        $.ajax({
            url: "actions/add-stock-from-po_search.php",
            method: "POST",
            data: {
                poNumber: poNumber,
            },
            success: function(response) {

                var poResult;

                var result = JSON.parse(response);

                if (result.status === 'success') {

                    var rowCount = 0;
                    $("#itemDataTable").empty();
                    $("#stockKeeper").text('');
                    $("#shopName").text();
                    $("#poShopName").text();
                    $("#date").text();
                    $("#subtotal").text();
                    $("#discount").text();
                    $("#nettotal").text();

                    poResult = result.invoiceData[0];
                    $("#stockKeeper").text(poResult.stockKeeper);
                    $("#shopName").text(poResult.shop);
                    $("#poShopName").text(poResult.poShop);
                    $("#date").text(poResult.created);
                    $("#subtotal").text(poResult.sub_total);
                    $("#discount").text(poResult.discount_percentage);
                    $("#nettotal").text(poResult.net_total);

                    if (result.items) {

                        result.items.forEach(function(itemData) {
                            rowCount++;
                            var row = '<tr>' +
                                '<td>' + rowCount + '</td>' +
                                '<td>' + itemData.invoiceItem + '</td>' +
                                '<td>' + itemData.invoiceItem_ucv + '</td>' +
                                '<td>' + itemData.invoiceItem_qty + '</td>' +
                                '<td>' + itemData.invoiceItem_unit + '</td>' +
                                '<td>' + itemData.invoiceItem_price + '</td>' +
                                '<td>' + itemData.invoiceItem_total + '</td>' +
                                '<td><input type="text" class="text-center" value="' + itemData.invoiceItem_qty + '"></td>' +
                                '</tr>';
                            document.getElementById('itemDataTable').insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        var row = '<tr colspan="8">No Data Found</tr>';
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });
</script>

</html>