<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}

$invoices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $doctor = $_POST['doctorName'] ?? null;

    $result = $conn->query("SELECT
    t.item_name,
    t.barcode,
    pb.name AS brandName,
    mu.unit,
    ucv.ucv_name,
    t.unit_price,
    t.total_qty,
    (t.unit_price * t.total_qty) AS total_amount
    FROM (
    SELECT
        ii.barcode,
        ii.invoiceItem AS item_name,
        SUM(ii.invoiceItem_qty) AS total_qty,
        ii.invoiceItem_price AS unit_price
    FROM invoices i
    INNER JOIN invoiceitems ii 
        ON i.invoice_id = ii.invoiceNumber
    WHERE
        i.d_name = '$doctor'
        AND i.created BETWEEN '$start_date' AND '$end_date'
    GROUP BY
        ii.barcode,
        ii.invoiceItem
    ) t
INNER JOIN p_medicine pm 
    ON pm.code = t.barcode
INNER JOIN p_brand pb 
    ON pb.id = pm.brand
INNER JOIN medicine_unit mu 
    ON mu.id = pm.medicine_unit_id
INNER JOIN unit_category_variation ucv 
    ON ucv.ucv_id = pm.unit_variation;
    ");

    if (!$result) {
        die("Query failed: " . $conn->error);
    } else {
        $invoices = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Report | Items qty by <?php echo isset($_POST['start_date']) ? "$doctor between $start_date and $end_date" : "doctor"; ?>
    </title>

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

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

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
                    <form action="" method="post">
                        <div class="row g-2 align-items-end px-4">

                            <!-- Doctor Select -->
                            <div class="col-md-3">
                                <label for="doctorName" class="form-label">Doctor</label>
                                <select class="form-control select2" id="doctorName" name="doctorName" value="<?= $doctor; ?>" required>
                                    <option value="0" disabled selected hidden>Select a doctor</option>
                                    <?php
                                    $doctors_rs = $conn->query("SELECT * FROM doctors ORDER BY name ASC");
                                    while ($doctors_row = $doctors_rs->fetch_assoc()) {
                                    ?>
                                        <option value="<?= htmlspecialchars($doctors_row['name']) ?>">
                                            <?= htmlspecialchars($doctors_row['name']) ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Date 1 -->
                            <div class="col-md-3">
                                <label for="date1" class="form-label">From Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control bg-dark text-white" value="<?= htmlspecialchars(isset($_POST['start_date']) ? $start_date : null) ?>" required>
                            </div>

                            <!-- Date 2 -->
                            <div class="col-md-3">
                                <label for="date2" class="form-label">To Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control bg-dark text-white" value="<?= htmlspecialchars(isset($_POST['end_date']) ? $end_date : null) ?>" required>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="row p-4">
                        <div class="col-12">
                            <table id="dm_data_table" class="table table-dark table-striped table-bordered table-hover">
                                <thead class="">
                                    <th class="bg-info">Barcode</th>
                                    <th class="bg-info">Item Name</th>
                                    <th class="bg-info">Brand</th>
                                    <th class="bg-info">Volume</th>
                                    <th class="bg-info">Price</th>
                                    <th class="bg-info">Quantity</th>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($invoices)) {
                                        foreach ($invoices as $invoice):
                                    ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($invoice['barcode']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['item_name']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['brandName']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['ucv_name']);
                                                    echo htmlspecialchars($invoice['unit']); ?></td>
                                                <td class="text-center"><?php echo htmlspecialchars($invoice['unit_price']); ?>
                                                <td class="text-center"><?php echo htmlspecialchars($invoice['total_qty']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No Items Data</td>
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

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

</body>

<script>
    $(document).ready(function() {
        if (<?php echo isset($_POST['doctorName']) ? 'true' : 'false'; ?>) {

            $("#doctorName").val(<?php echo json_encode($doctor); ?>);
        }
    })

    $(function() {
        $("#dm_data_table")
            .DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                searching: true,
                order: [
                    [1, 'asc']
                ],
                buttons: ["copy",
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        orientation: "portrait", // or 'landscape'
                        pageSize: "A4",
                        exportOptions: {
                            columns: ":visible"
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 10;
                            doc.pageMargins = [20, 20, 20, 20]; // top, left, bottom, right
                            doc.styles.tableHeader.fontSize = 11;
                            doc.styles.title = {
                                fontSize: 14,
                                alignment: 'center'
                            };
                            // Optionally set width of table to 100%
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    "excel",
                    "print",
                    "colvis"
                ],
            })
            .buttons()
            .container()
            .appendTo("#dm_data_table_wrapper .col-md-6:eq(0)");
    });


    // $(function() {
    //     $("#dm_data_table")
    //         .DataTable({
    //             responsive: true,
    //             lengthChange: false,
    //             autoWidth: false,
    //             searching: false,
    //             // aaSorting: [],
    //             order: [
    //                 [1, 'asc']
    //             ],
    //             buttons: ["pdf", "print", "colvis"],
    //         })
    //         .buttons()
    //         .container()
    //         .appendTo("#dm_data_table_wrapper .col-md-6:eq(0)");
    // });
</script>

</html>