// ==============================================================================

$(document).on("click", "#proceedGrnBtn", function () {
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

    var total_cost = $(this).find("#total_cost").text().trim(); // Total cost
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
        product_code: product_code,
        product_name: product_name,

        product_qty: product_qty,
        free_qty: free_qty,

        minimum_qty: minimum_qty,
        free_minimum_qty: free_minimum_qty,

        item_price: item_price,
        item_discount: item_discount,

        total_cost: total_cost,
        total_value: total_value,
        cost_per_unit: cost_per_unit,
        unit_s_price: unit_s_price,
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

  if (!exists) {
    // Append new row to the table
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

      "<td>" + "<input type='text' id='item_price' class='bg-dark form-control text-center' value=''  placeholder='Price'></td>" +

      "<td>" + "<input id='item_discount' type='text' class='bg-dark form-control text-center' value='' placeholder='Discount'>" + "</td>" +

      "<td>" + "<label id='total_cost'></label>" + "</td>" +

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

// Calculate minimum quantity unit based on product_unit
$(document).on("input", "#qty_input", function () {
  var product_unit = $(this).closest("tr").find("#product_unit").text();
  var ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
  if (product_unit === 'l') {
    var liters = parseFloat($(this).val());
    var milliliters = ucv_name * liters * 1000;
    $(this).closest("tr").find("#minimum_qty").text(milliliters + "ml");
  }
  if (product_unit === 'kg') {
    var kilo = parseFloat($(this).val());
    var grams = ucv_name * kilo * 1000;
    $(this).closest("tr").find("#minimum_qty").text(grams + "g");
  }
  if (product_unit === 'm') {
    var meter = parseFloat($(this).val());
    var centimete = ucv_name * meter * 100;
    $(this).closest("tr").find("#minimum_qty").text(centimete + "cm");
  }
  if (product_unit === 'ml') {
    var ml = parseFloat($(this).val());
    var mililiters = ucv_name * ml;
    $(this).closest("tr").find("#minimum_qty").text(mililiters + "ml");
  }
  if (product_unit === 'g') {
    var g = parseFloat($(this).val());
    var grams = ucv_name * g;
    $(this).closest("tr").find("#minimum_qty").text(grams + "g");
  }
  if (product_unit === 'cm') {
    var cm = parseFloat($(this).val());
    var centimeters = ucv_name * cm;
    $(this).closest("tr").find("#minimum_qty").text(centimeters + "cm");
  }
});

// free qty input for auto generate minimum qty
$(document).on("input", "#free_qty", function () {
  var product_unit = $(this).closest("tr").find("#product_unit").text();
  var ucv_name = parseFloat($(this).closest("tr").find("#ucv_name").text());
  if (product_unit === 'l') {
    var liters = parseFloat($(this).val());
    var milliliters = ucv_name * liters * 1000;
    $(this).closest("tr").find("#free_minimum_qty").text(milliliters + "ml");
  }
  if (product_unit === 'kg') {
    var kilo = parseFloat($(this).val());
    var grams = ucv_name * kilo * 1000;
    $(this).closest("tr").find("#free_minimum_qty").text(grams + "g");
  }
  if (product_unit === 'm') {
    var meter = parseFloat($(this).val());
    var centimete = ucv_name * meter * 100;
    $(this).closest("tr").find("#free_minimum_qty").text(centimete + "cm");
  }
  if (product_unit === 'ml') {
    var ml = parseFloat($(this).val());
    var mililiters = ucv_name * ml;
    $(this).closest("tr").find("#free_minimum_qty").text(mililiters + "ml");
  }
  if (product_unit === 'g') {
    var g = parseFloat($(this).val());
    var grams = ucv_name * g;
    $(this).closest("tr").find("#free_minimum_qty").text(grams + "g");
  }
  if (product_unit === 'cm') {
    var cm = parseFloat($(this).val());
    var centimeters = ucv_name * cm;
    $(this).closest("tr").find("#free_minimum_qty").text(centimeters + "cm");
  }
});

// calculate cost per unit 
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



$(document).on("input", "#item_discount", function () {
  const $row = $(this).closest("tr");
  var product_name = $row.find("#product_name").text();
  var ucv_name = $row.find("#ucv_name").text();
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
    $row.find("#total_cost").text(total_cost.toFixed(0));
    if (product_unit !== 'pack / bottle' && product_unit !== 'pieces') {
      var unit_cost = total_cost / minimum_qty;
      $row.find("#cost_per_unit").text(unit_cost.toFixed(2));
    }
  }
});


$(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function () {
  // $(this).prop('disabled', true);
  $(".confirmPObtn").prop('disabled', true);

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
      product_code: product_code,
      product_name: product_name,
      product_total_qty: product_total_qty,
      product_qty: product_qty,
      minimum_qty: minimum_qty,
      item_price: item_price,
      item_discount: item_discount,
      cost_per_unit: cost_per_unit,
      cost_per_item: cost_per_item,
      total_cost: total_cost,
      total_value: total_value,
      unit_s_price: unit_s_price,
      free_qty: free_qty,
    };
    poArray.push(productData);
  });

  $.ajax({
    url: "grnConfirmationInsert.php",
    method: "POST",
    data: {
      products: JSON.stringify(poArray),
    },
    success: function (response) {
      var result = JSON.parse(response);

      if (result.status === 'success') {
        SuccessMessageDisplay(result.message);
        setTimeout(() => {
          location.reload();
        }, 3000);
      } else if (result.status === 'error') {
        // $(".confirmPObtn").prop('disabled', false);
        ErrorMessageDisplay(result.message);
      } else if (result.status === 'sessionExpired') {
        // $(".confirmPObtn").prop('disabled', false);
        ErrorMessageDisplay(result.message);
        setTimeout(function () {
          window.open(window.location.href, '_blank');
        }, 5000);
      } else {
        ErrorMessageDisplay(result.status, result.message);
      }
      $(".confirmPObtn").prop('disabled', false);
    },
    error: function (xhr, status, error) {
      ErrorMessageDisplay("Connection failed. Check Internet connection.");
      $(".confirmPObtn").prop('disabled', false);
    },
  });

});

// ==============================================================================

function fbs() {
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
