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
  <title>Add New Brand</title>

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
      <section class="container-fluid">
        <div class="row">
          <div class="card card-default col-md-12 bg-dark">
            <div class="card-header">
              <h3 class="card-title">Create New Brand</h3>
              <div class="card-tools">
                <a href="manage-brand.php" class="btn btn-info btn-sm">Manage Brands</a>
              </div>
            </div>
            <div class="card-body">
              <!-- <form id="brand-add-form" enctype="multipart/form-data"> -->
              <div class="card-body">
                <div class="form-group">
                  <label for="medicineBrand">Brand Name</label>
                  <input type="text" class="form-control bg-dark" id="medicineBrand" placeholder="Enter Brand Name" name="medicineBrand">
                </div>
                <div class="form-group">
                  <label for="detailsText">Details</label>
                  <textarea name="detailsText" id="detailsText" cols="30" rows="5" class="form-control bg-dark"></textarea>
                </div>
                <!-- <div class="form-group">
                  <label>Image</label>
                  <div class="row">
                    <div class="col-8">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="uploadfile" oninput="img_preview.src=window.URL.createObjectURL(this.files[0])">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                      </div>
                    </div>
                    <div class="col-4">
                      <img id="img_preview" class="img-thambnail" src="" alt="medicine image" height="200px" width="200px;">
                    </div>
                  </div>
                </div> -->
              </div>
              <div class="mt-3 ml-4">
                <button type="button" class="btn btn-info" id="submit_button" onclick="submitBrand()"><i class="fa fa-save mr-2"></i>Save</button>
              </div>
              <!-- </form> -->
            </div>
          </div>
          <!--<div class="d-none d-lg-block col-lg-3">-->
          <!--  <div class="card">-->
          <!--    <a href="#"> <img src="dist/img/clinic.jpg" alt="" class="img-fluid w-100"> </a>-->
          <!--  </div>-->
          <!--</div>-->
        </div>
      </section>
    </div>
    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->
  </div>

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

    function submitBrand() {
      $('#submit_button').prop('disabled', true);
      const medicineBrand = $('#medicineBrand').val().trim();
      const detailsText = $('#detailsText').val().trim();
      const fileInput = $('#customFile')[0];
      const formData = new FormData();

      formData.append('medicineBrand', medicineBrand);
      formData.append('detailsText', detailsText);
      // if (fileInput.files.length > 0) {
      //   formData.append('uploadfile', fileInput.files[0]);
      // }

      try {
        $.ajax({
          url: 'actions/brand/addMedicineBrand.php',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function(response) {
            switch (response.status) {
              case 'success':
                SuccessMessageDisplay(response.message);
                setTimeout(function() {
                  location.reload();
                }, 1500);
                break;
              case 'session_expired':
                $('#submit_button').prop('disabled', false);
                handleExpiredSession(response.message);
                break;
              case 'error':
                $('#submit_button').prop('disabled', false);
                ErrorMessageDisplay(response.message);
                break;
              default:
                $('#submit_button').prop('disabled', false);
                ErrorMessageDisplay('An unknown error occurred.');
                break;
            }
          },
          error: function(xhr, status, error) {
            $('#submit_button').prop('disabled', false);
            ErrorMessageDisplay('Something went wrong! Check connection.');
          }
        });
      } catch (error) {
        $('#submit_button').prop('disabled', false);
        ErrorMessageDisplay(error.message);
      }
    }
  </script>

</body>

</html>