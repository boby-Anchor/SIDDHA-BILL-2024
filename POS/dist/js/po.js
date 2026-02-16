
$(document).ready(function () {
  $("#barcodeInput").focus();
});

function setPoShopOnBill(selectElement) {
  $('#po_shop_on_bill').text(selectElement.options[selectElement.selectedIndex].text);
}

function searchProducts(searchInput) {
  if (searchInput !== "") {
    $.ajax({
      type: "POST",
      url: "actions/searchNameProductPo.php",
      data: {
        searchName: searchInput,
      },
      success: function (response) {
        $("#productGrid").html(response);
      },
    });
  } else {
    $("#productGrid").html("<h1 style='color: white;'>Search product name.</h1>");
  }
}

function getBarcode3() {
  var selectPrices = document.getElementById("selectPrices").value;

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText;
      var barcodeResults = document.getElementById("barcodeResults");
      var existingRows = barcodeResults.querySelectorAll("tr[data-barcode]");

      for (var i = 0; i < existingRows.length; i++) {
        var existingBarcode = existingRows[i].getAttribute("data-barcode");
        if (existingBarcode === selectPrices) {
          alert("Product already added");
          document.getElementById("barcodeInput").value = "";
          document.getElementById("barcodeInput").focus();
          return;
        }
      }

      barcodeResults.insertAdjacentHTML("beforeend", txt);

      document.getElementById("barcodeInput").value = "";
      document.getElementById("barcodeInput").focus();
      document.getElementById("selectPrices").selectedIndex = -1;

      calculateSubTotal();
    }
  };

  var url = "poSearchBarcode.php?barcode=" + encodeURIComponent(selectPrices);

  req.open("GET", url, true);
  req.send();
}

function getBarcode2(barcode) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText;
      document.getElementById("selectPrices").innerHTML = txt;
    }
  };
  req.open("GET", "search_barcode2.php?barcode=" + barcode, true);
  req.send();
}

// barcode reader
function getBarcode(barcode, stock_s_price) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText;
      var barcodeResults = document.getElementById("barcodeResults");
      var existingRows = barcodeResults.querySelectorAll("tr[data-barcode]");

      for (var i = 0; i < existingRows.length; i++) {
        var existingBarcode = existingRows[i].getAttribute("data-barcode");
        if (existingBarcode === barcode + stock_s_price) {
          alert("Product already added");
          document.getElementById("barcodeInput").value = "";
          document.getElementById("barcodeInput").focus();
          return;
        }
      }

      barcodeResults.insertAdjacentHTML("beforeend", txt);

      document.getElementById("barcodeInput").value = "";
      document.getElementById("barcodeInput").focus();
      calculateSubTotal();
    }
  };

  var url =
    "search_barcode.php?barcode=" + encodeURIComponent(barcode) + "&stock_s_price=" + encodeURIComponent(stock_s_price);

  req.open("GET", url, true);
  req.send();
}

// update total by qty
function updateTotal(input) {
  var quantity = parseInt(input.value);
  var price = parseFloat(input.getAttribute("data-price"));
  var total = quantity * price;
  var row = input.closest("tr");
  var totalCell = row.querySelector(".total");
  totalCell.textContent = total.toFixed(2);
  // alert(totalCell.textContent);
  calculateSubTotal();
}

// qty update -
function decreaseQuantity(button) {
  var input = button.parentElement.nextElementSibling.querySelector("input");
  var quantity = parseInt(input.value);
  if (quantity > 1) {
    quantity--;
    input.value = quantity;
    updateTotal(input);
    // calculateSubTotal();
  }
}

// qty update +
function increaseQuantity(button) {
  var input = button.parentElement.previousElementSibling.querySelector("input");
  var quantity = parseFloat(input.value);
  quantity++;
  input.value = quantity;
  updateTotal(input);
  // calculateSubTotal();
}

// product remove
function removeRow(button) {
  var row = button.closest("tr");
  row.remove();
  calculateSubTotal();
}

// add discount
function addDiscount() {
  var discountPercentage = document.getElementById("discountPercentage").value;
  var productsAllTotal = parseFloat($("#subTotal").text().replace(/,/g, ""));

  var discountedTotal = productsAllTotal * (1 - discountPercentage / 100);
  $("#netTotal").text(discountedTotal.toLocaleString());
}

// netTotal calculation display
function checkNetTotal() {
  var subTotal = parseFloat($("#subTotal").text().replace(/,/g, ""));
  var billType = document.getElementById("selectBillType");
  var paymentMethod = document.getElementById("payment-method-selector");

  var discountPercentage = parseFloat(document.getElementById("discountAmount").value);

  var deliveryCharges = parseFloat(document.getElementById("deliveryCharges").value);

  var vas = parseFloat(document.getElementById("serviceChargeAmount").value);

  var dc = parseFloat(document.getElementById("deliveryCharges").value);

  if (billType.value === "2") {
    // online pay

    if (discountPercentage === null || isNaN(discountPercentage)) {
      // online no discount

      var netTotal = subTotal + vas + dc;
      $("#netTotal").text(netTotal.toLocaleString().trim());
    } else {
      // online with discount

      if (vas === null || isNaN(vas)) {
        // online with discount no vas
        var discountedTotal = subTotal * (1 - discountPercentage / 100);
        var netTotal = discountedTotal + dc;
        $("#netTotal").text(netTotal.toLocaleString().trim());
      } else {
        // online with discount and vas
        var discountedTotal = subTotal * (1 - discountPercentage / 100);
        var netTotal = discountedTotal + dc + vas;
        $("#netTotal").text(netTotal.toLocaleString().trim());
      }
    }
  }
}

