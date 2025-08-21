<?php
if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];

  foreach ($userLoginData as $userData) {
    $userName = $userData['name'];
    $userId = $userData['id'];

    $user_shop_rs = $conn->query("SELECT * FROM users
    INNER JOIN shop ON shop.shopId = users.shop_id
    INNER JOIN user_role ON user_role.user_role_id = users.user_role_id
    WHERE id = '$userId'
    ");
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

            <?php
            if ($user_shop_data['user_role'] == "Stock Keeper") {
            ?>
              <li class="nav-header">Special</li>
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
                  <?php
                  if ($userId == 13) {
                  ?>
                    <li class="nav-item">
                      <a href="report-shopWiseItemsSale.php" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <i class=""></i>
                        <p>Shop wise item sale qty</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="report-weeklySalesCompare.php" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <i class=""></i>
                        <p>Weekly Siddha Sale Compare Report</p>
                      </a>
                    </li>
                  <?php
                  }
                  ?>
                </ul>
              </li>
            <?php
            }
            ?>
            <li class="nav-header"> Stock Item functions</li>
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

                <?php
                if ($user_shop_data['shop_id'] != 1) {
                ?>
                  <li class="nav-item">
                    <a href="add-purchase.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Purchase Order</p>
                    </a>
                  </li>
                <?php
                }
                ?>

                <li class="nav-item">
                  <a href="manage-purchase.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders List</p>
                  </a>
                </li>

                <!-- PO bill and view for hub Start -->
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
                  <li class="nav-item">
                    <a href="add-stock-from-po.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add Stock from PO</p>
                    </a>
                  </li>
                <?php
                }
                ?>
                <!-- PO bill and view for hub End -->
              </ul>
            </li>

            <li class="nav-item">

              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>Stock
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">5</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="stock.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>View Shop Stock</p>
                  </a>
                </li>
                <!-- hub admin and hub stock keeper only Start-->
                <?php
                if ($user_shop_data['shop_id'] == 1) {
                ?>
                  <li class="nav-item">
                    <a href="view_all_stock.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>View all Stock</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="add-stock.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Add New Stock (GRN)</p>
                    </a>
                  </li>
                <?php
                }
                ?>
                <!-- hub admin and hub stock keeper only End-->
                <li class="nav-item" disabled>
                  <a href="return-invoice.php" class="nav-link">
                    <i class="nav-icon fas fa-reply-all"></i>
                    <p>Return stock to HUB</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="manage-grn.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>View GRNs</p>
                  </a>
                </li>
                <?php
                if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 2) {
                ?>
                  <li class="nav-item">
                    <a href="report-monthlySaleCompare.php" class="nav-link">
                      <i class="nav-icon fas fa-copy"></i>
                      <i class=""></i>
                      <p>Item Sale Qty Compare In Date Range</p>
                    </a>
                  </li>
                <?php
                }
                ?>
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
              <!-- Products management start -->
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
              <!-- Products management end -->

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
              <!-- Categories management end -->

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
              <!-- Brands management end -->

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
              <!-- UCV management end -->

              <!-- Supplier management -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Suppliers
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">4</span>
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
              <!-- Supplier management end -->

            <?php
            }
            ?>
            <li class="nav-header">Item Qty Reports</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Reports
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">3</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="report-dm-view.php" class="nav-link">
                    <i class="nav-icon fas fa-medkit"></i>
                    <i class=""></i>
                    <p>DM data Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="report-ItemsByDocInDateRange.php" class="nav-link">
                    <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                    <i class=""></i>
                    <p>Invoice Items by Doctor</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="report-ItemOutQty.php" class="nav-link">
                    <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                    <i class=""></i>
                    <p>Total Items Sale Report</p>
                  </a>
                </li>
                <?php
                if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 1) {
                ?>
                  <li class="nav-item">
                    <a href="report-weeklySalesCompare.php" class="nav-link">
                      <i class="nav-icon fas fa-copy"></i>
                      <i class=""></i>
                      <p>Weekly Sale Compare</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="report-monthlySaleCompare.php" class="nav-link">
                      <i class="nav-icon fas fa-copy"></i>
                      <i class=""></i>
                      <p>Monthly Sale Compare</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="report-ItemQtyFromInvoice.php" class="nav-link">
                      <i class="nav-icon fas fa-copy"></i>
                      <p>Invoice Item Qty Report</p>
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
                <?php
                }
                ?>
              </ul>
            </li>
            <?php
            if ($user_shop_data['user_role'] == "Stock Keeper" && $user_shop_data['shop_id'] == 2) {
            ?>
              <!-- <li class="nav-header">Item Qty Reports</li> -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Functions
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">1</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="" class="nav-link">
                      <i class="nav-icon fas fa-copy"></i>
                      <i class=""></i>
                      <p>Add doctor</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php
            }

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