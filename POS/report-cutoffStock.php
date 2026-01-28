<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
}

$totalRows = 0;
$totalValue = 0;
$totalCost = 0;
$price = 0;
$cost = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>2026/01/22 Cut off Stock</title>

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
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header">
                  <h3 class="card-title">2026/01/22 Cut off Stock</h3>
                </div>
                <div class="card-body overflow-auto">
                  <table id="stockTable" class="table table-bordered">
                    <thead>

                      <tr class="bg-info">
                        <th>#</th>
                        <th>Product</th>
                        <th>Volume</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Current Stock</th>
                        <th>Transferred Qty</th>
                        <th>New Transfer Qty</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['store_id'])) {

                        $userLoginData = $_SESSION['store_id'];

                        foreach ($userLoginData as $userData) {
                          $shop_id = $userData['shop_id'];
                          $sql = $conn->query("SELECT stock3.stock_id,p_medicine.img AS p_img,
                          p_medicine.name AS p_name,
                          p_brand.name AS bName,
                          stock3.stock_id,
                          stock3.stock_item_cost AS p_cost,
                          stock3.stock_item_code AS p_code,
                          stock3.stock_item_qty AS p_a_stock,
                          stock3.item_s_price AS p_s_price,
                          stock3.transferred_qty,
                          p_medicine_category.name AS p_category,
                          medicine_unit.unit AS unit , unit_category_variation.ucv_name
                          FROM stock3
                          INNER JOIN p_medicine ON p_medicine.code = stock3.stock_item_code
                          INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
                          INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                          INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                          INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                          WHERE stock3.stock_shop_id = '$shop_id'
                          ORDER BY p_medicine.name ASC");
                          while ($row = mysqli_fetch_assoc($sql)) {
                      ?>
                            <tr>
                              <td style="padding:5px" class="text-center">
                                <?= $row['p_code']; ?>
                              </td>
                              <td> <?= $row['p_name']; ?> </td>
                              <td> <label class="ucv_name"><?= $row['ucv_name'] ?></label> <label class="product_unit"> <?= $row['unit']; ?> </label> </td>
                              <td> <?= $row['bName']; ?></td>
                              <td> <?= $row['p_category']; ?></td>
                              <td> <label> <?= $row['p_s_price']; ?> </label> </td>
                              <td> <?= $row['p_a_stock'] > 0 ? $row['p_a_stock'] : 0; ?> </td>
                              <td> <?= $row['transferred_qty'] ?> </td>
                              <td>
                                <div class="row">
                                  <div class="col-6">
                                    <input type="number" class="col-12 form-control transferring_qty" name="transferring_qty" min="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                                  </div>
                                  <div class="col-6">
                                    <button class="btn btn-success col-12" onclick="updateTotal( <?= $row['stock_id']; ?>, <?= $row['transferred_qty']; ?>, this )">Add</button>
                                  </div>
                                </div>
                              </td>
                            </tr>
                      <?php }
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
    function updateTotal(stockId, transferredQty, btn) {
      $(btn).prop("disabled", true);

      const input = btn.closest('td').querySelector('input');
      const transferringQty = Number(input.value) || 0;
      const total_qty = transferredQty + transferringQty;

      var minimum_qty = 0;

      var product_unit = $(btn).closest("tr").find(".product_unit").text().trim();

      var ucv_name = parseFloat($(btn).closest("tr").find(".ucv_name").text().trim());

      if (product_unit === 'l') {
        minimum_qty = ucv_name * total_qty * 1000;
      }
      if (product_unit === 'kg') {
        minimum_qty = ucv_name * total_qty * 1000;
      }
      if (product_unit === 'm') {
        minimum_qty = ucv_name * total_qty * 100;
      }
      if (product_unit === 'ml') {
        console.log('in ml');
        minimum_qty = ucv_name * total_qty;
      }
      if (product_unit === 'g') {
        console.log('in grams');
        minimum_qty = ucv_name * total_qty;
      }
      if (product_unit === 'cm') {
        minimum_qty = ucv_name * total_qty;
      }
      const row = btn.closest('tr');
      const barcode = row.querySelector('td:nth-child(1)').innerText.trim();
      const price = row.querySelector('td:nth-child(6)').innerText.trim();

      console.log(stockId, barcode, transferringQty, minimum_qty, price, total_qty);

      $.ajax({
        url: "actions/stock_cutoff/updateTransferringQty.php",
        method: "POST",
        data: {
          stockId,
          barcode,
          transferringQty,
          minimum_qty,
          price,
          total_qty,
        },

        success: function(response) {
          console.log(response);

          var result = JSON.parse(response);

          switch (result.status) {
            case "success":
              SuccessMessageDisplay(result.message);
              setTimeout(() => {
                window.location.reload();
              }, 4000);
              break;

            case "sessionExpired":
              handleExpiredSession(result.message);
              break;

            case "sessionDataError":
              handleExpiredSession(result.message);
              break;

            default:
              ErrorMessageDisplay(result.message);
              break;
          }
        },
        error: function(xhr, status, error) {
          ErrorMessageDisplay("Connection error!");
          console.error(xhr.responseText);
        },
      });
    }
  </script>
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();

      //Initialize Select2 Elements
      $(".select2bs4").select2({
        theme: "bootstrap4",
      });
    });
  </script>

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