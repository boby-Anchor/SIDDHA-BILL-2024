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
            <div class="col-12">
                <div class="row w-100">

                    <!-- left side desing start -->
                    <div class="col-12">
                        <div class="card-body h-100 bg-dark overflow-hidden">
                            <div class="row">
                                <div class="col-6">
                                    <!-- <img src="dist/img/Siddha.lk (1).png" alt="" class="img-fluid"> -->
                                </div>


                                <!-- supplier selector start -->
                                <div class="col-12 p-4">
                                    <label for="select-supplier">Select Supplier</label>
                                    <select name="select-supplier" id="select-supplier" class="form-control bg-dark" onchange="select_suplier(this.value);">
                                        <option value="0">select supplier</option>

                                        <?php
                                        if (isset($_SESSION['store_id'])) {

                                            $userLoginData = $_SESSION['store_id'];

                                            foreach ($userLoginData as $userData) {
                                                $shop_id = $userData['shop_id'];
                                                $supplier_rs = $conn->query("SELECT DISTINCT p_supplier.* FROM p_supplier
                                        INNER JOIN p_medicine ON p_supplier.brand_id = p_medicine.brand
                                        INNER JOIN producttoshop ON p_medicine.id = producttoshop.medicinId
                                        WHERE producttoshop.shop_id = '$shop_id'
                                        ");
                                                while ($supplier_data = $supplier_rs->fetch_assoc()) {
                                        ?>
                                                    <option value="<?= $supplier_data["id"] ?>"><?= $supplier_data["name"] ?></option>
                                        <?php
                                                }
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>
                                <!-- supplier selector end -->

                                <!-- supplier products start -->
                                <div class="col-12 p-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Barcode Number" onkeyup="filterBySearch(this.value);" id="bnInput">
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Product Code" onkeyup="filterBySearch(this.value);" id="pcInput">
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="form-control bg-dark" placeholder="Product Name" onkeyup="filterBySearch(this.value);" id="pnInput">
                                        </div>
                                        <div class="col-12 products-table mt-4">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Product Preview</th>
                                                        <th scope="col">Product Name</th>
                                                        <th scope="col">Product Cost</th>
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

                                                            $p_medicine_rs = $conn->query("SELECT * FROM producttoshop
                                                            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
                                                            INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                                                            WHERE  shop_id='$shop_id' AND (producttoshop.productToShopStatus = 'added' OR producttoshop.productToShopStatus = 'all') ");
                                                            $tableRowCount = 1;
                                                            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
                                                    ?>
                                                                <tr>
                                                                    <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                                                                    <th scope="row"><?= $tableRowCount ?></th>
                                                                    <td>
                                                                        <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                                                                    </td>
                                                                    <td id="product_name"><?= $p_medicine_data['name'] ?></td>
                                                                    <td id="product_cost">
                                                                        <label for=""><?= $p_medicine_data['cost'] ?></label>
                                                                    </td>
                                                                    <td id="product_unit">
                                                                        <label for=""><?= $p_medicine_data['unit'] ?></label>
                                                                    </td>
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

                    <!-- right side desing start -->
                    <div class="col-12 d-flex flex-column align-items-center">
                        <div class="grn_tittle">
                            <h3>ADD TO STOCK</h3>
                        </div>
                        <div class="col-10">
                            <table class="table table-dark table-hover addedProTable">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Product Cost</th>
                                        <th scope="col">Quantity & Unit</th>
                                        <th scope="col">Selling Price</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="po_btn col-6 d-none flex-column">
                            <a type="button" class="btn btn-outline-success proceed-grn" id="proceedGrnBtn">Proceed Order <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- right side desing end -->
                </div>
            </div>
        </div>

        <!-- add new unit modal start -->

        <div class="container">
            <div class="modal fade" id="addunitmodal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add New Unit</h4>
                        </div>
                        <div class="modal-body">
                            <input type="text" placeholder="Unite Name..." id="newUnit">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="addNewUnit();">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
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

        $orderId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = 'siddhahub' AND table_name = 'stock2'");
        $orderId_row = $orderId_rs->fetch_assoc();
        $grnId = $orderId_row['AUTO_INCREMENT'];
        $grnNumber = "ST-$userId$shop_id" . "00" . $grnId;

        $grnDate = date("Y-m-d");
        $grnTime = date("H:i:s");

        ?>

        <!-- confirm po modal start -->
        <div class="container">
            <div class="modal fade" id="confirmGRN" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Stock Confirmation</h4>
                        </div>
                        <div class="modal-body">
                            <div class="grnId">
                                <div class="row">
                                    <div class="col-4 text-center">
                                        <label for="grnNumber">Stock Number</label>
                                        <?php
                                        echo "<span class=\"fs-2\" name=\"grnNumber\" id=\"grnNumber\">$grnNumber</span>";
                                        ?>

                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="grnDate">Date</label>
                                                </div>
                                                <div class="col-12">
                                                    <span class="fs-2" name="grnDate" id="grnDate"><?= $grnDate ?></span>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="col-4 text-center">
                                        <label for="grnTime">Added time</label>
                                        <span class="fs-2" name="grnTime" id="grnTime"><?= $grnTime ?></span>
                                    </div>
                                    <div class="orderItem col-12 mt-4 mb-3">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Item</th>
                                                    <th scope="col">Cost</th>
                                                    <th scope="col">Qty</th>
                                                    <th scope="col">Price</th>
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


    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <script>
        $(document).ready(function() {

            $(document).on("click", ".add-btn", function() {
                var product_code = $(this).closest("tr").find("#product_code").text();
                var product_name = $(this).closest("tr").find("#product_name").text();
                var product_cost = $(this).closest("tr").find("#product_cost").text();
                var product_unit = $(this).closest("tr").find("#product_unit").text();
                var product_qty = 1;


                var exists = false;
                $(".addedProTable tbody tr").each(function() {
                    if ($(this).find("#addproduct_name").text() === product_name) {
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {

                    var markup =
                        "<tr>" +
                        "<th scope='row'>" + product_code + "</th>" +
                        "<td id='addproduct_name'>" + product_name + "</td>" +
                        "<td id='addproduct_cost'>" + product_cost + "</td>" +
                        "<td>" +
                        "<div class='medi_uni'>" +
                        "<div class='row'>" +
                        "<div class='col-4'>" +
                        "<div class='row'>" +
                        "<div class='col-9'>" +
                        "<input type='text' class='bg-dark form-control text-center' value='" + product_qty + "'>" +
                        "</div>" +
                        "<div class='col-1'>" + "<lable id='product_unit'>" + product_unit + "</lable>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</td>" +
                        "<td>" +
                        "<input type='text' style='background:none;color: white;font-weight: bold;' id='sellingPrice' value='0'>" +
                        "</td>" +
                        "<td><i class='fa fa-trash-o cus-delete'></i></td>" +
                        "</tr>";

                    $(".addedProTable tbody").append(markup);

                    $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                    $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);


                } else {
                    alert("Product already exists in the list!");
                }
            });

            $(document).on("click", ".cus-delete", function() {
                $(this).closest("tr").remove();
                $("#proceedGrnBtn").removeAttr("data-toggle data-target");
                $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

            });
        });
    </script>

    <script>
        $('#myModal').on('shown.bs.modal', function() {
            $('#myInput').trigger('focus')
        })
    </script>

    <script>
        $(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function() {
            var grnNumber = document.getElementById("grnNumber").innerText;
            var grnDate = document.getElementById("grnDate").innerText;
            var grnTime = document.getElementById("grnTime").innerText;
            $(this).prop('disabled', true);

            var poArray = [];

            $("#grnConfirmationTableBody tr").each(function() {
                var product_code = $(this).find(".product_code").text();
                var product_name = $(this).find(".product_name").text();
                var product_cost = $(this).find(".product_cost").text();
                var product_qty = $(this).find(".product_qty").text();
                var qty_unit = $(this).find(".qty_unit").text();
                var sellingPrice = $(this).find(".sellingPrice").text();

                var productData = {
                    product_code: product_code,
                    product_name: product_name,
                    product_cost: product_cost,
                    product_qty: product_qty,
                    qty_unit: qty_unit,
                    grnNumber: grnNumber,
                    grnDate: grnDate,
                    grnTime: grnTime,
                    sellingPrice: sellingPrice,
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
                    console.log(response);
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