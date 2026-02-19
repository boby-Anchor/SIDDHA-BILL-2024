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
  <title>Manage Suppliers</title>

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

    <div class="content-wrapper bg-dark">
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header py-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="fs-17 font-weight-600 mb-0">Suppliers List</h6>
                    </div>
                    <div class="text-right">
                      <a href="add-supplier.php" class="btn btn-info btn-sm mr-1"><i class="fas fa-plus mr-1"></i>New Supplier</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <table id="example1" class="table table-bordered">
                    <thead>
                      <tr class="bg-info">
                        <th>SL</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $n = 0;
                      $sql = $conn->query("SELECT * FROM `p_supplier`");
                      while ($row = mysqli_fetch_assoc($sql)) {
                      ?>
                        <tr>
                          <th scope="row"><?php echo ++$n; ?></th>
                          <td><?php echo $row['name']; ?></td>
                          <td><?php echo $row['email']; ?></td>
                          <td><?php echo $row['phone']; ?></td>
                          <td>
                            <a class="btn btn-info btn-sm disabled" data-toggle="modal" data-target="#edit-customer<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></a>

                            <?php
                            $isActive = $row['status'] == 1;
                            $newStatus = $isActive ? 0 : 1;
                            $btnClass = $isActive ? 'btn-danger' : 'btn-success';
                            $icon     = $isActive ? 'fa-trash' : 'fa-undo';
                            $message  = $isActive ? 'Are you sure to Deactivate?' : 'Are you sure to Activate?';
                            ?>
                            <button type="submit"
                              class="btn <?php echo $btnClass; ?> btn-sm"
                              onclick="if(confirm('<?= $message ?>')) { updateStatus(<?= $row['id'] ?>, <?= $newStatus ?>); } return false;">
                              <i class="fa <?php echo $icon; ?>"></i>
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

    function updateStatus(id, status) {

      try {

        $.ajax({
          url: "actions/global/statusUpdate.php/update",
          type: "POST",
          data: {
            id: id,
            table: "p_supplier",
            status: status,
          },
          success: function(response) {
            console.log(response);

            const result = JSON.parse(response);

            switch (result.status) {
              case 'success':
                SuccessMessageDisplay(result.message);
                setTimeout(() => {
                  location.reload();
                }, 5000);
                break;
              case 'session_expired':
                handleExpiredSession(result.message);
                return;
                break;
              case 'error':
                ErrorMessageDisplay(result.message);
                return;
                break;

              default:
                ErrorMessageDisplay("An unknown error occurred.");
                return;
                break;
            };
          },
          error: function(xhr, status, error) {
            ErrorMessageDisplay("Something went wrong! Check connection.");
          },
        });
      } catch (error) {
        ErrorMessageDisplay(error.message)
      }

    }
  </script>

</body>

</html>