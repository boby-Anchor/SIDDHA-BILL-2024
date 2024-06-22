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
              <h1>Low Stock List</h1>
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
                  <lable class="note-cla">Minimum QTY is 12</lable>
                  <table id="example11" class="table table-bordered table-hover">
                    <thead>
                      <tr class="bg-info">
                        <!-- <th>#</th> -->
                        <th>Image</th>
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
                      // Check if the session variable is set
                      if (isset($_SESSION['store_id'])) {
                        // Retrieve session data
                        $userLoginData = $_SESSION['store_id'];

                        // Loop through each user data
                        foreach ($userLoginData as $userData) {
                          $shop_id = $userData['shop_id'];
                          $n = 0;

                          // Perform SQL query
                          $sql = "SELECT 
                          p_medicine.name AS pname,
                          p_medicine.img,
                          p_brand.name AS brandN,
                          p_medicine_category.name AS category,
                          stock2.stock_item_cost AS cost,
                          stock2.item_s_price AS price,
                          stock2.stock_item_qty AS availableStock
                      FROM 
                          stock2
                      INNER JOIN 
                          p_medicine ON p_medicine.code = stock2.stock_id
                      INNER JOIN 
                          p_medicine_category ON p_medicine_category.id = p_medicine.category
                      INNER JOIN 
                          p_brand ON p_brand.id = p_medicine.brand
                      WHERE 
                          stock2.stock_shop_id = '$shop_id' AND stock2.stock_item_qty <= 50000;
                      ";

                          $result = $conn->query($sql); // Execute the query

                          // Check if the query returned any results
                          if ($result->num_rows > 0) {
                            // Loop through the results
                            while ($row = $result->fetch_assoc()) {
                      ?>
                              <tr>
                                <td style="padding:5px" class="text-center">
                                  <img src="dist/img/product/<?php echo $row['img']; ?>" width="50" alt="Image">
                                </td>
                                <td><?php echo $row['pname']; ?></td>
                                <td><?php echo $row['brandN']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['cost']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><strong><?php echo $row['availableStock']; ?></strong></td>
                              </tr>
                      <?php
                            }
                          } else {
                            // No results found
                            echo "No data found.";
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