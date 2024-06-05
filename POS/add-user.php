<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
    include 'part/alert.php';
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customize Bill</title>
        <!-- All CSS -->
        <?php include("part/all-css.php"); ?>
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">

            <!-- Navbar -->
            <?php include("part/navbar.php"); ?>
            <!-- Navbar end -->

            <!-- Sidebar -->
            <?php include("part/sidebar.php"); ?>
            <!--  Sidebar end -->


            <div class="content-wrapper bg-dark">
                <div class="row w-100">

                    <div class="col-12 col-md-5 bg-dark data-enter-main">
                        <form id="userForm">
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="au-tittle">Create User</h1>
                                </div>
                                <div class="col-12 p-3">
                                    <input type="text" name="name" id="name" class="col-12 form-control-lg border-0" placeholder="Name">
                                </div>
                                <div class="col-12 p-3">
                                    <input type="text" name="username" id="username" class="col-12 form-control-lg border-0" placeholder="User Name">
                                </div>
                                <div class="col-12 p-3">
                                    <input type="password" name="password" id="password" class="col-12 form-control-lg border-0" placeholder="Password">
                                </div>
                                <div class="col-12 p-3">
                                    <input type="password" name="confirm_password" id="confirm_password" class="col-12 form-control-lg border-0" placeholder="Re-Enter Password">
                                </div>
                                <div class="col-12 p-3">
                                    <select name="user_role" id="user_role" class="form-control-lg col-12 form-control-lg border-0">
                                        <option value="">-Select Role-</option>
                                        <?php

                                        $user_role_rs = $conn->query("SELECT * FROM user_role");
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
                                        <option value="">-Select Shop-</option>
                                        <?php

                                        $user_shop_rs = $conn->query("SELECT * FROM shop");
                                        while ($user_shop_data = $user_shop_rs->fetch_assoc()) {
                                        ?>
                                            <option value="<?= $user_shop_data['shopId'] ?>"><?= $user_shop_data['shopName'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 p-3">
                                    <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>



                    <div class="col-12 col-md-7 manage-user-main">
                        <div class="row">
                            <div class="col-12">
                                <h2 class="mu-tittle">Manage Users</h2>
                            </div>
                            <div class="col-12 p-3">
                                <select name="user_role" id="filter_user_role" class="form-control-sm col-4 form-control-lg border-0">
                                    <option value="">Filter By</option>
                                    <?php

                                    $user_role_rs = $conn->query("SELECT * FROM user_role");
                                    while ($user_role_data = $user_role_rs->fetch_assoc()) {
                                    ?>
                                        <option value="<?= $user_role_data['user_role_id'] ?>"><?= $user_role_data['user_role'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="u-data-main" id="u_data_main">

                                <?php
                                $user_role_rs =  $conn->query("SELECT * FROM users INNER JOIN user_role ON user_role.user_role_id = users.user_role_id INNER JOIN shop ON shop.shopId = users.shop_id ORDER BY shop.shopId ASC");
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

                                    </div><?php
                                        }
                                            ?>
                                <!-- data will be display onchange with filter_by_user_role.php -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- confirm po modal end -->

        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->
        <!-- Alert -->
        <?php include("part/alert.php"); ?>
        <!-- Alert end -->
        <!-- All JS -->
        <?php include("part/all-js.php"); ?>
        <!-- All JS end -->
        <!-- Data Table JS -->
        <?php include("part/data-table-js.php"); ?>
        <!-- Data Table JS end -->

        <!-- select2 input field -->



        <!-- ========================================== -->
    </body>

    </html>
<?php
}
?>