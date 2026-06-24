<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
}

$today = date('Y-m-d');
$start_date = $_POST['start_date'] ?? $today;
$end_date   = $_POST['end_date'] ?? $today;

$start_datetime = "$start_date 00:00:00";
$end_datetime   = "$end_date 23:59:59";

$sql = $conn->query("SELECT
    s.name AS supplier,
    pm.code AS code,
    pm.name AS name,
    SUM(gi.grn_p_qty) AS total_quantity,
    gi.grn_p_price AS item_price,
    gi.grn_item_cost AS item_cost,
    gi.p_plus_discount AS discount,
    pb.name AS brand,
    ucv.ucv_name AS volume,
    mu.unit AS unit
FROM grn g
JOIN grn_item gi ON g.grn_number = gi.grn_number
LEFT JOIN p_supplier s ON g.supplier_id = s.id
LEFT JOIN p_medicine pm ON gi.grn_p_id = pm.code
LEFT JOIN p_brand pb ON pm.brand = pb.id
LEFT JOIN unit_category_variation ucv ON ucv.ucv_id = pm.unit_variation
LEFT JOIN medicine_unit mu ON mu.id = pm.medicine_unit_id
WHERE g.grn_date BETWEEN '$start_datetime' AND '$end_datetime'
  AND g.grn_shop_id = '1'
GROUP BY
    g.supplier_id,
    gi.grn_p_id");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>
    Qty from GRNs Between <?= $start_date ?> And <?= $end_date ?>
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
      <!-- Main content Start -->
      <section class="content">
        <div class="card bg-dark">
          <!-- Card Header start -->
          <div class="card-header">
            <h1 class="border-bottom mb-3">
              Total Item Qty From GRNs Between <?= $start_date ?> And <?= $end_date ?>
            </h1>
            <!-- Form start -->
            <form method="POST">
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
          <!-- Card Header end -->
        </div>

        <!-- Data table start -->
        <div class="card-body overflow-auto">
          <table id="stockTable" class="table table-bordered table-dark table-hover">
            <thead>
              <tr class="bg-info">
                <th>Barcode</th>
                <th>Product</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Item Price</th>
                <th>Discount %</th>
                <th>Item Cost</th>
                <th>Total Value</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (isset($sql)) {
                while ($row = mysqli_fetch_assoc($sql)) {
                  $item_price = $row['item_price'];
                  $total_qty = $row['total_quantity'];
                  $total_price = $item_price * $total_qty;
              ?>
                  <tr>
                    <td> <?= $row['code']; ?></td>
                    <td> <?= $row['name']; ?></td>
                    <td> <?= $row['brand']; ?></td>
                    <td> <?= $row['supplier']; ?></td>
                    <td><?= $row['volume'] . $row['unit']; ?> </td>
                    <td> <?= number_format($total_qty, 0)  ?> </td>
                    <td> <?= number_format($item_price, 0) ?> </td>
                    <td> <?= $row['discount']; ?></td>
                    <td> <?= $row['item_cost']; ?></td>
                    <td> <?= number_format($total_price, 0) ?> </td>
                  </tr>
              <?php
                }
              }
              ?>
            </tbody>
          </table>
        </div>
        <!-- Data table end -->
      </section>
      <!-- Main content end -->
    </div>
    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->
  </div>

  <!-- All JS -->
  <?php include("part/all-js.php"); ?>
  <!-- All JS end -->

  <!-- Data Table JS -->
  <?php include("part/data-table-js.php"); ?>
  <!-- Data Table JS end -->

  <!-- Page specific script -->
  <script>
    $(function() {
      $("#stockTable")
        .DataTable({
          responsive: true,
          lengthChange: false,
          autoWidth: false,
          // aaSorting: [],
          order: [
            [1, 'asc']
          ],
          buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
        })
        .buttons()
        .container()
        .appendTo("#stockTable_wrapper .col-md-6:eq(0)");
    });
  </script>

</body>

</html>