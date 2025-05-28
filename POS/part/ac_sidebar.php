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
              <a href="ac-dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-header">REPORTS</li>

            <li class="nav-item">
              <a href="manage-grn.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>View GRN</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="report-ItemQtyFromGrn.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>GRN Item Qty Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="report-ItemQtyFromPo.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>PO Item Qty Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="report-ItemOutQty.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <i class=""></i>
                <p>Total Items Sale Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="report-ItemOutQtyInDateRange.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <i class=""></i>
                <p>Item Sale Qty In Date Range</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="report-profitMargin.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Profit Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="cashier-all-sales-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>All Sales Report</p>
              </a>
            </li>

            <li class="nav-header"></li>

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