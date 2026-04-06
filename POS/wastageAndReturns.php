<?php
session_start();
if (!isset($_SESSION['store_id'])) {
  header("location:login.php");
  exit();
} else {
  require_once "config/db.php";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Returns and Wastage</title>

  <!-- Data Table CSS -->
  <?php include("part/data-table-css.php"); ?>
  <!-- Data Table CSS end -->

  <!-- All CSS -->
  <?php include("part/all-css.php"); ?>
  <!-- All CSS end -->

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include("part/navbar.php"); ?>
    <!-- Navbar end -->

    <!-- Sidebar -->
    <?php include("part/sidebar.php"); ?>
    <!--  Sidebar end -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper bg-dark">
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark">
                <div class="card-header">
                  <h3 class="card-title">Wastage details and Returns from shops</h3>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr class="bg-info">
                        <th class="adThText">ID</th>
                        <th class="adThText">Shop</th>
                        <th class="adThText">User</th>
                        <th class="adThText">Created at</th>
                        <th class="adThText">Description</th>
                        <th class="adThText">Items</th>
                        <th class="adThText">Total Value</th>
                        <th class="adThText">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $grn_details_result = $conn->query("SELECT wastage_batches.*,
                      shop.shopName AS shop,
                      us1.name AS user,
                      us2.name AS approver
                      FROM `wastage_batches`
                      INNER JOIN shop ON wastage_batches.shop_id = shop.shopId
                      INNER JOIN users us1 ON wastage_batches.created_by = us1.id
                      LEFT JOIN users us2 ON wastage_batches.approved_by = us2.id
                      ORDER BY created DESC LIMIT 100");
                      while ($grn_details_data = $grn_details_result->fetch_assoc()) { ?>
                        <tr>
                          <td class="batch_id"><?= $grn_details_data["id"] ?></td>
                          <td><?= $grn_details_data["shop"] ?></td>
                          <td><?= $grn_details_data["user"] ?></td>
                          <td><?= $grn_details_data["created"] ?></td>
                          <td><?= $grn_details_data["description"] ?></td>
                          <td>
                            <?php
                            $itemCount_result = $conn->query("SELECT COUNT(id) AS itemsCount FROM wastage_batch_items WHERE wastage_batch_items.wastage_batch_id = '" . $grn_details_data["id"] . "'");
                            $itemCount_data = $itemCount_result->fetch_assoc();
                            ?>
                            <button class="btn dropdown-toggle badge badge-info " type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom-start"> <?= $itemCount_data['itemsCount'] ?> </button>
                            <ul class="dropdown-menu">
                              <table class="table" id="poItemsTable<?= $grn_details_data["id"] ?>">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Barcode</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Brand</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Item Price</th>
                                    <th scope="col">Total Value</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $itemCount = 1;
                                  $poItems_result = $conn->query("SELECT wastage_batch_items.*, p_brand.name AS brand, p_medicine.name
                                  FROM wastage_batch_items
                                  INNER JOIN p_medicine ON wastage_batch_items.barcode = p_medicine.code
                                  INNER JOIN p_brand ON p_medicine.brand = p_brand.id
                                  WHERE wastage_batch_id = '" . $grn_details_data["id"] . "'");
                                  while ($poItems_data = $poItems_result->fetch_array()) { ?>
                                    <tr>
                                      <td><?= $itemCount++ ?></td>
                                      <td><?= $poItems_data["barcode"] ?></td>
                                      <td><?= $poItems_data["name"] ?></td>
                                      <td><?= $poItems_data["brand"] ?></td>
                                      <td><?= $poItems_data["qty"] ?></td>
                                      <td><?= $poItems_data["item_price"] ?></td>
                                      <td><?= $poItems_data["total_price"] ?></td>
                                    </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                              <!-- <button class="btn btn-warning" style="font-weight: bold; font-family: 'Source Sans Pro';" onclick="printTable('<?= $grn_details_data['id'] ?>','<?= $grn_details_data['id'] ?>','<?= $grn_details_data['created'] ?>','<?= $grn_details_data['id'] ?>');"> <i class="nav-icon fas fa-copy"></i> PRINT</button> -->
                            </ul>
                          </td>
                          <td><?= $grn_details_data["total_value"] ?></td>
                          <td><?php

                              if ($grn_details_data["status"] == 0) {
                              ?>
                              <button class="btn btn-success btn-sm" onclick="showAcceptModal(this)">
                                <i class="fa fa-edit"> Finalize </i>
                              </button>
                            <?php
                              } else {
                            ?>
                              Approved by:- <?= $grn_details_data["approver"] ?>
                              <br>
                              Description:- <?= $grn_details_data["approval_description"] ?>
                            <?php
                              }
                            ?>
                          </td>
                        </tr>
                      <?php
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
  <!-- Wastage details modal start -->
  <div class="modal" id="wastageAcceptanceModal" role="dialog">
    <div class="modal-dialog modal-md d-flex justify-content-between ">
      <div class="modal-content bg-dark align-items-center">


        <div class="card-body w-100 p-4 bg-dark">
          <label for="wastageDescription" class="form-label">
            Enter wastage acceptance details
          </label>
          <label id="selectedBatchId" class="d-none"></label>

          <textarea
            id="acceptanceDescription"
            class="form-control bg-dark text-light border"
            rows="4"
            maxlength="100"
            placeholder="Enter description..."
            oninput="updateCounter(this)"></textarea>

          <div class="text-end mt-1">
            <small class="text-secondary">
              <span id="charCount">0</span>/100
            </small>
          </div>
          <div class="card-footer">
            <button class="acceptanceSaveButton button bg-dark border rounded border-success"
              onclick="$(this).prop('disabled', true); saveAcceptance();">
              Save <i class="nav-icon fas fa-save p-2"></i>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- Wastage details modal end -->
  <!-- Wastage details modal start -->
  <div class="modal" id="wastageDetailsModal" role="dialog">
    <div class="modal-dialog modal-md d-flex justify-content-between ">
      <div class="modal-content bg-dark align-items-center">

        <div class="d-flex align-items-center justify-content-center bg-dark">
          <div class="text-center px-4 py-5" style="max-width: 500px;">

            <div class="mb-4">
              <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"></div>
            </div>

            <h2 class="fw-semibold text-white mb-3" style="letter-spacing: 0.5px;">
              Updates in Progress
            </h2>

            <p class="text-secondary mb-0 fs-5">
              We’re making improvements behind the scenes.<br>
              Thanks for your patience.
            </p>

          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- Wastage details modal end -->
</body>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var dropdownButtonList = [].slice.call(document.querySelectorAll('.btn.dropdown-toggle'));
    dropdownButtonList.map(function(button) {
      // Check if the button has already been initialized
      if (!button.classList.contains('dropdown-initialized')) {
        new bootstrap.Dropdown(button);
        // Mark the button as initialized to prevent re-initialization
        button.classList.add('dropdown-initialized');
      }
    });
  });

  function updateCounter(el) {
    document.getElementById("charCount").textContent = el.value.length;
  }

  function showDetailsModal(button) {
    var batch_id = $(button).closest("tr").find(".batch_id").text().trim() || null;
    $("#selectedBatchId").text(batch_id);
    $("#wastageDetailsModal").modal("show");
  }

  function showAcceptModal(button) {
    var batch_id = $(button).closest("tr").find(".batch_id").text().trim() || null;
    $("#selectedBatchId").text(batch_id);
    $("#wastageAcceptanceModal").modal("show");
  }

  function saveAcceptance() {
    var batch_id = $("#selectedBatchId").text().trim() || null;
    var description = $("#acceptanceDescription").val().trim() || null;

    if (description == null || description == '') {
      $('.acceptanceSaveButton').prop('disabled', false);
      ErrorMessageDisplay("Enter wastage acceptance description.")
      return;
    }

    $.ajax({
      url: "actions/wastage/saveAcceptance.php",
      method: "POST",
      data: {
        batch_id,
        description
      },

      success: function(response) {
        var result = JSON.parse(response);

        switch (result.status) {
          case "success":
            SuccessMessageDisplay(result.message);
            setTimeout(() => {
              window.location.reload();
            }, 3000);
            break;

          case "sessionExpired":
            handleExpiredSession(result.message);
            break;

          case "sessionDataError":
            handleExpiredSession(result.message);
            break;

          default:
            ErrorMessageDisplay(result.message);
            break;
        }
      },
      error: function(xhr, status, error) {
        ErrorMessageDisplay("Connection error!");
        console.error(xhr.responseText);
      },
    });

  }
</script>

</html>