<?php
session_start();
$shop_id;
$user_id;

if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    if (isset($_SESSION['store_id'])) {
        $userLoginData = $_SESSION['store_id'][0];
        $shop_id = $userLoginData['shop_id'];
        $user_id = $userLoginData['id'];
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Pharmacy</title>

        <!-- Data Table CSS -->
        <?php include("part/data-table-css.php"); ?>
        <!-- Data Table CSS end -->

        <!-- All CSS -->
        <?php include("part/all-css.php"); ?>
        <!-- All CSS end -->

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
            <div class="content-wrapper">
                <!-- Main content -->
                <section class="content bg-dark">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-dark">
                                    <div class="card-header">
                                        <h3 class="card-title">Purchase Orders</h3>
                                    </div>
                                    <div class="card-body">

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="bg-info">
                                                    <th class="adThText">Order Number</th>
                                                    <th class="adThText">Order From</th>
                                                    <th class="adThText">Order To</th>
                                                    <th class="adThText">Placed By</th>
                                                    <th class="adThText">Items</th>
                                                    <th class="adThText">Order Placed Date</th>
                                                    <th class="adThText">Sub Total</th>
                                                    <th class="adThText">Discount %</th>
                                                    <th class="adThText">Net Total</th>
                                                    <th class='adThText'>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($shop_id == "1") {
                                                    $hub_order_details_result = $conn->query("SELECT 
                                                            poinvoices.invoice_id AS invoice_id,
                                                            poinvoices.shop_id AS shop_id,
                                                            users.name AS user_name,
                                                            shop1.shopName AS shop_name,
                                                            shop2.shopName AS po_shop_name,
                                                            poinvoices.created AS po_date,
                                                            Round(poinvoices.sub_total,2) AS sub_total,
                                                            poinvoices.discount_percentage AS discount,
                                                            poinvoices.net_total AS net_total,
                                                            poinvoices.transferred AS status
                                                            FROM poinvoices
                                                            INNER JOIN users ON users.id = poinvoices.user_id
                                                            INNER JOIN shop AS shop1 ON shop1.shopId = poinvoices.shop_id
                                                            INNER JOIN shop AS shop2 ON shop2.shopId = poinvoices.po_shop_id
                                                            ORDER BY po_date DESC
                                                            LIMIT 100
                                                            ");
                                                } else {
                                                    $hub_order_details_result = $conn->query("SELECT 
                                                            poinvoices.invoice_id AS invoice_id,
                                                            poinvoices.shop_id AS shop_id,
                                                            users.name AS user_name,
                                                            shop1.shopName AS shop_name,
                                                            shop2.shopName AS po_shop_name,
                                                            poinvoices.created AS po_date,
                                                            Round(poinvoices.sub_total,2) AS sub_total,
                                                            poinvoices.discount_percentage AS discount,
                                                            poinvoices.net_total AS net_total,
                                                            poinvoices.transferred AS status
                                                            FROM poinvoices
                                                            INNER JOIN users ON users.id = poinvoices.user_id
                                                            INNER JOIN shop AS shop1 ON shop1.shopId = poinvoices.shop_id
                                                            INNER JOIN shop AS shop2 ON shop2.shopId = poinvoices.po_shop_id
                                                            WHERE shop2.shopId = '$shop_id'
                                                            ORDER BY po_date DESC
                                                            LIMIT 100
                                                            ");
                                                }

                                                while ($hub_order_details_data = $hub_order_details_result->fetch_assoc()) {
                                                ?>
                                                    <tr>
                                                        <th><?= $hub_order_details_data["invoice_id"] ?></th>
                                                        <th><?= $hub_order_details_data["shop_name"] ?></th>
                                                        <th><?= $hub_order_details_data["po_shop_name"] ?></th>
                                                        <th><?= $hub_order_details_data["user_name"] ?></th>
                                                        <th>
                                                            <?php
                                                            $itemCount_result = $conn->query("SELECT COUNT(invoiceNumber) AS itemCount
                                                                    FROM poinvoiceitems WHERE invoiceNumber = '" . $hub_order_details_data['invoice_id'] . "'");

                                                            $itemCount_data = $itemCount_result->fetch_assoc();
                                                            ?>
                                                            <button class="btn btn-info text-white" type="button"
                                                                onclick='viewPoItems(
                                                                    <?= json_encode($hub_order_details_data["invoice_id"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["shop_name"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["po_shop_name"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["user_name"]) ?>,
                                                                    <?= json_encode($hub_order_details_data["po_date"]) ?>
                                                                )'>
                                                                <?= $itemCount_data['itemCount'] ?>
                                                            </button>
                                                        </th>
                                                        <th><?= $hub_order_details_data["po_date"] ?></th>
                                                        <th><?= number_format($hub_order_details_data["sub_total"], 0) ?></th>
                                                        <th><?= number_format($hub_order_details_data["discount"], 0) ?></th>
                                                        <th><?= number_format($hub_order_details_data["net_total"], 0) ?></th>
                                                        <th><?php echo ($hub_order_details_data["status"] == 1) ? "Transferred" : "No"; ?></th>
                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- PO items Modal start -->
            <div class="modal fade" id="po-items-data-modal" tabindex="-1" aria-labelledby="poItemsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content bg-dark">
                        <div class="modal-header">
                            <h5 class="modal-title" id="poItemsModalLabel">Purchase Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Order Number:</strong> <span id="po_modal_order_number"></span></p>
                                    <p><strong>Order From:</strong> <span id="po_modal_shop_name"></span></p>
                                    <p><strong>Order To:</strong> <span id="po_modal_po_shop_name"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Placed By:</strong> <span id="po_modal_user_name"></span></p>
                                    <p><strong>Order Date:</strong> <span id="po_modal_po_date"></span></p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="po_items_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Barcode</th>
                                            <th>Item Name</th>
                                            <th>Item Price</th>
                                            <th>Qty</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody id="po_items_table_body"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-warning" type="button" onclick="handlePrint()">Print</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- PO items Modal end -->

        </div>

        <!-- Alert -->
        <?php include("part/alert.php"); ?>
        <!-- Alert end -->

        <!-- All JS -->
        <?php include("part/all-js.php"); ?>
        <!-- All JS end -->

        <script>
            function viewPoItems(poNumber, shopName, poShopName, userName, poDate) {
                $.ajax({
                    url: "actions/po/getItems.php",
                    method: "POST",
                    data: {
                        poNumber: poNumber
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            const tableBody = document.querySelector("#po_items_table_body");
                            tableBody.innerHTML = "";
                            let row_id = 0;

                            response.items.forEach((item) => {
                                const newRow = document.createElement("tr");
                                newRow.innerHTML = `
                                    <td>${++row_id}</td>
                                    <td>${item.code || ""}</td>
                                    <td>${item.name || item.invoiceItem || ""}</td>
                                    <td>${item.invoiceItem_price ? Number(item.invoiceItem_price).toLocaleString() : ""}</td>
                                    <td>${item.invoiceItem_qty ? Number(item.invoiceItem_qty).toLocaleString() : ""}</td>
                                    <td>${item.invoiceItem_total ? Number(item.invoiceItem_total).toLocaleString() : ""}</td>
                                `;
                                tableBody.appendChild(newRow);
                            });

                            $("#po-items-data-modal").data("poNumber", poNumber);
                            $("#po-items-data-modal").data("shopName", shopName);
                            $("#po-items-data-modal").data("poShopName", poShopName);
                            $("#po-items-data-modal").data("userName", userName);
                            $("#po-items-data-modal").data("poDate", poDate);

                            document.getElementById("po_modal_order_number").textContent = poNumber;
                            document.getElementById("po_modal_shop_name").textContent = shopName;
                            document.getElementById("po_modal_po_shop_name").textContent = poShopName;
                            document.getElementById("po_modal_user_name").textContent = userName;
                            document.getElementById("po_modal_po_date").textContent = poDate;

                            if (typeof $("#po-items-data-modal").modal === 'function') {
                                $("#po-items-data-modal").modal("show");
                            } else if (typeof bootstrap !== 'undefined') {
                                new bootstrap.Modal(document.getElementById("po-items-data-modal")).show();
                            }
                        } else {
                            alert(response.message || "Unable to fetch PO items.");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("Could not load PO items.");
                    }
                });
            }

            function handlePrint() {
                const poNumber = $("#po-items-data-modal").data("poNumber");
                const shopName = $("#po-items-data-modal").data("shopName");
                const poShopName = $("#po-items-data-modal").data("poShopName");
                const userName = $("#po-items-data-modal").data("userName");
                const poDate = $("#po-items-data-modal").data("poDate");
                printTable(poNumber, shopName, poShopName, userName, poDate);
            }

            function printTable(orderNumber, shopName, poShopName, userName, poDate) {
                const printWindow = window.open("", "_blank");
                printWindow.document.write("<html><head><title>Print Preview</title>");
                printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
                printWindow.document.write("</head><body>");
                printWindow.document.write('<div class="container">');
                printWindow.document.write('<h2 class="text-center bg-success text-light" style="margin-top:5px;padding:3px;">PURCHASE ORDER DETAILS</h2>');
                printWindow.document.write('<div class="col-12" style="margin-top: 20px;margin-bottom: 20px;font-family: monospace;">');
                printWindow.document.write('<div class="row">');
                printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>ORDER NUMBER : ' + orderNumber + '</h5></div>');
                printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>ORDER FROM : ' + shopName + '</h5></div>');
                printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>ORDER TO : ' + poShopName + '</h5></div>');
                printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>PLACED BY : ' + userName + '</h5></div>');
                printWindow.document.write('<div class="col-12" style="text-align: start;"><h6>ORDER DATE : ' + poDate + '</h6></div>');
                printWindow.document.write('<div class="col-12" style="text-align: start;"><h6>PRINTED : ' + new Date().toLocaleString() + '</h6></div>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');
                printWindow.document.write(document.getElementById('po_items_table').outerHTML);
                printWindow.document.write('</div>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }
        </script>

    </body>

    </html>