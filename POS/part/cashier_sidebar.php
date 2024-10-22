<?php
if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];

  foreach ($userLoginData as $userData) {
    $userName = $userData['name'];
    $userId = $userData['id'];

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

            <li class="nav-item">
              <a href="pos.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>POS Invoice</p>
              </a>
            </li>

            <!--<li class="nav-item">-->
            <!--  <a href="sales.php" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Sales</p>-->
            <!--  </a>-->
            <!--</li>-->

            <!--<li class="nav-item">-->
            <!--  <a href="return.php" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Return</p>-->
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



            <li class="nav-header">Reports</li>
            <!--<li class="nav-item">-->
            <!--  <a href="today-report.php" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Today Report</p>-->
            <!--  </a>-->
            <!--</li>-->

            <li class="nav-item">
              <a href="dm-view.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Doc Med Report</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="report-OldInvoices.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Old Invoices</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="cashier-today-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Today Sales Report</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="cashier-sales-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Sales Report</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="cashier-all-sales-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>All Sales Report</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="patient-invoiceReport.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Patient Invoice Reoprt</p>
              </a>
            </li>



            <li class="nav-header">Return</li>

            <li class="nav-item">
              <a href="return-invoice.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Return Invoice</p>
              </a>
            </li>

          </ul>
        </nav>

      </div>

    </aside>
<?php
  }
} else {
  echo "  ";
}
?>