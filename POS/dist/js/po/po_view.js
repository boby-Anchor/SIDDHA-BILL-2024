function viewPoItems(poNumber, shopName, poShopName, userName, poDate, subTotal, discount, netTotal) {
    InfoMessageDisplay("Fetching data.");
    $.ajax({
        url: "actions/po/getItems.php",
        method: "POST",
        data: {
            poNumber: poNumber
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                const tableBody = document.querySelector("#po_items_table_body");
                tableBody.innerHTML = "";
                let row_id = 0;

                response.items.forEach((item) => {
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                                    <td>${++row_id}</td>
                                    <td>${item.code || ""}</td>
                                    <td>${item.name || item.invoiceItem || ""}</td>
                                    <td>${item.sku || ""}</td>
                                    <td>${item.brand_name || ""}</td>
                                    <td>${item.invoiceItem_price ? Number(item.invoiceItem_price).toLocaleString() : ""}</td>
                                    <td>${item.invoiceItem_qty ? Number(item.invoiceItem_qty).toLocaleString() : ""}</td>
                                    <td>${item.invoiceItem_total ? Number(item.invoiceItem_total).toLocaleString() : ""}</td>
                                `;
                    tableBody.appendChild(newRow);
                });

                $("#po-items-data-modal").data("poNumber", poNumber);
                $("#po-items-data-modal").data("shopName", shopName);
                $("#po-items-data-modal").data("poShopName", poShopName);
                $("#po-items-data-modal").data("userName", userName);
                $("#po-items-data-modal").data("poDate", poDate);
                $("#po-items-data-modal").data("subTotal", subTotal);
                $("#po-items-data-modal").data("discount", discount);
                $("#po-items-data-modal").data("netTotal", netTotal);

                document.getElementById("po_modal_order_number").textContent = poNumber;
                document.getElementById("po_modal_shop_name").textContent = shopName;
                document.getElementById("po_modal_po_shop_name").textContent = poShopName;
                document.getElementById("po_modal_user_name").textContent = userName;
                document.getElementById("po_modal_po_date").textContent = poDate;

                if (typeof $("#po-items-data-modal").modal === 'function') {
                    $("#po-items-data-modal").modal("show");
                } else if (typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(document.getElementById("po-items-data-modal")).show();
                }
            } else {
                alert(response.message || "Unable to fetch PO items.");
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert("Could not load PO items.");
        }
    });
}

function handlePrint() {
    const poNumber = $("#po-items-data-modal").data("poNumber");
    const shopName = $("#po-items-data-modal").data("shopName");
    const poShopName = $("#po-items-data-modal").data("poShopName");
    const userName = $("#po-items-data-modal").data("userName");
    const poDate = $("#po-items-data-modal").data("poDate");
    printTable(poNumber, shopName, poShopName, userName, poDate);
}

function printTable(orderNumber, shopName, poShopName, userName, poDate) {
    const printWindow = window.open("", "_blank");
    printWindow.document.write("<html><head><title>Print Preview</title>");
    printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
    printWindow.document.write("</head><body>");
    printWindow.document.write('<div class="container">');
    printWindow.document.write('<h2 class="text-center bg-success text-light" style="margin-top:5px;padding:3px;">PURCHASE ORDER DETAILS</h2>');
    printWindow.document.write('<div class="col-12" style="margin-top: 20px;margin-bottom: 20px;font-family: monospace;">');
    printWindow.document.write('<div class="row">');
    printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>ORDER NUMBER : ' + orderNumber + '</h5></div>');
    printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>ORDER FROM : ' + shopName + '</h5></div>');
    printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>ORDER TO : ' + poShopName + '</h5></div>');
    printWindow.document.write('<div class="col-12" style="text-align: start;"><h5>PLACED BY : ' + userName + '</h5></div>');
    printWindow.document.write('<div class="col-12" style="text-align: start;"><h6>ORDER DATE : ' + poDate + '</h6></div>');
    printWindow.document.write('<div class="col-12" style="text-align: start;"><h6>PRINTED : ' + new Date().toLocaleString() + '</h6></div>');
    printWindow.document.write('</div>');
    printWindow.document.write('</div>');
    printWindow.document.write(document.getElementById('po_items_table').outerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function handleBillPrint() {
    const poNumber = $("#po-items-data-modal").data("poNumber");
    const shopName = $("#po-items-data-modal").data("shopName");
    const poShopName = $("#po-items-data-modal").data("poShopName");
    const userName = $("#po-items-data-modal").data("userName");
    const poDate = $("#po-items-data-modal").data("poDate");
    const subTotal = $("#po-items-data-modal").data("subTotal");
    const discount = $("#po-items-data-modal").data("discount");
    const netTotal = $("#po-items-data-modal").data("netTotal");

    // Build poArray from the table
    const poArray = [];
    $("#po_items_table_body tr").each(function () {
        const cells = $(this).find("td");
        const code = $(cells[1]).text().trim();
        const name = $(cells[2]).text().trim();
        const brand = $(cells[4]).text().trim();
        const price = parseFloat($(cells[5]).text().replace(/,/g, ''));
        const qty = parseFloat($(cells[6]).text().replace(/,/g, ''));
        const total = parseFloat($(cells[7]).text().replace(/,/g, ''));

        poArray.push({
            code: code,
            product_name: name,
            product_cost: price,
            product_qty: qty,
            product_total: total,
            ucv: '',
            item_price: '',
            unit_price: '',
            brand: brand,
            discount: 0,
            product_unit: ''
        });
    });

    const billData = [{
        po_shop_id: '',
        sub_total: subTotal,
        discount_percentage: discount,
        net_total: netTotal
    }];

    setDataToBill(billData, poArray);
    $('#po_shop_on_bill').text(poShopName);
    $('#bill_user_name').text(userName);
    $('.invoiceNumber').text(poNumber);
    $('#po_bill_date').text(poDate || '');
}

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
                            <span class="product_cost">${product_cost}</span>
                          </div>
                          <div class="col-3 text-center">
                            <span class="product_qty">${product_qty}</span>
                          </div>
                          <div class="col-3 text-center">
                            <span class="productTotal">${product_discount}</span>
                          </div>
                          <div class="col-3 text-center">
                            <span class="productTotal">${productTotal}</span>
                          </div>
                        </div>
                      </div>
                      <div class="col-12" style="border-bottom: #0e0e0e 0.1rem solid;"></div>
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
                          <div class="col-12" style="border-bottom: #0e0e0e 0.2rem solid;"></div>
                            <span class="productsAllTotal">
                                Sub total : ${productsAllTotal}
                            </span>
                        </div>

                        <div class="col-12 d-flex justify-content-end pt-2">
                          <span class="discount">
                            Discount : ${discount_percentage}%
                          </span>
                        </div>

                        <div class="col-12 d-flex justify-content-end pt-2">
                          <span class="netTotal">
                            Net Total : ${net_total}
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

    // After printing, close the print window
    printWindow.onafterprint = function () {
        printWindow.close();
    };
}