// subTotal Calculation display
function calculateSubTotal() {
  var productsAllTotal = 0;
  $(".barcodeResults tbody tr").each(function () {
    var product_name = $(this).find("#product_name").text().trim();
    var product_cost = $(this).find("#product_price").text().trim();
    var product_qty = $(this).find("#qty").val();

    if (product_qty === "" || product_qty === "0") {
      ErrorMessageDisplay(product_name + "invalid Quantity!");
    } else {
      var productTotal = product_cost * product_qty;
      productsAllTotal += productTotal;
      document.getElementById("subTotal").innerHTML = productsAllTotal;
      addDiscount();
    }
  });
}

// checkout
$("#checkoutBtn").on("click", function () {
  if ($(this).prop("disabled")) return;
  var po_shop_id = document.getElementById("po-shop-selector").value || null;

  disableCheckoutButton();
  if (po_shop_id == 0) {
    ErrorMessageDisplay("Shop එක select කරන්නේ නැද්ද?");
    enableCheckoutButton();
    return;
  }
  checkout(po_shop_id);
});

function checkout(po_shop_id) {
  var sub_total = $("#subTotal").text().trim() || null;
  var discount_percentage = $("#discountPercentage").val() || null;
  var net_total = $("#netTotal").text().replace(/,/g, "");

  var poArray = [];
  var billData = [];

  var bData = {
    po_shop_id: po_shop_id,
    sub_total: sub_total,
    discount_percentage: discount_percentage,
    net_total: net_total,
  }
  billData.push(bData);

  $("#barcodeResults tr").each(function () {
    var code = $(this).find("#code").text().trim();
    var ucv = $(this).find("#ucv").text().trim();
    var item_price = $(this).find("#item_price").text().trim();
    var unit_price = $(this).find("#unit_price").text().trim();
    var product_name = $(this).find("#product_name").text().trim();
    var brand = $(this).find("#brand").text().trim();
    var discount = $(this).find("#discount").text().trim();

    var product_cost = parseFloat($(this).find("#product_price").text().trim());
    var product_qty = parseInt($(this).find("#qty").val());
    var product_unit = $(this).find("#unit").text().trim();
    var product_total = parseFloat($(this).find("#totalprice").text().trim());

    // alert(product_unit);
    var productData = {
      code: code,
      ucv: ucv,
      item_price: item_price,
      unit_price: unit_price,
      product_name: product_name,
      brand: brand,
      discount: discount,
      product_cost: product_cost,
      product_qty: product_qty,
      product_unit: product_unit,
      product_total: product_total,
    };
    poArray.push(productData);
  });

  $.ajax({
    url: "actions/po/poBillConfirmationInsert.php",
    method: "POST",
    data: {
      billData: JSON.stringify(billData),
      products: JSON.stringify(poArray),
    },
    success: function (response) {
      var result = JSON.parse(response);
      $(".invoiceNumber").text(result.invoice_number);
      if (result.status === "success") {
        SuccessMessageDisplay(result.message);
        setDataToBill(billData, poArray)
      } else if (result.status === "session_expired") {
        handleExpiredSession(result.message);
        return;
      } else if (result.status === "error") {
        ErrorMessageDisplay(result.message);
        return;
      } else {
        ErrorMessageDisplay("PO insert failed. Check connection.");
        return;
      }
      enableCheckoutButton();
    },
    error: function (xhr, status, error) {
      ErrorMessageDisplay("Something went wrong! Check connection.");
      enableCheckoutButton();
    },
  });
}

function enableCheckoutButton() {
  $("#checkoutBtn").prop("disabled", false);
  $("#checkoutBtn").html('Checkout <i class="bi bi-arrow-right-circle-fill"></i>');
}

function disableCheckoutButton() {
  $("#checkoutBtn").prop("disabled", true);
  $("#checkoutBtn").text("Processing.....");
}

