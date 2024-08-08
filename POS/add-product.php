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
  <style>
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #138496;
      color: #fff;
    }
  </style>
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
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="container-fluid">
        <div class="row">
          <!-- medicine form start  -->
          <div class="card card-default col-md-12 col-lg-9">
            <div class="card-header py-2">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="fs-17 font-weight-600 mb-0">New Product</h4>
                </div>
                <div class="text-right">
                  <a href="manage-products.php" class="btn btn-info btn-sm mr-1">Manage Product</a>
                </div>
              </div>
            </div>

            <div class="card-body">

              <form action="actions/addProduct.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">

                  <div class="form-group ">
                    <div class="row">
                      <div class="d-flex col-md-4">
                        <label>Brand</label>
                      </div>
                      <div class="d-flex col-md-3">
                        <label>Category</label>
                      </div>
                      <div class="d-flex col-md-3">
                        <label for="name">Unit</label>
                      </div>
                    </div>
                    <div class="row">
                      <div class="d-flex col-md-4">
                        <select class="form-control select2" name="brand_product" required>
                          <option value="" selected="selected">Select Type</option>
                          <?php
                        $sql = $conn->query("SELECT * FROM `p_brand` ORDER BY name ASC"); 
                          while ($row = mysqli_fetch_assoc($sql)) {
                          ?>
                            <option class="text-capitalize" value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['name']); ?></option>
                          <?php
                          }
                          ?>
                        </select>
                        <a href="add-brand.php" class="btn btn-info"><i class="fas fa-plus"></i></a>
                      </div>
                      <div class="d-flex col-md-3">
                        <select class="form-control select2" name="category_product" required>
                          <option value="" selected="selected">Select Type</option>
                          <?php
                           $sql = $conn->query("SELECT * FROM `p_medicine_category` ORDER BY name ASC");
                          while ($row = mysqli_fetch_assoc($sql)) {
                          ?>
                            <option class="text-capitalize" value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['name']); ?></option>
                          <?php
                          }
                          ?>
                        </select>
                        <a href="add-category.php" class="btn btn-info"><i class="fas fa-plus"></i></a>
                      </div>
                      <div class="col-sm-4 col-md-2">
                        <select class="form-control select2 medicine-unit-select" name="unit" required>
                          <option value="0" selected="selected">Select Unit</option>
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

                      <div class="d-flex col-md-3">
                        <select class="form-control select2" name="unit_variation" required>
                          <option value="" selected="selected">Select Type</option>
                        </select>
                        <a href="add-unit-variation.php" class="btn btn-info"><i class="fas fa-plus"></i></a>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="d-flex col-md-6">
                        <label for="name">Product Name</label>
                      </div>
                      <div class="d-flex col-md-4">
                        <label for="name">Code</label>
                      </div>

                    </div>
                    <div class="row">
                      <div class="col-sm-12 col-md-6">
                        <input type="text" class="form-control" id="name" placeholder="Enter Product Name" name="product_name" required>
                      </div>
                      <div class="d-flex col-md-4">
                        <input type="text" class="form-control" id="code" placeholder="Enter Product Code" name="product_code" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="d-flex col-md-4">
                        <label>Product Details</label>
                      </div>
                      <div class="d-flex col-md-4">
                        <label></label>
                      </div>
                    </div>
                    <div class="row">
                      <div class="d-flex col-md-4">
                        <textarea class="form-control" rows="4" placeholder="Enter description..." name="details"></textarea>
                      </div>
                      <div class="d-flex col-md-6">
                        <div class="row">
                          <div class="col-8">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="customFile" name="uploadfile" oninput="img_preview.src=window.URL.createObjectURL(this.files[0])">
                              <label class="custom-file-label">Choose file</label><br>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-4">
                            <img id="img_preview" src="" alt="product image" height="250px" width="250px;">
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>


                  <div class="form-group pl-3">
                    <button type="submit" class="btn btn-info" name="submit">Submit</button>
                  </div>
              </form>
            </div>
          </div>

        </div>
    </div>
    </section>
    <!-- Main content end -->
  </div>
  <!-- Footer -->
  <?php include("part/footer.php"); ?>
  <!-- Footer End -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- Alert -->
  <?php include("part/alert.php"); ?>
  <!-- Alert end -->

  <!-- All JS -->
  <?php include("part/all-js.php"); ?>
  <!-- All JS end -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();

      //Initialize Select2 Elements
      $(".select2bs4").select2({
        theme: "bootstrap4",
      });

      $('.medicine-unit-select').select2({
        placeholder: "Select medicine unit"
      });
    });
  </script>

  <script>
    $(document).ready(function() {
      $('.medicine-unit-select').change(function() {
        var unitId = $(this).val();
        $.ajax({
          url: 'get_unit_category_variations.php',
          type: 'POST',
          data: {
            unitId: unitId
          },
          dataType: 'json',
          success: function(response) {
            var options = '<option value="" selected="selected">Select Type</option>';
            $.each(response, function(key, value) {
              options += '<option value="' + value.ucv_id + '">' + value.ucv_name + ' ' + value.unit + ' ' + '</option>';
            });
            $('select[name="unit_variation"]').html(options);
          },
          error: function(xhr, status, error) {
            // Handle errors
            console.error(xhr.responseText);
          }
        });
      });
    });
  </script>

</body>

</html>