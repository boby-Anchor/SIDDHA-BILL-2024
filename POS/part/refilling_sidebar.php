<?php
if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    $userName = $userLoginData[0]['name'];
    $user_id = $userLoginData[0]['id'];
    $shop_id = $userLoginData[0]['shop_id'];

    $user_shop_rs = $conn->query("SELECT * FROM users INNER JOIN shop ON shop.shopId = users.shop_id INNER JOIN user_role ON user_role.user_role_id = users.user_role_id WHERE id = '$user_id'");
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
                                    <i class="nav-icon fas fa-pen-square"></i>
                                    <p>Transfer to Hub</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>
                                Refill Reports
                                <i class="fas fa-angle-left right"></i>
                                <span class="badge badge-info right">3</span>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="report-refillingConverts.php" class="nav-link">
                                    <i class="nav-icon fas fa-pen-square"></i>
                                    <p>Converted Batches</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="stock.php" class="nav-link">
                                    <i class="nav-icon fab fa-stack-overflow"></i>
                                    <p>Shop Stock</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="poView.php" class="nav-link">
                                    <i class="nav-icon fas fa-reply-all"></i>
                                    <p>Purchase Orders</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
<?php
}