// Set data to bill
function setDataToBill(billData, poArray) {

  let productsAllTotal = 0;
  let discount_percentage = 0;

  if (Array.isArray(billData) && billData.length > 0) {
    discount_percentage = billData[0].discount_percentage || 0;
  }

  let html = "";

  // ===== Bill Header =====
  html += `
    <div class="col-12">
      <div class="row">
        <div class="col-3"><span class="product_cost">U.Price</span></div>
        <div class="col-3 text-center"><span class="product_qty">QTY</span></div>
        <div class="col-3 text-center"><span class="productTotal">D.%</span></div>
        <div class="col-3 text-center"><span class="productTotal">Total</span></div>
      </div>
    </div>
    `;

  // ===== Product Loop =====
  poArray.forEach(product => {
    const product_name = product.product_name || "";
    const product_brand = product.brand || "";
    const product_discount = product.discount || 0;
    const product_cost = parseFloat(product.product_cost) || 0;
    const product_qty = parseFloat(product.product_qty) || 0;

    const productTotal = product_cost * product_qty;
    productsAllTotal += productTotal;

    html += `
      <div class="col-12">
        <div class="row">
          <div class="col-6">
            <span class="product_name">${product_name}</span>
          </div>
          <div class="col-6">
            <span class="product_cost">${product_brand}</span>
          </div>
        </div>

        <div class="row">
          <div class="col-3">
            <span class="product_cost">${product_cost.toFixed(2)}</span>
          </div>
          <div class="col-3 text-center">
            <span class="product_qty">${product_qty}</span>
          </div>
          <div class="col-3 text-center">
            <span class="productTotal">${product_discount}</span>
          </div>
          <div class="col-3 text-center">
            <span class="productTotal">${productTotal.toFixed(2)}</span>
          </div>
        </div>
      </div>
      `;
  });

  let net_total = productsAllTotal;

  if (discount_percentage != 0) {
    net_total = productsAllTotal * (1 - discount_percentage / 100);
  }

  html += `
    <div class="col-12">
      <div class="row">
        <div>
          <div class="col-12 d-flex justify-content-end pt-2" 
               style="border-top: #0e0e0e 0.2rem solid;">
            <span class="productsAllTotal">
              Sub total : ${productsAllTotal.toFixed(2)}
            </span>
          </div>
        </div>

        <div class="col-12 d-flex justify-content-end pt-2">
          <span class="discount">
            Discount : ${discount_percentage}%
          </span>
        </div>

        <div class="col-12 d-flex justify-content-end pt-2">
          <span class="netTotal">
            Net Total : ${net_total.toFixed(2)}
          </span>
        </div>
      </div>

      <div class="col-12 d-flex justify-content-end pt-2"
            style="border-top: #0e0e0e 0.2rem solid;">
      </div>
    </div>
    `;

  document.getElementById("printInvoiceData").innerHTML = html;
  printPOBill();
}

// invoice print
function printPOBill() {
  var printWindow = window.open("", "_blank");
  printWindow.document.write("<html><head><title>Invoice</title>");

  function loadContent() {
    printWindow.document.write("<style>");
    printWindow.document.write(
      "\
        span {\
          font-size: 10px;\
          font-weight:bold;\
        }\
        .paperSize48 {\
          background-color: whitesmoke;\
          width: 48mm;\
        }\
        .billpreviewlogo48 {\
          height: 20px;\
          width: 120px;\
          background-position: center;\
          background-repeat: no-repeat;\
          background-size: contain;\
        }\
        .address48,\
        .datetime48,\
        .agent48 {\
          font-size: small;\
          font-weight: bold;\
        }\
        .productTable48 {\
          font-size: small;\
        }\
        .paperSize58 {\
          background-color: whitesmoke;\
          width: 58mm;\
        }\
        .billpreviewlogo {\
          height: 70px;\
          width: 120px;\
          background-position: center;\
          background-repeat: no-repeat;\
          background-size: contain;\
        }\
        .address58{\
          max-width: 130px;\
        }\
        .address58,\
        .datetime58,\
        .agent58 {\
          font-size: small;\
          font-weight: bold;\
        }\
        .productTable58 {\
          font-size: small;\
        }\
        .billpreviewlogo80 {\
          height: 100px;\
          width: 160px;\
          background-position: center;\
          background-repeat: no-repeat;\
          background-size: cover;\
        }\
        .paperSize80 {\
          background-color: whitesmoke;\
          width: 80mm;\
        }\
        .contactNumber{\
          font-size:medium;\
          font-weight: bold;\
        }\
      "
    );
    printWindow.document.write("</style>");

    printWindow.document.write("</head><body>");
    printWindow.document.write(document.getElementById("invoice-POS").innerHTML);
    printWindow.document.write("</body></html>");
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
  }

  function stylesheetLoaded() {
    if (++loadedStylesheets === totalStylesheets) {
      loadContent();
    }
  }

  var totalStylesheets = 1;
  var loadedStylesheets = 0;

  var bootstrapLink = printWindow.document.createElement("link");
  bootstrapLink.rel = "stylesheet";
  bootstrapLink.href = "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css";
  bootstrapLink.onload = stylesheetLoaded;
  printWindow.document.head.appendChild(bootstrapLink);

  if (totalStylesheets === 0) {
    loadContent();
  }

  // After printing, reload the pos.php file
  printWindow.onafterprint = function () {
    printWindow.close(); // Close the print window
    window.location.reload();
    // Reload the pos.php file in the main window
  };
}