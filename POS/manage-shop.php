<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pharmacy</title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fs-17 font-weight-600 mb-0">Shops</h6>
                                        </div>
                                        <div class="text-right">
                                            <a href="add-shop.php" class="btn btn-info btn-sm mr-1"><i class="fas fa-plus mr-1"></i>New Shop</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-info">
                                                <th>SL</th>
                                                <th>Shop Image</th>
                                                <th>Shop Name</th>
                                                <th>Shop Email</th>
                                                <th>Shop Phone</th>
                                                <th>Shop WhatsApp</th>
                                                <th>Shop Address</th>
                                                <th>Action</th>
                                                <th>Manager Name</th>
                                                <th>Manager Email</th>
                                                <th>Manager Phone</th>
                                                <th>Manager WhatsApp</th>
                                                <th>Manager Address</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($_SESSION['store_id'])) {

                                                $userLoginData = $_SESSION['store_id'];

                                                foreach ($userLoginData as $userData) {
                                                    $shop_id = $userData['shop_id'];
                                                    
                                                    if($shop_id=='1'){
                                                         $sql = $conn->query("SELECT * FROM `shop` WHERE shopStatus='1'");
                                                    }else{
                                                         $sql = $conn->query("SELECT * FROM `shop` WHERE shopStatus='1' AND shop.shopId = '$shop_id'");
                                                    }
                                                    
                                                    $n = 0;
                                                    // $sql = $conn->query("SELECT * FROM `shop` WHERE shopStatus='1' AND shop.shopId = '$shop_id'");
                                                    while ($row = mysqli_fetch_assoc($sql)) {
                                            ?>
                                                        <tr>
                                                            <th scope="row"><?php echo ++$n; ?></th>
                                                            <td>
                                                                <div class="shopImage" style="background-image:url('dist/img/shop/<?= $row['shopImg'] ?>');"></div>
                                                            </td>
                                                            <td><?php echo $row['shopName']; ?></td>
                                                            <td><?php echo $row['shopEmail']; ?></td>
                                                            <td><?php echo $row['shopTel']; ?></td>
                                                            <td><?php echo $row['shopWhatsApp']; ?></td>
                                                            <td><?php echo $row['shopAddress']; ?></td>
                                                            <td>
                                                                <div class="col-12">
                                                                    <div class="row grid gap-4">
                                                                        <div class="col-6">
                                                                            <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#editeShop<?= $row["shopId"] ?>"><i class="fa fa-edit"></i></a>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <a href="actions/remove.php?removeshop&code=<?php echo base64_encode($row['shopId']); ?>&wr=<?php echo base64_encode("shop"); ?>" class="btn btn-danger btn-sm delete" onclick="return confirm('Are you sure to delete?')"> <i class="fa fa-trash"></i> </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?php echo $row['shopManagerName']; ?></td>
                                                            <td><?php echo $row['shopManagerEmail']; ?></td>
                                                            <td><?php echo $row['shopManagerTel']; ?></td>
                                                            <td><?php echo $row['shopManagerWhatsApp']; ?></td>
                                                            <td><?php echo $row['shopManagerAddress']; ?></td>
                                                        </tr>

                                                        <!-- Modal edite shop Start  -->
                                                        <div class="modal fade" id="editeShop<?= $row["shopId"] ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Update Shop's Details</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form method="post" action="actions/editeShop.php">
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-12">
                                                                                        <div class="form-group">
                                                                                            <input type="text" value="<?= $row["shopId"] ?>" class="d-none" name="shopId">
                                                                                            <input type="text" class="form-control" name="shopName" value="<?= $row["shopName"] ?>" required>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="form-group">
                                                                                            <label for="name">Shop Tel</label>
                                                                                            <input type="tel" class="form-control" name="shopTel" value="<?= $row["shopTel"] ?>" required>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="form-group">
                                                                                            <label for="name">Shop WhatsApp</label>
                                                                                            <input type="tel" class="form-control" name="shopWhatsapp" value="<?= $row["shopWhatsApp"] ?>" required>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <div class="row">
                                                                                        <div class="col-12">
                                                                                            <label for="name">Shop Email</label>
                                                                                            <input type="email" class="form-control" name="shopEmail" value="<?= $row["shopEmail"] ?>" required>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="name">Shop Address</label>
                                                                                    <input type="text" class="form-control" name="shopAdress" value="<?= $row["shopAddress"] ?>" required>
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div class="col-12">
                                                                                        <label>Manager Name</label>
                                                                                        <input type="text" class="form-control" name="managerName" value="<?= $row["shopManagerName"] ?>" required>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <label for="name">Manager Tel</label>
                                                                                        <input type="tel" class="form-control" name="managerTel" value="<?= $row["shopManagerTel"] ?>" required>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <label for="name">Manager WhatsApp</label>
                                                                                        <input type="tel" class="form-control" name="managerWhatsapp" value="<?= $row["shopManagerWhatsApp"] ?>" required>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <label for="name">Manager Email</label>
                                                                                        <input type="email" class="form-control" name="managerEmail" value="<?= $row["shopManagerEmail"] ?>" required>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <label for="name">Manager Address</label>
                                                                                        <input type="text" class="form-control" name="managerAddress" value="<?= $row["shopManagerAddress"] ?>" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-info" name="saveEdite">Save</button>
                                                                    </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Modal edite shop End  -->
                                            <?php }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>



        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->
    </div>

    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->



    <!-- Page specific script -->
    <script>
        $(function() {
            $(".select2").select2();
            $(".select2bs4").select2({
                theme: "bootstrap4",
            });
        });
    </script>


</body>

</html>