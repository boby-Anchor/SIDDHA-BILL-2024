<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$start_date = '';
$end_date = '';
$shop_id = '';
$invoices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $shop_id = $_POST['shop_id'];

    $result = $conn->query("SELECT invoices.*,
        users.name AS cashier,
        COUNT(invoiceitems.invoiceNumber) AS itemCount
    FROM invoices
    INNER JOIN users
        ON invoices.user_id = users.id
    LEFT JOIN invoiceitems
        ON invoiceitems.invoiceNumber = invoices.invoice_id
    WHERE DATE(invoices.created)
        BETWEEN '$start_date' AND '$end_date'
        AND invoices.shop_id = '$shop_id'
    GROUP BY invoices.invoice_id
    ");
    $invoices = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $start_date = date("Y-m-d");
    $end_date = date("Y-m-d");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "Invoices Between " . $start_date . " and " . $end_date;
        } else {
            echo "View Invoices";
        }
        ?>
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

            <!-- Main content -->

            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Card start -->
                        <div class="card bg-dark">
                            <div class="card-header">
                                <h1>
                                    Invoices
                                    <?php
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        echo "Between " . $start_date . " and " . $end_date;
                                    }
                                    ?>
                                </h1>
                                <div class="border-top mb-3"></div>
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

                                        <div class="col-auto">
                                            <label for="end_date" class="col-form-label">Shop:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select name="shop_id" id="shop_id" class="form-control" required>
                                                <option value="" disabled selected hidden>Select Shop</option>
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
                                        <div class="ml-2">
                                            <button type="submit" class="btn btn-outline-success">Filter</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Form end -->

                            </div>
                        </div>
                        <!-- Card end -->
                    </div>

                    <!-- Data table start -->
                    <div class="card-body overflow-auto">
                        <table id="invoicesTable" class="table table-bordered table-dark table-hover">
                            <thead>
                                <tr class="bg-info">
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Doctor</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Discount Percentages</th>
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
                                            <td> <?= $invoice['invoice_id']; ?> <br> <?= $invoice['created']; ?></td>
                                            <td> <?= $invoice['p_name']; ?> <br> <?= $invoice['reg']; ?></td>
                                            <td> <?= $invoice['d_name']; ?></td>
                                            <td>
                                                <button class="btn fa fa-eye badge badge-info p-2 text-white" type="button"
                                                    onclick='viewInvoiceItems(
                                                        <?= json_encode($invoice["reg"]) ?>,
                                                        <?= json_encode($invoice["invoice_id"]) ?>,
                                                        <?= json_encode($invoice["p_name"]) ?>,
                                                        <?= json_encode($invoice["d_name"]) ?>,
                                                        <?= json_encode($invoice["cashier"]) ?>,
                                                        <?= json_encode($invoice["created"]) ?>
                                                    )'>
                                                    <?= $invoice['itemCount'] ?>
                                                </button>
                                            </td>
                                            <td> <?= $invoice['total_amount']; ?></td>
                                            <td> <?= $invoice['discount_percentage']; ?></td>
                                            <td> <?= $invoice['paidAmount']; ?></td>
                                            <td> <?= $invoice['cardPaidAmount']; ?></td>
                                            <td> <?= $invoice['balance']; ?></td>
                                            <td> <?= $invoice['cashier']; ?></td>
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
        function viewInvoiceItems(reg, invoiceNumber, patientName, doctorName, cashierName, invoiceDate) {
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
                            setDataToTable(reg, invoiceNumber, patientName, doctorName, cashierName, invoiceDate, response.items);
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

        function setDataToTable(reg, invoiceNumber, patientName, doctorName, cashierName, invoiceDate, items) {
            const tableBody = document.querySelector("#invoice_items_table_body");
            tableBody.innerHTML = "";
            let row_id = 0;

            items.forEach((item) => {
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                                <td>${++row_id}</td>
                                <td>${item.code || ""}</td>
                                <td>${item.name || item.invoiceItem || ""}</td>
                                <td class="text-center">${item.ucv_name}${item.unit}</td>
                                <td>${item.sku || ""}</td>
                                <td>${item.brand_name || ""}</td>
                                <td>${item.invoiceItem_price ? Number(item.invoiceItem_price).toLocaleString() : ""}</td>
                                <td>${item.invoiceItem_qty ? Number(item.invoiceItem_qty).toLocaleString() : ""}</td>
                                <td>${item.invoiceItem_total ? Number(item.invoiceItem_total).toLocaleString() : ""}</td>
                            `;
                tableBody.appendChild(newRow);
            });

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