<?php
if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];

  foreach ($userLoginData as $userData) {

    $userRoleId = $userData['user_role_id'];

    $sidebars = [
      '1' => 'super_admin_sidebar.php',
      '2' => 'hub_admin_sidebar.php',
      '3' => 'shop_admin_sidebar.php',
      '4' => 'stock_keeper_sidebar.php',
      '5' => 'cashier_sidebar.php',
      '6' => 'ac_sidebar.php',
    ];

    if (isset($sidebars[$userRoleId])) {
      require $sidebars[$userRoleId];
    }

    // 38
  }
}
