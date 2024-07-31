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

        button.add-btn.btn.btn-outline-primary,
        button.add-btn.btn.btn-outline-primary:focus,
        button.add-btn.btn.btn-outline-primary:hover {
            height: 30px;
            line-height: 15px;
            outline: 0 !important;
            box-shadow: none;
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

<body class="hold-transition layout-fixed bg-dark">

    <!-- Navbar -->
    <?php //include("part/navbar.php"); 
    ?>
    <!-- Navbar end -->

    <!-- Sidebar -->
    <?php // include("part/sidebar.php"); 
    ?>
    <!--  Sidebar end -->

    <!-- Content Wrapper. Contains page content -->
    <div class="bg-dark">

        <div class="row w-100">

            <!-- left side desing start -->
            <div class="col-12">
                <div class="card-body h-100 bg-dark overflow-hidden">
                    <div class="row">

                        <!-- supplier products start -->
                        <div class="col-12 ">
                            <h6>QTY Edit Test</h6>
                            <div class="row">

                                <div class="col-12 products-table mt-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Stock id</th>
                                                <th scope="col">Shop id</th>
                                                <th scope="col">Barcode</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Product Category</th>
                                                <th scope="col">Product Brand</th>
                                                <th scope="col">Unit Cost</th>
                                                <th scope="col">Discount</th>
                                                <th scope="col">Sell Price</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="filterBySupTable">
                                            <?php

                                            if (isset($_SESSION['store_id'])) {

                                                $userLoginData = $_SESSION['store_id'];

                                                foreach ($userLoginData as $userData) {
                                                    $shop_id = $userData['shop_id'];

                                                    $p_medicine_rs = $conn->query("SELECT unit_category_variation.ucv_name AS ucv_name,
                                                            medicine_unit.unit AS unit, p_medicine_category.name AS categoryname,
                                                            p_brand.name AS brandname,
                                                             t.*
                                                            FROM stock2 t
                                                            JOIN (
                                                                SELECT stock_item_code, stock_item_name, stock_item_cost, unit_s_price, added_discount, item_s_price, stock_shop_id
                                                                FROM stock2
                                                                GROUP BY stock_item_code, stock_item_name, stock_item_cost, unit_s_price, added_discount, item_s_price, stock_shop_id
                                                                HAVING COUNT(*) > 1
                                                            ) d 
                                                            ON t.stock_item_code = d.stock_item_code
                                                            AND t.stock_item_cost = d.stock_item_cost
                                                            AND t.unit_s_price = d.unit_s_price
                                                            AND t.added_discount = d.added_discount
                                                            AND t.item_s_price = d.item_s_price
                                                            AND t.stock_shop_id = d.stock_shop_id
                                                            INNER JOIN p_medicine ON t.stock_item_code = p_medicine.code
                                                            INNER JOIN p_medicine_category ON p_medicine.category = p_medicine_category.id
                                                            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                                                            INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                                                            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                                                            WHERE t.stock_shop_id = '1'
                                                            ORDER BY t.stock_item_code ASC, t.stock_id ASC;
                                                            ");

                                                    //ORDER BY t.stock_item_code ASC, t.stock_shop_id ASC, t.stock_id ASC;

                                                    $tableRowCount = 1;
                                                    while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
                                            ?>
                                                        <tr>
                                                            <th id="product_code" class="d-none"><?= $p_medicine_data['stock_item_code'] ?></th>
                                                            <th id="ucv_name" class="d-none"><?= $p_medicine_data['ucv_name'] ?> </th>
                                                            <td scope="row"><?= $tableRowCount ?></td>
                                                            <td scope="row">
                                                                <label id="stock_id"><?= $p_medicine_data['stock_id'] ?></label>
                                                            </td>
                                                            <td>
                                                                <label id="stock_shop_id"><?= $p_medicine_data['stock_shop_id'] ?></label>
                                                            </td>
                                                            <th>
                                                                <label><?= $p_medicine_data['stock_item_code'] ?></label>
                                                            </th>
                                                            <td>
                                                                <label id="stock_item_qty"><?= $p_medicine_data['stock_item_qty'] ?></label>
                                                            </td>
                                                            <td>
                                                                <label id="product_name"><?= $p_medicine_data['stock_item_name'] ?></label>
                                                                (<?= $p_medicine_data['ucv_name'] ?>
                                                                <?= $p_medicine_data['unit'] ?>)
                                                            </td>
                                                            <td id="product_category">
                                                                <label for=""><?= $p_medicine_data['categoryname'] ?></label>
                                                            </td>
                                                            <td id="product_brand">
                                                                <label for=""><?= $p_medicine_data['brandname'] ?></label>
                                                            </td>
                                                            <td id="unit_cost">
                                                                <label for=""><?= $p_medicine_data['unit_cost'] ?></label>
                                                            </td>
                                                            <td id="added_discount">
                                                                <label for=""><?= $p_medicine_data['added_discount'] ?></label>
                                                            </td>
                                                            <td id="item_s_price">
                                                                <label for=""><?= $p_medicine_data['item_s_price'] ?></label>
                                                            </td>
                                                            <td id="product_unit"><label for=""><?= $p_medicine_data['unit'] ?></label></td>
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
                                <th scope="col">stock id</th>
                                <th scope="col">shop id</th>
                                <th scope="col">Barcode</th>
                                <th scope="col">Old UP</th>
                                <th scope="col">Old Dsc</th>
                                <th scope="col">Old SP</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Minimum Unit Qty </th>
                                <th scope="col">Total Amount (full qty)</th>
                                <th scope="col">Discount(%)(1 Item)</th>
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



    <?php
    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {
            $userId = $userData['id'];
            $shop_id = $userData['shop_id'];
        }
    }
    ?>

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
                    if (rowProductUnit === 'pack / bottle') {
                        $(this).find(".auto-generate-m-unit").addClass("d-none");
                        $(this).find(".manual-enter-m-unit").removeClass("d-none");
                    } else {
                        $(this).find(".auto-generate-m-unit").removeClass("d-none");
                        $(this).find(".manual-enter-m-unit").addClass("d-none");
                    }
                });
            }

            $(document).on("click", ".add-btn", function() {
                // Fetch necessary data
                var stock_id = $(this).closest("tr").find("#stock_id").text().trim();
                var stock_shop_id = $(this).closest("tr").find("#stock_shop_id").text().trim();
                var old_unit_cost = $(this).closest("tr").find("#unit_cost").text().trim();
                var old_added_discount = $(this).closest("tr").find("#added_discount").text().trim();
                var old_item_s_price = $(this).closest("tr").find("#item_s_price").text().trim();

                var product_code = $(this).closest("tr").find("#product_code").text();
                var product_name = $(this).closest("tr").find("#product_name").text().trim();
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
                        "<th scope='row' class='d-none' id='product_code'>" + product_code + "</th>" +
                        "<th scope='row' id='stock_id'>" + stock_id + "</th>" +
                        "<th scope='row' id='shop_id'>" + stock_shop_id + "</th>" +
                        "<th scope='row'>" + product_code + "</th>" +

                        "<th scope='row' id='old_unit_cost'>" + old_unit_cost + "</th>" +
                        "<th scope='row' id='old_added_discount'>" + old_added_discount + "</th>" +
                        "<th scope='row' id='old_item_s_price'>" + old_item_s_price + "</th>" +

                        "<td id='product_name'>" + product_name + "</td>" +
                        "<td id='product_unit' class='d-none' >" + product_unit + "</td>" +

                        "<td>" +
                        "<input id='qty_input' type='text' class='bg-dark form-control text-center qty-input mb-2' value=''>" +
                        "</td>" +

                        "<td class='text-center auto-generate-m-unit '>" +
                        "<label id='minimum_qty' class='mb-2' ><i class='fa fa-solid fa-circle-notch fa-spin'></i></label>" +
                        "<label id='unit_id' class='mb-2' ><i class='fa fa-solid fa-circle-notch fa-spin'></i></label><br>" +
                        "</td>" +

                        "<td>" + "<input type='text' id='cost_input' class='bg-dark form-control text-center cost-input' value=''></td>" +

                        "<td>" + "<input placeholder='discount' id='item_discount' type='text' class='bg-dark form-control text-center itemdicount' value=''>" + "</td>" +

                        "<td>" + "<label id='item_sale_price'></label>" + "</td>" +

                        "<td>" + "<label id='cost_per_unit'></label>" + "</td>" +

                        "<td>" +
                        "<input placeholder='unit price' id='unit_s_price' type='text' class='bg-dark form-control text-center unitsell-price-input mb-2' value=''>" +
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
                var value = parseFloat($(this).val());
                var result;

                switch (product_unit) {
                    case 'l':
                        result = ucv_name * value * 1000;
                        $(this).closest("tr").find("#minimum_qty").text(result);
                        $(this).closest("tr").find("#unit_id").text("ml");
                        break;
                    case 'kg':
                        result = ucv_name * value * 1000;
                        $(this).closest("tr").find("#minimum_qty").text(result);
                        $(this).closest("tr").find("#unit_id").text("g");
                        break;
                    case 'm':
                        result = ucv_name * value * 100;
                        $(this).closest("tr").find("#minimum_qty").text(result);
                        $(this).closest("tr").find("#unit_id").text("cm");
                        break;
                    case 'ml':
                        result = ucv_name * value;
                        $(this).closest("tr").find("#minimum_qty").text(result);
                        $(this).closest("tr").find("#unit_id").text("ml");
                        break;
                    case 'g':
                        result = ucv_name * value;
                        $(this).closest("tr").find("#minimum_qty").text(result);
                        $(this).closest("tr").find("#unit_id").text("g");
                        break;
                    case 'cm':
                        result = ucv_name * value;
                        $(this).closest("tr").find("#minimum_qty").text(result);
                        $(this).closest("tr").find("#unit_id").text("cm");
                        break;
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

            // add minimum qty manually to generate cost per unit
            $(document).on("input", ".cost-input", function() {
                if ($(this).closest("tr").find(".auto-generate-m-unit").hasClass("d-none")) {
                    var cost = parseFloat($(this).val());
                    var manual_unit_input = parseFloat($(this).closest("tr").find(".manual_unit_input").val());
                    var cost_per_unit = cost / manual_unit_input;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }
            });

            // Calculate cost per unit based on product_unit
            $(document).on("input", ".cost-input", function() {
                if (product_unit === "l") {
                    var cost = parseFloat($(this).val());
                    var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 1000;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "kg") {
                    var cost = parseFloat($(this).val());
                    var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 1000;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "m") {
                    var cost = parseFloat($(this).val());
                    var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 100;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "ml") {
                    var cost = parseFloat($(this).val());
                    var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "g") {
                    var cost = parseFloat($(this).val());
                    var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }

                if (product_unit === "cm") {
                    var cost = parseFloat($(this).val());
                    var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                    var cost_per_unit = cost / milliliters;
                    $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
                }
            });

            // Calculate Discount
            $(document).on("input", ".itemdicount", function() {
                var add_discount = parseFloat($(this).val());
                var qty = parseFloat($(this).closest("tr").find(".qty-input").val());
                var cost_input = parseFloat($(this).closest("tr").find(".cost-input").val());

                var item_sell_price = cost_input / qty;

                $(this).closest("tr").find("#item_sale_price").text(item_sell_price.toFixed(2));
            });


            // Initial adjustment of visibility
            adjustVisibilityForPackRows()

        });
    </script>

    <script>
        $('#myModal').on('shown.bs.modal', function() {
            $('#myInput').trigger('focus')
        })
    </script>

    <script src="dist/js/edit-stock.js"></script>


</body>

</html>