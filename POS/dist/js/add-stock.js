// ==============================================================================
// Event listener for clicking the proceed button to show the confirmation modal
$(document).on("click", "#proceedGrnBtn", function () {

  var hasInvoiceNumber = $("#hasInvoiceNumber").prop("checked");
  var invoice_number = $("#invoice_number").val();

  if (hasInvoiceNumber && !invoice_number) {
    return (ErrorMessageDisplay("කෝ Invoice Number එක?"))
  }

  var poArray = [];
  var hasErrors = false; // Flag to track if there are any errors

  $(".addedProTable tbody tr").each(function () {
    var product_code = $(this).find("#product_code").text(); // Barcode
    var product_name = $(this).find("#product_name").text().trim(); // Item name

    var product_qty = parseInt($(this).find("#qty_input").val().trim()); // Qty input
    var free_qty = parseInt($(this).find("#free_qty").val().trim()) || 0; // Free qty input

    var minimum_qty = parseInt($(this).find("#minimum_qty").text().trim()) || 0; // Minimun qty
    var free_minimum_qty = parseInt($(this).find("#free_minimum_qty").text().trim()) || 0; // Free minimun qty

    var item_price = parseInt($(this).find("#item_price").val().trim()); // Item price input
    var item_discount = parseInt($(this).find("#item_discount").val().trim()); // Discount input

    var total_cost = $(this).find("#total_cost").val().trim(); // Total cost
    var total_value = $(this).find("#total_value").text().trim(); // Total value
    var cost_per_unit = $(this).find("#cost_per_unit").text().trim() || 0; // unit cost
    var unit_s_price = parseFloat($(this).find("#unit_s_price").val().trim()) || 0; // unit price

    // Data Validation
    if (isNaN(product_qty) || product_qty === 0) {
      ErrorMessageDisplay(product_name + " එකේ Qty දාන්නේ නැද්ද?");
      hasErrors = true;
      return false;
    } else if (isNaN(item_price) || item_price === 0) {
      ErrorMessageDisplay(product_name + " එකේ Price නැද්ද?");
      hasErrors = true;
      return false;
    } else if (isNaN(item_discount)) {
      ErrorMessageDisplay(product_name + " එකේ Discount නැද්ද?");
      hasErrors = true;
      return false;
    } else if (unit_s_price !== 0 && cost_per_unit > unit_s_price) {
      ErrorMessageDisplay(product_name + " එකේ Unit Cost > Unit Price..!");
      hasErrors = true;
      return false;
    } else {
      var productData = {
        product_code,
        product_name,

        product_qty,
        free_qty,

        minimum_qty,
        free_minimum_qty,

        item_price,
        item_discount,

        total_cost,
        total_value,
        cost_per_unit,
        unit_s_price,

        mfd,
        exp,
      };
      poArray.push(productData);
    }
  });

  // If no errors were found, generate the confirmation table and show the modal
  if (!hasErrors) {
    var tableHTML = "";
    poArray.forEach(function (product) {
      var totalProductQty = product.product_qty + product.free_qty;
      var minimumQty = product.minimum_qty === 0 ?
        0 : product.minimum_qty + product.free_minimum_qty;

      tableHTML += `
          <tr>
              <th scope="row" class="product_code">${product.product_code}</th>
              <td class="product_name">${product.product_name}</td>
              <td class="product_total_qty">${totalProductQty}</td>
              <td class="product_qty d-none">${product.product_qty}</td>
              <td class="minimum_qty">${minimumQty}</td>
              <td class="total_cost">${product.total_cost}</td>
              <td class="total_value">${product.total_value}</td> 
              <td class="cost_per_unit">${product.cost_per_unit}</td>
              <td class="item_discount">${product.item_discount}</td>
              <td class="item_price">${product.item_price}</td>
              <td class="unit_s_price">${product.unit_s_price}</td>
              <td class="free_qty d-none">${product.free_qty}</td>
          </tr>
      `;
    });

    // Insert the generated HTML into the confirmation table body
    document.getElementById("grnConfirmationTableBody").innerHTML = tableHTML;

    // Display the modal for confirmation if there are no errors
    $("#proceedGrnBtn").attr({
      "data-toggle": "modal",
      "data-target": "#confirmGRN",
    });
  }
});

