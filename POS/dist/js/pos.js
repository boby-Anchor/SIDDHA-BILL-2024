$(document).ready(function () {
  $("#barcodeInput").focus();
});

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
      calculateSubTotal();
    }
  };

  var url = "search_barcode3.php?barcode=" + encodeURIComponent(selectPrices);

  req.open("GET", url, true);
  req.send();
  // alert($("#barcodeResults tr").length);
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
  // alert($("#barcodeResults tr").length);
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

function searchProducts() {
  var searchInput = document.getElementById("search21").value.trim();
  if (searchInput !== "") {
    $.ajax({
      type: "POST",
      url: "actions/searchNameProductPos.php",
      data: {
        searchName: searchInput,
      },
      success: function (response) {
        $("#productGrid").html(response);
      },
    });
  }
}

//payment type online select //
document
  .getElementById("selectBillType")
  .addEventListener("change", function () {
    var selectedValue = this.value;

    var discountPercentageElement = document.getElementById("discountField");
    var deliveryChargesElement = document.getElementById(
      "deliveryChargesField"
    );
    var serviceChargesElement = document.getElementById("ServiceChargesField");
    var packingChargesElement = document.getElementById("packingChargesField");

    discountPercentageElement.classList.add("d-none");
    deliveryChargesElement.classList.add("d-none");
    serviceChargesElement.classList.add("d-none");
    packingChargesElement.classList.add("d-none");

    switch (selectedValue) {
      case "1":
        discountPercentageElement.classList.remove("d-none");
        break;

      case "2":
        discountPercentageElement.classList.remove("d-none");
        deliveryChargesElement.classList.remove("d-none");
        serviceChargesElement.classList.remove("d-none");
        break;

      case "3":
        discountPercentageElement.classList.remove("d-none");
        break;

      case "4":
        deliveryChargesElement.classList.remove("d-none");
        packingChargesElement.classList.remove("d-none");
        break;
    }
  });

// if select cash + card //
document
  .getElementById("payment-method-selector")
  .addEventListener("change", function () {
    var selectedValue = this.value;
    var cashAmountField = document.getElementById("cashAmountField");
    var cardAmountField = document.getElementById("cardAmountField");

    cashAmountField.classList.add("d-none");
    cardAmountField.classList.add("d-none");

    switch (selectedValue) {
      case "1":
        cashAmountField.classList.remove("d-none");
        break;

      case "2":
        cardAmountField.classList.remove("d-none");
        break;

      case "3":
        cardAmountField.classList.remove("d-none");
        cashAmountField.classList.remove("d-none");
        break;
    }
  });

$(document).on("keyup", function (e) {
  if (e.which == 9) {
    var selector = document.getElementById("payment-method-selector");
    var enterAmountField = document.getElementById("cashAmount");
    if (selector.value === "3" && enterAmountField.value.trim() !== "") {
      var cardAmountField = document.getElementById("cardAmount");
      if (cardAmountField) {
        cardAmountField.focus();
        e.preventDefault();
      }
    } else {
      $(".cashAmount").focus();
    }
  }
});

document.addEventListener("DOMContentLoaded", function () {
  var doctorNameField = document.getElementById("doctorNameField");
  var regNoField = document.getElementById("regNoField");

  // cash or card selector change
  var selector = document.getElementById("payment-method-selector");
  var billTypeSelector = document.getElementById("selectBillType");

  billTypeSelector.selectedIndex = 0;

  var event = new Event("change");
  billTypeSelector.dispatchEvent(event);
  event.preventDefault();

  document.addEventListener("keydown", function (event) {
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
  var event = new Event("change");
  selector.dispatchEvent(event);
  event.preventDefault();
}

function moveSelectorUp(selector) {
  var selectedIndex = selector.selectedIndex;
  if (selectedIndex > 0) {
    selectedIndex--;
  }
  selector.selectedIndex = selectedIndex;
  var event = new Event("change");
  selector.dispatchEvent(event);
  event.preventDefault();
}

// update total by qty
function updateTotal(input) {
  var quantity = parseInt(input.value);
  var price = parseFloat(input.getAttribute("data-price"));
  var total = quantity * price;
  var row = input.closest("tr");
  var totalCell = row.querySelector(".total");
  totalCell.textContent = total.toFixed(2);
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
      $("#netTotal").text(netTotal.toLocaleString());
    } else {
      // online with discount

      if (vas === null || isNaN(vas)) {
        // online with discount no vas
        var discountedTotal = subTotal * (1 - discountPercentage / 100);
        var netTotal = discountedTotal + dc;
        $("#netTotal").text(netTotal.toLocaleString());
      } else {
        // online with discount and vas
        var discountedTotal = subTotal * (1 - discountPercentage / 100);
        var netTotal = discountedTotal + dc + vas;
        $("#netTotal").text(netTotal.toLocaleString());
      }
    }
  }
}

// subTotal Calculation display
function calculateSubTotal() {
  var productsAllTotal = 0;

  var ajaxRequests = [];

  $(".barcodeResults tbody tr").each(function () {
    var product_cost = $(this).find("#product_price").text();
    var product_qty = $(this).find("#qty").val();

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
      var productTotal = product_cost * product_qty;
      productsAllTotal += productTotal;
      document.getElementById("subTotal").innerHTML = productsAllTotal;
      addDiscount();
    }
  });
}

