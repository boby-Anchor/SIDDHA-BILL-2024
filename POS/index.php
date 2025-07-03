<?php
session_start();

if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];
  $redirects = [
    '1' => 'super_admin_dashboard.php',
    '2' => 'all-today-cashiers.php',
    '3' => 'manage-shop.php',
    '4' => 'stock.php',
    '5' => 'pos.php',
    '6' => 'ac.php'
  ];

  foreach ($userLoginData as $userData) {
    $userRoleId = $userData['user_role_id'];

    if (isset($redirects[$userRoleId])) {
      echo "<script>window.location='" . $redirects[$userRoleId] . "';</script>";
      exit();
    } else {
      echo "<script>alert('Unknown role!');</script>";
      exit();
    }
  }
}
