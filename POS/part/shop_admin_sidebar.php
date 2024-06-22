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
      <!--<span class="brand-text font-weight-light text-uppercase">Siddha.lk</span>-->
      </a>

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
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Shops
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">2</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="add-shop.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add Shops</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="manage-shop.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Manage Shops</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="shop-add-user.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Manage User</p>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-header">EXPENSES & PAYMENT</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Expenses
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">3</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="expense.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add Expenses</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="manage-expense.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Manage Expenses</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="expense-category.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Expenses Category</p>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Payments
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">2</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="add-payment.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add Payments</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="manage-payment.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Manage Payments</p>
                  </a>
                </li>
              </ul>
            </li>


           

            <li class="nav-header">REPORTS</li>
            <li class="nav-item">
              <a href="today-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Today Report</p>
              </a>
            </li>
            <!--<li class="nav-item">-->
            <!--  <a href="current-month-report.php" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Current Month Report</p>-->
            <!--  </a>-->
            <!--</li>-->
            <!--<li class="nav-item">-->
            <!--  <a href="summary-report.php" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Summary Report</p>-->
            <!--  </a>-->
            <!--</li>-->
            
            
            
            <li class="nav-item">
              <a href="daily-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Daily Report</p>
              </a>
            </li>
            <!--<li class="nav-item">-->
            <!--  <a href="" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Due Customer Report</p>-->
            <!--  </a>-->
            <!--</li>-->
            <li class="nav-item">
              <a href="low-stock-report.php" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Low Stock Report</p>
              </a>
            </li>
            <li class="nav-item">
               <a href="today-cashiers-shop.php" class="nav-link">
            <i class="nav-icon fas fa-copy"></i>
                <p>Today Cashiers</p>
                </a>
            </li>
            
            <li class="nav-item">
               <a href="ac-cashier-today-report.php" class="nav-link">
            <i class="nav-icon fas fa-copy"></i>
                <p>Today Sale</p>
                </a>
            </li>
            
            <!--<li class="nav-item">-->
            <!--  <a href="top-customer.php" class="nav-link">-->
            <!--    <i class="nav-icon fas fa-copy"></i>-->
            <!--    <p>Top Customer</p>-->
            <!--  </a>-->
            <!--</li>-->

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