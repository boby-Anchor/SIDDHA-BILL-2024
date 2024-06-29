<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  include ('config/db.php');
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
  <?php include ("part/data-table-css.php"); ?>
  <!-- Data Table CSS end -->
  <!-- All CSS -->
  <?php include ("part/all-css.php"); ?>
  <!-- All CSS end -->

  <!-- bootstrap icon link -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="dist/css/customize_bill.css">

</head>

<body class="hold-transition sidebar-mini layout-fixed" onload="focus_filed();">
  <div class="wrapper">

    <!-- Navbar -->
    <?php include ("part/navbar.php"); ?>
    <!-- Navbar end -->

    <!-- Sidebar -->
    <?php include ("part/sidebar.php"); ?>
    <!--  Sidebar end -->

    <div class="content-wrapper bg-dark">

      <div class="row w-100">

        <div class="col-12 col-md-7">
            
            <!--amount-->
          <div class="col-12 total_div">
               
                <div class="row">
                    
        <div class="col-12 p-1" style="background: #000;">
                <div class="row">
                
                    <!--id="deliveryCharges" amountDiv-->
              <div class="col-2 p-2 " id="deliveryChargesField">
                <input type="text" placeholder="DC" class="form-control col-10" id="deliveryCharges"
                  name="deliveryCharges" onkeyup="checkNetTotal()">
              </div>
              
                    <!--id="valueAddedServices" name="valueAddedServices"-->
              <div class="col-2 p-2 " id="ServiceChargesField">
                <input type="text" class="form-control col-10" id="valueAddedServices" name="valueAddedServices"  placeholder="VAS"
                  onkeyup="checkNetTotal()">
              </div>
              
                    <!--id="packingChargesField" name="packingChargesField"-->
              <div class="col-2 p-2 " id="packingChargesField">
                <input type="text" class="form-control col-10" id="packingChargesField" name="packingChargesField"  placeholder="PC"
                  onkeyup="checkNetTotal()">
              </div>
           
           
                  <!--id="subTotal"-->
              <div class="col-3   justify-content-end ">
                <label class="subTotal" id="subTotal"></label>
                <label class="subTotal">RS(ST)  |</label>
              </div>
              
               <!--id="netTotal"  netTotal--> 
              <div class="col-3 text-right ">
                <label class="subTotal" id="netTotal"></label>
                <label class="subTotal">RS(NT)</label>
              </div>
                </div>
        </div>
        
        <div class="col-12 p-1" style="background: #000;">
                <div class="row" style="background: #000;">
                   <!--id="discountPercentage"-->
              <div class="col-4 p-2 " id="discountField"  style="color:#000 !important; background: #000;">
                <input type="text" placeholder="Discount %" class="form-control col-8" id="discountPercentage"
                  name="discountPercentage" onkeyup="addDiscount()">
              </div>
              
                    <!--id="cashAmount"-->
              <div class="col-4 p-2 " id="cashAmountField">
                <input type="text" placeholder="Enter Cash Amount" class="form-control col-10" id="cashAmount"
                  name="cashAmount" onkeyup="checkBalance(this)">
              </div>
              
                    <!--id="cardAmount"-->
              <div class="col-4 p-2  d-none" id="cardAmountField">
                <input type="text" placeholder="Enter Card Amount" class="form-control col-10" id="cardAmount"
                  name="cardAmount" onkeyup="checkBalance(this)">
              </div>
                    </div>
        </div>
             
        
                    <!--"payment-method-selector"--> <!--balance--> <!--checkoutBtn-->
             <div class="col-12 " style="background: #0000004a;">
                <div class="row">
                        <!--class="balance" id="balance"-->
                  <div class="col-6">
                        <!--class="balance" id="balance"-->
                    <div class="col-12">
                      <label class="balance" id="balance">000</label>
                    </div>
                  </div>
                        <!--name="payment-method-selector" id="payment-method-selector" class="payment-method-selector"-->
                  <div class="col-6 d-flex justify-content-end align-items-center">
                    <select name="payment-method-selector" id="payment-method-selector" class="payment-method-selector">
                      <?php
                      $payment_type_rs = $conn->query("SELECT * FROM payment_type");
                      while ($payment_type_row = $payment_type_rs->fetch_assoc()) {
                        ?>
                        <option value="<?= $payment_type_row['payment_type_id'] ?>">
                          <?= $payment_type_row['payment_type'] ?>
                        </option>
                        <?php
                      }
                      ?>
                    </select>
                            <!--id="checkoutBtn"-->
                    <button class="btn check-outBtn col-6" id="checkoutBtn" onclick="checkBalance()">Checkout <i
                        class="bi bi-arrow-right-circle-fill"></i></button>
                  </div>

                </div>
              </div>
              
            </div>
          </div>
            <!--top-->
          <div class="col-12">
            <div class="row">
              <div class="d-flex justify-content-evenly">
                <div class="p-2 p-x-2">
                  <input type="text" id="patientName" name="patientName" class="form-control"
                    placeholder="Patient Name">
                </div>
                <div class="p-2 p-x-2">
                  <input type="text" id="contactNo" name="contactNo" class="form-control" placeholder="Contact No.">
                </div>
                <div id="doctorNameField" class="p-2 p-x-2">
                  <input type="text" id="doctorName" name="doctorName" class="form-control" placeholder="Doctor Name">
                </div>
                <div id="regNoField" class="p-2 p-x-2">
                  <input type="text" id="regNo" name="regNo" class="form-control" placeholder="REG No">
                </div>
              </div>
              <br>

              <div class="col-4 mb-2 p-2 p-x-2">
                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode..."
                  onchange="getBarcode2(this.value);">
              </div>
              <div class="col-4 mb-2 p-2 p-x-2">
                <select class="form-control" name="" id="selectPrices" onchange="getBarcode3()"></select>
              </div>
              <div class="col-4 mb-2 p-2 p-x-2">
                <select class="form-control" name="selectBillType" id="selectBillType">
                  <?php
                  $bill_type_rs = $conn->query("SELECT * FROM bill_type");
                  while ($bill_type_row = $bill_type_rs->fetch_assoc()) {
                    ?>
                    <option value="<?= $bill_type_row['bill_type_id'] ?>"><?= $bill_type_row['bill_type_name'] ?>
                    </option>
                    <?php
                  }
                  ?>
                </select>
              </div>
              <!--auto-->
              <div class="col-12" style="height: 40vh; overflow:auto;">
                <div>
                  <table class="table barcodeResults">
                    <tbody id="barcodeResults"></tbody>
                  </table>
                </div>
                
              </div> 
             
              
            </div>
          </div>
          
        </div>
        
            <!--item Search List Right-->
        <div class="col-12 col-md-5">
          <div class="card-body h-100 bg-light overflow-hidden">

            <div class="row">

              <!-- Company category end -->

              <!-- Company Product list -->
              <div class="col-12" style="height: 100vh; overflow:auto; background-color: #0e0e0e;">
                <form action="" method="post" onkeyup="searchProducts(); return false;">
                  <!-- Added method attribute -->
                  <input type="search" class="form-control" name="search21" id="search21" placeholder="Search...">
                </form>
                <div class="row" id="productGrid" class="productGrid">
                  <?php
                  if (isset($_SESSION['store_id'])) {

                    $userLoginData = $_SESSION['store_id'];

                    foreach ($userLoginData as $userData) {
                      $shop_id = $userData['shop_id'];

                      $cm = runQuery("SELECT stock2.*, p_brand.name AS bName, p_medicine.code AS code, p_medicine.name AS name ,
                        medicine_unit.unit AS unit , unit_category_variation.ucv_name 
                      FROM stock2
                      INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
                      INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                      INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                      INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                      WHERE stock2.stock_shop_id = '$shop_id' AND stock2.stock_item_qty > 0 ORDER BY p_medicine.name ASC");

                      if (!empty($cm)) {
                        foreach ($cm as $v) {
                          ?>
                          <div class="col-md-4 col-sm-6 mt-3" onclick="getBarcode2('<?= $v['code']; ?>')">
                            <div class="product-grid h-100">
                              <!-- <div class="product-image">
                                <a href="#" class="image">
                                  <img src="dist/img/product/
                                  <?php #echo $v['img']; 
                                          ?> -->
                              <!-- " width="50" alt="Image"> -->
                              <!-- </a> -->
                              <!-- </div> -->
                              <div class="product-content">
                                <div class="name" style="color: #fff;"><?php echo $v['name']; ?> <br> <?= $v['code']; ?></div>
                                <div class="name" style="color: #f67019; font-size:20px;"><?php echo $v['bName']; ?></div>
                                <div class="price" style="color: #3dce12;">I:- RS <?php echo $v['item_s_price']; ?></div>
                                <div class="price" style="color: #d8f13b;">U:- RS <?php echo $v['unit_s_price']; ?></div>
                                <div class="price" style="color: #fff;">(<?= $v['ucv_name'] ?><?php echo $v['unit']; ?>)</div>
                              </div>
                            </div>
                          </div>
                        <?php }
                      }
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
                        success: function (response) {
                          $('#productGrid').html(response);
                          //console.log(response)
                        },

                      });
                    }
                  }

                  //payment type online select //
                  document.getElementById('selectBillType').addEventListener('change', function () {
                    var selectedValue = this.value;

                    var discountPercentageElement = document.getElementById('discountField');
                    var deliveryChargesElement = document.getElementById('deliveryChargesField');
                    var serviceChargesElement = document.getElementById('ServiceChargesField');
                    var packingChargesElement = document.getElementById('packingChargesField');

                    discountPercentageElement.classList.add('d-none');
                    deliveryChargesElement.classList.add('d-none');
                    serviceChargesElement.classList.add('d-none');
                    packingChargesElement.classList.add('d-none');

                    switch (selectedValue) {
                      case "1":
                        discountPercentageElement.classList.remove('d-none');
                        break;

                      case "2":
                        discountPercentageElement.classList.remove('d-none');
                        deliveryChargesElement.classList.remove('d-none');
                        serviceChargesElement.classList.remove('d-none');
                        break;

                      case "3":
                        discountPercentageElement.classList.remove('d-none');
                        break;

                      case "4":
                        deliveryChargesElement.classList.remove('d-none');
                        packingChargesElement.classList.remove('d-none');
                        break;
                    }
                  });

                  // if select cash + card //
                  document.getElementById('payment-method-selector').addEventListener('change', function () {

                    var selectedValue = this.value;
                    var cashAmountField = document.getElementById('cashAmountField');
                    var cardAmountField = document.getElementById('cardAmountField');

                    cashAmountField.classList.add('d-none');
                    cardAmountField.classList.add('d-none');

                    switch (selectedValue) {
                      case "1":
                        console.log(selectedValue);
                        cashAmountField.classList.remove('d-none')
                        break;

                      case "2":
                        console.log(selectedValue);
                        cardAmountField.classList.remove('d-none');
                        break;

                      case "3":
                        console.log(selectedValue);
                        cardAmountField.classList.remove('d-none');
                        cashAmountField.classList.remove('d-none');
                        break;
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
    <?php include ("part/footer.php"); ?>
    <!-- Footer End -->
    <!-- Alert -->
    <?php include ("part/alert.php"); ?>
    <!-- Alert end -->
    <!-- All JS -->
    <?php include ("part/all-js.php"); ?>
    <!-- All JS end -->
    <!-- Data Table JS -->
    <?php include ("part/data-table-js.php"); ?>
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
          $user_name = $userData['name'];

          $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'invoices'");
          $invoiceId_row = $invoiceId_rs->fetch_assoc();
          $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
          $invoiceNumber = "000" . $userId . $shop_id . $invoiceId;

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
                        <div class="billpreviewlogo<?= $bill_data['print_paper_size'] ?>"
                          style="background-image:url('<?= $bill_data['customize_bills_logo'] ?>');"></div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="col-12 d-flex justify-content-center">
                        <label class="contactNumber"
                          id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                      </div>
                      <div class="col-12 d-flex justify-content-center center">
                       <center>
                           <label id="addresspreview"
                          class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                          </label>
                       </center> 
                      </div>
                    </td>
                  </tr>
                </table>

                <div class="col-12">
                  <div class="row">
                    <div class="col-12" style="text-align: center;">
                      <span style="font-size: 10px;"><?= $currentDate ?>     <?= $currentTime ?></span> <br>

                      <span><span class="fw-bolder" style="font-size: 10px;"><?= $user_name ?> NO - </span> <span
                          class="invoiceNumber" id="invoiceNumber"><?= $invoiceNumber ?></span></span>
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
                        
                        
                         
                         <div class="col-12 d-flex justify-content-center">
                             <div class="check-by-box">
    <center><label style="font-wight:bold; marging-bottom:3px;">Check By</label></center>

    <label for="date">Date: <?= $currentDate ?><?= $currentTime ?></label>
   
    <label for="emp-no">EMP No:.............................</label>
    
    <label for="signature">Signature:..........................</label>
</div>

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
      $(document).ready(function () {
        $("#barcodeInput").focus();
      });
    </script>


    <script>
      $(document).on('keyup', function (e) {
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


      // <?php

      //   if (isset($_SESSION['store_id'])) {
      
      //     $userLoginData = $_SESSION['store_id'];
      
      //     foreach ($userLoginData as $userData) {
      //       $shop_id = $userData['shop_id'];
      
      //       $conn->query("INSERT INTO test (id, name1, name2) VALUES ('$shop_id','$shop_id','test1')");
      //       echo '<script> setFields();</script>';
      
      //       if ($shop_id == '2') {
      //         echo '<script> setFields();</script>';
      //       }
      

      //     }
      //   }
      
      //   ?>


      
      document.addEventListener('DOMContentLoaded', function () {

        var doctorNameField = document.getElementById('doctorNameField');
        var regNoField = document.getElementById('regNoField');


        
        // cash or card selector change
        var selector = document.getElementById('payment-method-selector');
        var billTypeSelector = document.getElementById('selectBillType');

        billTypeSelector.selectedIndex = 0;

        var event = new Event('change');
        billTypeSelector.dispatchEvent(event);
        event.preventDefault();

        document.addEventListener('keydown', function (event) {
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