<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pharmacy</title>

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
      <section class="container-fluid">
        <div class="row">
          <div class="card card-default col-md-12 col-lg-9">
            <div class="card-header">
              <h3 class="card-title">New Unit Variation</h3>
              <div class="card-tools">
                <a href="manage-brand.php" class="btn btn-info btn-sm">Manage Variation</a>
              </div>
            </div>
            <div class="card-body">
              <form action="actions/addUnitVariation.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label for="unitvariation">Variation Name</label>
                    <input type="text" class="form-control" placeholder="Enter Variation" name="unitvariation" required>
                  </div>
                  <div class="form-group">
                    <label for="name">Select Main Unit</label>
                    <select name="main_unit" class="form-control" required>
                      <option value="0">Select main unit</option>
                      <?php
                      $medicine_unit_rs = $conn->query("SELECT * FROM medicine_unit");
                      while ($medicine_unit_row = $medicine_unit_rs->fetch_assoc()) {
                      ?>
                        <option value="<?= $medicine_unit_row['id'] ?>"><?= $medicine_unit_row['unit'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="mt-3 ml-4">
                  <button type="submit" class="btn btn-info" name="submitBrand">Save</button>
                </div>
              </form>

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

</body>

</html>