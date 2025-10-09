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
    <title>GRN | Add Stock</title>

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

                    <!-- left side design start -->
                    <div class="col-12">
                        <div class="card-body h-100 bg-dark overflow-hidden">
                            <div class="row">
                                <!-- <div class="col-6">
                                    <img src="dist/img/Siddha.lk (1).png" alt="" class="img-fluid">
                                </div>

                                <!-- supplier selector start -->
                                <!-- <div class="col-12 p-4">
                                    <label for="select-supplier">Select Supplier</label>
                                    <select name="select-supplier" id="select-supplier" class="form-control bg-dark" onchange="select_supplier(this.value);">
                                        <option value="0">select supplier</option> -->

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
                                <!-- <option value="<?php // $supplier_data["id"] 
                                                    ?>"><?php // $supplier_data["name"] 
                                                        ?></option>
                                        <?php
                                        //         }
                                        //     }
                                        // }

                                        ?>
                                     </select>
                                </div>  -->
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
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="filterBySupTable">
                                                    <?php

                                                    if (isset($_SESSION['store_id'])) {

                                                        $userLoginData = $_SESSION['store_id'];

                                                        foreach ($userLoginData as $userData) {
                                                            $shop_id = $userData['shop_id'];
                                                            $user_id = $userData['id'];
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
                    <!-- left side design end -->

                    <!-- add to stock table -->
                    <div class="row">
                        <div class="col-12 d-flex flex-row align-items-center overflow-hidden">
                            <div class="col-4 bg-red"></div>
                            <div class="col-4">
                                <label class="bg-cyan p-1 rounded-lg text-lg">
                                    ADD TO STOCK
                                </label>
                            </div>
                            <div class="col-4 input-group">
                                <label class="col-3 form-check-label bg-dark border-0" for="invoice_number">
                                    Inv. No
                                </label>
                                <input type="text" class="col-6 form-control bg-dark" id="invoice_number" placeholder="Invoice Number">
                                <div class="col-2 my-2 mx-3">
                                    <input type="checkbox" class="form-check-input" id="hasInvoiceNumber" onclick="$('#invoice_number').prop('disabled', !this.checked).val('');" checked>
                                </div>
                            </div>
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
                                        <th scope="col">Total Cost</th>
                                        <th scope="col">Total Value</th>
                                        <th scope="col">Unit Cost</th>
                                        <th scope="col">1 Unit Price</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="po_btn col-6 d-none flex-column">
                        <a type="button" class="btn btn-outline-success" id="proceedGrnBtn">Proceed Order <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <!-- right side design end -->
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
                                                <th scope="col">Total Value</th>
                                                <th scope="col">Unit Cost</th>
                                                <th scope="col">Discount(%)</th>
                                                <th scope="col">Item S-Price</th>
                                                <th scope="col">Unit S-Price</th>
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

</body>

<script>
    const user_id = <?php echo $user_id; ?>;
</script>

<script src="dist/js/add-stock_new2.js"></script>

</html>