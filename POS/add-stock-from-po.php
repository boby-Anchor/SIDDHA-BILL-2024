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
                                <input type="text" id="poNumber" name="poNumber" class="form-control col-8 bg-dark"
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
                                <label>From: <label class="d-none" id="shopId"></label>
                                    <label id="shopName"></label></label>
                            </div>
                            <div class="col">
                                <label>Sent to: <label class="d-none" id="poShopId"></label>
                                    <label id="poShopName"></label></label>
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
                            <table class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <th>#</th>
                                    <th>Name</th>
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

                    <div class="row p-4">
                        <div class="col-4">
                            <button class="btn btn-success form-control d-none" id="addStockButton">Add
                                stock to shop</button>
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
    $(document).on("click", "#searchPo", function () {

        var poNumber = $("#poNumber").val();

        $.ajax({
            url: "actions/add-stock-from-po_search.php",
            method: "POST",
            data: {
                poNumber: poNumber,
            },
            success: function (response) {

                var poResult;

                var result = JSON.parse(response);

                if (result.status === 'success') {

                    var rowCount = 0;
                    $("#itemDataTable").empty();

                    poResult = result.invoiceData[0];
                    $("#stockKeeper").text(poResult.stockKeeper);
                    $("#shopName").text(poResult.shop);
                    $("#shopId").text(poResult.shop_id);
                    $("#poShopName").text(poResult.poShop);
                    $("#poShopId").text(poResult.po_shop_id);
                    $("#date").text(poResult.created);
                    $("#subtotal").text(poResult.sub_total);
                    $("#discount").text(poResult.discount_percentage);
                    $("#nettotal").text(poResult.net_total);

                    if (result.items) {

                        $("#addStockButton").removeClass("d-none");

                        result.items.forEach(function (itemData) {
                            rowCount++;
                            var row = '<tr>' +
                                '<td>' + rowCount + '</td>' +
                                '<td id="product_name">' + itemData.invoiceItem + '</td>' +
                                '<td id="product_code">' + itemData.item_code + '</td>' +
                                '<td id="ucv">' + itemData.invoiceItem_ucv + '</td>' +
                                '<td id="product_qty">' + itemData.invoiceItem_qty + '</td>' +
                                '<td>' + itemData.invoiceItem_unit + '</td>' +
                                '<td id="item_price">' + itemData.invoiceItem_price + '</td>' +
                                '<td id="item_total">' + itemData.invoiceItem_total + '</td>' +
                                '<td><input type="text" class="text-center" id="manual_qty" name="manual_qty" value="' + itemData.invoiceItem_qty + '"></td>' +
                                '</tr>';
                            document.getElementById('itemDataTable').insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        var row = '<tr colspan="8">No Data Found</tr>';
                        document.getElementById('itemDataTable').insertAdjacentHTML('beforeend', row);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });
</script>

<script src="dist/js/add-stock-from-po.js"></script>

</html>