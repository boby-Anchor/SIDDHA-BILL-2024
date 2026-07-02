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
  <title>Manage Brands</title>

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
      <section class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card bg-dark">
              <div class="card-header">
                <h3 class="card-title">Product Brands Management</h3>
              </div>
              <div class="card-body">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <tr class="bg-info">
                      <th>SL</th>
                      <!-- <th>Image</th> -->
                      <th>Name</th>
                      <th>Details</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $n = 0;
                    $sql = $conn->query("SELECT * FROM `p_brand` ");
                    while ($row = mysqli_fetch_assoc($sql)) {
                    ?>
                      <tr>
                        <td><?= ++$n; ?></td>
                        <td class="text-capitalize"><?= $row['name'] ?></td>
                        <td><?= $row['details'] ?></td>
                        <td>
                          <?php
                          $isActive  = $row['status'] == 1;
                          $newStatus = $isActive ? 0 : 1;

                          $buttonClass = $isActive ? "btn-danger" : "btn-success";
                          $icon        = $isActive ? "fa-trash" : "fa-undo";
                          $label       = $isActive ? "Deactivate" : "Activate";
                          $message     = $isActive ? "Are you sure to Deactivate " . $row['name'] . "?" : "Are you sure to Activate " . $row['name'] . "?";
                          ?>
                          <button class="btn btn-info btn-sm mr-1" type="button" onclick="getDetails(<?= $row['id']; ?>);">
                            <i class="fa fa-edit"></i> Edit
                          </button>
                          <button class="btn <?= $buttonClass; ?> btn-sm" type="button"
                            onclick="if(confirm('<?= $message ?>')) { updateStatus(<?= $row['id'] ?>, <?= $newStatus ?>); } return false;">
                            <i class="fa <?= $icon; ?>"></i> <?= $label; ?>
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
      </section>
    </div>

    <!-- Edit Brand Modal -->
    <div class=" modal fade" id="edit-brand-modal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title">Update Brand</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="brand-edit-form">
              <input type="hidden" id="brand_id" name="brand_id">
              <div class="form-group">
                <label for="brand_name">Brand Name</label>
                <input type="text" class="form-control bg-dark" id="brand_name" name="brand_name" placeholder="Enter Brand Name" required>
              </div>
              <div class="form-group">
                <label for="brand_details">Details</label>
                <textarea id="brand_details" name="details" class="form-control bg-dark" rows="3"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="update-brand-button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Edit Brand Modal end -->

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

  <script>
    function getDetails(id) {
      InfoMessageDisplay('Loading details...');
      $.ajax({
        url: "actions/brand/getBrandDetailsById.php",
        method: "POST",
        data: {
          id: id
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
        }
      });
    }

    function setData(data) {
      $('#brand_id').val(data.id);
      $('#brand_name').val(data.name);
      $('#brand_details').val(data.details);
      $('#edit-brand-modal').modal('show');
    }

    function updateBrand() {
      var brandId = $('#brand_id').val();
      var brandName = $('#brand_name').val();
      var brandDetails = $('#brand_details').val();

      if (!brandName.trim()) {
        ErrorMessageDisplay('Brand name is required.');
        return;
      }

      $.ajax({
        url: "actions/brand/updateBrandDetails.php",
        method: "POST",
        dataType: "json",
        data: {
          brand_id: brandId,
          brand_name: brandName,
          details: brandDetails
        },
        dataType: "json",
        success: function(response) {
          switch (response.status) {
            case 'success':
              SuccessMessageDisplay(response.message);
              $('#edit-brand-modal').modal('hide');
              setTimeout(function() {
                location.reload();
              }, 1500);
              break;
            case 'sessionExpired':
              handleExpiredSession(response.message);
              break;
            case 'error':
              ErrorMessageDisplay(response.message);
              break;
            default:
              ErrorMessageDisplay('An unknown error occurred.');
              break;
          }
        },
        error: function(xhr, status, error) {
          console.error(error);
          ErrorMessageDisplay('Something went wrong. Please check your connection.');
        }
      });
    }

    $('#update-brand-button').on('click', function() {
      updateBrand();
    });

    function updateStatus(id, status) {
      try {
        $.ajax({
          url: "actions/global/statusUpdate.php/update",
          type: "POST",
          data: {
            id: id,
            table: "p_brand",
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
                }, 1500);
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