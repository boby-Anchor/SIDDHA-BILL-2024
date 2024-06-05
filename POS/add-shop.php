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
      <section class="container-fluid">
        <div class="row">
          <!-- Add Shop -->
          <div class="card card-default col-md-12 col-lg-12">
            <div class="card-header py-2">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="fs-17 font-weight-600 mb-0">Add Shop</h6>
                </div>
                <div class="text-right">
                  <a href="manage-shop.php" class="btn btn-info btn-sm mr-1"><i class="fas fa-align-justify mr-1"></i>Manage Shop</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <form action="actions/addShop.php" method="POST" enctype="multipart/form-data" id="addShopForm">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="shopname" placeholder="Enter Shop Name" name="shopname" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Email</label>
                      <input type="email" class="form-control" id="shopemail" placeholder="Enter Shop Email" name="shopemail">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Address</label>
                      <input type="text" class="form-control" id="shopaddress" placeholder="Enter Address" name="shopaddress">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Tel</label>
                      <input type="text" class="form-control" id="shopphone" placeholder="Enter Shop Phone Number" name="shopphone">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop WhatsApp</label>
                      <input type="text" class="form-control" id="shopwhatsapp" placeholder="Enter Shop WhatsApp Number" name="shopwhatsapp">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Shop Image</label>
                    <div class="row">
                      <div class="col-6">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="customFile" name="uploadfile" oninput="img_preview.src=window.URL.createObjectURL(this.files[0])">
                          <label class="custom-file-label">Choose file</label><br>
                        </div>
                      </div>
                      <div class="col-6">
                        <img id="img_preview" src="" alt="shop image" height="200px" width="200px;" >
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Add Shop Manager -->
                <div class="card-header py-3">
                  <div class="d-flex justify-content-left align-items-center">
                    <div>
                      <h6 class="fs-17 font-weight-600 mb-0">Add Shop Manager</h6>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Manager Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="managername" placeholder="Enter Shop Manager Name" name="shopmanagername" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Manager Email</label>
                      <input type="email" class="form-control" id="manageremail" placeholder="Enter Shop Manager Email" name="shopmanageremail">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Manager Address</label>
                      <input type="text" class="form-control" id="manageraddress" placeholder="Enter Manager Address" name="shopmanageraddress">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Manager Tel</label>
                      <input type="text" class="form-control" id="managerphone" placeholder="Enter Shop Manager Phone Number" name="shopmanagerphone">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Shop Manager WhatsApp</label>
                      <input type="text" class="form-control" id="managerwhatsapp" placeholder="Enter Shop Manager WhatsApp Number" name="shopmanagerwhatsapp">
                    </div>
                  </div>
                </div>
                <div class="form-row">
                  <button type="submit" name="submit" class="btn btn-info"> <i class="fa fa-save mr-2"></i> Save </button>
                </div>
              </form>
            </div>
          </div>
          <!-- ads start  -->
          <!-- <div class="d-none d-lg-block col-lg-3">
            <div class="card">
              <a href="#"> <img src="dist/img/clinic.jpg" alt="" class="img-fluid"> </a>
            </div>
          </div> -->
          <!-- ads end  -->
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
      $(".select2").select2();

      $(".select2bs4").select2({
        theme: "bootstrap4",
      });

    });
  </script>

</body>

</html>