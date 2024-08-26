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

    <style>
        .table-wrap {
            max-height: 300px;
            overflow-x: auto;
            overflow-y: auto;
            margin-top: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        thead th {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
        }

        .table-wrap table thead th {
            background-color: rgb(233, 238, 248);
            line-height: 35px;
            text-indent: 5px;
            white-space: nowrap;
            text-align: left;
            font-weight: 500;
        }

        .table-wrap table tbody tr {
            border-bottom: 1px solid rgb(233, 238, 248);
        }

        .table-wrap table tbody td {
            line-height: 35px;
            text-indent: 5px;
            white-space: nowrap;
        }

        .table-wrap {
            border: 1px solid rgb(233, 238, 248);
        }

        /*************** Select box ***********/
        select.table-select,
        input.table-input {
            height: 35px;
            width: 150px;
            border: 1px solid rgb(233, 238, 248);
            text-indent: 5px;
        }

        .box {
            padding: 10px 0px;
        }

        .box select {
            color: #000;
            border: 1px solid #ddd;
            font-size: 14px;
            -webkit-appearance: none;
            appearance: none;
            outline: none;
        }

        .box::after {
            content: "\f107";
            font-family: FontAwesome;
            position: relative;
            color: #96969a !important;
            top: 2px;
            right: 25px;
            width: 20%;
            height: 100%;
            text-align: center;
            font-size: 22px;
            line-height: 35px;
            color: rgba(255, 255, 255, 0.5);
            background-color: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }

        .cus-delete {
            font-size: 20px;
            color: #f91c1c;
            font-weight: 500;
            cursor: pointer;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed bg-dark">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-dark">
            <div class="col-12">
                <div class="row w-100">

                    <!-- left side desing start -->
                    <div class="col-12">
                        <div class="card-body h-100 bg-dark overflow-hidden">
                            <div class="row">

                                <!-- supplier products start -->
                                <div class="col-12 p-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Barcode Number" onkeyup="fbs(this.value);" id="bnInput">
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Product Code" onkeyup="fbs(this.value);" id="pcInput">
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Product Name" onkeyup="fbs(this.value);" id="pnInput">
                                        </div>
                                        <div class="col-12 products-table mt-4">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Product Name</th>
                                                        <th scope="col">Product Category</th>
                                                        <th scope="col">Product Brand</th>
                                                        <th scope="col">Sell Price</th>
                                                        <th scope="col">Product Unit</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="filterBySupTable">
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                                <!-- supplier products end -->

                            </div>
                        </div>
                    </div>
                    <!-- left side desing end -->

                    <!-- add to stock table -->

                    <div class="col-12 d-flex flex-column align-items-center overflow-hidden ">
                        <div class="grn_tittle">
                            <h3>ADD TO STOCK</h3>
                        </div>
                        <div class="col-12">
                            <table class="table table-dark table-hover addedProTable">
                                <thead class="text-center">
                                    <tr>
                                        <th scope="col">Barcode</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Minimum Unit Qty </th>
                                        <th scope="col">Item Price</th>

                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="po_btn col-6 d-none flex-column">
                        <a type="button" class="btn btn-outline-success proceed-grn" id="proceedGrnBtn">Proceed Order <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <!-- right side desing end -->
                </div>
            </div>
        </div>
    </div>

    <!-- add new unit modal end -->

    <?php
    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {
            $userId = $userData['id'];
            $shop_id = $userData['shop_id'];
        }
    }

    $grn_number_result = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'grn'");
    $grn_number_data = $grn_number_result->fetch_assoc();
    $grnNumber = "GRN-000" . $grn_number_data['AUTO_INCREMENT'];

    $grnDate = date("Y-m-d");
    $grnTime = date("H:i:s");
    ?>

    <!-- confirm po modal start -->
    <div class="container">
        <div class="modal fade bg-success" id="confirmGRN" role="dialog">
            <div class="modal-dialog d-flex justify-content-between ">
                <div class="modal-content bg-dark align-items-center vw-100">
                    <div class="modal-header">
                        <h4 class="modal-title">Stock Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <div class="grnId">
                            <div class="row">
                                <div class="col-4 text-center text-black fw-bold">
                                    <label for="grnNumber">GRN No.</label>
                                    <?php
                                    echo "<span class=\"fs-2 text-dark fw-bold\" name=\"grnNumber\" id=\"grnNumber\">$grnNumber</span>";
                                    ?>
                                </div>

                                <div class="col-4 text-center">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-3">
                                                <label for="grnDate">Date</label>
                                            </div>
                                            <div class="col-8">
                                                <span class="fs-2 text-dark fw-bold" name="grnDate" id="grnDate"><?= $grnDate ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4 text-center">
                                    <label for="grnTime">Added time</label>
                                    <span class="fs-2 text-dark fw-bold" name="grnTime" id="grnTime"><?= $grnTime ?></span>
                                </div>
                                <div class="orderItem col-12 mt-4 mb-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Stock Id</th>
                                                <th scope="col">Barcode</th>
                                                <th scope="col">Product name</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Minimun Qty</th>
                                                <th scope="col">Item Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="grnConfirmationTableBody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success confirmPObtn">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- confirm po modal end -->

    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->

    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <script>
        $(document).ready(function() {
            var ucv_name;
            var product_unit;

            // Function to adjust visibility based on product_unit
            function adjustVisibilityForPackRows() {
                $(".addedProTable tbody tr").each(function() {
                    var rowProductUnit = $(this).find("#product_unit").text();
                    if (rowProductUnit !== 'pack / bottle') {
                        $(this).find(".auto-generate-m-unit").removeClass("d-none");
                        $(this).find(".manual-enter-m-unit").addClass("d-none");
                    }
                });
            }

            $(document).on("click", ".add-btn", function() {
                // Fetch necessary data
                var product_code = $(this).closest("tr").find("#product_code").text().trim();
                var stock_id = $(this).closest("tr").find("#stock_id").text().trim();
                var product_name = $(this).closest("tr").find("#product_name").text().trim();
                var itemSprice = $(this).closest("tr").find("#itemSprice").text().trim();
                var unitSprice = $(this).closest("tr").find("#unitSprice").text().trim();
                ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
                product_unit = $(this).closest("tr").find("#product_unit").text();

                var product_qty = 1;

                var exists = false;
                $(".addedProTable tbody tr").each(function() {
                    if ($(this).find("#product_code").text() === product_code) {
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    // Append new row to the table
                    var markup =
                        "<tr>" +
                        "<th scope='row' id='product_code'>" + product_code + "</th>" +
                        "<td id='addproduct_name'>" + product_name + "</td>" +
                        "<td id='product_unit' class='d-none' >" + product_unit + "</td>" +
                        "<td id='stock_id' class='d-none' >" + stock_id + "</td>" +

                        "<td>" +
                        "<input id='qty_input' type='text' class='bg-dark form-control text-center qty-input mb-2' value=''>" +
                        "</td>" +

                        "<td class='text-center auto-generate-m-unit '>" +
                        "<label id='minimum_qty' class='mb-2' ><i class='fa fa-solid fa-circle-notch fa-spin'></i></label><br>" +
                        "</td>" +

                        "<td class='text-center manual-enter-m-unit d-none'>" +
                        "<input type='text' id='manual_unit_input' class='bg-dark form-control text-center manual_unit_input mb-2' value=''>" +
                        "<input type='text' id='free_manual_unit_input' class='bg-dark form-control text-center free_manual_unit_input' value=''>" +
                        "</td>" +

                        "<td>" + "<label id='cost_input' class='text-center cost-input'>" + itemSprice + "</label></td>" +

                        "<td><i class='fa fa-trash-o cus-delete'></i></td>" +

                        "</tr>";

                    $(".addedProTable tbody").append(markup);

                    $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                    $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

                    // Update visibility based on product_unit
                    adjustVisibilityForPackRows()
                } else {
                    alert("Product already exists in the list!");
                }
            });

            // Event listener for clicking the delete button
            $(document).on("click", ".cus-delete", function() {
                $(this).closest("tr").remove();
                $("#proceedGrnBtn").removeAttr("data-toggle data-target");
                $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);
            });

            // Convert to minimum unit based on product_unit
            $(document).on("input", ".qty-input", function() {
                if (product_unit === 'l') {
                    var liters = parseFloat($(this).val());
                    var milliliters = ucv_name * liters * 1000;
                    $(this).closest("tr").find("#minimum_qty").text(milliliters + "ml");
                }
                if (product_unit === 'kg') {
                    var kilo = parseFloat($(this).val());
                    var grams = ucv_name * kilo * 1000;
                    $(this).closest("tr").find("#minimum_qty").text(grams + "g");
                }
                if (product_unit === 'm') {
                    var meter = parseFloat($(this).val());
                    var centimete = ucv_name * meter * 100;
                    $(this).closest("tr").find("#minimum_qty").text(centimete + "cm");

                }
                if (product_unit === 'ml') {
                    var ml = parseFloat($(this).val());
                    var mililiters = ucv_name * ml;
                    $(this).closest("tr").find("#minimum_qty").text(mililiters + "ml");
                }
                if (product_unit === 'g') {
                    var g = parseFloat($(this).val());
                    var grams = ucv_name * g;
                    $(this).closest("tr").find("#minimum_qty").text(grams + "g");
                }
                if (product_unit === 'cm') {
                    var cm = parseFloat($(this).val());
                    var centimeters = ucv_name * cm;
                    $(this).closest("tr").find("#minimum_qty").text(centimeters + "cm");
                }
            });

            // add minimum qty manuel for generate cost per unit
            $(document).on("input", ".cost-input", function() {
                if ($(this).closest("tr").find(".auto-generate-m-unit").hasClass("d-none")) {
                    var cost = parseFloat($(this).val());
                    var manual_unit_input = parseFloat($(this).closest("tr").find(".manual_unit_input").val());
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }
            });

            $(document).on("input", ".cost-input", function() {
                if (product_unit === "l") {
                    var cost = parseFloat($(this).val());
                    var milliliters = ucv_name * 1000;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "kg") {
                    var cost = parseFloat($(this).val());
                    var milliliters = ucv_name * 1000;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "m") {
                    var cost = parseFloat($(this).val());
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "ml") {
                    var cost = parseFloat($(this).val());
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "g") {
                    var cost = parseFloat($(this).val());
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "cm") {
                    var cost = parseFloat($(this).val());
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }
            });

            $(document).on("input", ".itemdiscount", function() {
                var add_discount = parseFloat($(this).val());
                // var qty = parseFloat($(this).closest("tr").find(".qty-input").val());
                var cost_input = parseFloat($(this).closest("tr").find(".cost-input").val());
                // var item_sell_price = cost_input / qty;
                //  var discount =   100 - add_discount;
                //  var item_cost = cost_input / qty;
                // var item_sell_price = item_cost / 100 * discount;
                $(this).closest("tr").find("#item_sale_price").text(cost_input.toFixed(2));
            });

            // Initial adjustment of visibility
            adjustVisibilityForPackRows()

        });
    </script>

    <script>
        $('#myModal').on('shown.bs.modal', function() {
            $('#myInput').trigger('focus')
        })

        $(document).on("click", ".proceed-grn", function() {

            document.getElementById("grnConfirmationTableBody").innerHTML = '';

            $(".addedProTable tbody tr").each(function() {
                var stock_id = $(this).find("#stock_id").text().trim();
                var product_code = $(this).find("th").text().trim();
                var product_name = $(this).find("#addproduct_name").text().trim();
                var product_cost = $(this).find("#cost_input").text().trim();
                var product_qty = parseInt($(this).find("#qty_input").val().trim());
                var minimum_qty = $(this).find("#minimum_qty").text().trim();

                if (product_qty === "" || product_qty === "0") {
                    Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: "error",
                        title: "Error: Product Quantity!",
                    });

                    $("#proceedGrnBtn").removeAttr("data-toggle data-target");
                } else {
                    var rows_data =
                        '<tr>' +
                        '<td class = "stock_id">' + stock_id + '</td>' +
                        '<td class = "barcode">' + product_code + '</td>' +
                        '<td class = "name">' + product_name + '</td>' +
                        '<td class = "qty">' + product_qty + '</td>' +
                        '<td class = "min_qty">' + minimum_qty + '</td>' +
                        '<td class = "price">' + product_cost + '</td>' +
                        '</tr>';
                }
                $("#grnConfirmationTableBody").append(rows_data);
            });
            $("#proceedGrnBtn").attr({
                "data-toggle": "modal",
                "data-target": "#confirmGRN",
            });
        });

        $(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function() {
            var grnNumber = document.getElementById("grnNumber").innerText;
            var grnDate = document.getElementById("grnDate").innerText;
            var grnTime = document.getElementById("grnTime").innerText;
            $(this).prop('disabled', true);

            var poArray = [];

            $("#grnConfirmationTableBody tr").each(function() {
                var stock_id = $(this).find(".stock_id").text();
                var product_qty = $(this).find(".qty").text();
                var min_qty = $(this).find(".min_qty").text();

                var productData = {
                    stock_id: stock_id,
                    product_qty: product_qty,
                    min_qty: min_qty,
                };
                poArray.push(productData);
            });

            $.ajax({
                url: "edit-stock-action.php",
                method: "POST",
                data: {
                    products: JSON.stringify(poArray),
                },
                success: function(response) {
                    Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true, // Show progress bar during timer
                        didOpen: (toast) => {
                            // Pause timer when mouse hover
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    }).fire({
                        icon: "success",
                        title: "Success: " + response, // Concatenate response text with "Success"
                    }).then(() => {
                        // Reload the page after the message is shown
                        location.reload(true);
                    });
                    $(".confirmPObtn").prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $(".confirmPObtn").prop('disabled', false);
                },
            });
        });

        function fbs(searchTxt) {
            var bnInput = document.getElementById("bnInput").value;
            var pcInput = document.getElementById("pcInput").value;
            var pnInput = document.getElementById("pnInput").value;
            var searchBy = "";

            if (bnInput) {
                searchBy += "barcode";
            }
            if (pcInput) {
                if (searchBy) {
                    searchBy += " & ";
                }
                searchBy += "product code";
            }
            if (pnInput) {
                if (searchBy) {
                    searchBy += " & ";
                }
                searchBy += "product name";
            }
            if (!searchBy) {
                searchBy = "all";
            }

            var form = new FormData();
            form.append("bnInput", bnInput);
            form.append("pcInput", pcInput);
            form.append("pnInput", pnInput);
            form.append("searchBy", searchBy);

            var req = new XMLHttpRequest();
            req.onreadystatechange = function() {
                if (req.readyState == 4 && req.status == 200) {
                    var response = req.responseText;
                    document.getElementById("filterBySupTable").innerHTML = response;
                }
            };
            req.open("POST", "fbs.php", true);
            req.send(form);
        }
    </script>

</body>

</html>