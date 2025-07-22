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
  <title>Pharmacy</title>

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
                  <h3 class="card-title">Products Stock</h3>
                </div>
                <div class="card-body overflow-auto">
                  <table id="stockTable" class="table table-bordered">
                    <thead>

                      <tr class="bg-info">
                        <th> </th>
                        <th>Inoice No</th>
                        <th>P. Name</th>
                        <th>Doctor</th>
                        <th>Date time</th>
                        <th>Total amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['store_id'])) {

                        $userLoginData = $_SESSION['store_id'];

                        foreach ($userLoginData as $userData) {
                          $shop_id = $userData['shop_id'];
                          $sql = $conn->query("SELECT * FROM `invoices` WHERE d_name='Dr. Devinda' AND DATE(created) BETWEEN '2024-11-01' AND '2024-11-30' ORDER BY DATE(created) ASC");
                          while ($row = mysqli_fetch_assoc($sql)) {
                            $totalRows++;
                           
                      ?>
                            <tr>
                              <td style="padding:5px" class="text-center">
                                <?= $totalRows; ?>
                              </td>
                              <td> <?= $row['invoice_id']; ?> </td>
                              <td> <?= $row['p_name']; ?> </td>
                              <td> <?= $row['d_name']; ?></td>
                              <td> <?= $row['created']; ?></td>
                              <td> <?= $row['total_amount']; ?></td>
                            </tr>
                      <?php }
                        }
                      }
                      ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="4" class="text-right"><strong>Total Rows:</strong></td>
                        <td colspan="2"><?= $totalRows; ?></td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-right"><strong>Total Cost:</strong></td>
                        <td colspan="2"><?= number_format($totalCost, 0); ?></td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-right"><strong>Total Value:</strong></td>
                        <td colspan="2"><?= number_format($totalValue, 0); ?></td>
                      </tr>
                    </tfoot>
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
            [0, 'asc']
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