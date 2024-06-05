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

  <style>
    .totalAmount {
      font-size: 50px;
      font-weight: bold;
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

    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>All Sops Today's Report</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="card card-body bg-success">
                        <h2 class="text-white text-uppercase">Sell Amount</h2>
                        <?php $currentDate = date('Y-m-d'); ?>
                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount FROM invoices WHERE DATE(`created`) = '$currentDate'")); ?>
                        <p class="totalAmount"><?php echo $result['total_amount']; ?> LKR</p>
                      </div>
                    </div>

                    <?php
                    $invoiceItemQty_rs = $conn->query("SELECT * FROM invoiceitems
                  INNER JOIN p_medicine ON  invoiceitems.invoiceItem = p_medicine.name 
                  WHERE DATE(`invoiceDate`) = '$currentDate'");

                    $total_profit = 0; // Initialize total profit
                    $total_cost = 0;

                    while ($invoiceItemQty_data = $invoiceItemQty_rs->fetch_assoc()) {
                      $stock_price_rs = $conn->query("SELECT * FROM stock2 WHERE stock2.stock_item_id = '" . $invoiceItemQty_data['code'] . "'");
                      $stock_price_data = $stock_price_rs->fetch_assoc();

                      if ($stock_price_data !== null) {
                        $stock_cost = $stock_price_data['stock_item_cost'];
                        $total_cost += $stock_cost * $invoiceItemQty_data['invoiceItem_qty']; // Add today's selling cost to total cost
                    }

                      // Check if $stock_price_data is not null before accessing its elements
                      if ($stock_price_data !== null) {
                        $stock_profit = $stock_price_data['stock_s_price'] - $stock_price_data['stock_item_cost'];
                        $today_profit = $stock_profit * $invoiceItemQty_data['invoiceItem_qty'];
                        $total_profit += $today_profit; // Add today's profit to total profit

                   
                      } else {
                        // Handle the case where $stock_price_data is null (optional)
                        // For example, you can display an error message or skip this item
                        echo "Error: Stock data not found for item with code " . $invoiceItemQty_data['code'];
                      }
                    }


                    ?>
                    <div class="col-md-3">
                      <div class="card card-body bg-danger">
                        <h2 class="text-white text-uppercase">Purchase Cost</h2>
                        <?php $result = mysqli_fetch_assoc($conn->query("SELECT SUM(grn_sub_total) AS grn_sub_total FROM grn WHERE DATE(`grn_date`) = '$currentDate'")); ?>
                        <p class="totalAmount"><?php echo $total_cost ?> LKR</p>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="card card-body bg-primary">
                        <h2 class="text-white text-uppercase">Total Profit Today</h2>
                        <p class="totalAmount"><?php echo $total_profit ?> LKR</p>
                      </div>
                    </div>
                   
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Top Sell Product</h4>
                  </div>
                  <div class="card-body">
                    <table id="mytable" class="table table-bordered table-hover">
                      <thead>
                        <tr class="bg-info">
                          <th>Total Sale</th>
                          <th>Product Name</th>
                          <th>Quantity</th>
                          <th>Price</th>
                          <th>Sale Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $sql = $conn->query("SELECT invoiceItem, invoiceItem_price, SUM(invoiceitems.invoiceItem_total) AS totalSale, SUM(invoiceItem_qty) AS invoiceItemQty 
                        FROM invoiceitems 
                        GROUP BY invoiceItem 
                        ORDER BY invoiceItemQty DESC;
                        ");
                        while ($row = mysqli_fetch_assoc($sql)) {
                        ?>
                          <tr>
                            <td><?php echo (!empty($sellQty['qty'])) ? $sellQty['qty'] : 0; ?></td>
                            <td><?php echo $row['invoiceItem']; ?></td>
                            <td><?php echo $row['invoiceItemQty']; ?></td>
                            <td><?php echo $row['invoiceItem_price']; ?></td>
                            <td><?php echo $row['totalSale'] ?></td>
                          </tr>
                        <?php
                        } ?>
                      </tbody>
                      <tfoot class="bg-light">
                        <tr>
                          <td colspan="4" class="text-right"><strong>Total Sales (Rs): </strong></td>
                          <td><strong id="totalSales"> 0.00 </strong></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-6">
               
              </div>
              <div class="col-6">
               
              </div>
              <div class="col-6">
                <div class="card">
                  <div class="card-header">
                    <h4>Cash Received</h4>
                  </div>
                  <div class="card-body">
                    <table id="mytable4" class="table table-bordered table-hover">
                      <thead>
                        <tr class="bg-info">
                          <th>#</th>
                          <th>Name</th>
                          <th>Customer/Supplier</th>
                          <th>Payment Date</th>
                          <th>Amount</th>
                        </tr>
                      </thead>
                     
                      <tfoot>
                        <tr>
                          <td colspan="4" class="text-right"><strong>Total (Rs): </strong> </td>
                          <td> <strong id="totalReceived"> 0.00 </strong> </td>
                        </tr>
                      </tfoot>
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
  </div>

  <!-- Alert -->
  <?php include("part/alert.php"); ?>
  <!-- Alert end -->

  <!-- All JS -->
  <?php include("part/all-js.php"); ?>
  <!-- All JS end -->

  <!-- Data Table JS -->
  <?php include("part/data-table-js.php"); ?>
  <!-- Data Table JS end -->


  <!-- Page specific script -->
  <script>
    $(function() {
      $(".select2").select2();

      $(".select2bs4").select2({
        theme: "bootstrap4",
      });
    });
  </script>

  <script>
    $(document).ready(function() {
      $('#mytable').DataTable({
        order: [
          [0, 'desc']
        ],
        // pageLength : 3,
        dom: 'Bfrtip',
        aaSorting: [],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        "footerCallback": function(row, data, start, end, display) {
          var totalAmount = 0;
          for (var i = 0; i < data.length; i++) {
            totalAmount += parseFloat(data[i][4]);
          }
          // console.log(totalAmount);
          $("#totalSales").text(totalAmount);
        }
      });

      $('#mytable2').DataTable({
        // order: [[0, 'desc']],
        dom: 'Bfrtip',
        aaSorting: [],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        "footerCallback": function(row, data, start, end, display) {
          //Get data here 
          // console.log(data);
          var totalAmount = 0;
          for (var i = 0; i < data.length; i++) {
            totalAmount += parseFloat(data[i][4]);
          }
          // console.log(totalAmount);
          $("#totalExpense").text(totalAmount);
        }
      });

      $('#mytable3').DataTable({
        // order: [[0, 'desc']],
        dom: 'Bfrtip',
        aaSorting: [],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        "footerCallback": function(row, data, start, end, display) {
          //Get data here 
          // console.log(data);
          var totalAmount = 0;
          for (var i = 0; i < data.length; i++) {
            totalAmount += parseFloat(data[i][3]);
          }
          // console.log(totalAmount);
          $("#totalPurchase").text(totalAmount);
        }
      });

      $('#mytable4').DataTable({
        // order: [[0, 'desc']],
        dom: 'Bfrtip',
        aaSorting: [],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        "footerCallback": function(row, data, start, end, display) {
          //Get data here 
          // console.log(data);
          var totalAmount = 0;
          for (var i = 0; i < data.length; i++) {
            totalAmount += parseFloat(data[i][4]);
          }
          // console.log(totalAmount);
          $("#totalReceived").text(totalAmount);
        }
      });
    });
  </script>

</body>

</html>