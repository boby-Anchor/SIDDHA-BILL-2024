<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {

            $shop_id = $userData['shop_id'];
            $user_id = $userData['id'];
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="utf-8" />

                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <title>Refilling Batches View</title>

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
                    <div class="content-wrapper bg-dark">
                        <!-- Main content -->
                        <section class="content">
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
                                                            <th class="adThText">Batch Number</th>
                                                            <th class='adThText'>Source Items</th>
                                                            <th class='adThText'>Converted Items</th>
                                                            <th class='adThText'>Created</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $stmt = $conn->prepare("SELECT 
                                                            rb.id AS batch_id,
                                                            rb.batch_number,
                                                            rb.created AS batch_created,
                                                            rbi.id AS item_id,
                                                            rbi.barcode,
                                                            rbi.qty,
                                                            rbi.is_source,
                                                            pm.name AS medicine_name,
                                                            pb.name AS brand_name,
                                                            mu.unit AS unit_name,
                                                            ucv.ucv_name AS volume
                                                        FROM refill_batch AS rb
                                                        LEFT JOIN refill_batch_item AS rbi
                                                            ON rb.id = rbi.refill_batch_id
                                                        LEFT JOIN p_medicine pm
                                                            ON pm.code = rbi.barcode
                                                        LEFT JOIN p_brand pb
                                                            ON pb.id = pm.brand
                                                        LEFT JOIN medicine_unit mu
                                                            ON mu.id = pm.medicine_unit_id
                                                        LEFT JOIN unit_category_variation ucv
                                                            ON ucv.ucv_id = pm.unit_variation
                                                        ORDER BY rb.id DESC, rbi.is_source DESC
                                                        ");

                                                        $stmt->execute();
                                                        $result = $stmt->get_result();

                                                        $batches = [];

                                                        while ($row = $result->fetch_assoc()) {
                                                            $batchId = $row['batch_id'];

                                                            // Initialize batch if not exists
                                                            if (!isset($batches[$batchId])) {
                                                                $batches[$batchId] = [
                                                                    'batch_id' => $row['batch_id'],
                                                                    'batch_number' => $row['batch_number'],
                                                                    'batch_created' => $row['batch_created'],
                                                                    'source_items' => [],
                                                                    'refill_items' => []
                                                                ];
                                                            }

                                                            // Skip if item_id is null (batch has no items)
                                                            if ($row['item_id'] !== null) {
                                                                $item = [
                                                                    'item_id' => $row['item_id'],
                                                                    'barcode' => $row['barcode'],
                                                                    'qty' => $row['qty'],
                                                                    'name' => $row['medicine_name'],
                                                                    'brand' => $row['brand_name'],
                                                                    'volume' => $row['volume'],
                                                                    'unit' => $row['unit_name'],
                                                                ];

                                                                if ($row['is_source'] == 1) {
                                                                    $batches[$batchId]['source_items'][] = $item;
                                                                } else {
                                                                    $batches[$batchId]['refill_items'][] = $item;
                                                                }
                                                            }
                                                        }

                                                        $stmt->close();

                                                        // Convert to indexed array if needed
                                                        $batches = array_values($batches);

                                                        foreach ($batches as $batch) {
                                                        ?>
                                                            <tr>
                                                                <th><?= htmlspecialchars($batch['batch_id']) ?> </th>
                                                                <th><?= htmlspecialchars($batch['batch_number']) ?></th>
                                                                <th>
                                                                    <button class="btn dropdown-toggle badge badge-info p-2 text-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom-start">
                                                                        <?= count($batch['source_items']);  ?>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <table class="table table-hover" id="poItemsTable<?= $batch['batch_number'] ?>">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col">#</th>
                                                                                    <th scope="col">Barcode</th>
                                                                                    <th scope="col">Item Name</th>
                                                                                    <th scope="col">Volume</th>
                                                                                    <th scope="col">Brand</th>
                                                                                    <th scope="col">Qty</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                $row = 1;
                                                                                foreach ($batch['source_items'] as $item) {
                                                                                ?>
                                                                                    <tr>
                                                                                        <th scope="row"><?= $row ?></th>
                                                                                        <td><?= $item["barcode"] ?></td>
                                                                                        <td><?= $item["name"] ?></td>
                                                                                        <td><?= $item["volume"] ?> <?= $item["unit"]  ?></td>
                                                                                        <td><?= $item["brand"] ?></td>
                                                                                        <td><?= $item["qty"] ?></td>
                                                                                    </tr>

                                                                                <?php
                                                                                    $row++;
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table>
                                                                        <!-- <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?= $hub_order_details_data['invoice_id'] ?>');"> <i class="nav-icon fas fa-copy"></i> PRINT</button> -->
                                                                    </ul>
                                                                </th>
                                                                <th>
                                                                    <button class="btn dropdown-toggle badge badge-info p-2 text-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom-start">
                                                                        <?= count($batch['refill_items']); ?>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <table class="table table-hover" id="poItemsTable<?= htmlspecialchars($batch['batch_number']) ?>">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col">#</th>
                                                                                    <th scope="col">Barcode</th>
                                                                                    <th scope="col">Item Name</th>
                                                                                    <th scope="col">Volume</th>
                                                                                    <th scope="col">Brand</th>
                                                                                    <th scope="col">Qty</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                $row = 1;
                                                                                foreach ($batch['refill_items'] as $item) {
                                                                                ?>
                                                                                    <tr>
                                                                                        <th scope="row"><?= $row ?></th>
                                                                                        <td><?= $item["barcode"] ?></td>
                                                                                        <td><?= $item["name"] ?></td>
                                                                                        <td><?= $item["volume"] ?> <?= $item["unit"]  ?></td>
                                                                                        <td><?= $item["brand"] ?></td>
                                                                                        <td><?= $item["qty"] ?></td>
                                                                                    </tr>

                                                                                <?php
                                                                                    $row++;
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table>
                                                                        <!-- <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?= $hub_order_details_data['invoice_id'] ?>');"> <i class="nav-icon fas fa-copy"></i> PRINT</button> -->
                                                                    </ul>
                                                                </th>
                                                                <th><?= htmlspecialchars($batch['batch_created']) ?></th>
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
                    <!-- Footer -->
                    <?php include("part/footer.php"); ?>
                    <!-- Footer End -->
                </div>

                <!-- Alert -->
                <?php include("part/alert.php"); ?>
                <!-- Alert end -->

                <!-- All JS -->
                <?php include("part/all-js.php"); ?>
                <!-- All JS end -->

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var dropdownButtonList = [].slice.call(document.querySelectorAll('.btn.dropdown-toggle'));
                        dropdownButtonList.map(function(button) {
                            // Check if the button has already been initialized
                            if (!button.classList.contains('dropdown-initialized')) {
                                new bootstrap.Dropdown(button);
                                // Mark the button as initialized to prevent re-initialization
                                button.classList.add('dropdown-initialized');
                            }
                        });
                    });
                </script>
                <script>
                    function printTable(orderNumber) {
                        var printWindow = window.open('', '_blank');
                        printWindow.document.write('<html><head><title>Print Preview</title>');
                        // Include Bootstrap CSS
                        printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
                        printWindow.document.write('</head><body>');
                        printWindow.document.write('<div class="container">');
                        printWindow.document.write('<h2 class="text-center bg-success text-light" style="margin-top:5px;padding:3px;">ORDER DETAILS</h2>');
                        printWindow.document.write('<div class="col-12" style="margin-top: 50px;margin-bottom: 20px;font-family: monospace;">');
                        printWindow.document.write('<div class="row">');
                        printWindow.document.write('<div class="col-12" style="text-align: start;">');
                        printWindow.document.write('<h5>ORDER NUMBER : ' + orderNumber + '</h5>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('<div class="col-12" style="text-align: start;">');
                        // printWindow.document.write('<h6>ORDER DATE : <?php //date('Y-m-d', strtotime($itemCount_data['orderDate'])) 
                                                                        ?></h6>');
                        printWindow.document.write('<h6>ORDER DATE : <?= !empty($itemCount_data['orderDate']) ? date('Y-m-d', strtotime($itemCount_data['orderDate'])) : 'No date available' ?></h6>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('<div class="col-12" style="text-align: start;">');
                        printWindow.document.write('<h6>ORDER TIME : <?= (!empty($itemCount_data['orderDate'])) ? date('H:i:s', strtotime($itemCount_data['orderDate'])) : 'No date available' ?></h6>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('</div>');
                        printWindow.document.write('</div>');
                        printWindow.document.write(document.getElementById('poItemsTable' + orderNumber).outerHTML);
                        printWindow.document.write('</div>');
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.focus();
                        printWindow.print();
                    }
                </script>

            </body>

            </html>
    <?php
    }
}

    ?>