function toggleDropdown() {
  var dropdownMenu = document.getElementById("dropdownMenu");
  var isExpanded = dropdownMenu.classList.contains("show");

  if (isExpanded) {
    dropdownMenu.classList.remove("show");
  } else {
    dropdownMenu.classList.add("show");
  }
}

document
.getElementById("dropdownButton")
.addEventListener("click", toggleDropdown);

function shopSelected(productId) {
  var allShop = document.getElementById("allShop" + productId);

  var checkedStatus;

  if (allShop.checked) {
    checkedStatus = "false";
  } else {
    checkedStatus = "true";
  }

  var form = new FormData();
  form.append("productId", productId);
  form.append("allShop", checkedStatus);
  form.append("shopId", "");
  form.append("shopcheck", "");

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4) {
      var response = req.responseText;
      console.log(response);
      location.reload();
    }
  };
  req.open("POST", "addToShopProductProcess.php", true);
  req.send(form);
}

function oneByoneSelect(Id, productId, shopId) {
  var shop = document.getElementById("shop" + Id);
  var lablename = document.getElementById("lable" + Id).innerText;
  var checkedStatus;

  if (shop.checked) {
    checkedStatus = "false";
  } else {
    checkedStatus = "true";
  }

  var form = new FormData();

  form.append("shopcheck", checkedStatus);
  form.append("productId", productId);
  form.append("shopId", shopId);
  form.append("allShop", "");

  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      $response = req.responseText;
      console.log($response);
    }
  };
  req.open("POST", "addToShopProductProcess.php", true);
  req.send(form);
}

function searchByCategory(selectedCategoryId) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      document.getElementById("productTable").innerHTML = response;
      handleCheckboxStyle();
    }
  };
  req.open(
    "GET",
    "searchByCategory.php?selectedCategoryId=" + selectedCategoryId,
    true
  );
  req.send();
}

function searchByCode(productCode) {
  console.log(productCode);

  var form = new FormData();
  form.append("productCode", productCode);
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      document.getElementById("productTable").innerHTML = response;
      attachEventListeners();
    }
  };
  req.open("POST", "searchByBarcode.php", true);
  req.send(form);
}

function searchByName(productName) {
  console.log(productName);
  var form = new FormData();
  form.append("productName", productName);
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState == 4 && req.status == 200) {
      var response = req.responseText;
      document.getElementById("productTable").innerHTML = response;
      attachEventListeners();
    }
  };
  req.open("POST", "searchByName.php", true);
  req.send(form);
}

function saveDetails() {
  location.reload();
}
