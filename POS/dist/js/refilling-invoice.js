function enableProceedButton() {
    $('#proceedButton').prop('disabled', false);
}

function collectSourceItems() {
    const items = [];
    const rows = document.querySelectorAll("#sourceItemsTable tbody tr");

    rows.forEach(row => {
        const item = {
            id: row.querySelector(".source_item_id").innerText.trim(),
            current_qty: parseInt(
                row.querySelector(".source_item_current_qty.d-none").innerText
            ),
            barcode: row.querySelector(".source_item_barcode").innerText.trim(),
            name: row.querySelector(".source_item_name").innerText.trim(),
            volume: row.querySelector(".source_item_volume").innerText.trim(),
            brand: row.querySelector(".source_item_brand").innerText.trim(),
            price: parseFloat(
                row.querySelector(".source_item_price").innerText
            ),
            qty: parseInt(
                row.querySelector(".source_item_qty input.source_qty").value
            )
        };

        // validation
        if (item.qty <= 0 || isNaN(item.qty)) {
            enableProceedButton();
            ErrorMessageDisplay("Invalid quantity detected");
            return;
        }

        items.push(item);
    });

    return items;
}

function collectRefillItems() {
    const refillItems = [];
    const rows = document.querySelectorAll("#refillingItemsTable tbody tr");

    rows.forEach(row => {
        const qtyInput = row.querySelector(".refill_item_qty");

        const item = {
            id: row.querySelector(".refill_item_id").innerText.trim(),
            barcode: row.querySelector(".refill_item_barcode").innerText.trim(),
            name: row.querySelector(".refill_item_name").innerText.trim(),
            volume: row.querySelector(".refill_item_volume").innerText.trim(),
            brand: row.querySelector(".refill_item_brand").innerText.trim(),
            price: parseFloat(row.querySelector(".refill_item_price").innerText),
            current_qty: parseInt(
                row.querySelector(".refill_item_current_qty").innerText
            ),
            qty: parseInt(
                row.querySelector(".refill_item_qty input.refill_qty").value
            )
        };

        // validation
        if (!item.qty || item.qty <= 0) {
            enableProceedButton();
            ErrorMessageDisplay("Invalid refill quantity detected");
            return;
        }

        refillItems.push(item);
    });
    console.log('in function');

    console.log(refillItems);

    return refillItems;
}

function isDuplicateSourceItem(barcode) {
    const rows = document.querySelectorAll("#sourceItemsTable tbody tr");

    for (let row of rows) {
        const existingBarcode = row.querySelector(".source_item_barcode").innerText.trim();
        if (existingBarcode === barcode) {
            return true;
        }
    }
    return false;
}

function isDuplicateRefillItem(barcode) {
    const rows = document.querySelectorAll("#refillingItemsTable tbody tr");

    for (let row of rows) {
        const existingBarcode = row.querySelector(".refill_item_barcode").innerText.trim();
        if (existingBarcode === barcode) {
            return true;
        }
    }
    return false;
}

async function setSourceItem(stock_id) {

    getItemData(stock_id, function (item_data) {

        console.log(item_data);

        const tableBody = document.querySelector("#sourceItemsTable tbody");

        item_data.forEach(row => {

            if (isDuplicateSourceItem(row.code)) {
                ErrorMessageDisplay("This item already exists in the table");
                return;
            }

            const newRow = document.createElement("tr");

            newRow.innerHTML = `
                <td class="d-none source_item_id">
                      ${row.id}
                </td>
                <td class="d-none source_item_current_qty">
                      ${row.qty}
                </td>
                <td class="d-none source_item_volume">
                      ${row.ucv_name}
                </td>
                <td class="d-none source_item_unit">
                      ${row.unit}
                </td>
                <td class="source_item_barcode">
                      ${row.code}
                </td>
                <td class="source_item_name">
                      ${row.name} ${row.ucv_name}${row.unit}
                </td>
                <td class="source_item_brand">
                      ${row.brand}
                </td>
                <td class="source_item_price">
                      ${row.item_s_price}
                </td>
                <td class="source_item_qty">
                    <input class="form-control text-center source_qty" name="source_qty" type="number" min="1" value="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                </td>
              `;
            tableBody.appendChild(newRow);
        });
    }),
        function (xhr) {
            console.error(xhr.responseText);
        };
    $('#sourceItemModal').modal('hide');
}

