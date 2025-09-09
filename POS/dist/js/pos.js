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

// set paththu name on popup
function setPaththu(paththuSelect) {
    document.getElementById("paththuName").value = paththuSelect.value;
    paththuSelect.value = 1;
}

// add paththu to bill
function addPaththu() {
    var barcodeResults = document.getElementById("barcodeResults");
    var paththuNameElement = document.getElementById("paththuName");
    var paththuName = paththuNameElement.value.trim();
    var paththuPriceElement = document.getElementById("paththuPrice");
    var paththuPrice = paththuPriceElement.value.trim();

    if (paththuName !== "" && paththuPrice !== "") {
        var txt = `
  <tr data-barcode="code_price">
  <th><input type="checkbox" class="d-none" name="isPaththu" id="isPaththu"></th>
  
  <td id="code" class="d-none"></td>
  <td id="ucv" class="d-none">1</td>
  <td id="item_price" class="d-none">${paththuPrice}</td>
  <td id="unit_price" class="d-none">0</td>

  <td id="product_name" ><label>${paththuName}</label></td>

  <td id="product_price">${paththuPrice}</td>
  <td>
      <div class="col-12">
          <div class="row">
              <div class="col-2 d-flex justify-content-center">
                  <button class="btn btn-secondary minusQty" onclick="decreaseQuantity(this)">-</button>
              </div>
              <div class="col-4">
                  <input class="form-control text-center" id="qty" name="qty" type="number" min="1" value="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" onchange="updateTotal(this)" data-price="${paththuPrice}">
              </div>
              <div class="col-2 d-flex justify-content-center">
                  <button class="btn btn-primary plusQty" onclick="increaseQuantity(this)">+</button>
              </div>
              <div class="col-2">
                  <labe id="unit">combine</labe>
              </div>
          </div>
      </div>
  </td>

  <td class="total" id="totalprice">${paththuPrice}</td>
  <td>
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16" onclick="removeRow(this)" style="cursor: pointer;">
          <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"></path>
      </svg>
  </td>
</tr>
`;

        barcodeResults.insertAdjacentHTML("beforeend", txt);

        paththuNameElement.value = "";
        paththuPriceElement.value = "";

        $("#addPaththuModal").modal("hide");
        calculateSubTotal();
    }
}

// add doctor medicine to bill
function addDoctorMedicine() {
    var doctorMedicineResults = document.getElementById("doctorMedicineResults");
    var doctorMedicineNameElement = document.getElementById("doctorMedicineName");
    var doctorMedicineName = doctorMedicineNameElement.value.trim();
    var doctorMedicineValueElement = document.getElementById(
        "doctorMedicinePrice"
    );
    var doctorMedicineValue = doctorMedicineValueElement.value.trim();

    var doctorMedicinePrice = doctorMedicineValue * 1.1;

    doctorMedicinePrice = Math.round(doctorMedicinePrice);

    if (doctorMedicineName !== "" && doctorMedicineValue !== "") {
        var txt = `
  <tr data-barcode="code_price">
  <td id="item_price" class="d-none">${doctorMedicineValue}</td>
  
  <td id="product_name" ><label>${doctorMedicineName}</label></td>
  
  <td class="text-right">Value</td>
  <td id="product_price">${doctorMedicineValue}</td>
  
  <td class="text-right">Price</td>
  <td class="total" id="totalprice">${doctorMedicinePrice}</td>
  <td>
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16" onclick="removeRow(this)" style="cursor: pointer;">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"></path>
  </svg>
  </td>
  </tr>
  `;

        doctorMedicineResults.innerHTML = txt;

        doctorMedicineNameElement.value = "";
        doctorMedicineValueElement.value = "";

        $("#doctorMedicineModal").modal("hide");
        calculateSubTotal();
    }
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

// netTotal calculation display
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
    var doctorMedicineTotal = 0;
    var productsAllTotal = 0;
    var paththuTotal = 0;

    // var ajaxRequests = [];

    $(".doctorMedicineResults tbody tr").each(function () {
        var dmPrice = parseFloat($(this).find("#totalprice").text());
        doctorMedicineTotal += dmPrice;
    });

    $(".barcodeResults tbody tr").each(function () {
        var $this = $(this);
        var product_cost = parseFloat($this.find("#product_price").text()) || 0;
        var product_qty = parseFloat($this.find("#qty").val()) || 0;
        var isPaththu = $this.find("#isPaththu").prop("checked");

        if (product_qty <= 0) {
            ErrorMessageDisplay("Invalid Product Quantity!");
            $("#checkoutBtn").removeAttr("data-toggle data-target");
            return;
        }
        var productTotal = product_cost * product_qty;
        if (isPaththu) {
            paththuTotal += productTotal;
        } else {
            productsAllTotal += productTotal;
        }
    });

    productsAllTotal += doctorMedicineTotal;

    $("#subTotal").text(productsAllTotal);
    $("#paththuTotal").text(paththuTotal);

    addDiscount();

    // $(".barcodeResults tbody tr").each(function() {
    //   var product_cost = $(this).find("#product_price").text();
    //   var product_qty = $(this).find("#qty").val();
    //   var isPaththu = $(this).find("#isPaththu").prop("checked");

    //   if (product_qty === "" || product_qty === "0") {
    //     Swal.mixin({
    //       toast: true,
    //       position: "top-end",
    //       showConfirmButton: false,
    //       timer: 3000,
    //     }).fire({
    //       icon: "error",
    //       title: "Error: Product Quantity!",
    //     });
    //     $("#checkoutBtn").removeAttr("data-toggle data-target");
    //   } else {
    //     var productTotal = product_cost * product_qty;
    //     if (isPaththu) {
    //       paththuTotal += productTotal;
    //       document.getElementById("paththuTotal").innerHTML = paththuTotal;
    //     } else {
    //       productsAllTotal += productTotal;
    //       document.getElementById("subTotal").innerHTML = productsAllTotal;
    //     }
    //     addDiscount();
    //   }
    // });
}

