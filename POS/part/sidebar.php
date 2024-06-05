<?php
if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];

  foreach ($userLoginData as $userData) {

    $userRoleId = $userData['user_role_id'];

    if ($userRoleId == '1') {
      require 'super_admin_sidebar.php';
    } elseif ($userRoleId == '2') {
      require 'hub_admin_sidebar.php';
    } elseif ($userRoleId == '3') {
      require 'shop_admin_sidebar.php';
    } elseif ($userRoleId == '4') {
      require 'stock_keeper_sidebar.php';
    } elseif ($userRoleId == '5') {
      require 'cashier_sidebar.php';
    }
  }
}
