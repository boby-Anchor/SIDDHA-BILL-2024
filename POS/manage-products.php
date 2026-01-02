<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    require_once 'config/db.php';
}

$sql = $conn->query("SELECT 
    p_medicine.id AS pid,
    p_medicine.name AS pname ,
    p_medicine.code AS barcode,
    p_medicine.img AS img,
    p_medicine.status,
    p_medicine_category.name AS categoryname,
    p_brand.name AS brandName,
    medicine_unit.unit AS unit,
    unit_category_variation.ucv_name
    FROM p_medicine
    INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
    INNER JOIN p_brand ON p_brand.id = p_medicine.brand
    INNER JOIN medicine_unit ON  medicine_unit.id = p_medicine.medicine_unit_id
    INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Products</title>

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

        <div class="content-wrapper bg-dark">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <h3 class="card-title">Products</h3>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <!-- <th>Image</th> -->
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Brand</th>
                                                <th>Status</th>
                                                <th>Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($sql)) {
                                            ?>
                                                <tr>
                                                    <!-- <td><img src="dist/img/product/<?php //echo $row['img']; 
                                                                                        ?>" alt="<?php //echo $row['pname']; 
                                                                                                    ?>" style="max-width: 50px;"></td> -->
                                                    <td><?php echo $row['barcode']; ?></td>
                                                    <td><?php echo $row['pname']; ?> ( <?php echo $row['ucv_name']; ?><?php echo $row['unit']; ?> ) </td>
                                                    <td><?php echo $row['categoryname']; ?></td>
                                                    <td><?php echo $row['brandName']; ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($row['status'] == 1) {
                                                        ?>
                                                            <label class="btn btn-success btn-sm"> Active </label>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <label class="btn btn-danger btn-sm"> Inactive </label>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-info btn-sm edit_button" data-toggle="modal" data-target="#item-data-modal"
                                                            data-id="<?= $row['pid']; ?>"
                                                            data-name="<?= htmlspecialchars($row['pname']); ?>"
                                                            data-barcode="<?= $row['barcode']; ?>"
                                                            data-brand="<?= $row['brandName']; ?>"
                                                            data-img="<?= $row['img']; ?>"
                                                            data-status="<?= (int)$row['status']; ?>">
                                                            <i class="fa fa-edit"> Edit </i>
                                                        </button>
                                                    </td>
                                                </tr>
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

    <!-- Edit Modal start -->
    <div class="modal fade" id="item-data-modal">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <!-- <form action="actions/addMedicineOptions.php" method="POST" enctype="multipart/form-data"> -->
                <div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row pb-3">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control" id="product_name" placeholder="Product Name" name="product_name" required disabled>
                            <input type="text" class="form-control d-none" id="original_name" name="original_name" required>
                            <input type="text" class="form-control d-none" id="product_id" name="product_id" required>
                            <input type="text" class="form-control d-none" id="original_barcode" name="original_barcode" required>
                        </div>
                        <div class="row py-3">
                            <!-- <div class="col-12"> -->
                            <label>Product Brand</label>
                            <!-- </div>
                                <div class="col-12"> -->
                            <input type="text" class="form-control" id="product_brand" disabled></input>
                            <!-- </div> -->
                        </div>
                        <div class="row py-3">
                            <label for="name">Product Barcode</label>
                            <input type="text" class="form-control" id="new_barcode" placeholder="Product Code" name="new_barcode" value="" required>
                        </div>
                        <div class="row py-3">
                            <div class="col-12">
                                <label>Update product status</label>
                            </div>
                            <div class="col-12">
                                <button id="status-button" class="btn btn-success">Active</button>
                            </div>
                        </div>
                        <!--  <br>
                            <div class="row">
                                <div class="d-flex col-md-6">
                                    <img src="dist/img/product/<?php //echo $row['img'];
                                                                ?>" style="max-width: 100px;">
                                </div>
                            </div> -->
                        <!-- <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" name="uploadfile">
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="update-details-button" name="updateDetailsButton" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal end  -->
</body>

<script>
    var $statusButton = $('#status-button');
    var $saveButton = $('#update-details-button');

    $('.edit_button').on('click', function() {
        $('#product_id').val($(this).data('id'));
        $('#product_name').val($(this).data('name'));
        $('#new_barcode').val($(this).data('barcode'));
        $('#original_barcode').val($(this).data('barcode'));
        $('#product_brand').val($(this).data('brand'));
        $('#product_img').attr('src', 'dist/img/product/' + $(this).data('img'));

        if ($(this).data('status') == 1) {
            $statusButton.addClass('btn-success').text('Active').data('barcode', $(this).data('barcode')).data('status', 1);
        } else {
            $statusButton.addClass('btn-danger').text('Inactive').data('barcode', $(this).data('barcode')).data('status', 0);
        }
    });

    $statusButton.on('click', function() {
        const barcode = $(this).data('barcode');
        const status = $(this).data('status');

        switch (status) {
            case 0:
                handleStatus(barcode, 1);
                break;

            case 1:
                handleStatus(barcode, 0);
                break;
        }
    });

    function handleStatus(barcode, status) {
        $.ajax({
            url: "actions/product/updateStatus.php",
            method: "POST",
            data: {
                barcode: barcode,
                status: status
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    switch (result.status) {
                        case "success":
                            $("#item-data-modal").modal("hide");
                            SuccessMessageDisplay(result.message);
                            setTimeout(() => {
                                location.reload();
                            }, 4000);
                            break;

                        case "sessionExpired":
                            handleExpiredSession(result.message);
                            break;

                        case "error":
                            console.log("error ekak");

                            ErrorMessageDisplay(result.message);
                            break;

                        default:
                            ErrorMessageDisplay("An unknown error occurred.");
                            break;
                    }
                } catch (error) {
                    ErrorMessageDisplay("Invalid server response");
                    console.error(error.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                ErrorMessageDisplay(error);
            },
        });
    }

    $saveButton.on('click', function() {
        const new_barcode = $('#new_barcode').val().trim();
        const original_barcode = $('#original_barcode').val().trim();
        $.ajax({
            url: "actions/product/updateProductDetails.php",
            method: "POST",
            data: {
                new_barcode: new_barcode,
                original_barcode: original_barcode,
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    switch (result.status) {
                        case "success":
                            $("#item-data-modal").modal("hide");
                            SuccessMessageDisplay(result.message);
                            setTimeout(() => {
                                location.reload();
                            }, 4000);
                            break;
                    }
                } catch (error) {
                    ErrorMessageDisplay("Invalid server response");
                    console.error(error.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                ErrorMessageDisplay(error);
            },
        });
    });
</script>

</html>