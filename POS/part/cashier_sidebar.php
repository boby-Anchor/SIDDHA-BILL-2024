<?php
if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];

  // foreach ($userLoginData as $userData) {
  //   $userName = $userData['name'];
  //   $userId = $userData['id'];

  $userName = $userLoginData[0]['name'];
  $userId = $userLoginData[0]['id'];
  $shop_id = $userLoginData[0]['shop_id'];

  $user_shop_rs = $conn->query("SELECT * FROM users INNER JOIN shop ON shop.shopId = users.shop_id INNER JOIN user_role ON user_role.user_role_id = users.user_role_id WHERE id = '$userId'");
  $user_shop_data = $user_shop_rs->fetch_assoc();
?>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <div class="shopLogoMain">
      <div class="shopImg" style="background-image: url('dist/img/shop/<?= $user_shop_data['shopImg'] ?>');"></div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel pb-3 mb-3">
        <!--   <div class="image">
               <img src="dist/img/user1.jpg" class="img-circle elevation-2" alt="User Image" />
            </div>-->
        <div class="info userNameMain">
          <a href="#" class="d-block userName">
            <?= $userName; ?>
          </a>
        </div>
        <div class="info userRoleMain">
          <a href="#" class="d-block userRole">
            <?= $user_shop_data['user_role'] ?>
          </a>
        </div>

      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search" />
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <!-- Edit stock for Yakkala Ayurveda Start -->
          <?php
          if ($shop_id == 9 || $userId == 38) {
          ?>
            <li class="nav-header"> Add Stock Items</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Special
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">1</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="edit-stock_new.php" class="nav-link">
                    <i class="nav-icon fas fa-copy"></i>
                    <i class=""></i>
                    <p>Edit item Qty</p>
                  </a>
                </li>
              </ul>
            </li>
            <?php
            if ($shop_id == 9) {
            ?>
              <li class="nav-header"> Stock Item functions </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Purchase
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">2</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="stock.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>View Shop Stock</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="add-purchase.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Purchase Order</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="manage-purchase.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Orders List</p>
                    </a>
                  </li>
                </ul>
              </li>
          <?php
            }
          }
          ?>
          <!-- Edit stock for Yakkala Ayurveda End -->

          <!-- Refilling area -->
          <?php if ($userId == 38) { ?>

            <li class="nav-item">
              <a href="#" class="nav-link">
                <!-- <i class="nav-icon fas fa-sliders-h"></i> -->
                <i class="nav-icon fas fa-wine-bottle"></i>
                <p>
                  Refilling Functions
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">2</span>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="refilling_invoice.php" class="nav-link">
                    <i class="nav-icon fas fa-prescription-bottle"></i>
                    <p>Convert</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <d class="nav-icon fas fa-pen-square"></d>
                    <p>Transfer to Hub</p>
                  </a>
                </li>
                <!-- <li class="nav-item" disabled> -->
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="fas fa-receipt"></i>
                    <p>
                      Refill Reports
                      <i class="fas fa-angle-left right"></i>
                      <span class="badge badge-info right">4</span>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="refilling_invoice.php" class="nav-link">
                        <i class="nav-icon fab fa-stack-overflow"></i>
                        <p>Refilling Stock</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="report-refillingConverts.php" class="nav-link">
                        <d class="nav-icon fas fa-pen-square"></d>
                        <p>Refilling Batches</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="return-invoice.php" class="nav-link">
                        <i class="nav-icon fas fa-reply-all"></i>
                        <p>Report</p>
                      </a>
                    </li>
                  </ul>
                </li>
            </li>
        </ul>
        </li>

      <?php } ?>

      <!-- Cashier -->
      <li class="nav-header"> Cashier Functions </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-sliders-h"></i>
          <p>
            Options
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info right">3</span>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pos.php" class="nav-link">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>POS Invoice</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="edit_unit_price.php" class="nav-link">
              <d class="nav-icon fas fa-pen-square"></d>
              <p>Update Unit Price</p>
            </a>
          </li>

          <li class="nav-item" disabled>
            <a href="return-invoice.php" class="nav-link">
              <i class="nav-icon fas fa-reply-all"></i>
              <p>Return Invoice</p>
            </a>
          </li>
        </ul>
      </li>

      <!--<li class="nav-item">-->
      <!--  <a href="sales.php" class="nav-link">-->
      <!--    <i class="nav-icon fas fa-copy"></i>-->
      <!--    <p>Sales</p>-->
      <!--  </a>-->
      <!--</li>-->

      <!--<li class="nav-item">-->
      <!--  <a href="#" class="nav-link">-->
      <!--    <i class="nav-icon fas fa-copy"></i>-->
      <!--    <p>-->
      <!--      Damages-->
      <!--      <i class="fas fa-angle-left right"></i>-->
      <!--      <span class="badge badge-info right">2</span>-->
      <!--    </p>-->
      <!--  </a>-->
      <!--  <ul class="nav nav-treeview">-->
      <!--    <li class="nav-item">-->
      <!--      <a href="add-damage.php" class="nav-link">-->
      <!--        <i class="far fa-circle nav-icon"></i>-->
      <!--        <p>Add Damage</p>-->
      <!--      </a>-->
      <!--    </li>-->
      <!--    <li class="nav-item">-->
      <!--      <a href="damage.php" class="nav-link">-->
      <!--        <i class="far fa-circle nav-icon"></i>-->
      <!--        <p>Damage Products</p>-->
      <!--      </a>-->
      <!--    </li>-->
      <!--  </ul>-->
      <!--</li>-->
      <!--<li class="nav-header">PEOPLES</li>-->
      <!--<li class="nav-item">-->
      <!--  <a href="#" class="nav-link">-->
      <!--    <i class="nav-icon fas fa-copy"></i>-->
      <!--    <p>-->
      <!--      Customers-->
      <!--      <i class="fas fa-angle-left right"></i>-->
      <!--      <span class="badge badge-info right">2</span>-->
      <!--    </p>-->
      <!--  </a>-->
      <!--  <ul class="nav nav-treeview">-->
      <!--    <li class="nav-item">-->
      <!--      <a href="add-customer.php" class="nav-link">-->
      <!--        <i class="far fa-circle nav-icon"></i>-->
      <!--        <p>Add Customers</p>-->
      <!--      </a>-->
      <!--    </li>-->
      <!--    <li class="nav-item">-->
      <!--      <a href="manage-customer.php" class="nav-link">-->
      <!--        <i class="far fa-circle nav-icon"></i>-->
      <!--        <p>Manage Customers</p>-->
      <!--      </a>-->
      <!--    </li>-->
      <!--  </ul>-->
      <!--</li>-->


      <!--<li class="nav-item">-->
      <!--  <a href="today-report.php" class="nav-link">-->
      <!--    <i class="nav-icon fas fa-copy"></i>-->
      <!--    <p>Today Report</p>-->
      <!--  </a>-->
      <!--</li>-->

      <!-- <li class="nav-header">Reports</li> -->

      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-file-invoice"></i>
          <p>
            Reports
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info right">6</span>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="report-dm-view.php" class="nav-link">
              <i class="nav-icon fas fa-medkit"></i>
              <p>Doc Med Report</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="report-ViewInvoices.php" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>View Invoices</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="report-OldInvoices.php" class="nav-link">
              <i class="nav-icon fas fa-print"></i>
              <p>Old Invoices</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="cashier-today-report.php" class="nav-link">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>My Today Sales Report</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="report-ItemOutQty.php" class="nav-link">
              <i class="nav-icon fas fa-prescription-bottle-alt"></i>
              <i class=""></i>
              <p>Total Items Sale Report</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="cashier-sales-report.php" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Sales Report</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="cashier-all-sales-report.php" class="nav-link">
              <i class="nav-icon far fa-chart-bar"></i>
              <p>All Sales Report</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="patient-invoiceReport.php" class="nav-link">
              <i class="nav-icon fab fa-accessible-icon"></i>
              <p>Patient Invoice Report</p>
            </a>
          </li>
        </ul>
      </li>

      </ul>
      </nav>

    </div>

  </aside>
<?php
  // }
} else {
  echo "  ";
}
?>