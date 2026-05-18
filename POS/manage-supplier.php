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
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
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
                          <td><?= $row['name']; ?></td>
                          <td><?= $row['email']; ?></td>
                          <td><?= $row['phone']; ?></td>
                          <td><?= $row['address']; ?></td>
                          <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit-supplier-modal" onclick="getDetails(<?= $row['id']; ?>)">
                              <i class="fa fa-edit"></i>
                            </button>
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

  <!-- Edit Modal start -->
  <div class="modal fade" id="edit-supplier-modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-dark">
        <div>
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update Supplier Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="row pb-3">
              <div class="col-6">
                <label for="name">Name</label>
                <input type="text" class="d-none" id="supplier_id" required>
                <input type="text" class="form-control" id="supplier_name" placeholder="Product Name" name="supplier_name" required>
              </div>
              <div class="col-6">
                <label>Supplier Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="" required>
              </div>
            </div>
            <div class="row pb-3">
              <div class="col-6">
                <label for="name">Contact No</label>
                <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact Number" value="" required>
              </div>
              <div class="col-6">
                <label for="name">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="" required>
              </div>
            </div>

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

  <!-- All JS -->
  <?php include("part/all-js.php"); ?>
  <!-- All JS end -->

  <!-- Data Table JS -->
  <?php include("part/data-table-js.php"); ?>
  <!-- Data Table JS end -->

  <!-- Page specific script -->
  <script>
    var $saveButton = $('#update-details-button');

    function getDetails(id) {
      InfoMessageDisplay('Loading details...');
      try {
        $.ajax({
          url: "actions/supplier/getSupplierDetailsById.php",
          method: "POST",
          data: {
            id
          },
          dataType: "json",
          success: function(response) {
            switch (response.status) {
              case "success":
                setData(response.data);
                break;

              case "sessionExpired":
                handleExpiredSession(response.message);
                break;

              case "error":
                ErrorMessageDisplay(response.message);
                break;

              default:
                ErrorMessageDisplay("An unknown error occurred.");
                break;
            }
          },
          error: function(xhr, status, error) {
            console.error(error);
            ErrorMessageDisplay(error);
          },
        });
      } catch (error) {
        ErrorMessageDisplay("Data get request failed.");
        console.error(error.message);
      }
    }

    function setData(data) {
      $("#edit-supplier-modal").modal("show");

      $('#supplier_id').val(data.id);
      $('#supplier_name').val(data.name);
      $('#email').val(data.email);
      $('#contact').val(data.phone);
      $('#address').val(data.address);

      if (data.status == 1) {
        $('#product_status').removeClass('border-danger').addClass('border-success');
      } else {
        $('#product_status').removeClass('border-success').addClass('border-danger');
      }
    }

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

    $saveButton.on('click', function() {
      const supplier_id = $('#supplier_id').val().trim() || null;
      const supplier_name = $('#supplier_name').val().trim() || null;
      const email = $('#email').val().trim() || null;
      const contact = $('#contact').val().trim() || null;
      const address = $('#address').val().trim() || null;

      console.log(supplier_id);
      console.log(supplier_name);
      console.log(email);
      console.log(contact);
      console.log(address);

      $.ajax({
        url: "actions/supplier/updateSupplierDetails.php",
        method: "POST",
        data: {
          supplier_id,
          supplier_name,
          email,
          contact,
          address,
        },
        success: function(response) {
          console.log(response);

          try {
            const result = JSON.parse(response);
            switch (result.status) {
              case "success":
                $("#edit-supplier-modal").modal("hide");
                SuccessMessageDisplay(result.message);
                setTimeout(() => {
                  location.reload();
                }, 3000);
                break;

              case "sessionExpired":
                handleExpiredSession(result.message);
                break;

              case "error":
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
    });
  </script>
</body>

</html>