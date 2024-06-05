<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include('config/db.php');
}
// include('actions/cart-pos.php');
//   include('actions/cart.php');
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
  <!-- Data Table CSS -->
  <?php include("part/data-table-css.php"); ?>
  <!-- Data Table CSS end -->
  <!-- All CSS -->
  <?php include("part/all-css.php"); ?>
  <!-- All CSS end -->

  <!-- bootstrap icon link -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="dist/css/customize_bill.css">

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

        <div class="col-12 col-md-7">

          <div class="col-12 total_div">

            <div class="row">
              <div class="col-12 text-right">
                <label class="subTotal" id="subTotal"></label>
                <label class="subTotal">LKR</label>
              </div>

              <div class="col-12 p-2 amountDiv">
                <input type="text" placeholder="Discount %" class="form-control col-4" id="discountPercentage" name="discountPercentage" onkeyup="addDiscount()">
              </div>

              <div class="col-12 p-2 amountDiv d-none" id="deliveryChargesField">
                <input type="text" placeholder="Delivery Charges" class="form-control col-4" id="deliveryCharges" name="deliveryCharges" onkeyup="checkNetTotal()">
              </div>

              <div class="col-12 p-2 amountDiv d-none" id="packingChargesField">
                <input type="text" placeholder="Packing Charges" class="form-control col-4" id="packingChargesField" name="packingChargesField" onkeyup="checkNetTotal()">
              </div>

              <div class="col-12 p-2 amountDiv d-none" id="ServiceChargesField">
                <label class="col-5">Value added Services:</label>
                <input type="text" class="form-control col-4" id="valueAddedServices" name="valueAddedServices" onkeyup="checkNetTotal()">
              </div>

              <div class="col-12 text-right">
                <label class="netTotal" id="netTotal"></label>
                <label class="netTotal">LKR</label>
              </div>

              <div class="col-12 p-2 amountDiv">
                <input type="text" placeholder="Enter Cash Amount" class="form-control col-4" id="cashAmount" name="cashAmount" onkeyup="checkBalance(this)">
              </div>

              <div class="col-12 p-2 amountDiv d-none" id="cardAmountField">
                <input type="text" placeholder="Enter Card Amount" class="form-control col-4" id="cardAmount" name="cardAmount" onkeyup="checkBalance(this)">
              </div>

              <div class="col-12 p-3" style="background: #0000004a;">
                <div class="row">
                  <div class="col-6">

                    <div class="col-12">
                      <label class="balance" id="balance">000</label>
                    </div>
                  </div>

                  <div class="col-6 d-flex justify-content-end align-items-center">
                    <select name="payment-method-selector" id="payment-method-selector" class="payment-method-selector">
                      <?php
                      $payment_type_rs =  $conn->query("SELECT * FROM payment_type");
                      while ($payment_type_row = $payment_type_rs->fetch_assoc()) {
                      ?>
                        <option value="<?= $payment_type_row['payment_type_id'] ?>"><?= $payment_type_row['payment_type'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <button class="btn check-outBtn col-6" id="checkoutBtn">Checkout <i class="bi bi-arrow-right-circle-fill"></i></button>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="row">
              <div class="col-4 mb-3">
                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode..." onchange="getBarcode2(this.value);">
              </div>
              <div class="col-4 mb-3">
                <select class="form-control" name="" id="selectPrices" onchange="getBarcode3()"></select>
              </div>
              <div class="col-4 mb-3">
                <select class="form-control" name="selectBillType" id="selectBillType">
                  <?php
                  $bill_type_rs = $conn->query("SELECT * FROM bill_type");
                  while ($bill_type_row = $bill_type_rs->fetch_assoc()) {
                  ?>
                    <option value="<?= $bill_type_row['bill_type_id'] ?>"><?= $bill_type_row['bill_type_name'] ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
              <div class="col-12">
                <div>
                  <table class="table barcodeResults">
                    <tbody id="barcodeResults"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-5">
          <div class="card-body h-100 bg-light overflow-hidden">

            <div class="row">

              <!-- Company category end -->

              <!-- Company Product list -->
              <div class="col-12" style="height: 100vh; overflow:auto;">
                <form action="" method="post" onkeyup="searchProducts(); return false;"> <!-- Added method attribute -->
                  <input type="search" class="form-control" name="search21" id="search21" placeholder="Search...">
                </form>
                <div class="row" id="productGrid">
                  <?php
                  if (isset($_SESSION['store_id'])) {

                    $userLoginData = $_SESSION['store_id'];

                    foreach ($userLoginData as $userData) {
                      $shop_id = $userData['shop_id'];
                      $cm = runQuery("SELECT * FROM stock2 INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code  WHERE stock2.stock_shop_id = '$shop_id'");
                      foreach ($cm as $v) {
                  ?>
                        <div class="col-md-4 col-sm-6 mt-3" onclick="getBarcode('<?= $v['code']; ?>','<?= $v['item_s_price']; ?>')">
                          <div class="product-grid h-100">
                            <div class="product-image">
                              <a href="#" class="image">
                                <img src="dist/img/product/<?php echo $v['img']; ?>" width="50" alt="Image">
                              </a>

                            </div>
                            <div class="product-content">
                              <h3 class="title"><a href="#"><?php echo $v['name']; ?></a></h3>
                              <div class="price">RS <?php echo $v['item_s_price']; ?></div>
                            </div>
                          </div>
                        </div>
                  <?php }
                    }
                  } ?>

                </div>
                <script>
                  function searchProducts() {
                    var searchInput = document.getElementById('search21').value.trim();
                    console.log(searchInput);
                    if (searchInput !== '') {
                      $.ajax({
                        type: 'POST',
                        url: 'actions/searchNameProductPos.php',
                        data: {
                          searchName: searchInput
                        },
                        success: function(response) {
                          $('#productGrid').html(response);
                          //console.log(response)
                        },

                      });
                    }
                  }

                  //payment type online select //
                  document.getElementById('selectBillType').addEventListener('change', function() {
                    var selectedValue = this.value;
                    var deliveryChargesElement = document.getElementById('deliveryChargesField');
                    var serviceChargesElement = document.getElementById('ServiceChargesField');

                    if (selectedValue === '2') {

                      if (deliveryChargesElement.classList.contains('d-none')) {
                        deliveryChargesElement.classList.remove('d-none');
                      }
                      if (serviceChargesElement.classList.contains('d-none')) {
                        serviceChargesElement.classList.remove('d-none');
                      }
                    } else {
                      deliveryChargesElement.classList.add('d-none');
                      serviceChargesElement.classList.add('d-none');
                    }
                  })

                  // if select cash + card //
                  document.getElementById('payment-method-selector').addEventListener('change', function() {
                    var selectedValue = this.value;
                    var cardAmountField = document.getElementById('cardAmountField');

                    if (selectedValue === '3') {

                      if (cardAmountField.classList.contains('d-none')) {
                        cardAmountField.classList.remove('d-none');
                      }

                    } else {
                      cardAmountField.classList.add('d-none');
                    }
                  });

                </script>
              </div>

              <!-- Company Product list end -->
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
    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

    <!-- select2 input field -->

    <!-- ========================================== -->

    <div id="invoice-POS" class="d-none">

      <?php
      if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        $currentDate = date("Y-m-d");
        $currentTime = date("H:i:s");

        foreach ($userLoginData as $userData) {
          $shop_id = $userData['shop_id'];
          $user_name = $userData['user_name'];

          $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'invoices'");
          $invoiceId_row = $invoiceId_rs->fetch_assoc();
          $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
          $invoiceNumber = "000" . $userId . $shop_id .  $invoiceId;

          $bill_data_rs = $conn->query("SELECT * FROM `customize_bills` WHERE `customize_bill_shop-id` = '$shop_id'");
          $bill_data = $bill_data_rs->fetch_assoc();

      ?>
          <div class="d-flex justify-content-center">
            <div class="col-12 p-2" style="width:<?= $bill_data['print_paper_size'] ?>mm ; background: whitesmoke;">
              <div class="row gap-1">
                <table>
                  <tr>
                    <td colspan="3">
                      <div class="col-12 d-flex justify-content-center p-2">
                        <div class="billpreviewlogo<?= $bill_data['print_paper_size'] ?>" style="background-image:url('<?= $bill_data['customize_bills_logo'] ?>');"></div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="col-12 d-flex justify-content-center">
                        <label class="contactNumber" id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                      </div>
                      <div class="col-12 d-flex justify-content-center ">
                        <label id="addresspreview" class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?></label>
                      </div>
                    </td>
                  </tr>
                </table>

                <div class="col-12">
                  <div class="row">
                    <div class="col-12" style="text-align: center;">
                      <span style="font-size: 10px;"><?= $currentDate ?> <?= $currentTime ?></span> <br>

                      <span><span class="fw-bolder" style="font-size: 10px;"><?= $user_name ?> NO - </span> <span class="invoiceNumber" id="invoiceNumber"><?= $invoiceNumber ?></span></span>
                    </div>
                  </div>
                </div>

                <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
              </div>
              <div class="printInvoiceData" id="printInvoiceData"> </div>
              <table>
                <tr style="font-weight: 600;">
                  <td>
                    <div class="col-12 pt-2">
                      <div class="row">
                        <div class="col-12 d-flex justify-content-center text-center">
                          <span id="billnotepreview" style="font-size:9px;"><?= $bill_data['bill_note'] ?></span>
                        </div>
                        <div class="col-12 d-flex justify-content-center">
                          <span>Thank You !</span>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
      <?php
        }
      }
      ?>
    </div>

    <!-- ========================================== -->

    <script>
      $(document).ready(function() {
        $("#barcodeInput").focus();
      });
    </script>
    <script>
      $(document).on('keyup', function(e) {
        if (e.which == 9) {
          var selector = document.getElementById('payment-method-selector');
          var enterAmountField = document.getElementById('cashAmount');
          if (selector.value === '3' && enterAmountField.value.trim() !== "") {



            var cardAmountField = document.getElementById('cardAmount');
            if (cardAmountField) {
              cardAmountField.focus();
              e.preventDefault();
            }
          } else {
            $(".cashAmount").focus();
          }
        }
      });

      // cash or card selector change
      document.addEventListener('DOMContentLoaded', function() {
        var selector = document.getElementById('payment-method-selector');

        document.addEventListener('keydown', function(event) {
          if (event.key === "ArrowDown") {
            moveSelectorDown(selector);
          } else if (event.key === "ArrowUp") {
            moveSelectorUp(selector);
          }
        });
      });

      function moveSelectorDown(selector) {
        var selectedIndex = selector.selectedIndex;
        if (selectedIndex < selector.options.length - 1) {
          selectedIndex++;
        }
        selector.selectedIndex = selectedIndex;
        var event = new Event('change');
        selector.dispatchEvent(event);
        event.preventDefault();
      }

      function moveSelectorUp(selector) {
        var selectedIndex = selector.selectedIndex;
        if (selectedIndex > 0) {
          selectedIndex--;
        }
        selector.selectedIndex = selectedIndex;
        var event = new Event('change');
        selector.dispatchEvent(event);
        event.preventDefault();
      }
    </script>
    <script src="dist/js/pos.js"></script>

  </div>
</body>

</html>