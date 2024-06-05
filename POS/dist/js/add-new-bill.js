$(document).ready(function () {
  $(".submitBtn").click(function () {
    var selectedShop = $("#selectedShop").val();
    var logo = $("#fileInput")[0].files[0]; // Get the file object
    var contactNo = $("#contactNo").val();
    var address = $("#address").val();
    var note = $("#note").val();

    // Create FormData object to send file along with other data
    var formData = new FormData();
    formData.append("selectedShop", selectedShop);
    formData.append("logo", logo);
    formData.append("contactNo", contactNo);
    formData.append("address", address);
    formData.append("note", note);

    // AJAX request
    $.ajax({
      url: "add_new_bill_process.php",
      type: "POST",
      data: formData,
      processData: false, // Important: prevent jQuery from processing the data
      contentType: false, // Important: prevent jQuery from setting contentType
      success: function (data) {
        alert(data); // Alert success or error message
        // Optionally, you can perform additional actions here after successful insertion
      },
      error: function (xhr, status, error) {
        alert("Error uploading file. Please try again."); // Alert if there's an error
      },
    });
  });
});
