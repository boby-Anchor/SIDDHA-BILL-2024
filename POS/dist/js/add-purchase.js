function select_suplier(sup_id) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
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
  req.onreadystatechange = function () {
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

{
  $(document).on("click", ".proceed-order", function () {
    var poArray = [];

    $(".addedProTable tbody tr")
      .slice(1)
      .each(function () {
        var product_code = $(this).find("th").text();
        var product_name = $(this).find("#addproduct_name").text();
        var product_cost = $(this).find("#addproduct_cost").text();
        var product_qty = $(this).find("input").val();
        var unit = $(this).find("select[name='quantity_unit']").val();

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

          $("#proceedOrderBtn").removeAttr("data-toggle data-target");
        } else if (unit === "0") {
          Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
          }).fire({
            icon: "error",
            title: "Error: Product Unit!",
          });

          $("#proceedOrderBtn").removeAttr("data-toggle data-target");
        } else {
          var productData = {
            product_code: product_code,
            product_name: product_name,
            product_cost: product_cost,
            product_qty: product_qty,
            unit: unit,
          };
          poArray.push(productData);

          $.ajax({
            url: "poConfirmation.php",
            method: "POST",
            data: {
              products: poArray,
            },
            success: function (response) {
              document.getElementById("orderConfirmationTableBody").innerHTML =
                response;
            },
            error: function (xhr, status, error) {
              console.error(xhr.responseText);
            },
          });

          $("#proceedOrderBtn").attr({
            "data-toggle": "modal",
            "data-target": "#confirmPO",
          });
        }
      });
  });
}

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
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      document.getElementById("filterBySupTable").innerHTML = response;
    }
  };
  req.open("POST", "filterBySearch.php", true);
  req.send(form);
}

// ===============================================================================

function refreshOptions(selectedItem) {
  if (selectedItem == "0") {
    var req = new XMLHttpRequest();
    req.onreadystatechange = function () {
      if (req.readyState == 4 && req.status == 200) {
        var response = req.responseText;
        document.getElementById("unitselectordiv").innerHTML = response;
      }
    };
    req.open("POST", "refreshUnitSelector.php", true);
    req.send();
  }
}
