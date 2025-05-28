<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
  if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {

      $shop_id = $userData['shop_id'];
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

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
            <section class="content">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Purchase Orders</h3>
                      </div>
                      <div class="card-body">

                        <table class="table table-bordered table-hover">
                          <thead>
                            <tr class="bg-info">
                              <th class="adThText">Order Number</th>
                              <th class="adThText">Order From</th>
                              <th class="adThText">Items</th>
                              <th class="adThText">Order Placed Date</th>
                              <th class="adThText">Required by</th>
                              <th class="adThText">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if ($shop_id == "1") {
                              $hub_order_details_result = $conn->query("SELECT DISTINCT hub_order_details_id , hub_order_number , shopName , order_date , hub_order_subTotal , order_status , order_status_id, delivery_date
                              FROM hub_order_details 
                              INNER JOIN hub_order ON hub_order.HO_number = hub_order_details.hub_order_number
                              INNER JOIN order_status ON order_status.order_status_id = hub_order_details.hub_order_status
                              INNER JOIN shop ON shop.shopId = hub_order.HO_shopId
                              ORDER BY `hub_order_details_id` DESC");
                            } else {
                              $hub_order_details_result = $conn->query("SELECT DISTINCT hub_order_details_id , hub_order_number , shopName , order_date , hub_order_subTotal , order_status , order_status_id, delivery_date
                              FROM hub_order_details 
                              INNER JOIN hub_order ON hub_order.HO_number = hub_order_details.hub_order_number
                              INNER JOIN order_status ON order_status.order_status_id = hub_order_details.hub_order_status
                              INNER JOIN shop ON shop.shopId = hub_order.HO_shopId 
                              WHERE shop.shopId = '$shop_id'
                              ORDER BY `hub_order_details_id` DESC");
                            }

                            if ($hub_order_details_result->num_rows > 0) {
                              while ($hub_order_details_data = $hub_order_details_result->fetch_assoc()) {
                            ?>
                                <tr>
                                  <th><?= $hub_order_details_data["hub_order_number"] ?></th>
                                  <th><?= $hub_order_details_data["shopName"] ?></th>
                                  <th>
                                    <?php
                                    $itemCount_result = $conn->query("SELECT COUNT(hub_order_id) AS itemCount  
                                  FROM hub_order WHERE HO_number = '" . $hub_order_details_data['hub_order_number'] . "'");
                                    $itemCount_data = $itemCount_result->fetch_assoc();
                                    ?>
                                    <!--Items list table start  -->
                                    <button class="btn dropdown-toggle badge badge-info " type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom-start"> <?= $itemCount_data['itemCount'] ?> </button>
                                    <ul class="dropdown-menu">
                                      <table class="table" id="poItemsTable<?= $hub_order_details_data['hub_order_number'] ?>">
                                        <thead>
                                          <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Barcode</th>
                                            <th scope="col">Item Name</th>
                                            <th scope="col">Volume</th>
                                            <th scope="col">Brand</th>
                                            <th scope="col">Qty</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php

                                          $poItems_result = $conn->query("SELECT hub_order.HO_item AS code,
                                          p_medicine.name AS name,
                                          p_brand.name AS brand, 
                                          hub_order.HO_qty AS HO_qty,
                                          hub_order.HO_price AS HO_price,
                                          unit_category_variation.ucv_name AS volume,
                                          medicine_unit.unit AS unit
                                          FROM hub_order
                                        INNER JOIN p_medicine ON p_medicine.code = hub_order.HO_item
                                        INNER JOIN p_brand ON p_medicine.brand = p_brand.id
                                        INNER JOIN unit_category_variation ON p_medicine.unit_variation = unit_category_variation.ucv_id
                                        INNER JOIN medicine_unit ON unit_category_variation.p_unit_id = medicine_unit.id
                                        WHERE HO_number = '" . $hub_order_details_data['hub_order_number'] . "'  ");

                                          $rowId = 0;
                                          $billTotal = 0;
                                          while ($poItems_data = $poItems_result->fetch_array()) {
                                            $rowId++;
                                            $billTotal += $poItems_data["HO_price"];
                                          ?>
                                            <tr>
                                              <th scope="row"><?= $rowId ?></th>
                                              <td><?= $poItems_data["code"] ?></td>
                                              <td><?= $poItems_data["name"] ?></td>
                                              <td><?= $poItems_data["volume"] ?> <?= $poItems_data["unit"] ?></td>
                                              <td><?= $poItems_data["brand"] ?></td>
                                              <td><?= $poItems_data["HO_qty"] ?></td>
                                            </tr>
                                          <?php
                                          }
                                          ?>
                                        </tbody>
                                        <!-- <tfoot>
                                          <tr>
                                            <td colspan="4" class="text-right"><strong>Total Cost:</strong></td>
                                            <td><?php //echo  number_format($billTotal, 0); ?></td>
                                          </tr>
                                        </tfoot> -->
                                      </table>
                                      <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?= $hub_order_details_data['hub_order_number'] ?>');">
                                        <i class="nav-icon fas fa-copy"></i> PRINT
                                      </button>
                                    </ul>
                                    <!--Items list table end  -->
                                  </th>
                                  <th><?= $hub_order_details_data['order_date'] ?></th>
                                  <th><?= $hub_order_details_data["delivery_date"] ?></th>
                                  <th>
                                    <?php
                                    if ($shop_id == "1") {
                                      if ($hub_order_details_data["order_status_id"] == '1' || $hub_order_details_data["order_status_id"] == '6') {
                                    ?>
                                        <button class="btn btn-info" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '2')">Accept</button>
                                      <?php
                                      } else if ($hub_order_details_data["order_status_id"] == '2') {
                                      ?>
                                        <button class="btn mr-1 bg-primary text-white" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '3')">Packing</button>
                                        <button class="btn ml-1 btn-danger" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '6')">Reject</button>
                                      <?php
                                      } else if ($hub_order_details_data["order_status_id"] == '3') {
                                      ?>
                                        <button class="btn mr-1 btn-warning" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '7')">Ready</button>
                                        <button class="btn ml-1 btn-danger" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '6')">Reject</button>
                                      <?php
                                      } else if ($hub_order_details_data["order_status_id"] == '7') {
                                      ?>
                                        <button class="btn mr-1 btn-warning" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '4')">Deliver</button>
                                        <button class="btn ml-1 btn-danger" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '6')">Reject</button>
                                      <?php
                                      } else if ($hub_order_details_data["order_status_id"] == '4') {
                                      ?>
                                        <label class='p-2 orderStatus btn-success'>Sent</label>
                                        <!-- <button class="btn ml-1 btn-danger" onclick="updateOrderStatus('<?php //echo $hub_order_details_data['hub_order_number'] 
                                                                                                              ?>' , '6')">Reject</button> -->
                                      <?php
                                      } else if ($hub_order_details_data["order_status_id"] == '5') {
                                      ?>
                                        <label class='p-2 orderStatus btn-success'>Completed</label>
                                      <?php
                                      } else if ($hub_order_details_data["order_status_id"] == '8') {
                                      ?>
                                        <label class='p-2 orderStatus btn-success'>Completed</label>
                                      <?php
                                      }
                                    } else {

                                      $orderStatusColor = array(
                                        "1" => "#FACC15",
                                        "2" => "#22C55E",
                                        "3" => "#3B82F6",
                                        "4" => "#0EA5E9",
                                        "5" => "#10B981",
                                        "6" => "#EF4444",
                                        "7" => "#8B5CF6",
                                        "8" => "#14B8A6"
                                      );

                                      $orderStatus = array(
                                        "1" => "Pending",
                                        "2" => "Approved",
                                        "3" => "Packaging",
                                        "4" => "Delivered",
                                        "5" => "Received",
                                        "6" => "Rejected",
                                        "7" => "Ready",
                                        "8" => "Completed"
                                      );

                                      if ($hub_order_details_data["order_status_id"] == '4') {
                                      ?>
                                        <button class="btn mr-1 btn-warning" onclick="updateOrderStatus('<?= $hub_order_details_data['hub_order_number'] ?>' , '8')">Received</button>
                                <?php
                                      } else {

                                        $order_status_id = $hub_order_details_data["order_status_id"];
                                        $bgColor = isset($orderStatusColor[$order_status_id]) ? $orderStatusColor[$order_status_id] : "white";
                                        $statusType = isset($orderStatus[$order_status_id]) ? $orderStatus[$order_status_id] : "Unknown";

                                        echo "
                                              <label class='p-2 orderStatus' style='background-color: $bgColor;color:white;'>$statusType</label>
                                            ";
                                      }
                                    }
                                  }
                                }
                                ?>
                                  </th>


                                </tr>
                              <?php
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
        </div>

        <!-- Alert -->
        <?php include("part/alert.php"); ?>
        <!-- Alert end -->

        <!-- All JS -->
        <?php include("part/all-js.php"); ?>
        <!-- All JS end -->

        <script src="dist/js/messageDisplay.js"> </script>

        <script>
          function updateOrderStatus(orderNO, status) {
            $(".btn").prop('disabled', true);

            $.ajax({
              url: "actions/purchase_orders/updateOrderStatusProcess.php",
              method: "POST",
              data: {
                orderNumber: orderNO,
                status: status,
              },
              success: function() {
                SuccessMessageDisplay("Order status updated!")
                setTimeout(function() {
                  location.reload();
                }, 3000);
              },
              error: function(xhr, status, error) {
                console.error(xhr.responseText);
                ErrorMessageDisplay("Order status update failed!");
                $(".btn").prop('disabled', false);
              },
            });
          }
        </script>

        <script>
          document.addEventListener("DOMContentLoaded", function() {
            var dropdownButtonList = [].slice.call(document.querySelectorAll('.btn.dropdown-toggle'));
            dropdownButtonList.map(function(button) {
              // Check if the button has already been initialized
              if (!button.classList.contains('dropdown-initialized')) {
                new bootstrap.Dropdown(button);
                // Mark the button as initialized to prevent re-initialization
                button.classList.add('dropdown-initialized');
              }
            });
          });
        </script>
        <script>
          function printTable(orderNumber) {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Preview</title>');
            // Include Bootstrap CSS
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="container">');
            printWindow.document.write('<h2 class="text-center bg-success text-light" style="margin-top:5px;padding:3px;">ORDER DETAILS</h2>');
            printWindow.document.write('<div class="col-12" style="margin-top: 50px;margin-bottom: 20px;font-family: monospace;">');
            printWindow.document.write('<div class="row">');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h5>ORDER NUMBER : ' + orderNumber + '</h5>');
            printWindow.document.write('</div>');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h6>ORDER DATE : <?= date('Y-m-d', strtotime($itemCount_data['orderDate'])) ?></h6>');
            printWindow.document.write('</div>');
            printWindow.document.write('<div class="col-12" style="text-align: start;">');
            printWindow.document.write('<h6>ORDER TIME : <?= date('H:i:s', strtotime($itemCount_data['orderDate'])) ?></h6>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
            printWindow.document.write(document.getElementById('poItemsTable' + orderNumber).outerHTML);
            printWindow.document.write('</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
          }
        </script>
      </body>

      </html>
  <?php
  }
}

  ?>