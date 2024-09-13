<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customize Bill</title>
        <!-- All CSS -->
        <?php include("part/all-css.php"); ?>

        <!-- font awsome link -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>

    <body class="hold-transition sidebar-mini layout-fixed" onload="focus_filed();">
        <div class="wrapper">

            <!-- Navbar -->
            <?php include("part/navbar.php"); ?>
            <!-- Navbar end -->

            <!-- Sidebar -->
            <?php include("part/sidebar.php"); ?>
            <!--  Sidebar end -->


            <div class="content-wrapper bg-dark">
                <div class="row w-100">

                    <div class="col-12 col-md-12 bg-dark customize-bar-main">
                        <div class="col-12 cbTittleMain mb-2">
                            <h1>Add Bill</h1>
                        </div>

                        <div class="col-12 ">
                            <table class="w-100" id="sortable">
                                <thead></thead>

                                <tr>
                                    <td colspan="3" class="">
                                        <div class="col-12 d-flex justify-content-center mb-2">
                                            <div class="col-10 p-2 customize-bar">
                                                <div class="row">
                                                    <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="">
                                                            <h2>Shop</h2>
                                                        </div>
                                                        <div class="">
                                                            <span>Select your shop here</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                                        <select name="selectedShop" id="selectedShop" class="bg-dark form-control border-0 col-6">
                                                            <option value="0">select shop</option>
                                                            <?php
                                                            $shop_rs = $conn->query("SELECT * FROM shop");
                                                            while ($shop_data = $shop_rs->fetch_assoc()) {
                                                            ?>
                                                                <option value="<?= $shop_data['shopId'] ?>"><?= $shop_data['shopName'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="">
                                        <div class="col-12 d-flex justify-content-center mb-2">
                                            <div class="col-10 p-2 customize-bar">
                                                <div class="row">
                                                    <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="">
                                                            <h2>Logo</h2>
                                                        </div>
                                                        <div class="">
                                                            <span>Add Your Logo here</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                                        <input type="file" id="fileInput" style="display: none;">
                                                        <div class="logoImg" style="background-image: url('');"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>



                                <tr>
                                    <td colspan="3">
                                        <div class="col-12 d-flex justify-content-center mb-2">
                                            <div class="col-10 p-2 customize-bar">
                                                <div class="row">
                                                    <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="">
                                                            <h2>Contact No</h2>
                                                        </div>
                                                        <div class="">
                                                            <span>Add Your Contact No here</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                                        <div class="">
                                                            <input type="text" id="contactNo" class="bg-dark form-control border-0 text-center" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <div class="col-12 d-flex justify-content-center mb-2">
                                            <div class="col-10 p-2 customize-bar">
                                                <div class="row">
                                                    <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="">
                                                            <h2>Address</h2>
                                                        </div>
                                                        <div class="">
                                                            <span>Add Your Address here</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                                        <div>
                                                            <input type="text" class="bg-dark form-control border-0 text-center" id="address" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <div class="col-12 d-flex justify-content-center mb-2">
                                            <div class="col-10 p-2 customize-bar">
                                                <div class="row">
                                                    <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="">
                                                            <h2>Note</h2>
                                                        </div>
                                                        <div class="">
                                                            <span>Add note to bill</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                                        <div class="">
                                                            <input type="text" id="note" class="bg-dark form-control border-0 text-center" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="col-11 d-flex justify-content-end">
                                <button class="btn btn-primary submitBtn">Submit</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 d-none">
                        <div class="col-12 billPreviewMain bg-dark d-flex justify-content-center">

                            <div class="paperSize<?= $billpreview_data['print_paper_size'] ?> bg-light" id="<?= $billpreview_data['print_paper_size'] ?>mm">
                                <table class="w-100">
                                    <thead></thead>
                                    <tbody>


                                        <tr>
                                            <td colspan="3">
                                                <div class="col-12 d-flex justify-content-center p-2">
                                                    <div class="billpreviewlogo<?= $billpreview_data['print_paper_size'] ?>" style="background-image: url('<?= $billpreview_data['customize_bills_logo'] ?>');"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="" id="contactNumberPreview"><?= $billpreview_data['customize_bills_mobile'] ?></label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center ">
                                                    <label id="addresspreview" class="address<?= $billpreview_data['print_paper_size'] ?>"><?= $billpreview_data['customize_bills_address'] ?></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: lightgray 0.2rem solid;">
                                            <td>
                                                <div class="col-12 d-flex justify-content-center ">
                                                    <label class="datetime<?= $billpreview_data['print_paper_size'] ?>">2024-03-15 15:57:35</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="agent<?= $billpreview_data['print_paper_size'] ?>">Hub Admin No-00011162</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="font-weight: 600;">
                                            <td class="pt-2">
                                                <?php
                                                for ($i = 0; $i < 5; $i++) {
                                                ?>
                                                    <div class="col-12 offset-1 productTable<?= $billpreview_data['print_paper_size'] ?>">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="product_name">Example Product</span>
                                                            </div>
                                                            <div class="col-4">
                                                                <span class="product_cost">1500</span>
                                                            </div>
                                                            <div class="col-2">
                                                                <span class="product_qty">5kg</span>
                                                            </div>
                                                            <div class="col-4" style="text-align: end;">
                                                                <span class="productTotal">7500.00</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr style="font-weight: 600;">
                                            <td>

                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-11 d-flex justify-content-end pt-2">
                                                            <span class="productsAllTotal">Total : 7500.00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="enterAmountFiled">Paid :8000 .00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="balance">Balance : 500.00</span>
                                                        </div>
                                                    </div>
                                                </div>


                                            </td>
                                        </tr>

                                        <tr style="font-weight: 600;">
                                            <td>
                                                <div class="col-12 pt-2">
                                                    <div class="row">
                                                        <div class="col-12 d-flex justify-content-center text-center">
                                                            <span id="billnotepreview" style="font-size:9px;"><?= $billpreview_data['bill_note'] ?></span>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-center">
                                                            <span>Thank You !</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                            <?php

                            ?>
                            <div class="paperSize48 d-none" id="48mm">
                                <table class="w-100">
                                    <thead></thead>
                                    <tbody>


                                        <tr>
                                            <td colspan="3">
                                                <div class="col-12 d-flex justify-content-center p-2">
                                                    <div class="billpreviewlogo48"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">070 777 9898</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center ">
                                                    <label class="address48">227/01 Asiri Mawatha <br> Kandy Road , Yakkala</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: lightgray 0.2rem solid;">
                                            <td>
                                                <div class="col-12 d-flex justify-content-center ">
                                                    <label class="datetime48">2024-03-15 15:57:35</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="agent48">Hub Admin No-00011162</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <?php
                                                for ($i = 0; $i < 5; $i++) {
                                                ?>
                                                    <div class="col-12 offset-1 productTable48">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="product_name">Example Product</span>
                                                            </div>
                                                            <div class="col-4">
                                                                <span class="product_cost">1500</span>
                                                            </div>
                                                            <div class="col-2">
                                                                <span class="product_qty">5kg</span>
                                                            </div>
                                                            <div class="col-4" style="text-align: end;">
                                                                <span class="productTotal">7500.00</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-11 d-flex justify-content-end pt-2">
                                                            <span class="productsAllTotal">Total : 7500.00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="enterAmountFiled">Paid :8000 .00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="balance">Balance : 500.00</span>
                                                        </div>
                                                    </div>
                                                </div>


                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="col-12 pt-2">
                                                    <div class="row">
                                                        <div class="col-12 d-flex justify-content-center text-center">
                                                            <span style="font-size:9px;">*Kindly note that once the Medication has been taken away. The hospital cannot be held responsible for any issues.</span>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-center">
                                                            <span>Thank You !</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>

                            <div class="paperSize58 d-none" id="58mm">
                                <table class="w-100">
                                    <thead></thead>
                                    <tbody>


                                        <tr>
                                            <td colspan="3">
                                                <div class="col-12 d-flex justify-content-center p-2">
                                                    <div class="billpreviewlogo58"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">070 777 9898</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">227/01 Asiri Mawatha <br> Kandy Road , Yakkala</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: lightgray 0.2rem solid;">
                                            <td>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">2024-03-15 15:57:35</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">Hub Admin No-00011162</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <?php
                                                for ($i = 0; $i < 5; $i++) {
                                                ?>
                                                    <div class="col-12 offset-1">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="product_name">Example Product</span>
                                                            </div>
                                                            <div class="col-4">
                                                                <span class="product_cost">1500</span>
                                                            </div>
                                                            <div class="col-2">
                                                                <span class="product_qty">5kg</span>
                                                            </div>
                                                            <div class="col-4" style="text-align: end;">
                                                                <span class="productTotal">7500.00</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-11 d-flex justify-content-end pt-2">
                                                            <span class="productsAllTotal">Total : 7500.00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="enterAmountFiled">Paid :8000 .00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="balance">Balance : 500.00</span>
                                                        </div>
                                                    </div>
                                                </div>


                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="col-12 pt-2">
                                                    <div class="row">
                                                        <div class="col-12 d-flex justify-content-center text-center">
                                                            <span style="font-size:9px;">*Kindly note that once the Medication has been taken away. The hospital cannot be held responsible for any issues.</span>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-center">
                                                            <span>Thank You !</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                            <div class="paperSize80 bg-light d-none" id="80mm">
                                <table class="w-100">
                                    <thead></thead>
                                    <tbody>


                                        <tr>
                                            <td colspan="3">
                                                <div class="col-12 d-flex justify-content-center p-2">
                                                    <div class="billpreviewlogo80"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">070 777 9898</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">227/01 Asiri Mawatha <br> Kandy Road , Yakkala</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: lightgray 0.2rem solid;">
                                            <td>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">2024-03-15 15:57:35</label>
                                                </div>
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label class="">Hub Admin No-00011162</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <?php
                                                for ($i = 0; $i < 5; $i++) {
                                                ?>
                                                    <div class="col-12 offset-1">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="product_name">Example Product</span>
                                                            </div>
                                                            <div class="col-4">
                                                                <span class="product_cost">1500</span>
                                                            </div>
                                                            <div class="col-2">
                                                                <span class="product_qty">5kg</span>
                                                            </div>
                                                            <div class="col-4" style="text-align: end;">
                                                                <span class="productTotal">7500.00</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-11 d-flex justify-content-end pt-2">
                                                            <span class="productsAllTotal">Total : 7500.00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="enterAmountFiled">Paid :8000 .00</span>
                                                        </div>

                                                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                                                            <span class="balance">Balance : 500.00</span>
                                                        </div>
                                                    </div>
                                                </div>


                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="col-12 pt-2">
                                                    <div class="row">
                                                        <div class="col-12 d-flex justify-content-center text-center">
                                                            <span style="font-size:9px;">*Kindly note that once the Medication has been taken away. The hospital cannot be held responsible for any issues.</span>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-center">
                                                            <span>Thank You !</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php ?>


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
        <!-- Data Table JS -->
        <?php include("part/data-table-js.php"); ?>
        <!-- Data Table JS end -->

        <!-- select2 input field -->

        <!-- ========================================== -->

        <script src="dist/js/customize_bill.js"></script>

    </body>

    </html>
<?php
}
?>