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

<body class="hold-transition sidebar-mini">
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
                                <div class="card-header">
                                    <h3 class="card-title">Products</h3>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-info">
                                                <th>Image</th>
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Brand</th>
                                                <th>Unit</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = $conn->query("SELECT p_medicine.id AS pid , p_medicine.name AS pname , `code` , p_medicine.img AS img , p_medicine_category.name AS categoryname , p_brand.name AS brandname ,  medicine_unit.unit AS unit , unit_category_variation.ucv_name  FROM p_medicine
                                            INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
                                            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                                            INNER JOIN medicine_unit ON  medicine_unit.id = p_medicine.medicine_unit_id
                                            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation");
                                            while ($row = mysqli_fetch_assoc($sql)) {
                                            ?>
                                                <tr>
                                                    <td class="text-capitalize"><img src="dist/img/product/<?php echo $row['img']; ?>" alt="<?php echo $row['pname']; ?>" style="max-width: 50px;"></td>
                                                    <td class="text-capitalize"><?php echo $row['code']; ?></td>
                                                    <td class="text-capitalize"><?php echo $row['pname']; ?> ( <?php echo $row['ucv_name']; ?><?php echo $row['unit']; ?> ) </td>
                                                    <td class="text-capitalize"><?php echo $row['categoryname']; ?></td>
                                                    <td class="text-capitalize"><?php echo $row['brandname']; ?></td>
                                                    <td class=""><?php echo $row['unit']; ?></td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <button class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-cogs"> Manage</i> </button>
                                                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; top: 30px; left: 0px; will-change: top, left;">
                                                                <a class="dropdown-item text-success" href="#" data-toggle="modal" data-target="#edit<?php echo $row['pid']; ?>"> <i class="fa fa-edit"></i> Edit </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="edit<?php echo $row['pid']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="actions/addMedicineOptions.php" method="POST" enctype="multipart/form-data">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="row">
                                                                            <label for="name">Product Name</label>
                                                                            <input type="text" class="form-control" id="po_product_name" placeholder="Product Name" name="po_product_name" value="<?php echo $row['pname']; ?>" required>
                                                                            <input type="text" class="form-control d-none" id="po_product_id" placeholder="Product id" name="po_product_id" value="<?php echo $row['pid']; ?>" required>
                                                                        </div>
                                                                        <div class="row">
                                                                            <label for="name">Product Code</label>
                                                                            <input type="text" class="form-control" id="po_product_code" placeholder="Product Code" name="po_product_code" value="<?php echo $row['code']; ?>" required>
                                                                        </div>

                                                                        <br>
                                                                        <div class="row">
                                                                            <div class="d-flex col-md-6">
                                                                                <img src="dist/img/product/<?php echo $row['img']; ?>" alt="<?php echo $row['pname']; ?>" style="max-width: 100px;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Image</label>
                                                                        <input type="file" class="form-control" name="uploadfile">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="submit" name="updateCategory" class="btn btn-primary">Save changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Edit Modal end  -->

                                            <?php } ?>
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


</body>

</html>