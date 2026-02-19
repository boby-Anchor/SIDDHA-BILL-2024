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

    <div class="content-wrapper bg-dark">
      <section class="container-fluid">
        <div class="row">
          <div class="card card-default col-md-12 bg-dark">
            <div class="card-header py-2">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="fs-17 font-weight-600 mb-0">Add Supplier</h6>
                </div>
                <div class="text-right">
                  <a href="manage-supplier.php" class="btn btn-info btn-sm mr-1"><i class="fas fa-align-justify mr-1"></i>Manage Supplier</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <form action="actions/supplier/addSupplier.php" method="POST">
                <div class="row">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Supplier Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="name" placeholder="Enter Supplier Name" name="name" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email">Supplier Email</label>
                      <input type="email" class="form-control" id="email" placeholder="Enter Supplier Email" name="email">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="address">Address</label>
                      <input type="text" class="form-control" id="address" placeholder="Enter Address" name="address">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone">Phone</label>
                      <input type="text" class="form-control" id="phone" placeholder="Enter Customer Phone" name="phone">
                    </div>
                  </div>

                </div>
                <div class="form-row">
                  <button class="btn btn-info" id="submit_button" onclick="submitSupplier()"> <i class="fa fa-save mr-2"></i> Save </button>
                </div>

              </form>
            </div>
          </div>
          <!-- ads start  -->
          <div class="d-none d-lg-block col-lg-3">
          </div>
          <!-- ads end  -->
        </div>
      </section>
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
  <!-- Page specific script -->
  <script>
    $(function() {
      $(".select2").select2();

      $(".select2bs4").select2({
        theme: "bootstrap4",
      });
    });

    function submitSupplier() {
      $("#submit_button").prop("disabled", true);
      const name = $("#name").val();
      const email = $("#email").val();
      const address = $("#address").val();
      const phone = $("#phone").val();

      try {
        $.ajax({
          url: "actions/supplier/addSupplier.php",
          type: "POST",
          data: {
            name,
            email,
            address,
            phone
          },
          success: function(response) {
            const result = JSON.parse(response);
            switch (result.status) {
              case 'success':
                SuccessMessageDisplay(result.message);
                setTimeout(() => {
                  location.reload();
                }, 5000);
                break;
              case 'session_expired':
                $("#submit_button").prop("disabled", false);
                handleExpiredSession(result.message);
                return;
                break;
              case 'error':
                $("#submit_button").prop("disabled", false);
                ErrorMessageDisplay(result.message);
                return;
                break;

              default:
                $("#submit_button").prop("disabled", false);
                ErrorMessageDisplay("An unknown error occurred.");
                return;
                break;
            };
          },
          error: function(xhr, status, error) {
            $("#submit_button").prop("disabled", false);
            ErrorMessageDisplay("Something went wrong! Check connection.");
          },
        });
      } catch (error) {
        ErrorMessageDisplay(error.message);
      }
    }
  </script>

</body>

</html>