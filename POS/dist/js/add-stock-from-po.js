

$(document).on("click", "#addStockButton", function () {

  var poItemData = [];

  var poShopId = $("#poShopId").text();

  $("#itemDataTable tr").each(function () {
    var product_code = $(this).find("#product_code").text();
    var product_name = $(this).find("#product_name").text();
    var ucv = $(this).find("#ucv").text();
    var product_qty = $(this).find("#product_qty").text();
    var item_price = $(this).find("#item_price").text();
    var manual_qty = $(this).find("#manual_qty").val();

    var productData = {
      poShopId: poShopId,
      product_code: product_code,
      product_name: product_name,
      ucv: ucv,
      product_qty: product_qty,
      item_price: item_price,
      manual_qty: manual_qty,
    };
    poItemData.push(productData);
  });

  $.ajax({
    url: "add-stock-from-po_action.php",
    method: "POST",
    data: {
      poItemData: JSON.stringify(poItemData),
    },
    success: function (response) {
      document.getElementById("grnConfirmationTableBody").innerHTML =
        response;
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
    },
  });

});