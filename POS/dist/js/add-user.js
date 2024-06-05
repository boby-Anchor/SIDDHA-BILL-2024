// add user start====
$(document).ready(function () {
  $("#submitBtn").click(function () {
    var name = $("#name").val();
    var username = $("#username").val();
    var password = $("#password").val();
    var confirm_password = $("#confirm_password").val();
    var user_role = $("#user_role").val();
    var user_added_shop = $("#user_added_shop").val();
    if (
      name == "" ||
      username == "" ||
      password == "" ||
      confirm_password == "" ||
      user_role == "" ||
      user_added_shop == ""
    ) {
      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: "error",
        title: "Please fill in all fields.",
      });
      return;
    }
    if (password != confirm_password) {
      Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: "error",
        title: "Passwords do not match.",
      });
      return;
    }

    $.ajax({
      type: "POST",
      url: "add-user-process.php",
      data: {
        name: name,
        username: username,
        password: password,
        user_role: user_role,
        user_added_shop: user_added_shop,
      },
      success: function (response) {
        Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 3000,
        }).fire({
          icon: "info",
          title: response,
        });
        if (response == "New record created successfully") {
          location.reload(true);
        }
      },

      error: function (xhr, status, error) {
        alert("Error: " + error);
      },
    });
  });
});

// filter users by job role start===
$(document).ready(function () {
  $("#filter_user_role").change(function () {
    var user_role = $(this).val();
    var formData = new FormData();
    formData.append("user_role", user_role);
    $.ajax({
      url: "filter_by_user_role.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#u_data_main").html(response);
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });
});
// filter users by job role end=====


