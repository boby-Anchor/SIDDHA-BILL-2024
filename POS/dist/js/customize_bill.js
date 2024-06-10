// input range scripts start=======
var rangeSlider = function () {
  var slider = $(".range-slider"),
    range = $(".range-slider__range"),
    value = $(".range-slider__value");

  slider.each(function () {
    value.each(function () {
      var value = $(this).prev().attr("value");
      $(this).html(value);
    });

    range.on("input", function () {
      $(this)
        .next(value)
        .html(this.value + "mm");
    });
  });
};

rangeSlider();

// input range scripts end=========

// table drag and move scripts start========
const rows = document.querySelectorAll("#sortable tr");
let dragSrcEl = null;

function handleDragStart(e) {
  e.dataTransfer.effectAllowed = "move";
  e.dataTransfer.setData("text/html", this.innerHTML);
  dragSrcEl = this;
  this.classList.add("dragging");
}

function handleDragOver(e) {
  if (e.preventDefault) {
    e.preventDefault();
  }
  e.dataTransfer.dropEffect = "move";
  return false;
}

function handleDragEnter(e) {
  this.classList.add("over");
}

function handleDragLeave() {
  this.classList.remove("over");
}

function handleDrop(e) {
  if (e.stopPropagation) {
    e.stopPropagation();
  }
  if (dragSrcEl !== this) {
    dragSrcEl.innerHTML = this.innerHTML;
    this.innerHTML = e.dataTransfer.getData("text/html");

    const rows = document.querySelectorAll("#sortable tbody tr");
    rows.forEach(function (row, index) {
      const position = index + 1;
      const title = row.querySelector("h2").innerText;

      var form = new FormData();
      form.append("position", position);
      form.append("title", title);

      var req = new XMLHttpRequest();
      req.onreadystatechange = function () {
        if (req.readyState == 4 && req.status == 200) {
          var response = req.responseText;
          updateRowOrder();
        }
      };
      req.open("POST", "customizeTablePositionInsert.php", true);
      req.send(form);
    });
  }
  return false;
}

function handleDragEnd() {
  rows.forEach(function (row) {
    row.classList.remove("over");
    row.classList.remove("dragging");
  });
}

rows.forEach(function (row) {
  row.addEventListener("dragstart", handleDragStart, false);
  row.addEventListener("dragenter", handleDragEnter, false);
  row.addEventListener("dragover", handleDragOver, false);
  row.addEventListener("dragleave", handleDragLeave, false);
  row.addEventListener("drop", handleDrop, false);
  row.addEventListener("dragend", handleDragEnd, false);
});

function updateRowOrder() {
  const rows = document.querySelectorAll("#sortable tbody tr");
  rows.forEach(function (row, index) {
    row.setAttribute("data-order", index + 1);
  });
}

// table drag and move scripts end==========

// table data sort by position script start=====
function sortRows() {
  const table = document.getElementById("sortable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));
  rows.sort((a, b) => {
    const orderA = parseInt(a.getAttribute("data-order"));
    const orderB = parseInt(b.getAttribute("data-order"));
    return orderA - orderB;
  });
  rows.forEach((row) => tbody.appendChild(row));
}
sortRows();
// table data sort by position script end=======

// upload logo js start=====
$(document).ready(function () {
  $(".logoImg").click(function () {
    $("#fileInput").click();
  });

  $("#fileInput").change(function () {
    var file = this.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $(".logoImg").css("background-image", "url(" + e.target.result + ")");
        // Send file to server via AJAX
        var formData = new FormData();
        formData.append("file", file);
        $.ajax({
          url: "updateCustomizeBill.php",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            // Handle response from server if needed
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // Handle error if any
          },
        });
      };
      reader.readAsDataURL(file);
    }
  });
});
// upload logo js end=====

// update contact number js start======
document
  .getElementById("contactNoInput")
  .addEventListener("input", function () {
    var inputValue = this.value;
    var selectedShopId = $("#selectedShopNumber").val();
    inputValue = inputValue.replace(/\D/g, "");

    inputValue = inputValue.slice(0, 10);

    this.value = inputValue;

    if (inputValue.length === 10 && !isNaN(inputValue)) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "updateCustomizeBill.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
        }
      };
      xhr.send("contactNo=" + inputValue);
    }
  });

// update contact number js end========

// update address js start======
document
  .getElementById("addressNoInput")
  .addEventListener("input", function () {
    var inputValue = this.value;

    this.value = inputValue;

    if (inputValue.length != 0) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "updateCustomizeBill.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
        }
      };
      xhr.send("address=" + inputValue);
    }
  });

// update address js end========

// update note js start======
document.getElementById("noteInput").addEventListener("input", function () {
  var inputValue = this.value;

  this.value = inputValue;

  if (inputValue.length != 0) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "updateCustomizeBill.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
      }
    };
    xhr.send("noteInput=" + inputValue);
  }
});

// update note js end========

// real time update bill contact preview

const contactNoInput = document.getElementById("contactNoInput");
const contactNumberPreview = document.getElementById("contactNumberPreview");
contactNoInput.addEventListener("input", function () {
  contactNumberPreview.textContent = contactNoInput.value;
});

// real time update bill address preview
const addressNoInput = document.getElementById("addressNoInput");
const addresspreview = document.getElementById("addresspreview");
addressNoInput.addEventListener("input", function () {
  addresspreview.textContent = addressNoInput.value;
});

// real time update bill note preview
const noteInput = document.getElementById("noteInput");
const billnotepreview = document.getElementById("billnotepreview");
noteInput.addEventListener("input", function () {
  billnotepreview.textContent = noteInput.value;
});

// select shop script start====
$(document).ready(function () {
  $("#selectedShop").change(function () {
    var selectedShopId = $(this).val();
    // console.log("Selected Shop ID: " + selectedShopId);

    var formData = new FormData();
    // formData.append("file", file);
    formData.append("selectedShopId", selectedShopId);
    $.ajax({
      url: "changeCutomizeBill.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#sortable").html(response);
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        // Handle error if any
      },
    });

    $.ajax({
      url: "changeCutomizeBillPreview.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#billpreviewTable tbody").html(response);
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        // Handle error if any
      },
    });
  });
});
// select shop script end======
