<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $start_date = date("Y-m-d 00:00:00", strtotime($_POST['start_date']));
  $end_date = date("Y-m-d 23:59:59", strtotime($_POST['end_date']));
  $po_shop = $_POST['po_shop'];

  $shopCondition = ($po_shop == 0) ? "" : "AND po_shop_id = '$po_shop'";

  $sql = $conn->query("SELECT
    pm.name AS item_name,
    agg.invoiceItem_price,
    agg.item_code,
    agg.total_qty,
    ucv.ucv_name AS volume,
    mu.unit AS unit,
    pb.name AS brand
FROM (
    SELECT
        poi.invoiceItem_price,
        poi.item_code,
        SUM(poi.invoiceItem_qty) AS total_qty
    FROM poinvoiceitems AS poi
    JOIN poinvoices AS po
        ON po.invoice_id = poi.invoiceNumber
    WHERE po.created BETWEEN '$start_date' AND '$end_date'
        $shopCondition
    GROUP BY
        poi.invoiceItem_price,
        poi.item_code
) AS agg
JOIN p_medicine AS pm
    ON agg.item_code = pm.code
JOIN unit_category_variation AS ucv
    ON pm.unit_variation = ucv.ucv_id
JOIN medicine_unit AS mu
    ON ucv.p_unit_id = mu.id
JOIN p_brand AS pb
    ON pm.brand = pb.id
");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>
    Qty from PO
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      echo "Between " . $_POST['start_date'] . " and " . $_POST['end_date'];
    } else {
      echo "";
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

    <div class="content-wrapper">

      <!-- Main content -->

      <section class="content bg-dark">
        <div class="row">
          <div class="col-12">
            <!-- Card start -->
            <div class="card bg-dark">
              <div class="card-header">
                <h1>
                  Total Item Qty From Po
                  <?php
                  if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    echo "Between " . $_POST['start_date'] . " and " . $_POST['end_date'];
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
                        value="<?= isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>" required>
                    </div>

                    <div class="col-auto">
                      <label for="end_date" class="col-form-label">End Date:</label>
                    </div>
                    <div class="col-auto">
                      <input type="date" id="end_date" name="end_date" class="form-control"
                        value="<?= isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>" required>
                    </div>

                    <div class="col-auto">
                      <label for="end_date" class="col-form-label">Shop:</label>
                    </div>
                    <div class="col-auto">
                      <select name="po_shop" id="po_shop" class="form-control" required>
                        <option value="" disabled selected hidden>Select Shop</option>
                        <option value="0">All Shops</option>
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
            <table id="stockTable" class="table table-bordered table-dark table-hover">
              <thead>
                <tr class="bg-info">
                  <th>Barcode</th>
                  <th>Product</th>
                  <th>Brand</th>
                  <th>Unit</th>
                  <th>Qty</th>
                  <th>Item Price</th>
                  <th>Total Value</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (isset($_POST['start_date'])) {
                  if (isset($sql)) {
                    while ($row = mysqli_fetch_assoc($sql)) {
                      $item_price = $row['invoiceItem_price'];
                      $total_qty = $row['total_qty'];
                      $total_price = $item_price * $total_qty;
                ?>
                      <tr>
                        <td> <?= $row['item_code']; ?></td>
                        <td> <?= $row['item_name']; ?></td>
                        <td> <?= $row['brand']; ?></td>
                        <td> <?= $row['volume']; ?> <?= $row['unit']; ?></td>
                        <td> <?= number_format($total_qty, 0)  ?> </td>
                        <td> <?= number_format($item_price, 0) ?> </td>
                        <td> <?= number_format($total_price, 0) ?> </td>
                      </tr>
                <?php
                    }
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