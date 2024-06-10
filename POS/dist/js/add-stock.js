// ==============================================================================proceed-grn

{
  $(document).on("click", ".proceed-grn", function () {
    var poArray = [];

    $(".addedProTable tbody tr").each(function () {
      var product_code = $(this).find("th").text();
      var product_name = $(this).find("#addproduct_name").text();
      var product_cost = parseInt($(this).find("#addproduct_cost").val());
      var product_qty = parseInt($(this).find("#qty_input").val());
      var free_qty = $(this).find("#free_qty").val();
      var minimum_qty = $(this).find("#minimum_qty").text();
      var free_minimum_qty = $(this).find("#free_minimum_qty").text();

      var cost_input = parseInt($(this).find("#cost_input").val());
      var cost_per_unit = $(this).find("#cost_per_unit").text();
      var unit_s_price = parseFloat($(this).find("#unit_s_price").val());
      var item_discount = parseInt($(this).find("#item_discount").val());
      var item_sale_price = $(this).find("#item_sale_price").text();
      var unit_barcode = $(this).find("#unit_barcode").val();

      var manual_unit_input = $(this).find("#manual_unit_input").val();
      var free_manual_unit_input = $(this)
        .find("#free_manual_unit_input")
        .val();


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

        $("#proceedGrnBtn").removeAttr("data-toggle data-target");
      } else {
        var productData = {
          product_code: product_code,
          product_name: product_name,
          product_cost: product_cost,
          product_qty: product_qty,
          free_qty: free_qty,
          minimum_qty: minimum_qty,
          cost_input: cost_input,
          cost_per_unit: cost_per_unit,
          unit_s_price: unit_s_price,
          item_discount: item_discount,
          item_sale_price: item_sale_price,
          free_minimum_qty: free_minimum_qty,
          manual_unit_input: manual_unit_input,
          free_manual_unit_input: free_manual_unit_input,
          unit_barcode: unit_barcode,
        };
        poArray.push(productData);

        $.ajax({
          url: "addToGrnConfirmation.php",
          method: "POST",
          data: {
            products: poArray,
          },
          success: function (response) {
            document.getElementById("grnConfirmationTableBody").innerHTML =
              response;
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
          },
        });

        $("#proceedGrnBtn").attr({
          "data-toggle": "modal",
          "data-target": "#confirmGRN",
        });
      }
    });
  });
}

// ==============================================================================

// update order status start ====
function updateOrderStatus(orderNumber, status) {
  // alert(orderNumber);
  var form = new FormData();
  form.append("orderNumber", orderNumber);
  form.append("status", status);

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var txt = req.responseText; // Removed the parentheses
      if (txt == "success") {
        location.reload(true);
      }
    }
  };
  req.open("POST", "updateOrderStatusProcess.php", true);
  req.send(form);
}

// update order status end ======
