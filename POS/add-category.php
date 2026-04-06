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
  <title>New Medicine Category</title>

  <!-- All CSS -->
  <?php include("part/all-css.php"); ?>
  <!-- All CSS end -->
</head>

<body class="hold-transition sidebar-mini">
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
          <div class="card card-default col-12 bg-dark">
            <div class="card-header">
              <h3 class="card-title">New Category</h3>
              <div class="card-tools">
                <a href="manage-category.php" class="btn btn-info btn-sm">Manage Category</a>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="name">Category Name</label>
                      <select class="form-control select2 ct-select2" id="medicineCategory">
                        <option value="" selected hidden>Select Category</option>
                        <?php
                        $sql = $conn->query("SELECT * FROM `p_medicine_category` ORDER BY `name` ASC");
                        while ($row = mysqli_fetch_assoc($sql)) {
                        ?>
                          <option><?php echo ucfirst($row['name']); ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>

              </div>
              <div class="mt-3 ml-4">
                <button class="btn btn-info" id="submitCategory">Save</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
  <!-- Footer -->
  <?php include("part/footer.php"); ?>
  <!-- Footer End -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>

  <!-- Alert -->
  <?php include("part/alert.php"); ?>
  <!-- Alert end -->

  <!-- All JS -->
  <?php include("part/all-js.php"); ?>
  <!-- All JS end -->
  <!-- Page specific script -->
  <script>
    $(function() {
      $(".select2").select2({
        tags: true,
        placeholder: "Type category name..."
      });
    });

    $("#submitCategory").on('click', function() {

      let category_name = $("#medicineCategory").val();

      if (category_name != '' && category_name != null) {
        $.ajax({
          url: "actions/category/addMedicineCategory.php",
          method: "POST",
          data: {
            category_name
          },
          success: function(response) {
            const result = JSON.parse(response);

            switch (result.status) {
              case "success":
                SuccessMessageDisplay(result.message);
                setTimeout(() => {
                  location.reload();
                }, 3000);
                break;

              case "sessionExpired":
                handleExpiredSession(result.message);
                break;

              case "error":
                ErrorMessageDisplay(result.message);
                break;

              default:
                ErrorMessageDisplay("An unknown error occurred.");
                break;
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
            ErrorMessageDisplay(xhr.responseText);
          },
        });

      } else {
        ErrorMessageDisplay('Category එකේ නම කෝ..?');
      }
    });
  </script>

</body>

</html>