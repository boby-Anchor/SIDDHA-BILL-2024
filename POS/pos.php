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
  <title>Home | Inv 1</title>

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

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <?php include("part/navbar.php");
    ?>
    <!-- Navbar end -->

    <!-- Sidebar -->
    <?php include("part/sidebar.php"); ?>
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
                    <input type="text" class="form-control col-10" id="valueAddedServices" name="valueAddedServices"
                      placeholder="VAS" onkeyup="checkNetTotal()">
                  </div>

                  <!--id="packingChargesField" name="packingChargesField"-->
                  <div class="col-2 p-2 " id="packingChargesField">
                    <input type="text" class="form-control col-10" id="packingChargesField" name="packingChargesField"
                      placeholder="PC" onkeyup="checkNetTotal()">
                  </div>

                  <!--id="subTotal"-->
                  <div class="col-3   justify-content-end ">
                    <label class="subTotal" id="subTotal"></label>
                    <label class="subTotal">RS(ST) |</label>
                  </div>

                  <!--id="netTotal"  netTotal-->
                  <div class="col-3 text-right ">
                    <label class="subTotal" id="netTotal"></label>
                    <label class="subTotal">RS(NT)</label>
                  </div>

                  <div class="col-3 text-right ">
                    <label class="subTotal" id="paththuTotal"></label>
                    <label class="subTotal">RS(PT)</label>
                  </div>
                </div>
              </div>

              <div class="col-12 p-1" style="background: #000;">
                <div class="row" style="background: #000;">
                  <!-- discountPercentage -->
                  <div class="col-4 p-2 " id="discountField" style="color:#000 !important; background: #000;">
                    <input type="text" placeholder="Discount %" class="form-control col-8" id="discountPercentage"
                      name="discountPercentage" onkeyup="addDiscount()">
                  </div>

                  <!-- cashAmount -->
                  <div class="col-4 p-2 " id="cashAmountField">
                    <input type="text" placeholder="Enter Cash Amount" class="form-control col-10" id="cashAmount"
                      name="cashAmount" onkeyup="checkBalance(this)">
                  </div>

                  <!-- cardAmount -->
                  <div class="col-4 p-2  d-none" id="cardAmountField">
                    <input type="text" placeholder="Enter Card Amount" class="form-control col-10" id="cardAmount"
                      name="cardAmount" onkeyup="checkBalance(this)">
                  </div>
                </div>
              </div>

              <!--"payment-method-selector"-->
              <!--balance-->
              <!--checkoutBtn-->
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
                <div class="col-3 p-2">
                  <input type="text" id="patientName" name="patientName" class="form-control"
                    placeholder="Patient Name">
                </div>
                <div class="col-3 p-2">
                  <input type="text" id="contactNo" name="contactNo" class="form-control" placeholder="Contact No.">
                </div>
                <div id="doctorNameField" class="col-3 p-2">
                  <select class="form-control select2" id="doctorName" name="doctorName">
                    <option value="" disabled selected hidden>Select a doctor</option>
                    <?php
                    $doctors_rs = $conn->query("SELECT * FROM doctors ORDER BY name ASC");
                    while ($doctors_row = $doctors_rs->fetch_assoc()) {
                      ?>
                      <option value="<?= $doctors_row['name'] ?>">
                        <?= $doctors_row['name'] ?>
                      </option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div id="regNoField" class="col-3 p-2">
                  <input type="text" id="regNo" name="regNo" class="form-control" placeholder="REG No">
                </div>
              </div>
              <br>

              <div class="col-4 mb-2 p-2 d-none">
                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode..."
                  onchange="getBarcode2(this.value);">
              </div>
              <div class="col-4 mb-2 p-2">
                <div class="row">
                  <div class="col-6">
                    <input type="number" id="token" class="form-control" placeholder="Token">
                  </div>
                  <div class="col-6">
                    <select id="bill_status" class="form-control" onchange="updateBillStatus(value)">
                      <option value="0" selected hidden disabled>Select Status</option>
                      <option value="1">Billing</option>
                      <option value="2">Ready</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-4 mb-2 p-2">
                <select class="form-control" id="selectPrices" onchange="getBarcode3()"></select>
              </div>
              <div class="col-4 mb-2 p-2">
                <select class="form-control" name="selectBillType" id="selectBillType">
                  <?php
                  $bill_type_rs = $conn->query("SELECT * FROM bill_type");
                  while ($bill_type_row = $bill_type_rs->fetch_assoc()) {
                    ?>
                    <option value="<?= $bill_type_row['bill_type_id'] ?>">
                      <?= $bill_type_row['bill_type_name'] ?>
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
                    <tbody id="barcodeResults" class="overflow-y-auto" style="max-height: 50px !important;"></tbody>
                  </table>
                  <table class="table doctorMedicineResults">
                    <tbody id="doctorMedicineResults"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!--item Search List Right-->
        <div class="col-12 col-md-5">
          <div class="card-body bg-light">

            <div class="row">

              <!-- Company Product list -->
              <div class="col-12 bg-dark" style="height: 100vh; overflow:auto;">
                <!-- <div class="form-row input-group">
                  <button class="btn btn-success ">Paththu</button>
                  <input type="search" class="" name="search21" id="search21" onkeyup="searchProducts(); return false;" placeholder="Search...">
                </div> -->

                <!-- Search and paththu button -->
                <div class="input-group mt-3 form-group ">
                  <input type="search" class="form-control mx-1" name="search21" id="search21"
                    onkeyup="searchProducts(); return false;" placeholder="Search...">
                  <button class="btn btn-outline-success mx-1" data-toggle="modal"
                    data-target="#addPaththuModal">Paththu</button>
                  <button class="btn btn-outline-info mx-1" data-toggle="modal"
                    data-target="#doctorMedicineModal">Doctor Medicine</button>
                </div>

                <!-- products grid -->
                <div class="row productGrid" id="productGrid">
                  <?php
                  if (isset($_SESSION['store_id'])) {

                    $userLoginData = $_SESSION['store_id'];

                    foreach ($userLoginData as $userData) {
                      $shop_id = $userData['shop_id'];

                      $cm = runQuery("SELECT stock2.*, p_brand.name AS bName, p_medicine.code AS code, p_medicine.name AS name,
                      medicine_unit.unit AS unit , unit_category_variation.ucv_name
                      FROM stock2
                      INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
                      INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                      INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                      INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
                      WHERE stock2.stock_shop_id = '$shop_id'
                      AND stock2.stock_item_qty > 0
                      ORDER BY p_medicine.name ASC");

                      if (!empty($cm)) {
                        foreach ($cm as $v) {
                          ?>
                          <div class="col-md-4 col-sm-6 mt-3" onclick="getBarcode2('<?= $v['code']; ?>')">
                            <div class="product-grid h-100 rounded-lg">
                              <div class="product-content">
                                <div class="title"><?php echo $v['name']; ?>
                                  <br>
                                  <?= $v['code']; ?>
                                </div>
                                <div class="sub-title">
                                  <?php echo $v['bName']; ?>
                                </div>
                                <div class="f-size item-price">
                                  I:- RS
                                  <?php echo $v['item_s_price']; ?>
                                </div>
                                <div class="f-size unit-price">
                                  U:- RS
                                  <?php echo $v['unit_s_price']; ?>
                                </div>
                                <div class="f-size">
                                  (<?= $v['ucv_name'] ?><?php echo $v['unit']; ?>)</div>
                              </div>
                            </div>
                          </div>
                        <?php }
                      }
                    }
                  } ?>

                </div>
              </div>
              <!-- Company Product list end -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- confirm po modal end -->

    <!-- Paththu add -->
    <div class="container">
      <div class="modal" id="addPaththuModal" role="dialog">
        <div class="modal-dialog d-flex justify-content-between ">
          <div class="modal-content bg-dark align-items-center vw-100">
            <div class="modal-header">
              <h4 class="modal-title">Paththu</h4>
            </div>
            <div class="modal-body">
              <div class="row">

                <div class="col-12 mb-2">
                  <!-- paththu name -->
                  <div class="form-group">
                    <label for="paththuName" class="form-label">Name</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="paththuName">

                      <select class="form-control" id="paththuSelect" onchange="setPaththu(this)">
                        <option selected>Add...</option>
                        <option value="Agili Paththuwa">Agili Paththuwa</option>
                        <option value="Athata Paththuwa">Athata Paththuwa</option>
                        <option value="Badata Paththuwa">Badata Paththuwa</option>
                        <option value="Bellata Paththuwa">Bellata Paththuwa</option>
                        <option value="Danahisa Idiripasa Paththuwa">Danahisa Idiripasa Paththuwa</option>
                        <option value="Danahisa Pitupasa Paththuwa">Danahisa Pitupasa Paththuwa </option>
                        <option value="Danahisata Paththuwa">Danahisata Paththuwa</option>
                        <option value="Gaath Paththuwa">Gaath Paththuwa</option>
                        <option value="Kalawata Paththuwa">Kalawata Paththuwa</option>
                        <option value="Kakulata Paththuwa">Kakulata Paththuwa</option>
                        <option value="Kenda Paththuwa">Kenda Paththuwa</option>
                        <option value="Konda Pitupasata Paththuwa">Konda Pitupasata Paththuwa </option>
                        <option value="Pitata Paththuwa">Pitata Paththuwa</option>
                        <option value="Thattamata Paththuwa">Thattamata Paththuwa</option>
                        <option value="Urahisa Pitupasata Paththuwa">Urahisa Pitupasata Paththuwa</option>
                        <option value="Urahisata Paththuwa">Urahisata Paththuwa</option>
                        <option value="Walalukara Paththuwa">Walalukara Paththuwa</option>
                        <option value="Welamitata Paththuwa">Welamitata Paththuwa</option>
                        <option value="Wilubata Paththuwa">Wilubata Paththuwa</option>
                        <option value="Yatipathulata Paththuwa">Yatipathulata Paththuwa</option>
                      </select>

                    </div>
                  </div>
                  <!-- price input -->
                  <div class="form-group">
                    <label for="paththuPrice" class="form-label">Price</label>
                    <input type="number" class="form-control" id="paththuPrice" min="0" step="0.01"
                      oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                  </div>

                </div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success addPaththuBtn" onclick="addPaththu()">Add</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Paththu add end -->
    <!-- doctor medicine add -->
    <div class="container">
      <div class="modal" id="doctorMedicineModal" role="dialog">
        <div class="modal-dialog d-flex justify-content-between ">
          <div class="modal-content bg-dark align-items-center vw-100">
            <div class="modal-header">
              <h4 class="modal-title">Doctor Medicine</h4>
            </div>
            <div class="modal-body">
              <div class="row">

                <div class="col-12 mb-2">
                  <!-- medicine name -->
                  <div class="form-group">
                    <label for="doctorMedicineName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="doctorMedicineName">
                  </div>
                  <!-- price input -->
                  <div class="form-group">
                    <label for="doctorMedicinePrice" class="form-label">Price</label>
                    <input type="number" class="form-control" id="doctorMedicinePrice" min="0" step="0.01"
                      oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                  </div>

                </div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success addPaththuBtn" onclick="addDoctorMedicine()">Add</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- doctor medicine add end -->
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

          $bill_data_rs = $conn->query("SELECT shop.shopName AS shopName, customize_bills.*
          FROM `customize_bills`
          INNER JOIN shop ON shopId = customize_bills.`customize_bill_shop-id`
          WHERE `customize_bill_shop-id` = '$shop_id'
          ");
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
                          style="background-image:url('<?= $bill_data['customize_bills_logo'] ?>');">
                        </div>
                        <!-- <div class="text-center">
                          <label style="font-size: large; font-weight: 100;">
                            <h3>
                              <b>
                                <?php //echo $bill_data['shopName'] 
                                    ?>
                              </b>
                            </h3>
                          </label>
                        </div> -->
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="col-12 d-flex justify-content-center">
                        <label class="contactNumber"
                          id="contactNumberPreview"><?= $bill_data['customize_bills_mobile'] ?></label>
                      </div>
                      <div class="col-12 d-flex justify-content-center text-center center">
                        <label id="addresspreview"
                          class="address<?= $bill_data['print_paper_size'] ?>"><?= $bill_data['customize_bills_address'] ?>
                        </label>
                      </div>
                    </td>
                  </tr>
                </table>

                <div class="col-12">
                  <div class="row">
                    <div class="col-12" style="text-align: center;">
                      <span><span class="text-left" style="font-size: 10px;"><?= $currentDate ?>
                        </span><span class="text-right"> <?= $currentTime ?></span> </span>
                      <br>
                      <span><span class="invoicePatientName" id="invoicePatientName"></span> <span
                          id="InvoiceContactNumber"></span></span>
                      <br>
                      <span><span class="fw-bold"><?= $user_name ?> Inv.</span> <span class="fw-bolder"
                          style="font-size: 10px;" id="invoiceNumber"></span></span>
                    </div>
                  </div>
                </div>

                <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
              </div>
              <!-- table header start -->
              <div class="row">
                <div class="col-4">
                  <span class="product_cost">U.Price</span>
                </div>
                <div class="col-4 text-center">
                  <span class="product_qty">
                    QTY
                  </span>
                </div>
                <div class="col-4 text-center">
                  <span class="productTotal">Total</span>
                </div>
              </div>
              <!-- table header end -->
              <div class="printInvoiceData" id="printInvoiceData">

              </div>
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

                        <!-- Checked by box -->
                        <div class="col-12 d-flex justify-content-center">
                          <div class="check-by-box">
                            <center>
                              <label style="font-weight:bold; margin-bottom:3px;">Checked
                                By</label>
                            </center>

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

  </div>
</body>

<script src="dist/js/pos.js"></script>
<script src="dist/js/messageDisplay.js"> </script>

<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();

    //Initialize Select2 Elements
    // $(".select2bs4").select2({
    //   theme: "bootstrap4",
    // });

    // $('.medicine-unit-select').select2({
    //   placeholder: "Select medicine unit"
    // });
  });
</script>

</html>