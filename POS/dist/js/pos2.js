function getBarcode32() {
  var selectPrices2 = document.getElementById("selectPrices2").value;

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText;
      var barcodeResults2 = document.getElementById("barcodeResults2");
      var existingRows = barcodeResults2.querySelectorAll("tr[data-barcode]");
      console.log(existingRows);

      for (var i = 0; i < existingRows.length; i++) {
        var existingBarcode = existingRows[i].getAttribute("data-barcode");
        if (existingBarcode === selectPrices2) {
          alert("Product already added");
          document.getElementById("barcodeInput2").value = "";
          document.getElementById("barcodeInput2").focus();
          return;
        }
      }

      barcodeResults2.insertAdjacentHTML("beforeend", txt);

      document.getElementById("barcodeInput2").value = "";
      document.getElementById("barcodeInput2").focus();
      document.getElementById("selectPrices2").selectedIndex = -1;

      calculateSubTotal2();
    }
  };

  var url = "search_barcode32.php?barcode=" + encodeURIComponent(selectPrices2);

  req.open("GET", url, true);
  req.send();
  $(".checkoutBtn2").toggleClass(
    "d-flex",
    $("#barcodeResults2 tr").length >= 0
  );
  $(".checkoutBtn2").toggleClass("d-none", $("#barcodeResults2 tr").length < 0);
}

function getBarcode22(barcode) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText;
      document.getElementById("selectPrices2").innerHTML = txt;
    }
  };
  req.open("GET", "search_barcode22.php?barcode=" + barcode, true);
  req.send();
  $(".checkoutBtn2").toggleClass(
    "d-flex",
    $("#barcodeResults2 tr").length >= 0
  );
  $(".checkoutBtn2").toggleClass("d-none", $("#barcodeResults2 tr").length < 0);
}

// barcode reader
function getBarcode2(barcode, stock_s_price) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText;
      var barcodeResults2 = document.getElementById("barcodeResults2");
      var existingRows = barcodeResults2.querySelectorAll("tr[data-barcode]");

      for (var i = 0; i < existingRows.length; i++) {
        var existingBarcode = existingRows[i].getAttribute("data-barcode");
        if (existingBarcode === barcode + stock_s_price) {
          alert("Product already added");
          document.getElementById("barcodeInput2").value = "";
          document.getElementById("barcodeInput2").focus();
          return;
        }
      }

      barcodeResults2.insertAdjacentHTML("beforeend", txt);

      document.getElementById("barcodeInput2").value = "";
      document.getElementById("barcodeInput2").focus();
      calculateSubTotal2();
    }
  };

  var url =
    "search_barcode2.php?barcode=" +
    encodeURIComponent(barcode) +
    "&stock_s_price=" +
    encodeURIComponent(stock_s_price);

  req.open("GET", url, true);
  req.send();
  $(".checkoutBtn2").toggleClass(
    "d-flex",
    $("#barcodeResults2 tr").length >= 0
  );
  $(".checkoutBtn2").toggleClass("d-none", $("#barcodeResults2 tr").length < 0);
}

function testFunction2() {
  console.log("testing123");
}

// update total by qty
function updateTotal2(input) {
  var quantity = parseInt(input.value);
  var price = parseFloat(input.getAttribute("data-price"));
  var total = quantity * price;
  var row = input.closest("tr");
  var totalCell = row.querySelector(".total");
  totalCell.textContent = total.toFixed(2);
  calculateSubTotal2();
}

// qty update -
function decreaseQuantity2(button) {
  var input = button.parentElement.nextElementSibling.querySelector("input");
  var quantity = parseInt(input.value);
  if (quantity > 1) {
    quantity--;
    input.value = quantity;
    updateTotal2(input);
  }
}

// qty update +
function increaseQuantity2(button) {
  var input =
    button.parentElement.previousElementSibling.querySelector("input");
  var quantity = parseFloat(input.value);
  quantity++;
  input.value = quantity;
  updateTotal2(input);
}

// product remove
function removeRow2(button) {
  var row = button.closest("tr");
  row.remove();
  $(".checkoutBtn2").toggleClass(
    "d-none",
    $("#barcodeResults2 tr").length === 0
  );
  $(".checkoutBtn2").toggleClass("d-flex", $("#barcodeResults2 tr").length > 0);
  calculateSubTotal2();
}

// add discount
function addDiscount2() {
  var discountPercentage = document.getElementById("discountPercentage2").value;
  var productsAllTotal = parseFloat($("#subTotal2").text().replace(/,/g, ""));

  var discountedTotal = productsAllTotal * (1 - discountPercentage / 100);
  $("#netTotal2").text(discountedTotal.toLocaleString());
}