async function setRefillingItem(stock_id) {

    getItemData(stock_id, function (item_data) {

        console.log(item_data);

        const tableBody = document.querySelector("#refillingItemsTable tbody");

        item_data.forEach(row => {

            if (isDuplicateRefillItem(row.code)) {
                ErrorMessageDisplay("This item already exists in the table");
                return;
            }

            const newRow = document.createElement("tr");

            newRow.innerHTML = `
                <td class="d-none refill_item_id">
                    ${row.id}
                </td>
                <td class="d-none refill_item_current_qty">
                    ${row.qty}
                </td>
                <td class="d-none refill_item_volume">
                    ${row.ucv_name}
                </td>
                <td class="d-none refill_item_unit">
                    ${row.unit}
                </td>
                <td class="refill_item_barcode">
                    ${row.code}
                </td>
                <td class="refill_item_name">
                    ${row.name}  ${row.ucv_name}${row.unit}
                </td>
                <td class="refill_item_brand">
                    ${row.brand}
                </td>
                <td class="refill_item_price">
                    ${row.item_s_price}
                </td>
                <td class="refill_item_qty">
                    <input class="form-control text-center refill_qty" name="refill_qty" type="number" min="1" value="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                </td>
              `;
            tableBody.appendChild(newRow);
        });
    }),
        function (xhr) {
            console.error(xhr.responseText);
        };
    $('#refillItemModal').modal('hide');
}

function getItemData(stock_id, onSuccess, onError = null) {
    console.log(stock_id);

    $.ajax({
        url: "actions/pos/selectProduct.php",
        method: "POST",
        data: {
            stock_id,
        },
        dataType: 'json',

        success: function (response) {
            console.log("success");

            switch (response.status) {
                case "success":
                    console.log('success switch');
                    onSuccess(response.data);
                    break;

                case "sessionExpired":
                    handleExpiredSession(response.message);
                    break;

                case "error":
                    handleExpiredSession(response.message);
                    break;

                default:
                    ErrorMessageDisplay("An unknown error occurred.");
                    break;
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            ErrorMessageDisplay(xhr.responseText);
        },
    });
}

function checkout() {
    const batchNumber = $('#batch_number').val().trim();

    if (!batchNumber) {
        enableProceedButton();
        ErrorMessageDisplay("Batch number is required");
        return;
    }

    const sourceItemsArray = collectSourceItems();
    const refillItemsArray = collectRefillItems();
    console.log(sourceItemsArray);
    console.log(refillItemsArray);

    if (sourceItemsArray.length === 0 || refillItemsArray.length === 0) {
        enableProceedButton();
        ErrorMessageDisplay("Invalid items submitted.");
        return;
    }

    $.ajax({
        url: "actions/refilling/process_stock_convert.php",
        type: "POST",
        dataType: "json",
        data: {
            batch_number: batchNumber,
            source_items: JSON.stringify(sourceItemsArray),
            refill_items: JSON.stringify(refillItemsArray)
        },
        success: function (response) {
            console.log(response);

            switch (response.status) {
                case "success":
                    SuccessMessageDisplay(response.message)
                    setTimeout(() => {
                        location.reload();
                    }, 4000);
                    break;

                case "error":
                    enableProceedButton();
                    ErrorMessageDisplay(response.message);
                    break;

                case "sessionExpired":
                    enableProceedButton();
                    handleExpiredSession(response.message);
                    break;

                default:
                    enableProceedButton();
                    ErrorMessageDisplay("An unknown error occurred.")
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
            alert("Server error occurred");
        }
    });

}