// ==============================================================================
// Event listener for clicking the add button to add products to the list
$(document).on("click", ".add-btn", function () {
  var product_code = $(this).closest("tr").find("#product_code").text();
  var product_name = $(this).closest("tr").find("#product_name").text().trim();
  // var itemSprice = $(this).closest("tr").find("#itemSprice").text().trim();
  var unitSprice = $(this).closest("tr").find("#unitSprice").text().trim();
  var ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
  var product_unit = $(this).closest("tr").find("#product_unit").text();

  var exists = false;
  $(".addedProTable tbody tr").each(function () {
    if ($(this).find("#product_code").text() === product_code) {
      exists = true;
      return false;
    }
  });

  // If the product does not already exist in the table, add it
  if (!exists) {
    var markup =
      "<tr>" +
      "<th scope='row' id='product_code'>" + product_code + "</th>" +
      "<td> <label id='product_name'>" + product_name + "</label>(<label id='ucv_name'>" + ucv_name + "</label><label id='product_unit'>" + product_unit + "</label>)</td>" +

      "<td>" +
      "<input id='qty_input' type='text' class='bg-dark form-control text-center mb-2' value=''  placeholder='Qty'>" +
      "<input id='free_qty' type='text' class='bg-dark form-control text-center free-qty-input' value='' placeholder='free qty..'>" +
      "</td>" +

      "<td class='text-center auto-generate-m-unit '>" +
      "<label id='minimum_qty' class='mb-2' ><i class='fa fa-solid fa-circle-notch fa-spin'></i></label><br>" +
      "<label id='free_minimum_qty'><i class='fa fa-solid fa-circle-notch fa-spin'></i></label>" +
      "</td>" +

      "<td class='text-center manual-enter-m-unit d-none'>" +
      "<input type='text' id='manual_unit_input' class='bg-dark form-control text-center manual_unit_input mb-2' value=''>" +
      "<input type='text' id='free_manual_unit_input' class='bg-dark form-control text-center free_manual_unit_input' value=''>" +
      "</td>" +

      "<td>" +
      "<input type='text' id='item_price' class='bg-dark form-control text-center mb-2' placeholder='Price'>" +
      "<div class='form-group input-group'>" +
      "<label class='d-flex align-items-center pr-1 m-0'>MFD</label>" +
      "<input type='date' id='mfd' class='bg-dark form-control'>" +
      "</div>" +
      "</td>" +

      "<td>" +
      "<input id='item_discount' type='text' class='bg-dark form-control text-center mb-2' value='' placeholder='Discount'>" +
      "<div class='form-group input-group'>" +
      "<label class='d-flex align-items-center pr-1 m-0'>EXP</label>" +
      "<input type='date' id='exp' class='bg-dark form-control'>" +
      "</div>" +
      "</td>" +

      "<td>" + "<input id='total_cost' type='text' class='bg-dark form-control text-center' value='' placeholder='Total Cost'>" + "</td>" +

      "<td>" + "<label id='total_value'></label>" + "</td>" +

      "<td>" + "<label id='cost_per_unit'></label>" + "</td>" +

      "<td>" +
      "<input placeholder='unit price' id='unit_s_price' type='text' class='bg-dark form-control text-center unitsell-price-input mb-2' value='" + unitSprice + "'>" +
      "</td>" +

      "<td><i class='fa fa-trash-o cus-delete'></i></td>" +

      "</tr>";

    $(".addedProTable tbody").append(markup);

    $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
    $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

    setTimeout(() => {
      window.scrollTo({
        top: document.documentElement.scrollHeight,
        behavior: "smooth"
      });
    }, 50);

  } else {
    ErrorMessageDisplay("Product already exists in the list!");
  }
});

// Event listener for clicking the delete button
$(document).on("click", ".cus-delete", function () {
  $(this).closest("tr").remove();
  $("#proceedGrnBtn").removeAttr("data-toggle data-target");
  $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
  $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);
});

