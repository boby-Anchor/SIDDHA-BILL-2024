<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    $userLoginData = $_SESSION['store_id'][0];
    $user_shop_id = $userLoginData['shop_id'] ?? null;
}

$today = date("Y-m-d");
$start_date = $_POST['start_date'] ?? $today;
$start_datetime = "$start_date 00:00:00";

$end_date   = $_POST['end_date'] ?? $today;
$end_datetime   = "$end_date 23:59:59";

$shop_id   = $_POST['shop_id'] ?? $user_shop_id;

$bill_type_id   = $_POST['selectBillType'] ?? 0;

try {
    $sql = "SELECT invoices.*,
           users.name AS cashier,
           COUNT(DISTINCT invoiceitems.invoiceItemId) AS itemCount,
           COUNT(DISTINCT dm_items.id) AS dmCount,
           bill_type.bill_type_name
    FROM invoices
    INNER JOIN users
        ON invoices.user_id = users.id
    LEFT JOIN invoiceitems
        ON invoiceitems.invoiceNumber = invoices.invoice_id
    LEFT JOIN dm_items
        ON dm_items.invoice_id = invoices.invoice_id
    INNER JOIN bill_type
        ON bill_type.bill_type_id = invoices.bill_type_id
    WHERE invoices.created BETWEEN '$start_datetime' AND '$end_datetime'
    ";

    if ($shop_id != 1) {
        $sql .= " AND invoices.shop_id = '$shop_id'";
    }

    if ($bill_type_id != 0) {
        $sql .= " AND invoices.bill_type_id = '$bill_type_id'";
    }
    $sql .= " GROUP BY invoices.invoice_id";

    $result = $conn->query($sql);
    if (!$result) {
        die($conn->error);
    }
    $invoices = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        Invoices Between <?= $start_date ?> and <?= $end_date ?>
    </title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper bg-dark">
            <!-- Main content Start-->
            <section class="content">
                <div class="row">
                    <div class="card bg-dark col-12">
                        <div class="card-header">
                            <h1 class="border-bottom mb-3">
                                Invoices Between <?= $start_date ?> And <?= $end_date ?>
                            </h1>
                            <!-- Form start -->
                            <form method="POST" id="filterForm">
                                <div class="row g-3 accent-cyan align-items-center px-3">
                                    <div class="col-auto">
                                        <label for="start_date" class="col-form-label">Start Date:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" id="start_date" name="start_date" class="form-control"
                                            value="<?= $start_date ?>" required>
                                    </div>

                                    <div class="col-auto">
                                        <label for="end_date" class="col-form-label">End Date:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" id="end_date" name="end_date" class="form-control"
                                            value="<?= $end_date ?>" required>
                                    </div>

                                    <?php
                                    if ($user_shop_id == 1) {
                                    ?>
                                        <div class="col-auto">
                                            <label for="end_date" class="col-form-label">Shop:</label>
                                        </div>

                                        <div class="col-auto">
                                            <select name="shop_id" id="shop_id" class="form-control" value="" required>
                                                <option value="0" disabled selected hidden>Select Shop</option>
                                                <?php
                                                $shops_rs = $conn->query("SELECT shop.shopId, shop.shopName FROM shop");
                                                while ($shops_row = $shops_rs->fetch_assoc()) {
                                                ?>
                                                    <option value="<?= $shops_row['shopId'] ?>">
                                                        <?= $shops_row['shopName'] ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="col-auto">
                                        <label for="end_date" class="col-form-label">Bill Type:</label>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-control" name="selectBillType" id="selectBillType" value="<?= $bill_type_id ?>">
                                            <option value="0"> All </option>
                                            <?php
                                            $bill_type_rs = $conn->query("SELECT * FROM bill_type");
                                            while ($bill_type_row = $bill_type_rs->fetch_assoc()) {
                                            ?>
                                                <option value="<?= $bill_type_row['bill_type_id'] ?>">
                                                    <?= $bill_type_row['bill_type_name'] ?>
                                                </option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <button type="submit" class="btn btn-outline-success">Filter</button>
                                    </div>
                                </div>
                            </form>
                            <!-- Form end -->
                        </div>
                    </div>
                    <!-- Card end -->

                    <!-- Data table start -->
                    <div class="card-body overflow-auto">
                        <table id="invoicesTable" class="table table-bordered table-dark table-hover">
                            <thead>
                                <tr class="bg-info">
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Doctor</th>
                                    <th>Bill Type</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Discount %</th>
                                    <th>Cash Paid</th>
                                    <th>Card Paid</th>
                                    <th>Balance</th>
                                    <th>Cashier</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($invoices)) {
                                    foreach ($invoices as $invoice) {
                                ?>
                                        <tr>
                                            <td>
                                                <span class="inv_number"><?= $invoice['invoice_id']; ?></span>
                                                <br>
                                                <span class="invoiceDate "> <?= $invoice['created']; ?></span>
                                            </td>
                                            <td>
                                                <span class="p_name"> <?= $invoice['p_name']; ?></span>
                                                <br>
                                                <span class="reg"> <?= $invoice['reg']; ?></span>
                                            </td>
                                            <td>
                                                <span class="doctor_name"> <?= $invoice['d_name']; ?></span>
                                            </td>
                                            <td> <?= $invoice['bill_type_name']; ?></td>
                                            <td>
                                                <button class="btn fa fa-eye badge badge-info p-2 text-white" type="button"
                                                    onclick='viewInvoiceItems(<?= json_encode($invoice["invoice_id"])  ?>,this)'>
                                                    <?= $invoice['dmCount'] + $invoice['itemCount'] ?>
                                                </button>
                                            </td>
                                            <td> <?= $invoice['total_amount']; ?></td>
                                            <td> <?= $invoice['discount_percentage']; ?></td>
                                            <td> <?= $invoice['paidAmount']; ?></td>
                                            <td> <?= $invoice['cardPaidAmount']; ?></td>
                                            <td> <?= $invoice['balance']; ?></td>
                                            <td class="cashier_name"> <?= $invoice['cashier']; ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Data table end -->
                </div>
            </section>
            <!-- Main content End -->
        </div>
        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->

        <!-- Alert -->
        <?php include("part/alert.php"); ?>
        <!-- Alert end -->
    </div>

    <!-- Invoice items Modal start -->
    <div class="modal fade" id="invoice-items-data-modal" tabindex="-1" aria-labelledby="invoiceItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceItemsModalLabel">Invoice Details</h5>
                    <button type="button" class="btn-close btn btn-secondary" data-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Invoice Number:</strong> <span id="invoice_modal_order_number"></span></p>
                            <p><strong>Registration Number:</strong> <span id="invoice_modal_reg"></span></p>
                            <p><strong>Patient Name:</strong> <span id="invoice_modal_patient_name"></span></p>

                        </div>
                        <div class="col-md-6">
                            <p><strong>Invoice Date:</strong> <span id="invoice_modal_invoice_date"></span></p>
                            <p><strong>Doctor Name:</strong> <span id="invoice_modal_doctor_name"></span></p>
                            <p><strong>Cashier:</strong> <span id="invoice_modal_cashier_name"></span></p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="doctor_medicine_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Item Price</th>
                                </tr>
                            </thead>
                            <tbody id="doctor_medicine_table_body"> </tbody>
                        </table>
                        <table class="table table-bordered" id="invoice_items_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Barcode</th>
                                    <th>Item Name</th>
                                    <th>Volume</th>
                                    <th>SKU</th>
                                    <th>Brand</th>
                                    <th>Item Price</th>
                                    <th>Qty</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody id="invoice_items_table_body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button class="btn btn-warning" type="button" onclick="handlePrint()"><i class="nav-icon fas fa-print"></i> Print</button>
                    <button class="btn btn-primary" type="button" onclick="handleBillPrint()"><i class="nav-icon fas fa-receipt"></i> Print Bill</button> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- PO items Modal end -->

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

    <!-- Page specific script -->
    <script>
        function viewInvoiceItems(invoiceNumber, btn) {
            InfoMessageDisplay("Fetching data.");
            $.ajax({
                url: "actions/invoices/getItems.php",
                method: "POST",
                data: {
                    invoiceNumber: invoiceNumber
                },
                dataType: "json",
                success: function(response) {
                    switch (response.status) {
                        case "success":
                            setDataToTable(btn, response);
                            break;

                        case "sessionExpired":
                            handleExpiredSession(response.message);
                            break;

                        default:
                            ErrorMessageDisplay(response.message);
                            break;
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Could not load invoice items.");
                }
            });
        }

        function setDataToTable(btn, response) {
            const dmTableBody = document.querySelector("#doctor_medicine_table_body");
            let dm_row_id = 0;
            dmTableBody.innerHTML = "";
            if (response.dmItems?.length) {
                response.dmItems.forEach((dmItem) => {
                    const newDmRow = document.createElement("tr");
                    newDmRow.innerHTML = `
                                <td>${++dm_row_id}</td>
                                <td>${dmItem.dmName}</td>
                                <td>${dmItem.itemPrice}</td>
                                <td>${dmItem.totalPrice}</td>
                            `;
                    dmTableBody.appendChild(newDmRow);
                });
            } else {
                dmTableBody.innerHTML = "<tr><td colspan='4' class='text-center'>No Doctor Medicine Data</td></tr>";
            }

            const tableBody = document.querySelector("#invoice_items_table_body");
            let row_id = 0;
            tableBody.innerHTML = "";
            if (response.items?.length) {
                response.items.forEach((item) => {
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                                <td>${++row_id}</td>
                                <td>${item.code || ""}</td>
                                <td>${item.name || item.invoiceItem || ""}</td>
                                <td class="text-center">${item.ucv_name || ""}${item.unit || ""}</td>
                                <td>${item.sku || ""}</td>
                                <td>${item.brand_name || ""}</td>
                                <td>${item.invoiceItem_price ? Number(item.invoiceItem_price).toLocaleString() : ""}</td>
                                <td>${item.invoiceItem_qty ? Number(item.invoiceItem_qty).toLocaleString() : ""}</td>
                                <td>${item.invoiceItem_total ? Number(item.invoiceItem_total).toLocaleString() : ""}</td>
                            `;
                    tableBody.appendChild(newRow);
                });
            } else {
                tableBody.innerHTML = "<tr><td colspan='9' class='text-center'>No Invoice Items Data</td></tr>";
            }

            const $tr = $(btn).closest("tr");

            const invoiceNumber = $tr.find(".inv_number").text().trim();
            const reg = $tr.find(".reg").text().trim();
            const doctorName = $tr.find(".doctor_name").text().trim();
            const cashierName = $tr.find(".cashier_name").text().trim();
            const patientName = $tr.find(".p_name").text().trim();
            const invoiceDate = $tr.find(".invoiceDate ").text().trim();

            document.getElementById("invoice_modal_reg").textContent = reg;
            document.getElementById("invoice_modal_order_number").textContent = invoiceNumber;
            document.getElementById("invoice_modal_patient_name").textContent = patientName;
            document.getElementById("invoice_modal_doctor_name").textContent = doctorName;
            document.getElementById("invoice_modal_cashier_name").textContent = cashierName;
            document.getElementById("invoice_modal_invoice_date").textContent = invoiceDate;

            $("#invoice-items-data-modal").modal("show");
        }

        $(function() {
            $("#invoicesTable")
                .DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    // aaSorting: [],
                    order: [
                        [0, 'asc']
                    ],
                    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                })
                .buttons()
                .container()
                .appendTo("#invoicesTable_wrapper .col-md-6:eq(0)");
        });
    </script>

</body>

</html>