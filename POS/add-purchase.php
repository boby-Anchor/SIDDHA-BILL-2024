
<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
  $userLoginData = $_SESSION['store_id'];
  foreach ($userLoginData as $userData) {
    $userName = $userData['name'];
    $userId = $userData['id'];
    $shop_id = $userData['shop_id'];

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Home | Pharmacy</title>

      <!-- overlayScrollbars -->
      <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
      <!-- Product -->
      <link rel="stylesheet" href="dist/css/product.css">
      <!-- All CSS -->
      <?php include("part/all-css.php"); ?>
      <!-- All CSS end -->


      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

      <style>
        .table-wrap {
          max-height: 300px;
          overflow-x: auto;
          overflow-y: auto;
          margin-top: 10px;
        }

        table {
          border-collapse: collapse;
          width: 100%;
        }

        thead th {
          position: -webkit-sticky;
          position: sticky;
          top: 0;
        }

        .table-wrap table thead th {
          background-color: rgb(233, 238, 248);
          line-height: 35px;
          text-indent: 5px;
          white-space: nowrap;
          text-align: left;
          font-weight: 500;
        }

        .table-wrap table tbody tr {
          border-bottom: 1px solid rgb(233, 238, 248);
        }

        .table-wrap table tbody td {
          line-height: 35px;
          text-indent: 5px;
          white-space: nowrap;
        }

        .table-wrap {
          border: 1px solid rgb(233, 238, 248);
        }

        /*************** Select box ***********/
        select.table-select,
        input.table-input {
          height: 35px;
          width: 150px;
          border: 1px solid rgb(233, 238, 248);
          text-indent: 5px;
        }

        button.add-btn.btn.btn-outline-primary,
        button.add-btn.btn.btn-outline-primary:focus,
        button.add-btn.btn.btn-outline-primary:hover {
          height: 30px;
          line-height: 15px;
          outline: 0 !important;
          box-shadow: none;
        }

        .box {
          padding: 10px 0px;
        }

        .box select {
          color: #000;
          border: 1px solid #ddd;
          font-size: 14px;
          -webkit-appearance: none;
          appearance: none;
          outline: none;
        }

        .box::after {
          content: "\f107";
          font-family: FontAwesome;
          position: relative;
          color: #96969a !important;
          top: 2px;
          right: 25px;
          width: 20%;
          height: 100%;
          text-align: center;
          font-size: 22px;
          line-height: 35px;
          color: rgba(255, 255, 255, 0.5);
          background-color: rgba(255, 255, 255, 0.1);
          pointer-events: none;
        }

        .cus-delete {
          font-size: 20px;
          color: #f91c1c;
          font-weight: 500;
          cursor: pointer;
        }
        
        .labQty{
            color:red;
            font-size: 18px;
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

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
          <div class="col-12 bg-dark">
            <div class="row w-100">

              <!-- left side design start -->
              <div class="col-12">
                <div class="card-body h-100 ">
                  <div class="row">

                    <!-- supplier products start -->
                    <div class="col-12 px-4">
                      <div class="row">
                        <div class="col-4">
                          <input type="text" class="form-control bg-dark" placeholder="Barcode Number" onkeyup="filterBySearch(this.value);" id="bnInput">
                        </div>
                        <div class="col-4">
                          <input type="text" class="form-control bg-dark" placeholder="Product Code" onkeyup="filterBySearch(this.value);" id="pcInput">
                        </div>
                        <div class="col-4">
                          <input type="text" class="form-control bg-dark" placeholder="Product Name" onkeyup="filterBySearch(this.value);" id="pnInput">
                        </div>
                        <div class="col-12 products-table mt-3">
                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Unit</th>
                                <th scope="col">Product Cost</th>
                                <th scope="col">Product Sell price</th>
                                <th scope="col"></th>
                              </tr>
                            </thead>
                            <tbody id="filterBySupTable">
                              <?php
                              $p_medicine_rs = $conn->query("SELECT producttoshop.*, p_brand.name AS brand,
                              p_medicine.name AS medName, medicine_unit.unit AS unit, stock2.stock_item_cost AS cost,stock2.stock_item_qty AS siq,
                              stock2.item_s_price AS item_s_price
                              FROM producttoshop
                              INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
                              INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
                              INNER JOIN stock2 ON p_medicine.code = stock2.stock_item_code
                              INNER JOIN p_brand ON p_medicine.brand = p_brand.id
                              WHERE producttoshop.shop_id = '$shop_id' AND productToShopStatus = 'added' ORDER BY stock2.stock_id ASC");

                              $tableRowCount = 1;
                              while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
                              ?>
                                <tr>
                                  <th id="product_code" class="d-none"><?= $p_medicine_data['medicinId'] ?></th>

                                  <th scope="row"><?= $tableRowCount ?></th>

                                  <td id="product_name"><?= $p_medicine_data['medName'] ?></td>
                                  <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>

                                  <td id="product_unit">
                                    <label for=""><?= $p_medicine_data['unit'] ?></label>
                                    <br>
                                     <label class="labQty"><?= $p_medicine_data['siq'] ?></label>
                                  </td>
                                  <td id="product_cost">
                                    <label for=""><?= $p_medicine_data['cost'] ?></label>
                                  </td>
                                  <td id="product_sprice">
                                    <label for=""><?= $p_medicine_data['item_s_price'] ?></label>
                                  </td>

                                  <td><button class="btn btn-outline-success add-btn">Add</button></td>
                                </tr>
                              <?php
                                $tableRowCount++;
                              }
                              ?>

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <!-- supplier products end -->

                  </div>
                </div>
              </div>
              <!-- left side design end -->

              <!-- right side design start -->
              <div class="col-12">
                <div class="po_tittle">
                  <h3>PO PRODUCTS</h3>
                </div>
                <table class="table table-dark table-hover addedProTable">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Product Name</th>
                       <th scope="col">Brand</th>
                      <th scope="col">Product Cost</th>
                      <th scope="col">Qty</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row" id="addproduct_code"></th>
                      <td id="addproduct_name"></td>
                      <td id="addproduct_brand"></td>
                      <td id="addproduct_cost"></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
                <div class="po_btn col-12 d-none justify-content-end align-items-center">
                  <a type="button" class="btn btn-outline-success proceed-order" id="proceedOrderBtn">Proceed Order <i class="fas fa-arrow-right"></i></a>
                </div>
              </div>

              <!-- right side design end -->
            </div>
          </div>
        </div>

        <!-- add new unit modal start -->

        <div class="container">
          <div class="modal fade" id="addunitmodal" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Add New Unit</h4>
                </div>
                <div class="modal-body">
                  <input type="text" placeholder="Unite Name..." id="newUnit">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-success" onclick="addNewUnit();">Save</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- add new unit modal end -->
        <?php
        if (isset($_SESSION['store_id'])) {

          $userLoginData = $_SESSION['store_id'];

          foreach ($userLoginData as $userData) {
            $userId = $userData['id'];
            $shop_id = $userData['shop_id'];
          }
        }

        $orderId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'hub_order'");
        $orderId_row = $orderId_rs->fetch_assoc();
        $orderId = $orderId_row['AUTO_INCREMENT'];
        $orderNumber = "PO-$userId$shop_id" . "0000" . $orderId;

        $poDate = date("Y-m-d");
        $poTime = date("H:i:s");
        ?>

        <!-- confirm po modal start -->
        <div class="container">
          <div class="modal fade" id="confirmPO" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Order Confirmation Form</h4>
                </div>
                <div class="modal-body">
                  <div class="orderId">
                    <div class="row">
                      <div class="col-4 text-center">
                        <label for="orderNumber">Order Number</label>
                        <?php
                        echo "<span class=\"fs-2\" name=\"orderNumber\" id=\"orderNumber\">$orderNumber</span>";
                        ?>

                      </div>
                      <div class="col-4 text-center">
                        <div class="col-12">
                          <div class="row">
                            <div class="col-12">
                              <label for="orderDate">Order Date</label>
                            </div>
                            <div class="col-12">
                              <span class="fs-2" name="orderDate" id="orderDate"><?= $poDate ?></span>
                            </div>
                          </div>
                        </div>

                      </div>
                      <div class="col-4 text-center">
                        <label for="orderTime">Order Time</label>
                        <span class="fs-2" name="orderTime" id="orderTime"><?= $poTime ?></span>
                      </div>
                      <div class="orderItem col-12 mt-4 mb-3">
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Order Item</th>
                              <th scope="col">Brand</th>
                              <th scope="col">Price</th>
                              <th scope="col">Qty</th>
                              <th scope="col">Total Price</th>
                            </tr>
                          </thead>
                          <tbody id="orderConfirmationTableBody">
                            <tr>
                              <th scope="row"></th>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                            <tr>
                              <td colspan="4"></td>
                              <td class="order-confirmation-total"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-success confirmPObtn">Save</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
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

        <script>
          $(document).ready(function() {

            $(document).on("click", ".add-btn", function() {
              var product_code = $(this).closest("tr").find("#product_code").text();
              var product_name = $(this).closest("tr").find("#product_name").text();
               var product_brand = $(this).closest("tr").find("#product_brand").text();
              var product_cost = $(this).closest("tr").find("#product_cost").text();
              var product_unit = $(this).closest("tr").find("#product_unit").text();
              var product_qty = 1;

              var exists = false;
              $(".addedProTable tbody tr").each(function() {
                if ($(this).find("#addproduct_name").text() === product_name) {
                  exists = true;
                  return false;
                }
              });

              if (!exists) {
                var markup =
                  "<tr>" +
                  "<th scope='row'>" + product_code + "</th>" +
                  "<td id='addproduct_name'>" + product_name + "</td>" +
                  "<td id='addproduct_brand'>" + product_brand + "</td>" +
                  "<td id='addproduct_cost'>" + product_cost + "</td>" +
                  "<td>" +
                  "<div class='medi_uni'>" +
                  "<div class='col-6'>" +
                  "<div class='input-group'>" +
                  "<input type='text' class='form-control bg-dark' value='" + product_qty + "'>" +
                  "<div class='input-group-append unit-select-main' id='unitselectordiv'>" +
                  "<select name='quantity_unit' class='form-control bg-dark' id='qtyUnitSelector' >" +
                  "<option value='" + product_unit + "'>" + product_unit + "</option>" +
                  "</select>" +

                  "</div>" +
                  "</div>" +
                  "</div>" +
                  "</div>" +
                  "</td>" +
                  "<td><i class='fa fa-trash-o cus-delete'></i></td>" +
                  "</tr>";

                $(".addedProTable tbody").append(markup);

                $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

              } else {
                alert("Product already exists in the list!");
              }
            });

            $(document).on("click", ".cus-delete", function() {
              $(this).closest("tr").remove();
              $("#proceedOrderBtn").removeAttr("data-toggle data-target");
              $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 1);
              $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 1);
            });
          });
        </script>

        <script>
          $('#myModal').on('shown.bs.modal', function() {
            $('#myInput').trigger('focus')
          })
        </script>

        <script>
          $(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function() {
            var orderNumber = document.getElementById("orderNumber").innerText;
            var orderDate = document.getElementById("orderDate").innerText;
            var orderTime = document.getElementById("orderTime").innerText;
            $(this).prop('disabled', true);

            var poArray = [];

            $("#orderConfirmationTableBody tr").each(function() {
              var product_code = $(this).find(".product_code").text();
              var product_name = $(this).find(".product_name").text();
              var product_brand = $(this).find(".product_brand").text();
              var product_cost = $(this).find(".product_cost").text();
              var product_qty = $(this).find(".product_qty").text();
              var qty_unit = $(this).find(".qty_unit").text();

              var productData = {
                product_code: product_code,
                product_name: product_name,
                product_brand: product_brand,
                product_cost: product_cost,
                product_qty: product_qty,
                qty_unit: qty_unit,
                orderNumber: orderNumber,
                orderDate: orderDate,
                orderTime: orderTime,
              };
              poArray.push(productData);
            });

            $.ajax({
              url: "poConfirmationInsert.php",
              method: "POST",
              data: {
                products: JSON.stringify(poArray),
              },
              success: function() {
                Swal.mixin({
                  toast: true,
                  position: "top-end",
                  showConfirmButton: false,
                  timer: 3000,
                }).fire({
                  icon: "success",
                  title: "Success: Order Placed Success !",
                });
                $(".confirmPObtn").prop('disabled', false);
                location.reload(true);
              },
              error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.mixin({
                  toast: true,
                  position: "top-end",
                  showConfirmButton: false,
                  timer: 3000,
                }).fire({
                  icon: "error",
                  title: "Order Failed !",
                });
                $(".confirmPObtn").prop('disabled', false);
              },
            });
          });
        </script>
    </body>
    <script src="dist/js/add-purchase.js"></script>
    </html>
<?php
  }
}
?>
