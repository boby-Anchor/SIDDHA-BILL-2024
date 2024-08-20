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
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Purchase
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">4</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="add-purchase.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add Purchase Order</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="manage-purchase.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Order List</p>
                  </a>
                </li>

                <?php
                if ($user_shop_data['shop_id'] == 1) {
                ?>

                  <li class="nav-item">
                    <a href="po.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>PO bill</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="poView.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>PO view</p>
                    </a>
                  </li>
                <?php
                }
                ?>

              </ul>
            </li>

            <li class="nav-item">

              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Stock
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">4</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <!-- meka hub admin and hub stock keeper only -->
                <?php
                if ($user_shop_data['shop_id'] == 1) {
                ?>
                  <li class="nav-item">
                    <a href="view_all_stock.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>View all Stock</p>
                    </a>
                  </li>
                <?php
                } ?>
                <!-- meka hub admin and hub stock keeper only -->
                <li class="nav-item">
                  <a href="stock.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>View Shop Stock</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="add-stock.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add New Stock</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="manage-grn.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p> GRN</p>
                  </a>
                </li>
              </ul>
            </li>

            <?php
            if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 9) {

            ?>
              <li class="nav-item">
                <a href="manage-products.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Edit Barcode</p>
                </a>
              </li>
            <?php
            }

            if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 1) {
            ?>
              <li class="nav-header">PRODUCT INFORMATION</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Products
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">3</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="add-product.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Products</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="manage-products.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Products</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="addtoShop-product.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add to Shop</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php
            }
            ?>

            <?php
            if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 1) {
            ?>

              <!-- Categories management -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Category
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">2</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="add-category.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Category</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="manage-category.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Category</p>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- Brands management -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Brands
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">2</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="add-brand.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Brands</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="manage-brand.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Brands</p>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- UCV management -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Unit Variation
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">1</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="add-unit-variation.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Unit Variation</p>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- Supplier management -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Suppliers
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">3</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="add-supplier.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Suppliers</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="manage-supplier.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Suppliers</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="new-supply.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add New Supply</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="manage-supply.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Manage Supply</p>
                    </a>
                  </li>
                </ul>
              </li>

            <?php
            }
            ?>

            <?php
            if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 7) {
            ?>
              <!-- Testing Area -->
              <li class="nav-header"></li>
              <li class="nav-header"></li>
              <li class="nav-header">(Danger Zone) NO ENTRY!!</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Test Area
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">3</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="edit-stock.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Stock Edit</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="report-ItemOutQty.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Item Out Qty</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="test_dashboard.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Test dashboard</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="test_dashboard2.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Test dashboard 2</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="po.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Return to Supplier</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="onlineBill.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Online Bill Test</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="test.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Test</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="test1.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Test 1</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php
            }
            ?>
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