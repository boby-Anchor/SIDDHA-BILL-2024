<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
}

// Week 1
$week1_start = "";
$week1_end = "";

// Week 2
$week2_start = "";
$week2_end = "";

// Week 3
$week3_start = "";
$week3_end = "";

// Week 4
$week4_start = "";
$week4_end = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $start_date = $_POST['start_date'];
  // $end_date = $_POST['end_date'];
  // $po_shop = $_POST['po_shop'];

  $week1_start = date("Y-m-d 00:00:00", strtotime($start_date));
  $week1_end = date("Y-m-d 23:59:59", strtotime("+6 days", strtotime($start_date)));

  $week2_start = date("Y-m-d 00:00:00", strtotime("+7 days", strtotime($start_date)));
  $week2_end = date("Y-m-d 23:59:59", strtotime("+13 days", strtotime($start_date)));

  $week3_start = date("Y-m-d 00:00:00", strtotime("+14 days", strtotime($start_date)));
  $week3_end = date("Y-m-d 23:59:59", strtotime("+20 days", strtotime($start_date)));

  $week4_start = date("Y-m-d 00:00:00", strtotime("+21 days", strtotime($start_date)));
  $week4_end = date("Y-m-d 23:59:59", strtotime("+27 days", strtotime($start_date)));

  $sql = $conn->query("SELECT ii.barcode, ii.invoiceItem AS item_name, ii.invoiceItem_price AS item_price,
  SUM(CASE WHEN i.created BETWEEN '$week1_start' AND '$week1_end' THEN ii.invoiceItem_qty ELSE 0 END) AS week_1_total_quantity,
  SUM(CASE WHEN i.created BETWEEN '$week2_start' AND '$week2_end' THEN ii.invoiceItem_qty ELSE 0 END) AS week_2_total_quantity,
  SUM(CASE WHEN i.created BETWEEN '$week3_start' AND '$week3_end' THEN ii.invoiceItem_qty ELSE 0 END) AS week_3_total_quantity,
  SUM(CASE WHEN i.created BETWEEN '$week4_start' AND '$week4_end' THEN ii.invoiceItem_qty ELSE 0 END) AS week_4_total_quantity
  FROM invoices i
  JOIN invoiceitems ii ON i.invoice_id = ii.invoiceNumber
  WHERE ii.barcode IS NOT NULL AND ii.barcode != '' AND ii.invoiceItem_price > 0
  AND i.created BETWEEN '$week1_start' AND '$week4_end'
  GROUP BY ii.barcode, ii.invoiceItem, ii.invoiceItem_price
  ");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>
    Weekly Sale Compare
    <?php
    if (isset($_POST["start_date"])) {
      echo ("Between " . date("Y M d", strtotime($week1_start)) . " And " . date("Y M d", strtotime($week4_end)));
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
                  Weekly Sale Compare
                  <?php
                  if (isset($_POST["start_date"])) {
                    echo ("Between " . date("Y M d", strtotime($week1_start)) . " And " . date("Y M d", strtotime($week4_end)));
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

                    <!-- <div class="col-auto">
                      <label for="end_date" class="col-form-label">End Date:</label>
                    </div>
                    <div class="col-auto">
                      <input type="date" id="end_date" name="end_date" class="form-control"
                        value="<?php // isset($_POST['end_date']) ? $_POST['end_date'] : ''; 
                                ?>" required>
                    </div> -->

                    <!-- <div class="col-auto">
                      <label for="end_date" class="col-form-label">Shop:</label>
                    </div>
                    <div class="col-auto">
                      <select name="po_shop" id="po_shop" class="form-control" required>
                        <option value="" disabled selected hidden>Select Shop</option>
                        <?php
                        // $shops_rs = $conn->query("SELECT shop.shopId, shop.shopName FROM shop");
                        // while ($shops_row = $shops_rs->fetch_assoc()) {
                        ?>
                          <option value="<?php // $shops_row['shopId'] 
                                          ?>">
                            <?php //$shops_row['shopName'] 
                            ?>
                          </option>
                        <?php
                        // }
                        ?>
                      </select>
                    </div> -->
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
                  <th>Price</th>
                  <th>Week 1 Sale <?= isset($_POST['start_date']) ? date("Y M d", strtotime($week1_start)) . " - " . date("Y M d", strtotime($week1_end)) : '' ?></th>
                  <th>Week 2 Sale <?= isset($_POST['start_date']) ? date("Y M d", strtotime($week2_start)) . " - " . date("Y M d", strtotime($week2_end)) : ''  ?></th>
                  <th>Week 3 Sale <?= isset($_POST['start_date']) ? date("Y M d", strtotime($week3_start)) . " - " . date("Y M d", strtotime($week3_end)) : '' ?></th>
                  <th>Week 4 Sale <?= isset($_POST['start_date']) ? date("Y M d", strtotime($week4_start)) . " - " . date("Y M d", strtotime($week4_end)) : '' ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (isset($_POST['start_date'])) {
                  if (isset($sql)) {
                    while ($row = mysqli_fetch_assoc($sql)) {
                      $barcode = $row['barcode'];

                      $p_data_sql = $conn->query("SELECT mu.unit AS unit, ucv.ucv_name AS volume, pb.name AS brand
                      from p_medicine pm
                      JOIN p_brand pb ON pm.brand = pb.id
                      JOIN unit_category_variation ucv ON ucv.ucv_id = pm.unit_variation
                      JOIN medicine_unit mu ON mu.id = pm.medicine_unit_id
                      WHERE pm.code = '$barcode'
                      ");
                      $p_data_row = mysqli_fetch_assoc($p_data_sql);
                      $volume = $p_data_row['volume'] ?? 0;
                      $unit = $p_data_row["unit"] ?? '';
                      $brand = $p_data_row["brand"] ?? '';
                ?>
                      <tr>
                        <td> <?= $barcode; ?></td>
                        <td> <?= $row['item_name']; ?></td>
                        <td> <?= $brand; ?></td>
                        <td> <?= $volume . $unit ?></td>
                        <td> <?= $row['item_price']; ?></td>
                        <td> <?= $row['week_1_total_quantity']; ?></td>
                        <td> <?= $row['week_2_total_quantity']; ?></td>
                        <td> <?= $row['week_3_total_quantity']; ?></td>
                        <td> <?= $row['week_4_total_quantity']; ?></td>
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