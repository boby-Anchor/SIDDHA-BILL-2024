$(document).ready(function () {
  $("#barcodeInput").focus();
});

function searchProducts() {
  var searchInput = document.getElementById("search21").value.trim();
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
      console.log(existingRows);

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

      // calculateSubTotal();
      calculateSubTotal();
    }
  };

  var url = "poSearchBarcode.php?barcode=" + encodeURIComponent(selectPrices);

  req.open("GET", url, true);
  req.send();
  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length >= 0);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length < 0);
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
  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length >= 0);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length < 0);
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
    "search_barcode.php?barcode=" +
    encodeURIComponent(barcode) +
    "&stock_s_price=" +
    encodeURIComponent(stock_s_price);

  req.open("GET", url, true);
  req.send();
  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length >= 0);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length < 0);
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
  var input =
    button.parentElement.previousElementSibling.querySelector("input");
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
  // alert($(".table tbody tr").length);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length === 0);
  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length > 0);
  calculateSubTotal();
}

// add discount
function addDiscount() {
  var discountPercentage = document.getElementById("discountPercentage").value;
  var productsAllTotal = parseFloat($("#subTotal").text().replace(/,/g, ""));

  var discountedTotal = productsAllTotal * (1 - discountPercentage / 100);
  $("#netTotal").text(discountedTotal.toLocaleString());
}

// netTotal calculation dislay
function checkNetTotal() {
  var subTotal = parseFloat($("#subTotal").text().replace(/,/g, ""));
  var billType = document.getElementById("selectBillType");
  var paymentMethod = document.getElementById("payment-method-selector");

  var discountPercentage = parseFloat(
    document.getElementById("discountAmount").value
  );

  var deliveryCharges = parseFloat(
    document.getElementById("deliveryCharges").value
  );

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
    var product_name = $(this).find("#product_name").text();
    var product_cost = $(this).find("#product_price").text();
    var product_qty = $(this).find("#qty").val();

    if (product_qty === "" || product_qty === "0") {
      ErrorMessageDisplay(product_name + "invalid Quantity!");
      $("#checkoutBtn").removeAttr("data-toggle data-target");
    } else {
      var productTotal = product_cost * product_qty;
      productsAllTotal += productTotal;
      document.getElementById("subTotal").innerHTML = productsAllTotal;
      addDiscount();
    }
  });
}

// invoice print
function printInvoice() {
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
    printWindow.document.write(
      document.getElementById("invoice-POS").innerHTML
    );
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
  bootstrapLink.href =
    "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css";
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

// checkout
function checkout() {
  var po_shop_id = document.getElementById("po-shop-selector").value;
  var invoice_number = $(".invoiceNumber").text();
  var sub_total = $("#subTotal").text().trim();
  var discount_percentage = $("#discountPercentage").val();
  var net_total = $("#netTotal").text().replace(/,/g, "");

  if (po_shop_id == 0) {
    ErrorMessageDisplay("Shop එක select කරන්නේ නැද්ද?");
    return;
  }

  var poArray = [];
  var inArray = [];

  $("#barcodeResults tr").each(function () {
    var code = $(this).find("#code").text();
    var ucv = $(this).find("#ucv").text();
    var item_price = $(this).find("#item_price").text();
    var unit_price = $(this).find("#unit_price").text();
    var product_name = $(this).find("#product_name").text();
    var brand = $(this).find("#brand").text();
    var discount = $(this).find("#discount").text();

    var product_cost = parseFloat($(this).find("#product_price").text());
    var product_qty = parseInt($(this).find("#qty").val());
    var product_unit = $(this).find("#unit").text();
    var product_total = parseFloat($(this).find("#totalprice").text());

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
      invoice_number: invoice_number,

      po_shop_id: po_shop_id,
      sub_total: sub_total,
      discount_percentage: discount_percentage,
      net_total: net_total,
    };
    poArray.push(productData);
    inArray.push(productData);
  });

  $.ajax({
    url: "poBillConfirmationInsert.php",
    method: "POST",
    data: {
      products: JSON.stringify(poArray),
    },
    success: function (response) {
      SuccessMessageDisplay("Success: Order Placed Successfully!");
      $(".confirmPObtn").prop("disabled", false);

      $.ajax({
        url: "poPrintAddData.php",
        method: "POST",
        data: {
          products: inArray,
        },
        success: function (response) {
          document.getElementById("printInvoiceData").innerHTML = response;
          printInvoice();
        },
        error: function (xhr, status, error) {
          ErrorMessageDisplay("Bill print error!");
        },
      });
    },
    error: function (xhr, status, error) {
      ErrorMessageDisplay("Something went wrong!");
    },
  });
}
