// ==============================================================================

$(document).on("click", "#proceedGrnBtn", function() {
  var poArray = [];
  var hasErrors = false; // Flag to track if there are any errors

  $(".addedProTable tbody tr").each(function() {
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
          MessageDisplay("error", "Error", product_name + " එකේ Qty දාන්නේ නැද්ද?");
          hasErrors = true;
          return false;
      } else if (isNaN(item_price) || item_price === 0) {
          MessageDisplay("error", "Error", product_name + " එකේ Price නැද්ද?");
          hasErrors = true;
          return false;
      } else if (isNaN(item_discount)) {
          MessageDisplay("error", "Error", product_name + " එකේ Discount නැද්ද?");
          hasErrors = true;
          return false;
      } else if (unit_s_price !== 0 && cost_per_unit > unit_s_price) {
          MessageDisplay("error", "Error", product_name + " එකේ Unit Cost > Unit Price..!");
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
      poArray.forEach(function(product) {
          var totalProductQty = product.product_qty + product.free_qty;
          var minimumQty = product.minimum_qty === 0 ?
              0 : product.minimum_qty + product.free_minimum_qty;

          tableHTML += `
          <tr>
              <th scope="row" class="product_code">${product.product_code}</th>
              <td class="product_name">${product.product_name}</td>
              <td class="product_qty">${totalProductQty}</td>
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

function MessageDisplay(icon, status, message) {
  $("#proceedGrnBtn").removeAttr("data-toggle data-target");

  Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  }).fire({
    icon: icon,
    title: status + ": " + message,
  });
}

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

// ===============================================================================
