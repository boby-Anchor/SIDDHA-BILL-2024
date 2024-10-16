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
                                <!-- <div class="col-6"> -->
                                <!-- <img src="dist/img/Siddha.lk (1).png" alt="" class="img-fluid"> -->
                                <!-- </div> -->

                                <!-- supplier selector start -->
                                <!-- <div class="col-12 p-4">
                                    <label for="select-supplier">Select Supplier</label>
                                    <select name="select-supplier" id="select-supplier" class="form-control bg-dark" onchange="select_suplier(this.value);">
                                        <option value="0">select supplier</option>

                                        <?php
                                        // if (isset($_SESSION['store_id'])) {

                                        //     $userLoginData = $_SESSION['store_id'];

                                        //     foreach ($userLoginData as $userData) {
                                        //         $shop_id = $userData['shop_id'];
                                        //         $supplier_rs = $conn->query("SELECT DISTINCT p_supplier.* FROM p_supplier
                                        //         INNER JOIN p_medicine ON p_supplier.brand_id = p_medicine.brand
                                        //         INNER JOIN producttoshop ON p_medicine.id = producttoshop.medicinId
                                        //         WHERE producttoshop.shop_id = '$shop_id'
                                        //         ");
                                        //         while ($supplier_data = $supplier_rs->fetch_assoc()) {
                                        ?>
                                                    <option value="<?php // $supplier_data["id"] 
                                                                    ?>"><?php // $supplier_data["name"] 
                                                                        ?></option>
                                        <?php
                                        //         }
                                        //     }
                                        // }

                                        ?>
                                    </select>
                                </div> -->
                                <!-- supplier selector end -->

                                <!-- supplier products start -->
                                <div class="col-12 p-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Barcode Number" onkeyup="fbs();" id="bnInput">
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Product Code" onkeyup="fbs();" id="pcInput">
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Product Name" onkeyup="fbs();" id="pnInput">
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
                                                    <?php

                                                    if (isset($_SESSION['store_id'])) {

                                                        $userLoginData = $_SESSION['store_id'];

                                                        foreach ($userLoginData as $userData) {
                                                            $shop_id = $userData['shop_id'];
                                                            //    p_medicine.code AS code ,
                                                            $p_medicine_rs = $conn->query("SELECT p_medicine.id AS pid, p_medicine.name AS p_name, 
                                                            p_medicine.code AS code,
                                                            p_medicine.img AS img,
                                                            p_medicine_category.name AS category, p_brand.name AS brand,
                                                            medicine_unit.unit AS unit, unit_category_variation.ucv_name,
                                                            stock2.stock_id AS stock_id,
                                                            stock2.item_s_price AS itemSprice,
                                                            stock2.unit_s_price AS unitSprice
                                                            FROM producttoshop
                                                            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
                                                            INNER JOIN p_medicine_category ON p_medicine.category = p_medicine_category.id
                                                            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                                                            INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                                                            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                                                            LEFT JOIN `stock2` ON `stock2`.`stock_item_code` = `p_medicine`.`code`
                                                            WHERE
                                                            shop_id='$shop_id' AND (producttoshop.productToShopStatus = 'added' OR producttoshop.productToShopStatus = 'all') 
                                                            GROUP BY p_medicine.name, itemSprice
                                                            ORDER BY p_medicine.name ASC
                                                            ");

                                                            $tableRowCount = 1;
                                                            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
                                                    ?>
                                                                <tr>
                                                                    <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                                                                    <th id="stock_id" class="d-none"><?= $p_medicine_data['stock_id'] ?></th>
                                                                    <th id="ucv_name" class="d-none"><?= $p_medicine_data['ucv_name'] ?> </th>
                                                                    <th id="unitSprice" class="d-none"><?= $p_medicine_data['unitSprice'] ?> </th>
                                                                    <th scope="row"><?= $tableRowCount ?></th>
                                                                    <!-- <td>
                                                                        <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                                                                    </td> -->
                                                                    <td>
                                                                        <label id="product_name"><?= $p_medicine_data['p_name'] ?></label>
                                                                        (<?= $p_medicine_data['ucv_name'] ?>
                                                                        <?= $p_medicine_data['unit'] ?>)
                                                                    </td>
                                                                    <td id="product_category"><?= $p_medicine_data['category'] ?></td>
                                                                    <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
                                                                    <td id="itemSprice"><?= $p_medicine_data['itemSprice'] ?></td>
                                                                    <td id="product_unit"><?= $p_medicine_data['unit'] ?></td>
                                                                    <td><button class="btn btn-outline-success add-btn">Add</button></td>
                                                                </tr>
                                                    <?php
                                                                $tableRowCount++;
                                                            }
                                                        }
                                                    }
                                                    ?>

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
                                        <th scope="col">Discount(%)</th>
                                        <th scope="col">(1 Item) Sell Price</th>
                                        <th scope="col">(1 Unit) Cost</th>
                                        <th scope="col">(1 Unit) Sell Price & Barcode</th>

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
                                                <th scope="col">B-CODE</th>
                                                <th scope="col">P-Name</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">MU-Qty</th>
                                                <th scope="col">Total Cost</th>
                                                <th scope="col">Unit Cost</th>
                                                <th scope="col">Unit S-Price</th>
                                                <th scope="col">Discount(%)</th>
                                                <th scope="col">Item S-Price</th>
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
    <!-- confirm po modal end -->


    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->


    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->


    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <script src="dist/js/add-stock.js"></script>

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
                var product_code = $(this).closest("tr").find("#product_code").text();
                var product_name = $(this).closest("tr").find("#product_name").text().trim();
                // var itemSprice = $(this).closest("tr").find("#itemSprice").text().trim();
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

                        "<td>" +
                        "<input id='qty_input' type='text' class='bg-dark form-control text-center qty-input mb-2' value=''>" +
                        "<input id='free_qty' placeholder='free of qty..' type='text' class='bg-dark form-control text-center free-qty-input' value=''>" +
                        "</td>" +

                        "<td class='text-center auto-generate-m-unit '>" +
                        "<label id='minimum_qty' class='mb-2' ><i class='fa fa-solid fa-circle-notch fa-spin'></i></label><br>" +
                        "<label id='free_minimum_qty'><i class='fa fa-solid fa-circle-notch fa-spin'></i></label>" +
                        "</td>" +

                        "<td class='text-center manual-enter-m-unit d-none'>" +
                        "<input type='text' id='manual_unit_input' class='bg-dark form-control text-center manual_unit_input mb-2' value=''>" +
                        "<input type='text' id='free_manual_unit_input' class='bg-dark form-control text-center free_manual_unit_input' value=''>" +
                        "</td>" +

                        "<td>" + "<input type='text' id='cost_input' class='bg-dark form-control text-center cost-input' value=''></td>" +

                        "<td>" + "<input placeholder='discount' id='item_discount' type='text' class='bg-dark form-control text-center itemdiscount' value=''>" + "</td>" +

                        "<td>" + "<label id='item_sale_price'></label>" + "</td>" +

                        "<td>" + "<label id='cost_per_unit'></label>" + "</td>" +



                        "<td>" +
                        "<input placeholder='unit price' id='unit_s_price' type='text' class='bg-dark form-control text-center unitsell-price-input mb-2' value=''>" +
                        "<input placeholder='barcode' id='unit_barcode' type='text' class='bg-dark form-control text-center unit_barcode' value=''>" +
                        "</td>" +


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

            // free qty input for auto generate minimum qty
            $(document).on("input", ".free-qty-input", function() {
                if (product_unit === 'l') {
                    var liters = parseFloat($(this).val());
                    var milliliters = ucv_name * liters * 1000;
                    $(this).closest("tr").find("#free_minimum_qty").text(milliliters + "ml");

                }
                if (product_unit === 'kg') {
                    var kilo = parseFloat($(this).val());
                    var grams = ucv_name * kilo * 1000;
                    $(this).closest("tr").find("#free_minimum_qty").text(grams + "g");
                }
                if (product_unit === 'm') {
                    var meter = parseFloat($(this).val());
                    var centimete = ucv_name * meter * 100;
                    $(this).closest("tr").find("#free_minimum_qty").text(centimete + "cm");

                }
                if (product_unit === 'ml') {
                    var ml = parseFloat($(this).val());
                    var mililiters = ucv_name * ml;
                    $(this).closest("tr").find("#free_minimum_qty").text(mililiters + "ml");
                }
                if (product_unit === 'g') {
                    var g = parseFloat($(this).val());
                    var grams = ucv_name * g;
                    $(this).closest("tr").find("#free_minimum_qty").text(grams + "g");
                }
                if (product_unit === 'cm') {
                    var cm = parseFloat($(this).val());
                    var centimeters = ucv_name * cm;
                    $(this).closest("tr").find("#free_minimum_qty").text(centimeters + "cm");
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

            // Calculate cost per unit based on product_unit
            // $(document).on("input", ".cost-input", function() {
            //     var product_unit = $(this).closest("tr").find("#product_unit").text();
            //     var cost = parseFloat($(this).val());

            //     var units = ["l", "kg", "m", "ml", "g", "cm"];

            //     if (units.includes(product_unit)) {
            //         var cost_per_unit = cost / ucv_name;
            //         $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            //     }
            // });

            $(document).on("input", ".cost-input", function() {
                if (product_unit === "l") {
                    var cost = parseFloat($(this).val());
                    // var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 1000;
                    // var cost_per_unit = cost / milliliters;
                    var milliliters = ucv_name * 1000;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "kg") {
                    var cost = parseFloat($(this).val());
                    // var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 1000;
                    // var cost_per_unit = cost / milliliters;
                    var milliliters = ucv_name * 1000;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "m") {
                    var cost = parseFloat($(this).val());
                    // var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 100;
                    // var cost_per_unit = cost / milliliters;
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "ml") {
                    var cost = parseFloat($(this).val());
                    // var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                    // var cost_per_unit = cost / milliliters;
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "g") {
                    var cost = parseFloat($(this).val());
                    // var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                    // var cost_per_unit = cost / milliliters;
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "cm") {
                    var cost = parseFloat($(this).val());
                    // var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                    // var cost_per_unit = cost / milliliters;
                    var cost_per_unit = cost / ucv_name;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }
            });

            // Calculate discounted price
            // $(document).on("input", ".itemdiscount", function() {
            //     var add_discount = parseFloat($(this).val());

            //     var discount = add_discount + 100;

            //     var qty = parseFloat($(this).closest("tr").find(".qty-input").val());

            //     var cost_input = parseFloat($(this).closest("tr").find(".cost-input").val());
            //     var item_cost = cost_input / qty;

            //     var item_sell_price = item_cost / 100 * discount;

            //     $(this).closest("tr").find("#item_sale_price").text(item_sell_price.toFixed(2));
            // });

            // $(document).on("input", ".itemdiscount", function() {
            //     var add_discount = parseFloat($(this).val());
            //     var cost_input = parseFloat($(this).closest("tr").find(".cost-input").val());

            //     stock_cost = cost_input * qty * discount


            //     var item_sell_price = cost_input - (cost_input * (add_discount / 100));

            //     $(this).closest("tr").find("#item_sale_price").text(item_sell_price.toFixed(2));
            // });

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

        $('#myModal').on('shown.bs.modal', function() {
            $('#myInput').trigger('focus')
        })

        $(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function() {
            var grnNumber = document.getElementById("grnNumber").innerText;
            var grnDate = document.getElementById("grnDate").innerText;
            var grnTime = document.getElementById("grnTime").innerText;
            $(this).prop('disabled', true);

            var poArray = [];

            $("#grnConfirmationTableBody tr").each(function() {
                var product_code = $(this).find(".product_code").text();
                var product_name = $(this).find(".product_name").text();
                var product_qty = $(this).find(".product_qty").text();
                var minimum_qty = $(this).find(".minimum_qty").text();
                var item_discount = $(this).find(".item_discount").text();
                var cost_input = $(this).find(".cost_input").text();
                if (item_discount > 0) {
                    cost_input = cost_input / 100 * (100 - item_discount)
                }

                var cost_per_unit = $(this).find(".cost_per_unit").text();
                var unit_s_price = $(this).find(".unit_s_price").text();

                var item_sale_price = $(this).find(".item_sale_price").text();
                var free_qty = $(this).find(".free_qty").text();
                var free_minimum_qty = $(this).find(".free_minimum_qty").text();
                var unit_barcode = $(this).find(".unit_barcode").text();

                var productData = {
                    product_code: product_code,
                    product_name: product_name,
                    product_qty: product_qty,
                    minimum_qty: minimum_qty,
                    cost_input: cost_input,
                    cost_per_unit: cost_per_unit,
                    unit_s_price: unit_s_price,
                    item_discount: item_discount,
                    item_sale_price: item_sale_price,
                    free_qty: free_qty,
                    free_minimum_qty: free_minimum_qty,
                    unit_barcode: unit_barcode,

                };
                poArray.push(productData);
            });

            $.ajax({
                url: "grnConfirmationInsert.php",
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
    </script>

</body>

</html>