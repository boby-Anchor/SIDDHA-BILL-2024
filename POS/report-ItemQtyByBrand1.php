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
$price = 0;
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
      <section class="content bg-dark">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header">
                  <h3 class="card-title">Prducts Qty By Brand</h3>
                </div>
                <div class="card-body overflow-auto">
                  <table id="stockTable" class="table table-bordered table-dark table-hover">
                    <thead>
                      <tr class="bg-info">
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Shop</th>
                        <th>Item Price</th>
                        <th>Sale Qty</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['store_id'])) {

                        $userLoginData = $_SESSION['store_id'];

                        foreach ($userLoginData as $userData) {

                          $sql = $conn->query("SELECT
                          i.name as item,
                          b.name AS brand,
                          s.shopName as shop,
                          st.item_s_price as item_price,
                          ROUND(SUM(st.stock_item_qty)) AS total_qty
                      FROM 
                          stock2 st
                      JOIN 
                      p_medicine i ON st.stock_item_code = i.code
                      JOIN 
                      p_brand b ON i.brand = b.id
                      JOIN 
                          shop s ON st.stock_shop_id = s.shopId
                      GROUP BY 
                          b.name, s.shopName
                      HAVING
                      total_qty >= 0
                      ORDER BY 
                          b.name, s.shopName ASC
                          ");
                          while ($row = mysqli_fetch_assoc($sql)) {
                      ?>
                            <tr id="">
                              <td> <?php echo $row['item']; ?></td>
                              <td> <?php echo $row['brand']; ?></td>
                              <td> <?php echo $row['shop']; ?></td>
                              <td> <?php echo $row['item_price']; ?> </td>
                              <td> <?php echo $row['total_qty']; ?> </td>
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
      $("#stockTable")
        .DataTable({
          responsive: true,
          lengthChange: false,
          autoWidth: false,
          // aaSorting: [],
          order: [
            [2, 'desc']
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