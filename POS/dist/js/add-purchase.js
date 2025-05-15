
function select_suplier(sup_id) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.response;
      document.getElementById("filterBySupTable").innerHTML = response;
    } else {
      console.log(response);
    }
  };
  req.open("GET", "actions/filterBySupplier.php?sup_id=" + sup_id, true);
  req.send();
}

// ==============================================================================

function addNewUnit() {
  var newUnit = document.getElementById("newUnit").value;

  var req = new XMLHttpRequest();
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      var iconType;

      if (response == "Unit added successfully !") {
        iconType = "success";
        refreshOptions(0);
      } else {
        iconType = "error";
      }

      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: iconType,
        title: response,
      });
    }
  };
  req.open("GET", "'../../actions/addNewUnit.php?newUnit=" + newUnit, true);
  req.send();
}

// ==============================================================================


$(document).on("click", ".proceed-order", function() {

  if (!$("#order_date").val()) {
    ErrorMessageDisplay("Select order Date");
  }
  var poArray = [];

  var markup = "";

  $(".addedProTable tbody tr").each(function() {
    var product_code = $(this).find("#product_code").text();
    var product_name = $(this).find("#product_name").text();
    var product_brand = $(this).find("#product_brand").text();
    var product_qty = $(this).find("#product_qty").val();
    var product_unit = $(this).find("#product_unit").text();

    if (product_qty === "" || product_qty === "0") {
      ErrorMessageDisplay(product_name + "Invalid qty");
      $("#proceedOrderBtn").removeAttr("data-toggle data-target");
    } else {
      var productData = {
        product_code: product_code,
        product_name: product_name,
        product_brand: product_brand,
        product_qty: product_qty,
        product_unit: product_unit,
      };
      console.log(productData);

      markup +=
        "<tr class=''>" +
        "<th class='product_code'>" + product_code + "</th>" +
        "<td class='product_name'>" + product_name + "</td>" +
        "<td class='product_brand'>" + product_brand + "</td>" +
        "<td class'product_unit'>" + product_unit + "</td>" +
        "<td class='product_qty'>" + product_qty + " </td>" +
        "<td class=''></td>" +
        "</tr>";

      $(".orderConfirmationTable tbody").html(markup);

      $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
      $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

      poArray.push(productData);

      // $.ajax({
      //   url: "poConfirmation.php",
      //   method: "POST",
      //   data: {
      //     products: poArray,
      //   },
      //   success: function (response) {
      //     document.getElementById("orderConfirmationTableBody").innerHTML =
      //       response;
      //   },
      //   error: function (xhr, status, error) {
      //     console.error(xhr.responseText);
      //   },
      // });

      $("#proceedOrderBtn").attr({
        "data-toggle": "modal",
        "data-target": "#confirmPO",
      });
    }
  });
});


$(document).on("click", ".add-btn", function() {
  var product_code = $(this).closest("tr").find("#product_code").text();
  var product_name = $(this).closest("tr").find("#product_name").text();
  var product_brand = $(this).closest("tr").find("#product_brand").text();
  var product_volume = $(this).closest("tr").find("#product_volume").text();
  var product_unit = $(this).closest("tr").find("#product_unit").text();

  var exists = false;
  $(".addedProTable tbody tr").each(function() {
    if ($(this).find("#product_code").text() === product_code) {
      exists = true;
      return false;
    }
  });

  if (!exists) {
    var markup =
      "<tr class='row'>" +
      "<th class='col-1' id='product_code'>" + product_code + "</th>" +
      "<td class='col-3' id='product_name'>" + product_name + "</td>" +
      "<td class='col-2' id='product_brand'>" + product_brand + "</td>" +
      "<td class='col-1 text-center' id='product_unit'>" + product_volume + product_unit + "</td>" +
      "<td class='col-2' colspan=2> <input type='number' class='form-control bg-dark' id='product_qty' oninput='this.value = this.value.replace(/[^0-9]/g, '')' /> </td>" +
      "<td class='col-1'></td>" +
      "<td class='col-1 text-right'><i class='fa fa-trash-o cus-delete'></i></td>" +
      "</tr>";

    $(".addedProTable tbody").append(markup);

    $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
    $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

  } else {
    ErrorMessageDisplay("Product already exists in the list!");
  }
});

$(document).on("click", ".cus-delete", function() {
  $(this).closest("tr").remove();
  $("#proceedOrderBtn").removeAttr("data-toggle data-target");
  $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 1);
  $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 1);
});

$('#myModal').on('shown.bs.modal', function() {
  $('#myInput').trigger('focus')
})


// ==============================================================================

function filterBySearch(searchTxt) {
  // alert("in search");
  //console.log("in search");
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
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      document.getElementById("filterBySupTable").innerHTML = response;
    }
  };
  req.open("POST", "filterBySearch-PO.php", true);
  req.send(form);
}

// ===============================================================================

function refreshOptions(selectedItem) {
  if (selectedItem == "0") {
    var req = new XMLHttpRequest();
    req.onreadystatechange = function() {
      if (req.readyState == 4 && req.status == 200) {
        var response = req.responseText;
        document.getElementById("unitselectordiv").innerHTML = response;
      }
    };
    req.open("POST", "refreshUnitSelector.php", true);
    req.send();
  }
}

$(document).off("click", ".confirmPObtn").on("click", ".confirmPObtn", function() {

  $(".confirmPObtn").prop('disabled', true);

  var deliveryDate = $("#order_date").val()
  var orderNumber = document.getElementById("orderNumber").innerText;
  var orderDate = document.getElementById("orderDate").innerText;
  var orderTime = document.getElementById("orderTime").innerText;
  $(this).prop('disabled', true);

  var poArray = [];
  var poBillData = {
    orderNumber: orderNumber,
    orderDate: orderDate,
    orderTime: orderTime,
    deliveryDate: deliveryDate,
  }

  $("#orderConfirmationTableBody tr").each(function() {
    var product_code = $(this).find(".product_code").text();
    var product_name = $(this).find(".product_name").text();
    var product_brand = $(this).find(".product_brand").text();
    var product_qty = $(this).find(".product_qty").text();
    var qty_unit = $(this).find(".qty_unit").text();

    var productData = {
      product_code: product_code,
      product_name: product_name,
      product_brand: product_brand,
      product_qty: product_qty,
    };
    poArray.push(productData);
  });

  $.ajax({
    url: "poConfirmationInsert.php",
    method: "POST",
    data: {
      products: JSON.stringify(poArray),
      poBillData: JSON.stringify(poBillData),
    },
    success: function(response) {
      SuccessMessageDisplay(response);
      setTimeout(function() {
        location.reload();
      }, 3000);
    },
    error: function(xhr, status, error) {
      console.error(xhr.responseText);
      ErrorMessageDisplay("Order Failed !");
      $(".confirmPObtn").prop('disabled', false);
    },
  });
});