// netTotal calculation dislay
function checkNetTotal2() {
  var subTotal = parseFloat($("#subTotal2").text().replace(/,/g, ""));
  var billType = document.getElementById("selectBillType2");
  var paymentMethod = document.getElementById("payment-method-selector2");

  var discountPercentage = parseFloat(
    document.getElementById("discountAmount2").value
  );

  var deliveryCharges = parseFloat(
    document.getElementById("deliveryCharges2").value
  );

  var vas = parseFloat(document.getElementById("serviceChargeAmount2").value);

  var dc = parseFloat(document.getElementById("deliveryCharges2").value);

  if (billType.value === "2") {
    // online pay

    if (discountPercentage === null || isNaN(discountPercentage)) {
      // online no discount

      var netTotal = subTotal + vas + dc;
      $("#netTotal2").text(netTotal.toLocaleString());
    } else {
      // online with discount

      if (vas === null || isNaN(vas)) {
        // online with discount no vas
        var discountedTotal = subTotal * (1 - discountPercentage / 100);
        var netTotal = discountedTotal + dc;
        $("#netTotal2").text(netTotal.toLocaleString());
      } else {
        // online with discount and vas
        var discountedTotal = subTotal * (1 - discountPercentage / 100);
        var netTotal = discountedTotal + dc + vas;
        $("#netTotal2").text(netTotal.toLocaleString());
      }
    }
  }
}

// subTotal Calculation display
function calculateSubTotal2() {
  var balance = $("#balance2").text().replace(/,/g, "");
  var enterAmountFiled = $("#enterAmountFiled2").val();
  var invoiceNumber = $("#invoiceNumber2").text();
  var poArray = [];
  var inArray = [];

  var ajaxRequests = [];

  $(".barcodeResults2 tbody tr").each(function () {
    var product_name = $(this).find("#product_name2").text();
    var code = $(this).find("#code2").text();
    var product_cost = $(this).find("#product_price2").text();
    var product_qty = $(this).find("#qty2").val();
    var product_unit = $(this).find("#unit2").text();
    if (product_qty === "" || product_qty === "0") {
      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: "error",
        title: "Error: Product Quantity!",
      });
      $("#checkoutBtn2").removeAttr("data-toggle data-target");
    } else {
      var productData = {
        product_name: product_name,
        code: code,
        product_cost: product_cost,
        product_qty: product_qty,
        product_unit: product_unit,
        balance: balance,
        invoiceNumber: invoiceNumber,
      };

      poArray.push(productData);
      inArray.push(productData);
    }
  });

  $.when.apply($, ajaxRequests).then(function () {
    // Update subtotal
    var poAjaxRequest = $.ajax({
      url: "invoiceConfirmation2.php",
      method: "POST",
      data: {
        products: poArray,
      },
      success: function (response) {
        document.getElementById("subTotal2").innerHTML = response;
        addDiscount2();
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });

    ajaxRequests.push(poAjaxRequest);
    // Enable checkout button
    $("#checkoutBtn2").attr({
      "data-toggle": "modal",
      "data-target": "#confirmPO2",
    });
  });
}

function checkBalance2(input) {
  // Retrieve the value from the payment method selector
  var paymentMethod = document.getElementById("payment-method-selector").value;

  // Retrieve the total amount for the current invoice
  var totalAmount = parseFloat($("#netTotal").text().replace(/,/g, ""));

  // Initialize total entered amount based on the payment method
  var totalEnteredAmount = 0;

  // Determine how to calculate the total entered amount
  if (paymentMethod === "3") {
    // Special case handling if payment method is "3"
    var cashAmount = parseFloat($("#cashAmount").val()) || 0;
    var cardAmount = parseFloat($("#cardAmount").val()) || 0;
    totalEnteredAmount = cashAmount + cardAmount;
  } else {
    totalEnteredAmount = parseFloat(input.value) || 0;
  }

  // Calculate the balance
  var balance = totalEnteredAmount - totalAmount;

  // Update the balance display
  var balanceElement = $(".balance");
  balanceElement.text(
    balance.toLocaleString("en-US", { minimumFractionDigits: 2 })
  );

  // Update the balance display style based on the balance value
  if (balance > 0) {
    balanceElement.addClass("positive-balance").removeClass("negative-balance");
  } else if (balance < 0) {
    balanceElement.addClass("negative-balance").removeClass("positive-balance");
  } else {
    balanceElement.removeClass("positive-balance negative-balance");
  }

  // If Enter key is pressed, proceed with checkout if balance is non-negative
  if (event && event.which === 13) {
    event.preventDefault();
    if (balance >= 0) {
      checkout();
    }
  }
}

function getBarcode3() {
  var selectPrices = document.getElementById("selectPrices").value;

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
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

  var url = "search_barcode3.php?barcode=" + encodeURIComponent(selectPrices);
  req.open("GET", url, true);
  req.send();

  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length > 0);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length === 0);
}