function checkBalance(input) {
  var selector = document.getElementById("payment-method-selector");

  // Check if selector value is '3'
  if (selector.value === "3") {
    var cashAmount = parseFloat($("#cashAmount").val());
    var cardAmount = parseFloat($("#cardAmount").val());

    // Calculate the total entered amount by summing both input fields
    var totalEnteredAmount = cashAmount + cardAmount;

    var productsAllTotal = parseFloat($("#netTotal").text().replace(/,/g, ""));

    var balance = totalEnteredAmount - productsAllTotal;
    var formattedBalance = balance.toLocaleString("en-US", {});

    $(".balance").text(formattedBalance);

    if (balance > 0) {
      $(".balance")
        .addClass("positive-balance")
        .removeClass("negative-balance");
      if (event.which == 13) {
        event.preventDefault();
        var balance = parseFloat($("#balance").text().replace(/,/g, ""));
        if (balance >= 0) {
          checkout();
        }
      }
    } else if (balance < 0) {
      $(".balance")
        .addClass("negative-balance")
        .removeClass("positive-balance");
    } else {
      $(".balance").removeClass("positive-balance negative-balance");
      if (event.which == 13) {
        event.preventDefault();
        var balance = parseFloat($("#balance").text().replace(/,/g, ""));
        if (balance >= 0) {
          checkout();
        }
      }
    }
  } else {
    var enteredAmount = parseFloat(input.value);
    var discountedTotal = parseFloat($("#netTotal").text().replace(/,/g, ""));
    balance = enteredAmount - discountedTotal;

    var formattedBalance = balance.toLocaleString("en-US", {});

    $(".balance").text(formattedBalance);

    if (balance > 0) {
      $(".balance")
        .addClass("positive-balance")
        .removeClass("negative-balance");
      if (event.which == 13) {
        event.preventDefault();
        var balance = parseFloat($("#balance").text().replace(/,/g, ""));
        if (balance >= 0) {
          checkout();
        }
      }
    } else if (balance < 0) {
      $(".balance")
        .addClass("negative-balance")
        .removeClass("positive-balance");
    } else {
      $(".balance").removeClass("positive-balance negative-balance");
      if (event.which == 13) {
        event.preventDefault();
        var balance = parseFloat($("#balance").text().replace(/,/g, ""));
        if (balance >= 0) {
          checkout();
        }
      }
    }
  }
}

// print invoice
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
        .check-by-box {\
            border: 2px solid #000 !important;\
            width:100%;\
            padding: 5px;\
            font-family: Arial, sans-serif;\
        }\
    .check-by-box label {\
            display: block;\
            font-size: 10px !important;\
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

function checkout() {
  $.ajax({
    url: "invoiceConfirmation.php",
    method: "POST",
    data: {
      products: inArray,
    },
    success: function (response) {
      document.getElementById("invoiceNumber").innerHTML = response;
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
    },
  });

  var patientName = $("#patientName").val();
  var contactNo = $("#contactNo").val();

  if (patientName !== "") {
    var doctorName = $("#doctorName").val();
    var regNo = $("#regNo").val();

    var balance = $("#balance").text().replace(/,/g, "");
    var discountPercentage = $("#discountPercentage").val();
    var deliveryCharges = $("#deliveryCharges").val();
    var valueAddedServices = $("#valueAddedServices").val();
    var cashAmount = $("#cashAmount").val();
    var cardAmount = $("#cardAmount").val();
    var paymentmethodselector = $("#payment-method-selector").val();

    if (balance === "" || balance < "0") {
      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: "error",
        title: "Error: Paid Amount is Not Enough",
      });
    } else {
      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: "success",
        title: "Success: Checkout Success !",
      });

      var invoiceNumber = $(".invoiceNumber").text();

      var poArray = [];
      var inArray = [];

      $("#barcodeResults tr").each(function () {
        var code = $(this).find("#code").text();
        var ucv = $(this).find("#ucv").text();
        var item_price = $(this).find("#item_price").text();
        var unit_price = $(this).find("#unit_price").text();
        var product_name = $(this).find("#product_name").text();
        var product_cost = parseFloat($(this).find("#product_price").text());
        var product_qty = parseInt($(this).find("#qty").val());
        var selectBillType = $("#selectBillType").val();
        var product_unit = $(this).find("#unit").text();
        var productTotal = parseFloat($(this).find("#totalprice").text());

        // alert(product_unit);
        var productData = {
          code: code,
          ucv: ucv,
          item_price: item_price,
          unit_price: unit_price,
          product_name: product_name,
          product_cost: product_cost,
          product_qty: product_qty,
          product_unit: product_unit,
          productTotal: productTotal,
          invoiceNumber: invoiceNumber,

          patientName: patientName,
          contactNo: contactNo,
          doctorName: doctorName,
          regNo: regNo,

          balance: balance,
          discountPercentage: discountPercentage,
          deliveryCharges: deliveryCharges,
          valueAddedServices: valueAddedServices,
          cashAmount: cashAmount,
          cardAmount: cardAmount,
          paymentmethodselector: paymentmethodselector,
          selectBillType: selectBillType,
        };
        poArray.push(productData);
        inArray.push(productData);
      });

      $.ajax({
        url: "invoiceConfirmationInsert.php",
        method: "POST",
        data: {
          products: JSON.stringify(poArray),
        },
        success: function (response) {
          console.log(response);
          Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
          }).fire({
            icon: "success",
            title: "Success: Order Placed Successfully!",
          });
          $(".confirmPObtn").prop("disabled", false);

          $.ajax({
            url: "invoicePrintAddData.php",
            method: "POST",
            data: {
              products: inArray,
            },
            success: function (response) {
              document.getElementById("printInvoiceData").innerHTML = response;
              printInvoice();
            },
            error: function (xhr, status, error) {
              console.error(xhr.responseText);
            },
          });
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
          }).fire({
            icon: "error",
            title: "Error: Something went wrong!",
          });
        },
      });
    }
  } else {
    Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
    }).fire({
      icon: "error",
      title: "Error: Enter Patient's Details",
    });
  }
}