// Calculate minimum quantity of product qty
$(document).on("input", "#qty_input", function () {
  var product_unit = $(this).closest("tr").find("#product_unit").text();
  var ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
  $(this).closest("tr").find("#minimum_qty").text(calUnitQty(product_unit, ucv_name, parseFloat($(this).val())));
});

// Auto generate minimum qty of free items
$(document).on("input", "#free_qty", function () {
  var product_unit = $(this).closest("tr").find("#product_unit").text();
  var ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
  $(this).closest("tr").find("#free_minimum_qty").text(calUnitQty(product_unit, ucv_name, parseFloat($(this).val())));
});

// Calculate minimum quantity based on product unit and ucv_name
function calUnitQty(product_unit, ucv_name, qty) {

  let unit_qty;
  switch (product_unit) {
    case "l":
      unit_qty = ucv_name * qty * 1000;
      return (unit_qty + "ml");
      break;
    case "kg":
      unit_qty = ucv_name * qty * 1000;
      return (unit_qty + "kg");
      break;
    case "m":
      unit_qty = ucv_name * qty * 100;
      return (unit_qty + "m");
      break;
    case "ml":
      unit_qty = ucv_name * qty;
      return (unit_qty + "ml");
      break;
    case "g":
      unit_qty = ucv_name * qty;
      return (unit_qty + "g");
      break;
    case "cm":
      unit_qty = ucv_name * qty;
      return (unit_qty + "cm");
      break;
  }
}

// Calculate total value when item price is changed
$(document).on("input", "#item_price", function () {
  const $row = $(this).closest("tr");
  const price = parseFloat($(this).val());
  var product_name = $row.find("#product_name").text();
  var qty = parseFloat($row.find("#qty_input").val());

  if (isNaN(qty)) {
    $(this).val("");
    ErrorMessageDisplay(product_name + " එකේ Qty දාලා ඉන්න.");
  } else {
    total_value = price * qty;
    $row.find("#total_value").text(total_value.toFixed(2));
  }
});

// calculate total cost and cost per unit when item discount is changed
$(document).on("input", "#item_discount", function () {
  const $row = $(this).closest("tr");
  var product_name = $row.find("#product_name").text();
  var product_unit = $row.find("#product_unit").text();
  var qty = parseFloat($row.find("#qty_input").val());
  var minimum_qty = parseFloat($row.find("#minimum_qty").text().replace(/[^\d.]/g, ''));
  var item_price = parseFloat($row.find("#item_price").val());
  var item_discount = parseFloat($(this).val());

  if (isNaN(qty)) {
    $(this).val("");
    ErrorMessageDisplay(product_name + " එකේ Qty දාලා ඉන්න.");
  } else if (isNaN(item_price)) {
    $(this).val("");
    ErrorMessageDisplay(product_name + " එකේ Item Price දාලා ඉන්න.");
  } else {
    var total_value = item_price * qty;
    $row.find("#total_value").text(total_value.toFixed(0));
    var total_cost = (total_value * (100 - item_discount)) / 100;
    $row.find("#total_cost").val(total_cost.toFixed(0));
    if (product_unit !== 'pack / bottle' && product_unit !== 'pieces') {
      var unit_cost = total_cost / minimum_qty;
      $row.find("#cost_per_unit").text(unit_cost.toFixed(2));
    }
  }
});

// calculate item discount and cost per unit when total cost is changed
$(document).on("input", "#total_cost", function () {
  const $row = $(this).closest("tr");
  var product_name = $row.find("#product_name").text();
  var product_unit = $row.find("#product_unit").text();
  var qty = parseFloat($row.find("#qty_input").val());
  var minimum_qty = parseFloat($row.find("#minimum_qty").text().replace(/[^\d.]/g, ''));
  var item_price = parseFloat($row.find("#item_price").val());
  var total_cost = parseFloat($(this).val());

  if (isNaN(qty)) {
    $(this).val("");
    ErrorMessageDisplay(product_name + " එකේ Qty දාලා ඉන්න.");
  } else if (isNaN(item_price)) {
    $(this).val("");
    ErrorMessageDisplay(product_name + " එකේ Item Price දාලා ඉන්න.");
  } else {
    var total_value = item_price * qty;
    $row.find("#total_value").text(total_value.toFixed(0));
    var item_discount = ((total_value - total_cost) / total_value) * 100;
    if (item_discount < 0) {
      item_discount = 0;
    }
    $row.find("#item_discount").val(item_discount.toFixed(2));
    if (product_unit !== 'pack / bottle' && product_unit !== 'pieces') {
      var unit_cost = total_cost / minimum_qty;
      $row.find("#cost_per_unit").text(unit_cost.toFixed(2));
    }
  }
});

