<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$selected_date = '';
$invoices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = $_POST['reg_no'];

    // $result = $conn->query("SELECT * 
    // FROM invoiceitems
    // INNER JOIN invoices ON invoices.invoice_id = invoiceitems.invoiceNumber
    // WHERE invoices.reg = '$reg_no' AND invoiceitems.isPaththu=0;
    // ");

    $result = $conn->query("SELECT invoices.*,  users.name AS user, shop.shopName AS shop_name
    FROM invoices
    INNER JOIN users ON invoices.user_id = users.id
    INNER JOIN shop ON invoices.shop_id = shop.shopId
    WHERE invoices.reg = '$reg_no' ORDER BY Date('created') ASC
    ");


    $invoices = $result->fetch_all(MYSQLI_ASSOC);
}
$totalPrice = 0;
$totalValue = 0;

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
                            <form action="" method="post">
                                <div class="col-auto">
                                    <label for="start_date" class="col-form-label">Reg. No:</label>
                                </div>
                                <div class="col-auto input-group">
                                    <input type="text" id="reg_no" name="reg_no" class="form-control" required>
                                    <button type="submit" class="form-control col-2 btn btn-success ml-2"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <!-- <div class="col-2">
                        </div> -->

                        <!-- <div class="col-6 row">
                            <div class="col-6">
                                <label>Total Value:</label>
                                <label id="totalValueLabel"><?= htmlspecialchars($totalValue) ?></label>
                            </div>
                            <div class="col-6">
                                <label>Total Price:</label>
                                <label id="totalPriceLabel"><?= htmlspecialchars($totalPrice) ?></label>
                            </div>
                        </div> -->
                    </div>

                    <div class="row p-4">
                        <div class="col-12">
                            <table class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <!-- <th>Invoice ID</th> -->
                                    <th>Date</th>
                                    <th>Item Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>


                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($invoices)) {
                                        foreach ($invoices as $invoice):

                                    ?>
                                            <tr>
                                                <td colspan="5" class="col-auto">
                                                    <label class="col-auto"> Inv. No :</label> <label class="col-auto"> <?= htmlspecialchars($invoice['invoice_id']); ?> </label>
                                                    <label class="col-auto">Date :</label><label class="col-auto"><?= htmlspecialchars(date('Y-m-d', strtotime($invoice['created']))); ?></label>
                                                    <label class="col-auto">User :</label><label class="col-auto"><?= htmlspecialchars($invoice['user']); ?></label>
                                                    <label class="col-auto">Shop :</label><label class="col-auto"><?= htmlspecialchars($invoice['shop_name']); ?></label>
                                                    <label class="col-auto">Bill Total :</label><label class="col-auto"><?= htmlspecialchars($invoice['total_amount']); ?></label>
                                                </td>
                                                <!-- <td><label>Shop :</label><?= htmlspecialchars($invoice['shop_id']); ?></td> -->
                                            </tr>
                                            <!-- <tr>
                                                <td><label>Shop :</label><?= htmlspecialchars($invoice['shop_id']); ?></td>
                                                <td><label>Shop :</label><?= htmlspecialchars($invoice['shop_id']); ?></td>
                                                <td><label>Shop :</label><?= htmlspecialchars($invoice['shop_id']); ?></td>
                                                <td><label>Shop :</label><?= htmlspecialchars($invoice['shop_id']); ?></td>
                                                <td><label>Shop :</label><?= htmlspecialchars($invoice['shop_id']); ?></td>
                                            </tr> -->

                                            <?php

                                            $dmDataResult = $conn->query("SELECT * 
                                            FROM dm_items
                                            WHERE invoice_id = '" . $invoice['invoice_id'] . "'
                                            ");

                                            $dm_Item_data = $dmDataResult->fetch_all(MYSQLI_ASSOC);

                                            foreach ($dm_Item_data as $data) {

                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($data['dmName']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['totalPrice']); ?></td>
                                                </tr>
                                            <?php

                                            }





                                            $itemDataResult = $conn->query("SELECT * 
                                            FROM invoiceitems
                                            WHERE invoiceNumber = '" . $invoice['invoice_id'] . "'
                                            AND invoiceitems.isPaththu = 0
                                            ");


                                            $item_data = $itemDataResult->fetch_all(MYSQLI_ASSOC);

                                            foreach ($item_data as $data) {

                                            ?>
                                                <tr>
                                                    <!-- <td><?php echo htmlspecialchars($data['invoiceNumber']); ?></td> -->
                                                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($invoice['created']))); ?></td>
                                                    <td><?php echo htmlspecialchars($data['invoiceItem']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['invoiceItem_price']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['invoiceItem_qty']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['invoiceItem_total']); ?></td>
                                                </tr>
                                        <?php

                                            }
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No Data</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- <script>
                        document.getElementById('totalPriceLabel').textContent = "<?= htmlspecialchars($totalPrice) ?>";
                        document.getElementById('totalValueLabel').textContent = "<?= htmlspecialchars($totalValue) ?>";
                    </script> -->

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
    <div id="invoice-POS" class="d-none">

        <?php
        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            $currentDate = date("Y-m-d");
            $currentTime = date("H:i:s");

            foreach ($userLoginData as $userData) {
                $shop_id = $userData['shop_id'];
                $user_name = $userData['name'];

                $bill_data_rs = $conn->query("SELECT shop.shopName AS shopName, customize_bills.*
          FROM `customize_bills`
          INNER JOIN shop ON shopId = customize_bills.`customize_bill_shop-id`
          WHERE `customize_bill_shop-id` = '$shop_id'
          ");
                $bill_data = $bill_data_rs->fetch_assoc();
        ?>
                <div class="d-flex justify-content-center">
                    <div class="col-12 p-2" style="width:<?= $bill_data['print_paper_size'] ?>mm ; background: whitesmoke;">
                        <div class="row gap-1">
                            <table>
                                <tr>
                                    <td colspan="3">
                                        <div class="col-12 d-flex justify-content-center p-2">
                                            <div class="billpreviewlogo<?= $bill_data['print_paper_size'] ?>"
                                                style="background-image:url('<?= $bill_data['customize_bills_logo'] ?>');">
                                            </div>
                                            <!-- <div class="text-center">
                          <label style="font-size: large; font-weight: 100;">
                            <h3>
                              <b>
                                <?php //echo $bill_data['shopName'] 
                                ?>
                              </b>
                            </h3>
                          </label>
                        </div> -->
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="col-12 d-flex justify-content-center">
                                            <label class="contactNumber"
                                                id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                                        </div>
                                        <div class="col-12 d-flex justify-content-center text-center center">
                                            <label id="addresspreview"
                                                class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12" style="text-align: center;">
                                        <span><span class="text-left" style="font-size: 10px;"><?= $currentDate ?>
                                            </span><span class="text-right"> <?= $currentTime ?></span> </span>
                                        <br>
                                        <span><span class="invoicePatientName" id="invoicePatientName"></span> <span
                                                id="InvoiceContactNumber"></span></span>
                                        <br>
                                        <span><span class="fw-bold"><?= $user_name ?> Inv.</span> <span class="fw-bolder"
                                                style="font-size: 10px;" id="invoiceNumber"></span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
                        </div>
                        <!-- table header start -->
                        <div class="row">
                            <div class="col-4">
                                <span class="product_cost">U.Price</span>
                            </div>
                            <div class="col-4 text-center">
                                <span class="product_qty">
                                    QTY
                                </span>
                            </div>
                            <div class="col-4 text-center">
                                <span class="productTotal">Total</span>
                            </div>
                        </div>
                        <!-- table header end -->
                        <div class="printInvoiceData" id="printInvoiceData">

                        </div>
                        <table>
                            <tr style="font-weight: 600;">
                                <td>
                                    <div class="col-12 pt-2">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center text-center">
                                                <span id="billnotepreview"
                                                    style="font-size:9px;"><?= $bill_data['bill_note'] ?></span>
                                            </div>
                                            <div class="col-12 d-flex justify-content-center">
                                                <span>Thank You !</span>
                                            </div>

                                            <!-- Checked by box -->
                                            <div class="col-12 d-flex justify-content-center">
                                                <div class="check-by-box">
                                                    <center>
                                                        <label style="font-weight:bold; margin-bottom:3px;">Checked
                                                            By</label>
                                                    </center>

                                                    <label for="date">Date: <?= $currentDate ?><?= $currentTime ?></label>

                                                    <label for="emp-no">EMP No:.............................</label>

                                                    <label for="signature">Signature:..........................</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>

</body>

</html>