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
<style>
  .note-cla {
    color: red;
    font-weight: bold;
    font-size: 16px;
  }
</style>

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

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include("part/navbar.php"); ?>
    <!-- Navbar end -->

    <!-- Sidebar -->
    <?php include("part/sidebar.php"); ?>
    <!--  Sidebar end -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>ALL Shops Low Stock List</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Stock</li>
              </ol>
            </div>
          </div>
        </div>
        <!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Products</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <lable class="note-cla">Minimum QTY is 30</lable>
                  <table id="example11" class="table table-bordered table-hover">
                    <thead>
                      <tr class="bg-info">
                        <th>Image</th>
                        <th>Product</th>
                        <th>Cost</th>
                        <th>Price</th>
                        <?php
                        // Fetch all shops
                        $shop_rs = $conn->query("SELECT `shopId`, `shopName` FROM shop");
                        while ($shop_row = $shop_rs->fetch_assoc()) {
                        ?>
                          <th><?= $shop_row['shopName'] ?></th>
                        <?php
                        }
                        ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['store_id'])) {
                        $userLoginData = $_SESSION['store_id'];

                        // Fetch data from the stock table
                        $sql = $conn->query("
            SELECT * FROM stock2
            INNER JOIN shop ON shop.shopId = stock2.stock_shop_id
            INNER JOIN p_medicine ON stock2.stock_item_id = p_medicine.code
            WHERE stock2.stock_item_qty <= 30
            ORDER BY p_medicine.name DESC");

                        // Initialize an array to store product data indexed by product ID
                        $products = array();

                        // Group products by product ID
                        while ($row = mysqli_fetch_assoc($sql)) {
                          $product_id = $row['stock_item_id'];

                          // Initialize product data if not exists
                          if (!isset($products[$product_id])) {
                            $products[$product_id] = array(
                              'name' => $row['name'],
                              'cost' => $row['stock_item_cost'],
                              'prices' => array(),
                              'qty_by_shop' => array(),
                              'img' => $row['img']
                            );
                          }

                          // Store selling prices for the product
                          $products[$product_id]['prices'][$row['stock_s_price']] = $row['stock_s_price'];

                          // Store product availability by shop
                          $products[$product_id]['qty_by_shop'][$row['stock_s_price']][$row['shopId']] = $row['stock_qty'];
                        }

                        // Loop through products and display them
                        foreach ($products as $product_id => $product) {
                          // Loop through unique prices for the product
                          foreach ($product['prices'] as $price) {
                      ?>
                            <tr>
                              <td style="padding:5px" class="text-center">
                                <img src="dist/img/product/<?php echo $product['img']; ?>" width="50" alt="Image">
                              </td>
                              <td><?php echo $product['name']; ?></td>
                              <td><?php echo $product['cost']; ?></td>
                              <td><?php echo $price; ?></td>
                              <?php
                              // Loop through all shops and display product quantity for the price
                              $shop_rs = $conn->query("SELECT `shopId` FROM shop");
                              while ($shop_row = $shop_rs->fetch_assoc()) {
                                $shop_id = $shop_row['shopId'];
                                $qty = isset($product['qty_by_shop'][$price][$shop_id]) ? $product['qty_by_shop'][$price][$shop_id] : '-';
                              ?>
                                <td><?php echo $qty; ?></td>
                              <?php
                              }
                              ?>
                            </tr>
                      <?php
                          }
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
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Footer -->
    <?php include("part/footer.php"); ?>
    <!-- Footer End -->


    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

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

      $('#example11').DataTable({
        order: [
          [6, 'asc']
        ],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
        // "footerCallback": function (row, data, start, end, display) {                
        //   //Get data here 
        //   // console.log(data);
        //   var sellAmount = 0;
        //   var purchaseAmount = 0;
        //   var expense = 0;
        //   var returned = 0;
        //   for (var i = 0; i < data.length; i++) {
        //       sellAmount += parseFloat(data[i][1]);
        //       purchaseAmount += parseFloat(data[i][2]);
        //       expense += parseFloat(data[i][3]);
        //       returned += parseFloat(data[i][4]);
        //   }
        //   // console.log(totalAmount);
        //   $("#sellAmount").text(sellAmount);
        //   $("#purchaseAmount").text(purchaseAmount);
        //   $("#expense").text(expense);
        //   $("#returned").text(returned);
        // }
      });

    });
  </script>
</body>

</html>