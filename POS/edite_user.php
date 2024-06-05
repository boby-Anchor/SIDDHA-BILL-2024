<?php
require "config/db.php";
session_start();

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {

        $shop_id = $userData['shop_id'];

        if (isset($_POST['user_id'])) {

            $user_id = $_POST['user_id'];

            $user_id_rs =  $conn->query("SELECT * FROM users INNER JOIN user_role ON user_role.user_role_id = users.user_role_id INNER JOIN shop ON shop.shopId = users.shop_id WHERE users.id = '$user_id'");
            while ($user_id_row = $user_id_rs->fetch_assoc()) {
?>
                <div class="row">
                    <div class="col-12">
                        <h1 class="au-tittle">Edite User</h1>
                    </div>
                    <div class="col-12 p-3">
                        <input type="text" name="name" id="name" class="col-12 form-control-lg border-0" value="<?= $user_id_row['name'] ?>" readonly>
                        <input type="text" name="uid" id="uid" class="col-12 form-control-lg border-0 d-none" value="<?= $user_id_row['id'] ?>" readonly>
                    </div>
                    <div class="col-12 p-3">
                        <input type="text" name="username" id="username" class="col-12 form-control-lg border-0" value="<?= $user_id_row['user_name'] ?>">
                    </div>
                    <div class="col-12 p-3">
                        <input type="password" name="password" id="password" class="col-12 form-control-lg border-0" value="<?= $user_id_row['user_pass'] ?>">
                    </div>
                    <div class="col-12 p-3">
                        <input type="password" name="confirm_password" id="confirm_password" class="col-12 form-control-lg border-0" placeholder="Re-Enter Password">
                    </div>
                    <div class="col-12 p-3">
                        <select name="user_role" id="user_role" class="form-control-lg col-12 form-control-lg border-0">
                            <option value="<?= $user_id_row['user_role_id'] ?>"><?= $user_id_row['user_role'] ?></option>
                            <?php
                            if ($user_id_row['user_role_id'] == "2") {
                                $user_role_rs = $conn->query("SELECT * FROM user_role WHERE user_role_id NOT IN ('1') NOT user_role_id = '" . $user_id_row['user_role_id'] . "' ");
                            } else {
                                $user_role_rs = $conn->query("SELECT * FROM user_role WHERE user_role_id NOT IN ('1', '2') AND NOT user_role_id = '" . $user_id_row['user_role_id'] . "' ");
                            }
                            // $user_role_rs = $conn->query("SELECT * FROM user_role WHERE NOT user_role_id = '" . $user_id_row['user_role_id'] . "' ");
                            while ($user_role_data = $user_role_rs->fetch_assoc()) {
                            ?>
                                <option value="<?= $user_role_data['user_role_id'] ?>"><?= $user_role_data['user_role'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12 p-3">
                        <select name="user_added_shop" id="user_added_shop" class="form-control-lg col-12 form-control-lg border-0">
                            <option value="<?= $user_id_row['shop_id'] ?>"><?= $user_id_row['shopName'] ?></option>
                            <?php
                            if ($user_id_row['user_role_id'] == "2") {
                                $user_shop_rs = $conn->query("SELECT * FROM shop WHERE NOT shopId = '" . $user_id_row['shop_id'] . "'");
                            } else {
                                $user_shop_rs = $conn->query("SELECT * FROM shop WHERE shopId = '" . $user_id_row['shop_id'] . "'");
                            }

                            while ($user_shop_data = $user_shop_rs->fetch_assoc()) {
                            ?>
                                <!-- <option value="<?= $user_shop_data['shopId'] ?>"><?= $user_shop_data['shopName'] ?></option> -->
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12 p-3">
                        <button type="button" id="saveBtn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            <?php
            }
            ?>
            <script>
                // edite user details start===
                $(document).ready(function() {
                    $("#saveBtn").click(function() {
                        var uid = $("#uid").val();
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
                            url: "edite-user-process.php",
                            data: {
                                uid: uid,
                                name: name,
                                username: username,
                                password: password,
                                user_role: user_role,
                                user_added_shop: user_added_shop,
                            },
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
                                if (response == "Record updated successfully") {
                                    location.reload(true);
                                }
                            },

                            error: function(xhr, status, error) {
                                alert("Error: " + error);
                            },
                        });
                    });
                });
                // edite user details end=====
            </script>
<?php
        } else {

            echo "No value received";
        }
    }
}
