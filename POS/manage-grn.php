<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['store_id'])) {
    header("location: login.php");
    exit();
} else {
    include('config/db.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    } else {
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>GRNs Between <?= $start_date ?> And <?= $end_date ?></title>

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

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-dark">
            <!-- Main content -->
            <section class="content bg-dark">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <h3>
                                        View Goods Receipt Notes Between <?= $start_date ?> And <?= $end_date ?>
                                    </h3>
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
                                            <div class="ml-2">
                                                <button type="submit" class="btn btn-outline-success">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form end -->
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <th class="adThText">GRN Number</th>
                                                <th class="adThText">Invoice Number</th>
                                                <th class="adThText">Supplier</th>
                                                <th class="adThText">Items</th>
                                                <th class="adThText">GRN Added Date</th>
                                                <th class="adThText">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $grn_details_result = $conn->query("SELECT `grn`.*,
                                                p_supplier.name AS supplier,
                                                COUNT(grn_item.grn_number) AS item_count
                                                FROM `grn`
                                                LEFT JOIN p_supplier ON grn.supplier_id = p_supplier.id
                                                INNER JOIN grn_item ON grn.grn_number = grn_item.grn_number
                                                WHERE grn_date BETWEEN '" . date("Y-m-d 00:00:00", strtotime($start_date)) . "' AND '" . date("Y-m-d 23:59:59", strtotime($end_date)) .
                                                "' GROUP BY grn.grn_number
                                                ORDER BY grn_date DESC");

                                            if ($grn_details_result && $grn_details_result->num_rows > 0) {

                                                while ($grn_details_data = $grn_details_result->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?= $grn_details_data["grn_number"] ?></td>
                                                        <td><?= $grn_details_data["invoice_number"] ?></td>
                                                        <td><?= $grn_details_data["supplier"] ?></td>
                                                        <td>
                                                            <button class="btn fa fa-eye badge badge-info p-2 text-md"
                                                                onclick="getItems(
                                                                '<?= $grn_details_data['grn_number'] ?>',
                                                                '<?= $grn_details_data['invoice_number'] ?>',
                                                                '<?= $grn_details_data['grn_date'] ?>',
                                                                '<?= $grn_details_data['supplier'] ?>'
                                                                )">
                                                                <?= $grn_details_data["item_count"] ?>
                                                            </button>
                                                        </td>
                                                        <td><?= $grn_details_data["grn_date"] ?></td>
                                                        <td><?= number_format($grn_details_data["grn_sub_total"], 0) ?></td>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        No GRN Data Found
                                                    </td>
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
            </section>
        </div>
        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->
    </div>

    <!-- GRN items Modal start -->
    <div class="modal fade" id="grn-items-data-modal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-dark">
                <div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">GRN Items Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered border-5" id="grn_items_table">
                                    <thead>
                                        <td>#</td>
                                        <td>barcode</td>
                                        <td>Name</td>
                                        <td>Volume</td>
                                        <td>SKU</td>
                                        <td>Brand</td>
                                        <td>Qty</td>
                                        <td>Free Qty</td>
                                        <td>Price</td>
                                        <td>Total Value</td>
                                        <td>Discount</td>
                                        <td>Total Cost</td>
                                        <td>Unit Cost</td>
                                    </thead>
                                    <tbody id="grn_items_table_body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row w-100 justify-content-between">
                            <button class="btn btn-warning" onclick="handlePrint(this)">Print</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- GRN items Modal end  -->

    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

    <script>
        function getItems(grn_number, invoice_number, grn_date, supplier) {
            InfoMessageDisplay("Fetching data.")
            $.ajax({
                url: "actions/grn/getItems.php",
                method: "POST",
                data: {
                    grn_number,
                },
                dataType: 'json',

                success: function(response) {

                    switch (response.status) {
                        case "success":
                            const tableBody = document.querySelector("#grn_items_table_body");
                            tableBody.innerHTML = '';
                            var row_id = 0;

                            response.data.forEach((row) => {
                                const newRow = document.createElement("tr");

                                newRow.innerHTML = `
                                    <td>
                                        ${++row_id}
                                    </td>
                                    <td>
                                        ${row.grn_p_id}
                                    </td>
                                    <td>
                                        ${row.item_name}
                                    </td>
                                    <td>
                                        ${row.ucv_name}${row.unit}
                                    </td>
                                    <td>
                                        ${row.sku ? row.sku : ''}
                                    </td>
                                    <td>
                                        ${row.brand}
                                    </td>
                                    <td>
                                        ${row.grn_p_qty}
                                    </td>
                                    <td>
                                        ${row.p_free_qty}
                                    </td>
                                    <td>
                                        ${row.grn_p_price}
                                    </td>
                                    <td>
                                        ${row.grn_p_qty * row.grn_p_price}
                                    </td>
                                    <td>
                                        ${row.p_plus_discount}
                                    </td>
                                    <td>
                                        ${row.grn_p_cost}
                                    </td>
                                    <td>
                                        ${row.grn_u_cost}
                                    </td>
                                `;
                                tableBody.appendChild(newRow);
                            });
                            $("#grn-items-data-modal").data("grn", grn_number);
                            $("#grn-items-data-modal").data("invoice_number", invoice_number);
                            $("#grn-items-data-modal").data("grn_date", grn_date);
                            $("#grn-items-data-modal").data("supplier", supplier);
                            $("#grn-items-data-modal").modal("show");
                            break;

                        case "sessionExpired":
                            handleExpiredSession(response.message);
                            break;

                        default:
                            ErrorMessageDisplay(response.message);
                            break;
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                },
            });
        }


        function handlePrint() {
            const grn_number = $("#grn-items-data-modal").data("grn");
            const invoice_number = $("#grn-items-data-modal").data("invoice_number");
            const grn_date = $("#grn-items-data-modal").data("grn_date");
            const supplier = $("#grn-items-data-modal").data("supplier");
            printTable(grn_number, invoice_number, grn_date, supplier);
        }

        function printTable(grnNumber, invoice_number, grnDate, supplier_name) {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Preview</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="container">');
            printWindow.document.write('<h2 class="text-center" style="margin-top:5px;padding:3px;">GOODS RECEIPT NOTES</h2>');
            printWindow.document.write('<div class="col-12" style="margin-top: 50px;margin-bottom: 20px;font-family: monospace;">');
            printWindow.document.write('<div class="row">');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h5>GRN Number : ' + grnNumber + '</h5>');
            printWindow.document.write('</div>');
            if (supplier_name) {
                printWindow.document.write('<div class="col-12" style="text-align: start;">');
                printWindow.document.write('<h5>Supplier Name : ' + supplier_name + '</h5>');
                printWindow.document.write('</div>');
            }
            if (invoice_number) {
                printWindow.document.write('<div class="col-12" style="text-align: start;">');
                printWindow.document.write('<h5>Invoice Number : ' + invoice_number + '</h5>');
                printWindow.document.write('</div>');
            }
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h6>Added : ' + grnDate + '</h6>');
            printWindow.document.write('</div>');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h6>Printed : <?= date('Y-m-d H:i:s') ?></h6>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
            printWindow.document.write(document.getElementById('grn_items_table').outerHTML);
            printWindow.document.write('</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
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
</body>

</html>