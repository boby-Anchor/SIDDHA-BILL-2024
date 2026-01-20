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
$totalCost = 0;
$price = 0;
$cost = 0;
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
                        <th>Name</th>
                        <th>NameAlias</th>
                        <th>PartNo</th>
                        <th>Description</th>
                        <th>Parent</th>
                        <th>BaseUnits</th>
                        <th>AdditionalUnit</th>
                        <th>Conversion</th>
                        <th>Denominator</th>
                        <th>Category</th>
                        <th>GodownName</th>
                        <th>MfdOn</th>
                        <th>EXPIRYPERIOD</th>
                        <th>BatchName</th>
                        <th>OpeningBalance</th>
                        <th>OpeningRate</th>
                        <th>OpeningValue</th>
                        <th>IgnoreNegativeStock</th>
                        <th>IsCostCentresOn</th>
                        <th>IsBatchwiseOn</th>
                        <th>HasMFGDate</th>
                        <th>ISPERISHABLEON</th>
                        <th>RateOfVAT</th>
                        <th>BasicRateOfExcise</th>
                        <th>Remark</th>
                        <th>CostingMethod</th>
                        <th>ValuationMethod</th>
                        <th>stdCostDate</th>
                        <th>stdCost</th>
                        <th>stdPriceDate</th>
                        <th>stdPrice</th>
                        <th>MfgUnit</th>
                        <th>BOMItem</th>
                        <th>BOMGodown</th>
                        <th>BOMQty</th>
                        <th>BOMUnit</th>
                        <th>ExciseCommodity</th>
                        <th>VATCommodity</th>
                        <th>ExciseGodown</th>
                        <th>ValuationType</th>
                        <th>ExciseUnitName</th>
                        <th>StockItemType</th>
                        <th>CompanyName</th>
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
                          stock2.item_s_price AS p_s_price , p_medicine_category.name AS p_category ,
                          medicine_unit.unit AS unit , unit_category_variation.ucv_name
                          FROM stock2
                          INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
                          INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
                          INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                          INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                          INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                          WHERE stock2.stock_shop_id = '$shop_id' ORDER BY p_medicine.name ASC");
                          while ($row = mysqli_fetch_assoc($sql)) {
                            $totalRows++;

                            $totalValue += $price;
                      ?>
                            <tr>
                              <td> <?= $row['p_name']; ?> </td>
                              <td style="padding:5px" class="text-center">
                                <?= $row['p_code']; ?>
                              </td>
                              <td> </td>
                              <td> </td>
                              <td> </td>
                              <td><?= $row['unit']; ?></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td> <?= $row['p_category']; ?></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td> <?= $row['p_a_stock'] > 0 ? $row['p_a_stock'] : 0; ?> </td>
                              <td> <?= number_format($row['p_cost'], 0); ?></td>
                              <td class="text-center"> <label class="product-selling-price"><?= number_format($row['p_s_price']); ?></label> </td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <!-- <td>
                                <?php
                                // if ($row['p_a_stock'] > 0) {
                                //   if ($row['unit'] == "ml") {
                                //     // Price per unit for ml
                                //     $cost = $row['p_cost'] * $row['p_a_stock'];
                                //   } else if ($row['unit'] == "l") {
                                //     // Convert unit capacity value from liters to milliliters before calculating
                                //     // $cost = $row['p_cost'] / ($row['ucv_name'] * 1000) * $row['p_a_stock'];
                                //   } else {
                                //     // Default calculation for other units
                                //     $cost = $row['p_a_stock'] * $row['p_cost'];
                                //   }
                                //   $totalCost += $cost;
                                //   echo number_format($cost, 0);
                                // } else {
                                //   echo 0;
                                // }
                                ?>
                              </td> -->
                              <!-- <td>
                                <?php
                                // if ($row['p_a_stock'] > 0) {
                                //   if ($row['unit'] == "ml") {
                                //     // Price per unit for ml
                                //     $price = $row['p_s_price'] * $row['p_a_stock'];
                                //   } else if ($row['unit'] == "l") {
                                //     // Convert unit capacity value from liters to milliliters before calculating
                                //     $price = $row['p_s_price'] / ($row['ucv_name'] * 1000) * $row['p_a_stock'];
                                //   } else {
                                //     // Default calculation for other units
                                //     $price = $row['p_a_stock'] * $row['p_s_price'];
                                //   }
                                //   echo number_format($price, 0);
                                // } else {
                                //   echo 0;
                                // }
                                ?>
                              </td> -->
                              <td> <?= $row['bName']; ?></td>
                            </tr>
                      <?php }
                        }
                      }
                      ?>
                    </tbody>
                    <!-- <tfoot>
                      <tr>
                        <td colspan="7" class="text-right"><strong>Total Rows:</strong></td>
                        <td colspan="2"><?= $totalRows; ?></td>
                      </tr>
                      <tr>
                        <td colspan="7" class="text-right"><strong>Total Cost:</strong></td>
                        <td colspan="2"><?= number_format($totalCost, 0); ?></td>
                      </tr>
                      <tr>
                        <td colspan="7" class="text-right"><strong>Total Value:</strong></td>
                        <td colspan="2"><?= number_format($totalValue, 0); ?></td>
                      </tr>
                    </tfoot> -->
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
            [1, 'asc']
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