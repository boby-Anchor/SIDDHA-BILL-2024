<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$invoiceNumber = '';
$items = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['invoiceNumber'])) {
        $invoiceNumber = $_POST['invoiceNumber'];

        $invResult = $conn->query("SELECT invoices.*, users.name AS cashier, shop.shopName AS shop, bill_type.bill_type_name AS billType,
        payment_type.payment_type AS paymentType
        FROM invoices
        INNER JOIN users ON invoices.user_id = users.id
        INNER JOIN shop ON invoices.shop_id = shop.shopId
        INNER JOIN bill_type ON invoices.bill_type_id = bill_type.bill_type_id
        INNER JOIN payment_type ON invoices.payment_method = payment_type.payment_type_id
        WHERE invoice_id = '$invoiceNumber';
        ");
        $invoiceData = $invResult->fetch_all(MYSQLI_ASSOC);

        $result = $conn->query("SELECT *
        FROM invoiceitems
        WHERE invoiceNumber = '$invoiceNumber' AND isPaththu = 0;
        ");
        $items = $result->fetch_all(MYSQLI_ASSOC);

        $dmResult = $conn->query("SELECT *
        FROM dm_items
        WHERE invoice_id = '$invoiceNumber';
        ");
        $dmItems = $dmResult->fetch_all(MYSQLI_ASSOC);
    }
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
                                <div class="input-group">
                                    <label for="invoiceNumber" class="form-control bg-dark">Inv. No:</label>
                                    <input type="text" id="invoiceNumber" name="invoiceNumber"
                                        class="form-control col-8 bg-dark"
                                        value="<?php echo htmlspecialchars($invoiceNumber); ?>" required>
                                    <button type="submit" class="form-control col-2 btn btn-success ml-2"><i
                                            class="fas fa-search"></i></button>
                                    <button class="form-control col-2 btn btn-success ml-2" onclick="printInvBill()"><i
                                            class="fas fa-print"></i></button>
                                </div>
                            </form>
                        </div>

                        <?php
                        if (!empty($invoiceData)) {
                            foreach ($invoiceData as $invData):
                        ?>
                                <div class="col-8 row">
                                    <div class="col-2 p-1">
                                        <label>Name: <?= $invData['p_name'] ?></label>
                                    </div>
                                    <div class="col-2 p-1">
                                        <label>Contact No: <?= $invData['contact_no'] ?></label>
                                    </div>
                                    <div class="col-2 p-1">
                                        <label>Doctor: <?= $invData['d_name'] ?></label>
                                    </div>
                                    <div class="col-2 p-1">
                                        <label>Reg. No: <?= $invData['reg'] ?></label>
                                    </div>
                                    <div class="col-2 p-1">
                                        <label>Date: <?= $invData['created'] ?></label>
                                    </div>
                                </div>

                                <div class="col-12 row">
                                    <div class="col-2">
                                        <label>Cashier: <?= $invData['cashier'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Shop: <?= $invData['shop'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Bill Type: <?= $invData['billType'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Paid Method: <?= $invData['paymentType'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Amount: <?= $invData['total_amount'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Discount: <?= $invData['discount_percentage'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Cash: <?= $invData['paidAmount'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Card: <?= $invData['cardPaidAmount'] ?></label>
                                    </div>
                                    <div class="col-2">
                                        <label>Balance: <?= $invData['balance'] ?></label>
                                    </div>
                                </div>
                        <?php
                            endforeach;
                        }
                        ?>
                    </div>

                    <div class="row p-4">
                        <div class="col-12">
                            <table class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Type</th>
                                    <th>Is Paththu?</th>
                                    <th>Value</th>
                                    <th>Item Total</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $rowNo = 0;
                                    if (!empty($dmItems)) {

                                        foreach ($dmItems as $dmItem):
                                            $rowNo++
                                    ?>
                                            <tr>
                                                <td><?= $rowNo ?></td>
                                                <td><?= htmlspecialchars($dmItem['dmName']); ?></td>
                                                <td>1</td>
                                                <td>Doctor medicine</td>
                                                <td><?= number_format(htmlspecialchars($dmItem['totalPrice']), 0); ?></td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    }

                                    if (!empty($items)) {

                                        foreach ($items as $item):
                                            $rowNo++
                                        ?>
                                            <tr>
                                                <td><?= $rowNo ?></td>
                                                <td><?= htmlspecialchars($item['invoiceItem']); ?></td>
                                                <td><?= htmlspecialchars($item['invoiceItem_qty']); ?></td>
                                                <td><?= htmlspecialchars($item['invoiceItem_unit']); ?></td>
                                                <td><?= $item['isPaththu'] == 0 ? "Invoice" : "Paththu"; ?></td>
                                                <td><?= number_format(htmlspecialchars($item['invoiceItem_price']), 0); ?></td>
                                                <td><?= number_format(htmlspecialchars($item['invoiceItem_total']), 0); ?></td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    }
                                    if (empty($items) && empty($dmItems)) {
                                        ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No data</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
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
                                        <span><span class="text-left"
                                                style="font-size: 10px;"><?= date("Y-m-d", strtotime($invData['created'])) ?>
                                            </span><span class="text-right">
                                                <?= date("H:i:s", strtotime($invData['created'])); ?></span> </span>
                                        <br>
                                        <span><span class="invoicePatientName"
                                                id="invoicePatientName"><?= $invData['p_name'] ?></span> <span
                                                id="InvoiceContactNumber"><?= $invData['contact_no'] ?></span></span>
                                        <br>
                                        <span><span class="fw-bold"><?= $invData['cashier'] ?> Inv.</span> <span
                                                class="fw-bolder" style="font-size: 10px;"
                                                id="invoiceNumber"><?= $invoiceNumber ?></span></span>
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
                            <?php
                            if (!empty($dmItems) || !empty($items)) {

                                if (!empty($dmItems)) {

                                    foreach ($dmItems as $dmItem) {

                            ?>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="product_name"><?= $dmItem['dmName'] ?></span>
                                                </div>
                                                <div class="col-4">
                                                    <span class="product_cost"><?= $dmItem['totalPrice'] ?></span>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <span class="product_qty">
                                                        1
                                                    </span>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <span class="productTotal"><?= $dmItem['totalPrice'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                }

                                if (!empty($items)) {

                                    foreach ($items as $item) {

                                    ?>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="product_name"><?= $item['invoiceItem'] ?></span>
                                                </div>
                                                <div class="col-4">
                                                    <span class="product_cost"><?= $item['invoiceItem_price'] ?></span>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <span class="product_qty"> <?= $item['invoiceItem_qty'] ?> </span>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <span class="productTotal"><?= $item['invoiceItem_total'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }

                                ?>

                                <div class="col-12">
                                    <div class="row">
                                        <!-- <div>
                                            <div class="col-12 d-flex justify-content-start pt-2" style="border-top: #0e0e0e 0.2rem solid;">
                                                <span class="productsAllTotal">Sub total : <?= $subTotal ?></span>
                                            </div>
                                        </div> -->

                                        <!-- <div class="col-12 d-flex justify-content-start pt-2">
                                            <span class="productsAllTotal">Discount %: <?= $discountPercentage ?></span>
                                        </div> -->

                                        <!-- <div class="col-12 d-flex justify-content-start pt-2">
                                            <span class="productsAllTotal">VAS & delivery: <?= $vas_delivery ?></span>
                                        </div> -->

                                        <div class="col-12 d-flex justify-content-start pt-2"
                                            style="border-top: #0e0e0e 0.2rem solid;">
                                            <span class="productsAllTotal">Net Total : <?= $invData['total_amount'] ?></span>
                                        </div>

                                        <div class="col-12 d-flex justify-content-start pt-2">
                                            <span class="enterAmountFiled">Cash Amount :<?= $invData['paidAmount'] ?></span>
                                        </div>
                                        <div class="col-12 d-flex justify-content-start pt-2" style="border-bottom: #0e0e0e 0.1rem solid;">
                                            <span class="enterAmountFiled">Card Amount :<?= $invData['cardPaidAmount'] ?></span>
                                        </div>

                                        <div class="col-12 d-flex justify-content-start pt-2"
                                            style="border-bottom: #0e0e0e 0.1rem solid;">
                                            <span class="balance">Balance : <?= $invData['balance'] ?></span>
                                        </div>
                                    </div>
                                </div>

                            <?php

                            } else {
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center">No Data</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </div>
                        <table>
                            <tr style="font-weight: 600;">
                                <td>
                                    <div class="col-12 pt-2">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center text-center">
                                                <span id="billnotepreview" style="font-size:9px;">THIS IS A RE-PRINTED OLD
                                                    INVOICE</span>
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

<script>
    function printInvBill() {

        var printWindow = window.open("", "_blank");
        printWindow.document.write("<html><head><title>Invoice</title>");

        function loadContent() {
            printWindow.document.write("<style>");
            printWindow.document.write(
                "\
      span {\
        font-size: 10px;\
        font-weight:bold;\
      }\
      .paperSize48 {\
        background-color: whitesmoke;\
        width: 48mm;\
      }\
      .billpreviewlogo48 {\
        height: 20px;\
        width: 120px;\
        background-position: center;\
        background-repeat: no-repeat;\
        background-size: contain;\
      }\
      .address48,\
      .datetime48,\
      .agent48 {\
        font-size: small;\
        font-weight: bold;\
      }\
      .productTable48 {\
        font-size: small;\
      }\
      .paperSize58 {\
        background-color: whitesmoke;\
        width: 58mm;\
      }\
      .billpreviewlogo {\
        height: 70px;\
        width: 120px;\
        background-position: center;\
        background-repeat: no-repeat;\
        background-size: contain;\
      }\
      .address58{\
        max-width: 130px;\
      }\
      .address58,\
      .datetime58,\
      .agent58 {\
        font-size: small;\
        font-weight: bold;\
      }\
      .productTable58 {\
        font-size: small;\
      }\
      .billpreviewlogo80 {\
        height: 100px;\
        width: 160px;\
        background-position: center;\
        background-repeat: no-repeat;\
        background-size: cover;\
      }\
      .paperSize80 {\
        background-color: whitesmoke;\
        width: 80mm;\
      }\
      .contactNumber{\
        font-size:medium;\
        font-weight: bold;\
      }\
        .check-by-box {\
            border: 2px solid #000 !important;\
            width:100%;\
            padding: 5px;\
            font-family: Arial, sans-serif;\
        }\
    .check-by-box label {\
            display: block;\
            font-size: 10px !important;\
        }\
    "
            );
            printWindow.document.write("</style>");

            printWindow.document.write("</head><body>");
            printWindow.document.write(
                document.getElementById("invoice-POS").innerHTML
            );
            printWindow.document.write("</body></html>");
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function stylesheetLoaded() {
            if (++loadedStylesheets === totalStylesheets) {
                loadContent();
            }
        }

        var totalStylesheets = 1;
        var loadedStylesheets = 0;

        var bootstrapLink = printWindow.document.createElement("link");
        bootstrapLink.rel = "stylesheet";
        bootstrapLink.href =
            "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css";
        bootstrapLink.onload = stylesheetLoaded;
        printWindow.document.head.appendChild(bootstrapLink);

        if (totalStylesheets === 0) {
            loadContent();
        }

        // After printing, reload the pos.php file
        printWindow.onafterprint = function() {
            printWindow.close(); // Close the print window
            window.location.reload();
            // Reload the pos.php file in the main window
        };
    }
</script>

</html>