// Confirm Purchase Order
$(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function () {
  $(".confirmPObtn").prop('disabled', true);
  var invoice_number = $("#invoice_number").val().trim() || null;
  var supplier_id = $("#supplier_select").val()
  var poArray = [];

  $("#grnConfirmationTableBody tr").each(function () {
    var product_code = $(this).find(".product_code").text();
    var product_name = $(this).find(".product_name").text();
    var product_total_qty = $(this).find(".product_total_qty").text();
    var product_qty = $(this).find(".product_qty").text();
    var minimum_qty = $(this).find(".minimum_qty").text();
    var total_cost = $(this).find(".total_cost").text();
    var total_value = $(this).find(".total_value").text();
    var cost_per_unit = $(this).find(".cost_per_unit").text();
    var item_discount = $(this).find(".item_discount").text();
    var item_price = $(this).find(".item_price").text();
    var unit_s_price = $(this).find(".unit_s_price").text();
    var free_qty = $(this).find(".free_qty").text();
    var cost_per_item = item_price * (1 - item_discount / 100);

    var productData = {
      product_code,
      product_name,
      product_total_qty,
      product_qty,
      minimum_qty,
      item_price,
      item_discount,
      cost_per_unit,
      cost_per_item,
      total_cost,
      total_value,
      unit_s_price,
      free_qty,
      mfd,
      exp,
    };
    poArray.push(productData);
  });

  $.ajax({
    url: "grnConfirmationInsert.php",
    method: "POST",
    data: {
      invoice_number,
      supplier_id,
      products: JSON.stringify(poArray),
    },
    dataType: "json",
    success: function (response) {

      if (response.status === 'success') {
        SuccessMessageDisplay(response.message);
        setTimeout(() => {
          location.reload();
        }, 3000);
      } else if (response.status === 'error') {
        $(".confirmPObtn").prop('disabled', false);
        ErrorMessageDisplay(response.message);
      } else if (response.status === 'sessionExpired') {
        $(".confirmPObtn").prop('disabled', false);
        ErrorMessageDisplay(response.message);
        setTimeout(function () {
          window.open(window.location.href, '_blank');
        }, 5000);
      } else {
        ErrorMessageDisplay(response.status, response.message);
      }
    },
    error: function (xhr, status, error) {
      ErrorMessageDisplay("Connection failed. Check Internet connection.");
      $(".confirmPObtn").prop('disabled', false);
    },
  });
});

// ==============================================================================

function fbs() {

  if ($("#supplier_select").val() == 0 || $("#supplier_select").val() == null) {
    ErrorMessageDisplay("Select supplier!");
    return
  }

  var bnInput = document.getElementById("bnInput").value;
  var pcInput = document.getElementById("pcInput").value;
  var pnInput = document.getElementById("pnInput").value;
  var searchBy = "";

  if (bnInput) {
    searchBy += "barcode";
  }
  if (pcInput) {
    if (searchBy) {
      searchBy += " & ";
    }
    searchBy += "product code";
  }
  if (pnInput) {
    if (searchBy) {
      searchBy += " & ";
    }
    searchBy += "product name";
  }
  if (!searchBy) {
    searchBy = "all";
  }

  var form = new FormData();
  form.append("bnInput", bnInput);
  form.append("pcInput", pcInput);
  form.append("pnInput", pnInput);
  form.append("searchBy", searchBy);

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      document.getElementById("filterBySupTable").innerHTML = response;
    }
  };
  req.open("POST", "fbs.php", true);
  req.send(form);
}

// ==============================================================================
