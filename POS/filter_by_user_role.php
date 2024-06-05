<?php
require "config/db.php";
session_start();

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {

        $shop_id = $userData['shop_id'];

        if (isset($_POST['user_role'])) {

            $user_role = $_POST['user_role'];

            if ($user_role == "") {
                $user_role_rs =  $conn->query("SELECT * FROM users INNER JOIN user_role ON user_role.user_role_id = users.user_role_id INNER JOIN shop ON shop.shopId = users.shop_id WHERE shop.shopId = '$shop_id' ORDER BY shop.shopId ASC");
            } else if ($user_role == "" & $shop_id == "1") {
                $user_role_rs =  $conn->query("SELECT * FROM users INNER JOIN user_role ON user_role.user_role_id = users.user_role_id INNER JOIN shop ON shop.shopId = users.shop_id  ORDER BY shop.shopId ASC");
            } else if ($shop_id == "1") {
                $user_role_rs =  $conn->query("SELECT * FROM users INNER JOIN user_role ON user_role.user_role_id = users.user_role_id INNER JOIN shop ON shop.shopId = users.shop_id WHERE users.user_role_id = '$user_role'");
            }  else {
                $user_role_rs =  $conn->query("SELECT * FROM users INNER JOIN user_role ON user_role.user_role_id = users.user_role_id INNER JOIN shop ON shop.shopId = users.shop_id WHERE users.user_role_id = '$user_role' AND  shop.shopId = '$shop_id' ");
            }


            while ($user_role_row = $user_role_rs->fetch_assoc()) {
?>
                <div class="col-12 d-flex">
                    <div class="col-3">
                        <span><?= $user_role_row['name'] ?></span>
                    </div>
                    <div class="col-3">
                        <?= $user_role_row['user_role'] ?>
                    </div>
                    <div class="col-2">
                        <?= $user_role_row['user_name'] ?>
                    </div>
                    <div class="col-2">
                        <?= $user_role_row['shopName'] ?>
                    </div>
                    <div class="col-3">
                        <button class="btn btn-danger delete-btn" data-unique-id="<?= $user_role_row['id'] ?>">Delete</button>
                        <button class="btn btn-success edite-btn" data-unique-id="<?= $user_role_row['id'] ?>">Edit</button>
                    </div>

                </div>
            <?php
            }
            ?>
            <script>
                // edite users start===
                $(document).ready(function() {
                    $(".edite-btn").click(function() {
                        var user_id = $(this).data("unique-id");

                        var formData = new FormData(); // Corrected typo here

                        formData.append("user_id", user_id);

                        $.ajax({
                            url: "edite_user.php",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $("#userForm").html(response);
                            },
                            error: function(xhr, status, error) {
                                console.error("Error:", error);
                            },
                        });

                    });
                });
                // edite users end=====

                // delete users start===
                $(document).ready(function() {
                    $(".delete-btn").click(function() {
                        var user_id = $(this).data("unique-id");

                        var formData = new FormData(); // Corrected typo here

                        formData.append("user_id", user_id);

                        $.ajax({
                            url: "delete_user_process.php",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.mixin({
                                    toast: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    timer: 3000,
                                }).fire({
                                    icon: "info",
                                    title: response,
                                });
                                if (response == "Record deleted successfully") {
                                    location.reload(true);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error:", error);
                            },
                        });

                    });
                });
                // delete users end=====
            </script>
<?php
        } else {

            echo "No value received";
        }
    }
}
