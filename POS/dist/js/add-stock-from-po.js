$(document).on("click", "#searchPo", function() {
  var poNumber = $("#poNumber").val();
  $.ajax({
      url: "actions/add-stock-from-po_search.php",
      method: "POST",
      data: {
          poNumber: poNumber,
      },
      success: function(response) {
          var poResult;
          var result = JSON.parse(response);

          if (result.status === 'success') {
              var rowCount = 0;
              $("#itemDataTable").empty();
              poResult = result.invoiceData[0];
 
              $("#stockKeeper").text(poResult.stockKeeper);
              $("#shopName").text(poResult.shop);
              $("#shopId").text(poResult.shop_id);
              $("#poShopName").text(poResult.poShop);
              $("#poShopId").text(poResult.po_shop_id);
              $("#date").text(poResult.created);
              $("#subtotal").text(poResult.sub_total);
              $("#discount").text(poResult.discount_percentage);
              $("#nettotal").text(poResult.net_total);

              if (result.items) {
                  $("#addStockButton").removeClass("d-none");

                  result.items.forEach(function(itemData) {
                      rowCount++;
                      var row = '<tr>' +
                          '<td>' + rowCount + '</td>' +
                          '<td id="product_name">' + itemData.invoiceItem + '</td>' +
                          '<td id="product_code">' + itemData.item_code + '</td>' +
                          '<td id="ucv">' + itemData.invoiceItem_ucv + '</td>' +
                          '<td id="type">' + itemData.invoiceItem_unit + '</td>' +
                          '<td id="product_qty">' + itemData.invoiceItem_qty + '</td>' +
                          '<td id="item_price">' + itemData.invoiceItem_price + '</td>' +
                          '<td id="item_total">' + itemData.invoiceItem_total + '</td>' +
                          '<td><input type="text" class="text-center" id="manual_qty" name="manual_qty" value="' + itemData.invoiceItem_qty + '" readonly></td>' +
                          '</tr>';
                      document.getElementById('itemDataTable').insertAdjacentHTML('beforeend', row);
                  });
              } else {
                  var row = '<tr colspan="8">No Data Found</tr>';
                  document.getElementById('itemDataTable').insertAdjacentHTML('beforeend', row);
              }
          }
      },
      error: function(xhr, status, error) {
          console.error(xhr.responseText);
      },
  });
});

$(document).on("click", "#addStockButton", function() {
  $("#addStockButton").prop('disabled', true);
  var poItemData = [];
  var poShopId = $("#poShopId").text();

  $("#itemDataTable tr").each(function() {
      var product_code = $(this).find("#product_code").text();
      var product_name = $(this).find("#product_name").text();
      var ucv = $(this).find("#ucv").text();
      var type = $(this).find("#type").text();
      var product_qty = $(this).find("#product_qty").text();
      var item_price = $(this).find("#item_price").text();
      var manual_qty = $(this).find("#manual_qty").val();

      var productData = {
          poShopId: poShopId,
          product_code: product_code,
          product_name: product_name,
          ucv: ucv,
          type: type,
          product_qty: product_qty,
          item_price: item_price,
          manual_qty: manual_qty,
      };
      poItemData.push(productData);
  });

  $.ajax({
      url: "actions/add-stock-from-po_action.php",
      method: "POST",
      data: {
          poItemData: JSON.stringify(poItemData),
          poNumber :$("#poNumber").val(),
      },
      success: function(response) {
          Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true, // Show progress bar during timer
              didOpen: (toast) => {
                  // Pause timer when mouse hover
                  toast.addEventListener('mouseenter', Swal.stopTimer);
                  toast.addEventListener('mouseleave', Swal.resumeTimer);
              }
          }).fire({
              icon: "success",
              title: "Success: " + response,
          }).then(() => {
              location.reload(true);
          });
      },
      error: function(xhr, status, error) {
          console.error(xhr.responseText);
      },
  });
});
