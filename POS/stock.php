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
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Price</th>
                        <th>Available Stock</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['store_id'])) {

                        $userLoginData = $_SESSION['store_id'];

                        foreach ($userLoginData as $userData) {
                          $shop_id = $userData['shop_id'];
                          $sql = $conn->query("SELECT stock2.stock_id,p_medicine.img AS p_img , p_medicine.name AS p_name , p_brand.name AS bName,
                          stock2.stock_item_cost AS p_cost , stock2.stock_item_code AS p_code , stock2.stock_item_qty AS p_a_stock ,
                          stock2.item_s_price AS p_s_price , p_medicine_category.name AS p_category
                          , medicine_unit.unit AS unit , unit_category_variation.ucv_name
                          FROM stock2
                          INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
                          INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
                          INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                          INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                          INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                          WHERE stock2.stock_shop_id = '$shop_id' ORDER BY `stock2`.`stock_id` ASC");
                          while ($row = mysqli_fetch_assoc($sql)) {

                      ?>
                            <tr>
                              <td style="padding:5px" class="text-center">
                                <img src="dist/img/product/<?php echo $row['p_img']; ?>" width="50" alt="Image">
                                <br>
                                <?php echo $row['stock_id']; ?>
                              </td>
                              <td> <?php echo $row['p_name']; ?>
                              (<?= $row['ucv_name'] ?><?php echo $row['unit']; ?>)
                              </td>
                              <td> <?php echo $row['bName']; ?></td>
                              <td> <?php echo $row['p_category']; ?></td>
                              <td> <?php echo $row['p_cost']; ?>.00</td>
                              <td class="text-center"> <label for="" class="product-selling-price"><?php echo $row['p_s_price']; ?></label> </td>
                              <td> <?php echo $row['p_a_stock']; ?> </td>
                            </tr>
                      <?php }
                        }
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
            [5, 'desc']
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