function getItems(grn_number, invoice_number, grn_date, supplier) {
    InfoMessageDisplay("Fetching data.");
    $.ajax({
        url: "actions/grn/getItems.php",
        method: "POST",
        data: {
            grn_number,
        },
        dataType: 'json',

        success: function (response) {

            switch (response.status) {
                case "success":
                    const tableBody = document.querySelector("#grn_items_table_body");
                    tableBody.innerHTML = '';
                    var row_id = 0;
                    var billTotalValue = 0;
                    var billTotalCost = 0;

                    response.data.forEach((row) => {
                        const newRow = document.createElement("tr");

                        var totalValue = row.grn_p_qty * row.grn_p_price;
                        billTotalValue += totalValue;
                        billTotalCost += parseFloat(row.grn_p_cost);

                        newRow.innerHTML = `
                                    <td>
                                        ${++row_id}
                                    </td>
                                    <td>
                                        ${row.grn_p_id}
                                    </td>
                                    <td>
                                        ${row.item_name}
                                    </td>
                                    <td>
                                        ${row.ucv_name}${row.unit}
                                    </td>
                                    <td>
                                        ${row.sku ? row.sku : ''}
                                    </td>
                                    <td>
                                        ${row.brand}
                                    </td>
                                    <td>
                                        ${row.grn_p_qty}
                                    </td>
                                    <td>
                                        ${row.p_free_qty}
                                    </td>
                                    <td class="text-right">
                                        ${row.grn_p_price}
                                    </td>
                                    <td class="text-right">
                                        ${totalValue}
                                    </td>
                                    <td>
                                        ${row.p_plus_discount}
                                    </td>
                                    <td class="text-right">
                                        ${parseFloat(row.grn_p_cost)}
                                    </td>
                                    <td class="text-right">
                                        ${row.grn_u_cost}
                                    </td>
                                `;
                        tableBody.appendChild(newRow);
                    });


                    const tableFooter = document.createElement("tr");

                    tableFooter.innerHTML = `
                                <td colspan="9">
                                    Total Amount
                                </td>
                                <td class="text-right">
                                    ${billTotalValue}
                                </td>
                                <td></td>
                                <td class="text-right">
                                    ${billTotalCost}
                                </td>
                                <td></td>
                            `;

                    tableBody.appendChild(tableFooter);

                    $("#grn-items-data-modal").data("grn", grn_number);
                    $("#grn-items-data-modal").data("invoice_number", invoice_number);
                    $("#grn-items-data-modal").data("grn_date", grn_date);
                    $("#grn-items-data-modal").data("supplier", supplier);

                    document.getElementById("grn_modal_grn_number").textContent = grn_number;
                    document.getElementById("grn_modal_invoice_number").textContent = invoice_number;
                    document.getElementById("grn_modal_supplier").textContent = supplier;
                    document.getElementById("grn_modal_grn_date").textContent = grn_date;

                    $("#grn-items-data-modal").modal("show");
                    break;

                case "sessionExpired":
                    handleExpiredSession(response.message);
                    break;

                default:
                    ErrorMessageDisplay(response.message);
                    break;
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}


function handlePrint() {
    const grn_number = $("#grn-items-data-modal").data("grn");
    const invoice_number = $("#grn-items-data-modal").data("invoice_number");
    const grn_date = $("#grn-items-data-modal").data("grn_date");
    const supplier = $("#grn-items-data-modal").data("supplier");
    printTable(grn_number, invoice_number, grn_date, supplier);
}

function printTable(grnNumber, invoice_number, grnDate, supplier_name) {
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Print Preview</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="container">');
    printWindow.document.write('<h2 class="text-center" style="margin-top:5px;padding:3px;">GOODS RECEIPT NOTES</h2>');
    printWindow.document.write('<div class="col-12" style="margin-top: 50px;margin-bottom: 20px;font-family: monospace;">');
    printWindow.document.write('<div class="row">');
    printWindow.document.write('<div class="col-12" style="text-align: start;">');
    printWindow.document.write('<h5>GRN Number : ' + grnNumber + '</h5>');
    printWindow.document.write('</div>');
    if (supplier_name) {
        printWindow.document.write('<div class="col-12" style="text-align: start;">');
        printWindow.document.write('<h5>Supplier Name : ' + supplier_name + '</h5>');
        printWindow.document.write('</div>');
    }
    if (invoice_number) {
        printWindow.document.write('<div class="col-12" style="text-align: start;">');
        printWindow.document.write('<h5>Invoice Number : ' + invoice_number + '</h5>');
        printWindow.document.write('</div>');
    }
    printWindow.document.write('<div class="col-12" style="text-align: start;">');
    printWindow.document.write('<h6>Added : ' + grnDate + '</h6>');
    printWindow.document.write('</div>');
    printWindow.document.write('<div class="col-12" style="text-align: start;">');
    printWindow.document.write('<h6>Printed : <?= date("Y - m - d H: i: s") ?></h6>');
    printWindow.document.write('</div>');
    printWindow.document.write('</div>');
    printWindow.document.write('</div>');
    printWindow.document.write(document.getElementById('grn_items_table').outerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}