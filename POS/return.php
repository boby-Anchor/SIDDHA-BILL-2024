<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  require_once "config/db.php";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Return report</title>

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
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header">
                  <h3 class="card-title">Return List</h3>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example1" class="table table-bordered table-hover">
                    <thead>
                      <tr class="bg-info">
                        <th>SL</th>
                        <th>Date</th>
                        <th>ID</th>
                        <th>Returned</th>
                        <th>Wastage</th>
                        <th>Amount</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                     
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

  <!-- sample modal -->
  <div class="modal" id="sampleModal" role="dialog">
    <div class="modal-dialog modal-lg d-flex justify-content-between ">
      <div class="modal-content bg-dark align-items-center">


        <div class="d-flex align-items-center justify-content-center bg-dark">
          <div class="text-center m-5 border rounded-lg border-info p-5" style="max-width: 400px;">

            <div class="mb-4">
              <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"></div>
            </div>

            <h2 class="fw-semibold text-white mb-3" style="letter-spacing: 0.5px;">
              Updates are in Progress
            </h2>

            <p class="text-secondary">
              We’re making improvements behind the scenes.<br>
              Thanks for your patience.
            </p>

          </div>
        </div>



      </div>
    </div>
  </div>

  <!-- Alert -->
  <?php include("part/alert.php"); ?>
  <!-- Alert end -->

  <!-- All JS -->
  <?php include("part/all-js.php"); ?>
  <!-- All JS end -->

  <!-- Data Table JS -->
  <?php include("part/data-table-js.php"); ?>
  <!-- Data Table JS end -->
  <!-- Page specific script -->
  <script>
    $(function() {
      $(".select2").select2();

      $(".select2bs4").select2({
        theme: "bootstrap4",
      });
    });
  </script>
</body>

<script>
  $(document).ready(function() {
    $("#sampleModal").modal("show");
  });
</script>

</html>