function getBarcode2(barcode) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      var txt = req.responseText;
      document.getElementById("selectPrices").innerHTML = txt;
    }
  };
  req.open("GET", "search_barcode2.php?barcode=" + barcode, true);
  req.send();

  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length > 0);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length === 0);
}

function getBarcode(barcode, stock_s_price) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
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

  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length > 0);
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length === 0);
}

function updateTotal(input) {
  var quantity = parseInt(input.value);
  var price = parseFloat(input.getAttribute("data-price"));
  var total = quantity * price;
  var row = input.closest("tr");
  var totalCell = row.querySelector(".total");
  totalCell.textContent = total.toFixed(2);
  calculateSubTotal();
}

function decreaseQuantity(button) {
  var input = button.parentElement.nextElementSibling.querySelector("input");
  var quantity = parseInt(input.value);
  if (quantity > 1) {
    quantity--;
    input.value = quantity;
    updateTotal(input);
  }
}

function increaseQuantity(button) {
  var input =
    button.parentElement.previousElementSibling.querySelector("input");
  var quantity = parseFloat(input.value);
  quantity++;
  input.value = quantity;
  updateTotal(input);
}

function removeRow(button) {
  var row = button.closest("tr");
  row.remove();
  $(".checkoutBtn").toggleClass("d-none", $("#barcodeResults tr").length === 0);
  $(".checkoutBtn").toggleClass("d-flex", $("#barcodeResults tr").length > 0);
  calculateSubTotal();
}

function addDiscount() {
  var discountPercentage = document.getElementById("discountPercentage").value;
  var productsAllTotal = parseFloat($("#subTotal").text().replace(/,/g, ""));
  var discountedTotal = productsAllTotal * (1 - discountPercentage / 100);
  $("#netTotal").text(discountedTotal.toLocaleString());
}

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

  if (billType.value === "2") {
    // online pay
    var netTotal = subTotal + (vas || 0) + (deliveryCharges || 0);
    if (discountPercentage) {
      netTotal = netTotal - (netTotal * discountPercentage) / 100;
    }
    $("#netTotal").text(netTotal.toLocaleString());
  }
}

function calculateSubTotal() {
  var poArray = [];
  var inArray = [];

  $(".barcodeResults tbody tr").each(function () {
    var product_name = $(this).find("#product_name").text();
    var code = $(this).find("#code").text();
    var product_cost = $(this).find("#product_price").text();
    var product_qty = $(this).find("#qty").val();
    var product_unit = $(this).find("#unit").text();

    if (product_qty === "" || product_qty === "0") {
      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: "error",
        title: "Error: Product Quantity!",
      });
      $("#checkoutBtn").removeAttr("data-toggle data-target");
    } else {
      var productData = {
        product_name: product_name,
        code: code,
        product_cost: product_cost,
        product_qty: product_qty,
        product_unit: product_unit,
        invoiceNumber: $("#invoiceNumber").text(),
      };

      poArray.push(productData);
      inArray.push(productData);
    }
  });

  $.ajax({
    url: "invoiceConfirmation.php",
    method: "POST",
    data: { products: poArray },
    success: function (response) {
      document.getElementById("subTotal").innerHTML = response;
      addDiscount();
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
    },
  });

  $("#checkoutBtn").attr({
    "data-toggle": "modal",
    "data-target": "#confirmPO",
  });
}

function checkBalance(input) {
  var selector = document.getElementById("payment-method-selector");
  var totalEnteredAmount;
  var productsAllTotal = parseFloat($("#netTotal").text().replace(/,/g, ""));

  if (selector.value === "3") {
    // Specific handling for selector value 3
    var cashAmount = parseFloat($("#cashAmount").val());
    var cardAmount = parseFloat($("#cardAmount").val());
    totalEnteredAmount = cashAmount + cardAmount;
  } else {
    totalEnteredAmount = parseFloat(input.value);
  }

  var balance = totalEnteredAmount - productsAllTotal;
  $(".balance").text(balance.toLocaleString("en-US", {}));

  if (balance > 0) {
    $(".balance").addClass("positive-balance").removeClass("negative-balance");
  } else if (balance < 0) {
    $(".balance").addClass("negative-balance").removeClass("positive-balance");
  } else {
    $(".balance").removeClass("positive-balance negative-balance");
  }

  if (event.which === 13) {
    event.preventDefault();
    if (balance >= 0) {
      checkout();
    }
  }
}

function printInvoice() {
  var printWindow = window.open("", "_blank");
  printWindow.document.write("<html><head><title>Invoice</title>");
  printWindow.document.write("<style>");
  printWindow.document.write("/* Your styles here */");
  printWindow.document.write("</style>");
  printWindow.document.write("</head><body>");
  printWindow.document.write(document.getElementById("invoice-POS").innerHTML);
  printWindow.document.write("</body></html>");
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();

  printWindow.onafterprint = function () {
    printWindow.close();
  };
}
