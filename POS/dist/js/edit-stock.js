$(document).on("click", ".proceed-grn", function () {
  var poArray = [];

  $(".addedProTable tbody tr").each(function () {
    var stock_id = $(this).find("#stock_id").text();
    var stock_shop_id = $(this).find("#shop_id").text();
    var product_code = $(this).find("#product_code").text();

    var old_unit_cost = $(this).find("#old_unit_cost").text();
    var old_added_discount = $(this).find("#old_added_discount").text();
    var old_item_s_price = $(this).find("#old_item_s_price").text();
    var product_name = $(this).find("#product_name").text();

    var product_qty = parseInt($(this).find("#qty_input").val());
    var minimum_qty = $(this).find("#minimum_qty").text();

    var cost_input = parseInt($(this).find("#cost_input").val());

    var item_discount = parseInt($(this).find("#item_discount").val());

    var item_sale_price = $(this).find("#item_sale_price").text();

    var cost_per_unit = $(this).find("#cost_per_unit").text();

    var unit_s_price = parseFloat($(this).find("#unit_s_price").val());

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
        stock_id: stock_id,
        stock_shop_id: stock_shop_id,
        product_code: product_code,

        old_unit_cost: old_unit_cost,
        old_added_discount: old_added_discount,
        old_item_s_price: old_item_s_price,

        product_name: product_name,
        product_qty: product_qty,
        minimum_qty: minimum_qty,
        cost_input: cost_input,

        item_discount: item_discount,
        item_sale_price: item_sale_price,
        cost_per_unit: cost_per_unit,
        unit_s_price: unit_s_price,
      };

      poArray.push(productData);

      $.ajax({
        url: "edit-stock-action.php",
        method: "POST",
        data: {
          products: JSON.stringify(poArray),
        },
        success: function (response) {
          Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
          }).fire({
            icon: "success",
            title: "Stock Updated Successfully!",
          });
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

  console.log(poArray);
});
