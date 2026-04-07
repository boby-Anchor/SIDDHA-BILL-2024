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
  <title>Manage Categories</title>

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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <section class="content bg-dark">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header">
                  <h3 class="card-title">Manage Categories</h3>
                </div>
                <div class="card-body">
                  <table id="example1" class="table table-bordered">
                    <thead>
                      <tr class="bg-info">
                        <th class="col-2">#</th>
                        <th class="col-4">Name</th>
                        <th class="col-4">Status</th>
                        <th class="col-3">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $n = 0;
                      $sql = $conn->query("SELECT * FROM `p_medicine_category` ORDER BY `name` ASC");
                      while ($row = mysqli_fetch_assoc($sql)) {
                      ?>
                        <tr>
                          <td><?php echo ++$n; ?></td>
                          <td class="text-capitalize"><?php echo $row['name'] ?></td>
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
                          <td>
                            <div class="btn-group">
                              <button class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cogs"></i>
                                Manage
                              </button>
                              <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; top: 36px; left: 0px; will-change: top, left;">
                                <a class="dropdown-item text-info" href="#" data-toggle="modal" data-target="#edit<?php echo $row['id']; ?>"> <i class="fa fa-edit"></i> Edit </a>
                                <?php
                                if ($row['status'] == 1) {
                                ?>
                                  <a class="dropdown-item text-dark bg-opacity-25 fw-semibold" href="actions/remove.php?removeCategory=<?php echo $row['id']; ?>&status=0"> ❌ Inactive </a>
                                <?php
                                } else {
                                ?>
                                  <a class="dropdown-item text-dark bg-opacity-25 fw-semibold" href="actions/remove.php?removeCategory=<?php echo $row['id']; ?>&status=1"> ✅ Active </a>
                                <?php
                                }
                                ?>
                              </div>
                            </div>
                          </td>
                        </tr>
                        <div class="modal fade" id="edit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content bg-dark">
                              <form action="actions/category/update.php?id=<?= $row['id']; ?>" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <div class="form-group">
                                    <label for="name">Category Name</label>
                                    <input type="text" class="form-control" id="medicineCategory" placeholder="Enter Category Name" name="medicineCategory" value="<?php echo $row['name']; ?>">
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