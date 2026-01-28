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
  <title>Cut off Stock Migration</title>

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
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header">
                  <h3 class="card-title">Stock Migration Batches</h3>
                </div>
                <div class="card-body overflow-auto">
                  <table id="stockTable" class="table table-bordered">
                    <thead>

                      <tr class="bg-info">
                        <th>#</th>
                        <th>Sent At</th>
                        <th>Barcode</th>
                        <th>Product</th>
                        <th>Volume</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Receiving Qty</th>
                        <!-- <th>Issues</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['store_id'])) {
                        $userLoginData = $_SESSION['store_id'][0];

                        $shop_id = $userLoginData['shop_id'];
                        $row_count = 0;

                        $sql = $conn->query("SELECT stock3_transfers.barcode AS barcode,
                          stock3_transfers.qty,
                          stock3_transfers.created,
                          stock3_transfers.price,
                          stock3_transfers.notes,
                          p_medicine.name AS p_name,
                          p_brand.name AS bName,
                          p_medicine_category.name AS p_category,
                          medicine_unit.unit AS unit , unit_category_variation.ucv_name
                          FROM stock3_transfers
                          INNER JOIN p_medicine ON p_medicine.code = stock3_transfers.barcode
                          INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
                          INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                          INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                          INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                          ORDER BY  stock3_transfers.created DESC");

                        while ($row = mysqli_fetch_assoc($sql)) {
                          $row_count++
                      ?>
                          <tr>
                            <td> <?= $row_count; ?> </td>
                            <td> <?= $row['created']; ?> </td>
                            <td> <?= $row['barcode']; ?> </td>
                            <td> <?= $row['p_name']; ?> </td>
                            <td> <?= $row['ucv_name'] ?><?= $row['unit']; ?></td>
                            <td> <?= $row['bName']; ?></td>
                            <td> <?= $row['p_category']; ?></td>
                            <td> <?= $row['price']; ?></td>
                            <td> <?= $row['qty']; ?></td>
                            <!-- <td>
                              <div class="row">
                                <div class="col-8">
                                  <input type="number" class="col-12 form-control transferring_qty" value="<?php // $row['notes'] ?>" name="transferring_qty" min="0">
                                </div>
                                <div class="col-4">
                                  <button class="btn btn-success col-12" onclick="submitIssue(this)">Save</button>
                                </div>
                              </div>
                            </td> -->
                          </tr>
                      <?php }
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
    function submitIssue(notes, btn) {
      $(btn).prop("disabled", true);

      const input = btn.closest('td').querySelector('input');
      const transferringQty = Number(input.value) || 0;

      const row = btn.closest('tr');
      const barcode = row.querySelector('td:nth-child(1)').innerText.trim();

      console.log(stockId, transferredQty, transferringQty, barcode);

      const total_qty = transferredQty + transferringQty;

      $.ajax({
        url: "actions/stock_cutoff/updateTransferringQty.php",
        method: "POST",
        data: {
          stockId,
          barcode,
          transferringQty,
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
            [1, 'desc']
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