function checkBalance(input) {
    const selector = document.getElementById("payment-method-selector");
    const productsAllTotal = parseFloat($("#netTotal").text().replace(/,/g, ""));
    let totalEnteredAmount;

    if (selector.value === "3") {
        // Case for mixed payment (cash + card)
        const cashAmount = parseFloat($("#cashAmount").val()) || 0;
        const cardAmount = parseFloat($("#cardAmount").val()) || 0;
        totalEnteredAmount = cashAmount + cardAmount;
    } else {
        // Case for single payment method
        totalEnteredAmount = parseFloat(input.value) || 0;
    }

    // Calculate balance
    const balance = totalEnteredAmount - productsAllTotal;
    $(".balance").text(balance.toLocaleString("en-US", {}));

    // Update balance styling
    if (balance > 0) {
        $(".balance").addClass("positive-balance").removeClass("negative-balance");
    } else if (balance < 0) {
        $(".balance").addClass("negative-balance").removeClass("positive-balance");
    } else {
        $(".balance").removeClass("positive-balance negative-balance");
    }

    // Handle Enter keypress for data check
    if (event.which === 13) {
        event.preventDefault();
        const displayedBalance = parseFloat($("#balance").text().replace(/,/g, ""));
        if (displayedBalance >= 0) {
            dataCheck();
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

function dataCheck() {

    var itemData = [];
    var paththuTotal = parseFloat($("#paththuTotal").text());
    var combinePrice;
    var billHasPaththu;
    var billHasCombine;

    $("#barcodeResults tr").each(function () {
        var isPaththu = $(this).find("#isPaththu").prop("checked") ? 1 : 0;
        var code = $(this).find("#code").text();
        var ucv = $(this).find("#ucv").text();
        var item_price = $(this).find("#item_price").text();
        var unit_price = $(this).find("#unit_price").text();
        var product_name = $(this).find("#product_name").text();
        var product_brand = $(this).find("#brand").text();
        var product_cost = parseFloat($(this).find("#product_price").text());
        var product_qty = parseInt($(this).find("#qty").val());
        var product_unit = $(this).find("#unit").text();
        var productTotal = parseFloat($(this).find("#totalprice").text());

        if (product_unit == "combine") {
            combinePrice = productTotal;
            billHasCombine = true;
        }

        console.log(isPaththu);
        if (isPaththu) {
            billHasPaththu = true;
        }

        var productData = {
            isPaththu: isPaththu,
            code: code,
            ucv: ucv,
            item_price: item_price,
            unit_price: unit_price,
            product_name: product_name,
            product_brand: product_brand,
            product_cost: product_cost,
            product_qty: product_qty,
            product_unit: product_unit,
            productTotal: productTotal,
        };
        itemData.push(productData);
    });

    if (billHasPaththu && billHasCombine && paththuTotal === combinePrice) {
        checkout(itemData);
    } else if (billHasPaththu && billHasCombine) {
        alert("පත්තුවේ ගාන වැරදී.");
    } else if (billHasPaththu) {
        alert("පත්තු හදන්නේ නැද්ද..??");
    } else if (billHasCombine) {
        alert("පත්තුවට බඩු දාන්න.");
    } else {
        checkout(itemData);
    }
}

function checkout(itemData) {

    updateBillStatus("3");

    var billData = [];
    var dMData = [];
    var patientName = $("#patientName").val().trim();

    if (patientName !== "") {
        var contactNo = $("#contactNo").val().trim();
        var doctorName = $("#doctorName").val();
        var regNo = $("#regNo").val();

        var balance = $("#balance").text().replace(/,/g, "");
        var discountPercentage = $("#discountPercentage").val() || 0;
        var subTotal = $("#subTotal").text();
        var netTotal = $("#netTotal").text();

        var deliveryCharges = $("#deliveryCharges").val();
        var valueAddedServices = $("#valueAddedServices").val();
        var cashAmount = $("#cashAmount").val();
        var cardAmount = $("#cardAmount").val();
        var paymentMethodSelector = $("#payment-method-selector").val();
        var selectBillType = $("#selectBillType").val();

        document.getElementById("invoicePatientName").innerText = patientName;
        document.getElementById("InvoiceContactNumber").innerText = contactNo;

        var bData = {
            patientName: patientName,
            contactNo: contactNo,
            doctorName: doctorName,
            regNo: regNo,

            balance: balance,
            subTotal: subTotal,
            netTotal: netTotal,
            discountPercentage: discountPercentage,
            deliveryCharges: deliveryCharges,
            valueAddedServices: valueAddedServices,
            cashAmount: cashAmount,
            cardAmount: cardAmount,
            paymentMethodSelector: paymentMethodSelector,
            selectBillType: selectBillType,
        };

        billData.push(bData);

        $("#doctorMedicineResults tr").each(function () {
            var product_name = $(this).find("#product_name").text();
            var item_cost = $(this).find("#item_price").text().trim();
            var item_price = $(this).find("#totalprice").text();

            var productData = {
                product_name: product_name,
                item_cost: item_cost,
                item_price: item_price,
            };
            dMData.push(productData);
        });

        $.ajax({
            url: "invoiceSave.php",
            method: "POST",
            data: {
                billData: JSON.stringify(billData),
                itemData: JSON.stringify(itemData),
                dMData: JSON.stringify(dMData),
            },

            success: function (response) {

                var result = JSON.parse(response);

                if (result.status === 'success') {

                    document.getElementById("invoiceNumber").innerHTML = result.invoiceNumber;
                    SuccessMessageDisplay(result.message);

                    //invoice print add data
                    $.ajax({
                        url: "invoicePrintAddData.php",
                        method: "POST",
                        data: {
                            billData: JSON.stringify(billData),
                            itemData: JSON.stringify(itemData),
                            dMData: JSON.stringify(dMData),
                        },
                        success: function (response) {
                            document.getElementById("printInvoiceData").innerHTML = response;
                            printInvoice();
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        },
                    });

                } else if (result.status === 'sessionExpired') {
                    ErrorMessageDisplay(result.message);
                    setTimeout(function () {
                        window.open(window.location.href, '_blank');
                    }, 4000);
                    return;
                } else if (result.status === 'sessionDataError') {
                    ErrorMessageDisplay(result.message);
                    setTimeout(function () {
                        window.open(window.location.href, '_blank');
                    }, 4000);
                    return;
                } else {
                    ErrorMessageDisplay(result.message);
                }

            }
        });

    } else {
        ErrorMessageDisplay("Enter Patient's Details");
    }
}

function updateBillStatus(value) {

    var token = $("#token").val();

    switch (value) {
        case "1":
            $.ajax({
                url: "https://pharmacy-order-backend.siddha.lk/order/",
                // url: "http://localhost:5000/order/",
                method: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    order_number: token,
                }),
                success: function (response) {
                    SuccessMessageDisplay(response.message);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                },
            });

            break;
        case "2":
            $.ajax({
                url: "https://pharmacy-order-backend.siddha.lk/order/",
                method: "PATCH",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    order_number: token,
                }),
                success: function (response) {
                    SuccessMessageDisplay(response.message);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    ErrorMessageDisplay(xhr.responseText);
                },
            });

            break;
        case "3":
            $.ajax({
                url: "https://pharmacy-order-backend.siddha.lk/order/" + token,
                method: "PATCH",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    order_number: token,
                }),
                success: function (response) {
                    SuccessMessageDisplay(response.message);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    ErrorMessageDisplay(xhr.responseText);
                },
            });

            break;
    }

}

function issetInvoiceNumber() {
    getInvoiceNumber()
        .then(function (response) {
        })
        .catch(function (xhr) {
            console.error(xhr.responseText);
            ErrorMessageDisplay(xhr.responseText);
        });
}

function getInvoiceNumber() {
    return $.ajax({
        url: "invoiceConfirmation.php",
        method: "POST",
        // data: {
        //   products: inArray,
        // },
    });
}