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
                        <!-- Manage Bills -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Manage Bills</p>
                                <i class="fas fa-angle-left right"></i>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="add-new-bill.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add Bill</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="customize_bill.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Customize Bill</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Manage User -->
                        <li class="nav-item">
                            <a href="hub-add-user.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage User</p>
                            </a>
                        </li>
                        <!-- Sales -->
                        <!--<li class="nav-item">-->
                        <!--    <a href="sales.php" class="nav-link">-->
                        <!--        <i class="nav-icon fas fa-copy"></i>-->
                        <!--        <p>Sales</p>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <!-- Return -->
                        <li class="nav-item">
                            <a href="return.php" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Return</p>
                            </a>
                        </li>
                        <!-- Purchase -->
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
                                    <a href="manage-purchase.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Order List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Stock -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Stock</p>
                                <i class="fas fa-angle-left right"></i>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="view_all_stock.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>View ALL Shops Stock</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="stock.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Stock</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="manage-grn.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage GRN</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Damages -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>
                                    Damages
                                    <i class="fas fa-angle-left right"></i>
                                    <span class="badge badge-info right">2</span>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="damage.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Damage Products</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Shops -->
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
                            </ul>
                        </li>
                        <!-- PRODUCT INFORMATION -->
                        <li class="nav-header">PRODUCT INFORMATION</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>
                                    Products
                                    <i class="fas fa-angle-left right"></i>
                                    <span class="badge badge-info right">2</span>
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
                        <!-- Category -->
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
                                    <a href="manage-category.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Category</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Brands -->
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
                                    <a href="manage-brand.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Brands</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- EXPENSES & PAYMENT -->
                        <!--<li class="nav-header">EXPENSES & PAYMENT</li>-->
                        <!--<li class="nav-item">-->
                        <!--     <a href="#" class="nav-link">-->
                        <!--         <i class="nav-icon fas fa-copy"></i>-->
                        <!--         <p>-->
                        <!--             Expenses-->
                        <!--             <i class="fas fa-angle-left right"></i>-->
                        <!--             <span class="badge badge-info right">3</span>-->
                        <!--         </p>-->
                        <!--     </a>-->
                        <!--     <ul class="nav nav-treeview">-->
                        <!--         <li class="nav-item">-->
                        <!--             <a href="expense.php" class="nav-link">-->
                        <!--                 <i class="far fa-circle nav-icon"></i>-->
                        <!--                 <p>Add Expenses</p>-->
                        <!--             </a>-->
                        <!--         </li>-->
                        <!--         <li class="nav-item">-->
                        <!--             <a href="manage-expense.php" class="nav-link">-->
                        <!--                 <i class="far fa-circle nav-icon"></i>-->
                        <!--                 <p>Manage Expenses</p>-->
                        <!--             </a>-->
                        <!--         </li>-->
                        <!--         <li class="nav-item">-->
                        <!--             <a href="expense-category.php" class="nav-link">-->
                        <!--                 <i class="far fa-circle nav-icon"></i>-->
                        <!--                 <p>Expenses Category</p>-->
                        <!--             </a>-->
                        <!--         </li>-->
                        <!--     </ul>-->
                        <!-- </li>-->
                        <!-- Payments -->
                        <!--<li class="nav-item">-->
                        <!--    <a href="#" class="nav-link">-->
                        <!--        <i class="nav-icon fas fa-copy"></i>-->
                        <!--        <p>-->
                        <!--            Payments-->
                        <!--            <i class="fas fa-angle-left right"></i>-->
                        <!--            <span class="badge badge-info right">2</span>-->
                        <!--        </p>-->
                        <!--    </a>-->
                        <!--    <ul class="nav nav-treeview">-->
                        <!--        <li class="nav-item">-->
                        <!--            <a href="add-payment.php" class="nav-link">-->
                        <!--                <i class="far fa-circle nav-icon"></i>-->
                        <!--                <p>Add Payments</p>-->
                        <!--            </a>-->
                        <!--        </li>-->
                        <!--        <li class="nav-item">-->
                        <!--            <a href="manage-payment.php" class="nav-link">-->
                        <!--                <i class="far fa-circle nav-icon"></i>-->
                        <!--                <p>Manage Payments</p>-->
                        <!--            </a>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--</li>-->
                        <!-- PEOPLES -->
                        <li class="nav-header">Suppliers</li>
                        <!--<li class="nav-item">-->
                        <!--    <a href="#" class="nav-link">-->
                        <!--        <i class="nav-icon fas fa-copy"></i>-->
                        <!--        <p>-->
                        <!--            Customers-->
                        <!--            <i class="fas fa-angle-left right"></i>-->
                        <!--            <span class="badge badge-info right">2</span>-->
                        <!--        </p>-->
                        <!--    </a>-->
                        <!--    <ul class="nav nav-treeview">-->

                        <!--        <li class="nav-item">-->
                        <!--            <a href="manage-customer.php" class="nav-link">-->
                        <!--                <i class="far fa-circle nav-icon"></i>-->
                        <!--                <p>Manage Customers</p>-->
                        <!--            </a>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--</li>-->
                        <!-- Suppliers -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>
                                    Suppliers
                                    <i class="fas fa-angle-left right"></i>
                                    <span class="badge badge-info right">2</span>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="manage-supplier.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Suppliers</p>
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
                        <!-- REPORTS -->
                        <li class="nav-header">REPORTS</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>
                                    REPORTS
                                    <i class="fas fa-angle-left right"></i>
                                    <span class="badge badge-info right">4</span>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="report-itemOutQty.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Item Out-Qty Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="low-stock-report.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Low Stock Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="today-report.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Today Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="today-cashiers-shop.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Today Cashiers</p>
                                    </a>
                                </li>
                                <!--<li class="nav-item">-->
                                <!--    <a href="summary-report.php" class="nav-link">-->
                                <!--        <i class="nav-icon fas fa-copy"></i>-->
                                <!--        <p>Summary Report</p>-->
                                <!--    </a>-->
                                <!--</li>-->
                                <li class="nav-item">
                                    <a href="daily-report.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Daily Report</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>
                                    ALL Shops
                                    <i class="fas fa-angle-left right"></i>
                                    <span class="badge badge-info right">3</span>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="all-low-stock-report.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Low Stock Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="all-today-report.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Today Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="all-today-cashiers.php" class="nav-link">
                                        <i class="nav-icon fas fa-copy"></i>
                                        <p>Today Cashiers</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="ac-cashier-today-report.php" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Today Sale</p>
                            </a>
                        </li>


                        <!--<li class="nav-item">-->
                        <!--    <a href="" class="nav-link">-->
                        <!--        <i class="nav-icon fas fa-copy"></i>-->
                        <!--        <p>Due Customer Report</p>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <!--<li class="nav-item">-->
                        <!--    <a href="current-month-report.php" class="nav-link">-->
                        <!--        <i class="nav-icon fas fa-copy"></i>-->
                        <!--        <p>Current Month Report</p>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <!--<li class="nav-item">-->
                        <!--    <a href="top-customer.php" class="nav-link">-->
                        <!--        <i class="nav-icon fas fa-copy"></i>-->
                        <!--        <p>Top Customer</p>-->
                        <!--    </a>-->
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