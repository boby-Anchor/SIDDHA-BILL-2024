<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customize Bill</title>
        <!-- All CSS -->
        <?php include("part/all-css.php"); ?>
    </head>

    <body class="hold-transition sidebar-mini layout-fixed" onload="focus_filed();">
  <div class="wrapper">

    <!-- Navbar -->
    <?php include("part/navbar.php"); ?>
    <!-- Navbar end -->

    <!-- Sidebar -->
    <?php include("part/sidebar.php"); ?>
    <!--  Sidebar end -->


    <div class="content-wrapper bg-dark">
      <div class="row w-100">

        <div class="col-12 col-md-7 bg-dark">

        </div>

        <div class="col-12 col-md-5">
        
        </div>

      </div>
    </div>

    </div>

    <!-- confirm po modal end -->

    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->
    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->
    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->
    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

    <!-- select2 input field -->


              
    <!-- ========================================== -->

    
</body>

    </html>
<?php
}
?>