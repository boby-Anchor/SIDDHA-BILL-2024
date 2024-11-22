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
                <div class="row w-100 mb-3">

                    <!-- left side desing start -->
                    <div class="col-12">
                        <div class="card-body h-100 bg-dark overflow-hidden">
                            <div class="row">

                                <!-- supplier products start -->
                                <div class="col-12 p-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Barcode" onkeyup="fbs();" id="bnInput">
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
                                                            $p_medicine_rs = $conn->query("SELECT p_medicine.id AS pid, p_medicine.name AS p_name, 
                                                            p_medicine.code AS code,
                                                            p_medicine.img AS img,
                                                            p_medicine_category.name AS category, p_brand.name AS brand,
                                                            medicine_unit.unit AS unit, unit_category_variation.ucv_name,
                                                            stock2.stock_id AS stock_id,
                                                            stock2.stock_item_cost AS item_cost,
                                                            stock2.unit_cost AS unit_cost,
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
                                                            stock_shop_id='$shop_id' AND medicine_unit.unit != 'pieces' OR medicine_unit.unit != pack / bottle
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
                                                                    <th id="itemCost" class="d-none"><?= $p_medicine_data['item_cost'] ?> </th>
                                                                    <th id="unitCost" class="d-none"><?= $p_medicine_data['unit_cost'] ?> </th>
                                                                    <th id="unitSprice" class="d-none"><?= $p_medicine_data['unitSprice'] ?> </th>
                                                                    <th scope="row"><?= $tableRowCount ?></th>
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
                            <h3>Edit Unit Price</h3>
                        </div>
                        <div class="col-12">
                            <table class="table table-dark table-hover addedProTable">
                                <thead class="text-center">
                                    <tr>
                                        <th scope="col">Barcode</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Brand</th>
                                        <th scope="col">Item Cost</th>
                                        <th scope="col">Unit Cost</th>
                                        <th scope="col">Item Price</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="po_btn col-6 d-none flex-column updatePriceBtn">
                        <a type="button" class="btn btn-outline-success " id="updatePriceBtn">Update Price <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <!-- right side desing end -->
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

    <script>
        // ==============================================================================

        $(document).on("click", "#updatePriceBtn", function() {

            $(".updatePriceBtn").prop('disabled', true);

            var poArray = [];
            var hasErrors = false; // Flag to track if there are any errors

            $(".addedProTable tbody tr").each(function() {
                var product_code = $(this).find("#product_code").text(); // Barcode
                var product_name = $(this).find("#product_name").text().trim(); // Item name

                var item_cost = $(this).find("#item_cost").text().trim(); // Item Cost
                var unit_cost = $(this).find("#unit_cost").text().trim(); // Unit Cost
                var item_price = $(this).find("#item_price").text().trim() || 0; // Item Price
                var unit_price = parseFloat($(this).find("#unit_price").val().trim()) || 0; // Unit price

                // Data Validation
                if (unit_price !== 0 && unit_cost > unit_price) {
                    ErrorMessageDisplay(product_name + " එකේ Unit Cost > Unit Price..!");
                    hasErrors = true;
                    return false;
                } else {
                    var productData = {
                        product_code: product_code,
                        product_name: product_name,

                        item_cost: item_cost,
                        unit_cost: unit_cost,
                        item_price: item_price,
                        unit_price: unit_price,
                    };
                    poArray.push(productData);
                }
            });

            if (!hasErrors) {
                $.ajax({
                    url: "edit_unit_price_action.php",
                    method: "POST",
                    data: {
                        products: JSON.stringify(poArray),
                    },
                    success: function(response) {
                        var result = JSON.parse(response);

                        if (result.status === 'success') {
                            $(".addedProTable tbody").empty();
                            Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            }).fire({
                                icon: "success",
                                title: result.message
                            }).then(() => {
                                location.reload(true);
                            });

                        } else if (result.status === 'error') {
                            $(".confirmPObtn").prop('disabled', false);
                            ErrorMessageDisplay(result.message);
                        } else if (result.status === 'sessionExpired') {
                            $(".confirmPObtn").prop('disabled', false);
                            ErrorMessageDisplay(result.message);
                            setTimeout(function() {
                                window.open(window.location.href, '_blank');
                            }, 5000);
                        } else {
                            $(".confirmPObtn").prop('disabled', false);
                            MessageDisplay("error", "Error" + result.status, result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $.ajax({
                            url: "error_log.php",
                            method: "POST",
                            data: {
                                message: JSON.stringify(xhr.responseText),
                            },
                            success: function(response) {
                                if (response === 'success') {
                                    MessageDisplay("error", "404", "Connection failed.");
                                } else if (response === 'error') {
                                    MessageDisplay("error", "404", "Log and Connection failed. " + result.message);
                                } else {
                                    MessageDisplay("error", "FATAL ERROR", "Contact IT department.");
                                }
                            },
                            error: function(xhr, status, error) {
                                MessageDisplay("error", "FATAL ERROR", "Contact IT department.");
                                console.error(xhr.responseText);
                            },
                        });
                    },
                });
            }

        });

        // ==============================================================================

        $(document).on("click", ".add-btn", function() {
            var product_code = $(this).closest("tr").find("#product_code").text();
            var product_name = $(this).closest("tr").find("#product_name").text().trim();
            var product_brand = $(this).closest("tr").find("#product_brand").text().trim();

            var itemCost = $(this).closest("tr").find("#itemCost").text().trim();
            var unitCost = $(this).closest("tr").find("#unitCost").text().trim();
            var itemSprice = $(this).closest("tr").find("#itemSprice").text().trim();
            var unitSprice = $(this).closest("tr").find("#unitSprice").text().trim();

            var ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
            var product_unit = $(this).closest("tr").find("#product_unit").text();

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
                    "<td> <label id='product_name'>" + product_name + "</label>(<label id='ucv_name'>" + ucv_name + "</label><label id='product_unit'>" + product_unit + "</label>)</td>" +

                    "<td> <label id='product_brand'>" + product_brand + "</label></td>" +

                    "<td> <label id='item_cost'>" + itemCost + "</label></td>" +
                    "<td> <label id='unit_cost'>" + unitCost + "</label></td>" +
                    "<td> <label id='item_price'>" + itemSprice + "</label></td>" +

                    "<td>" + "<input id='unit_price' type='text' class='bg-dark form-control text-center' value='" + unitSprice + "' placeholder='Unit price'>" + "</td>" +

                    "<td><i class='fa fa-trash-o cus-delete'></i></td>" +

                    "</tr>";

                $(".addedProTable tbody").append(markup);

                $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

            } else {
                ErrorMessageDisplay("Product already exists in the list!");
            }
        });

        // Event listener for clicking the delete button
        $(document).on("click", ".cus-delete", function() {
            $(this).closest("tr").remove();
            $("#updatePriceBtn").removeAttr("data-toggle data-target");
            $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
            $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);
        });

        // ==============================================================================

        function fbs() {
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
            req.open("POST", "edit_unit_price_search.php", true);
            req.send(form);
        }

        // ===============================================================================
    </script>

</body>

</html>