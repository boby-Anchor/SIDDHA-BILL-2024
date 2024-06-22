<?php
session_start();
if (isset($_SESSION['store_id'])) {

  $userLoginData = $_SESSION['store_id'];

  foreach ($userLoginData as $userData) {

    $userRoleId = $userData['user_role_id'];

    if ($userRoleId == '1') {
      echo "<script>window.location='super_admin_dashboard.php';</script>";
      exit; // Exit the loop after redirection
    } elseif ($userRoleId == '2') {
      echo "<script>window.location='all-today-cashiers.php';</script>";
      exit; // Exit the loop after redirection
    } elseif ($userRoleId == '3') {
      echo "<script>window.location='manage-shop.php';</script>";
      exit; // Exit the loop after redirection
    } elseif ($userRoleId == '4') {
      echo "<script>window.location='add-stock.php';</script>";
      exit; // Exit the loop after redirection
    } elseif ($userRoleId == '5') {
      echo "<script>window.location='pos.php';</script>";
      exit; // Exit the loop after redirection
    } elseif ($userRoleId == '6') {
      echo "<script>window.location='ac.php';</script>";
      exit; // Exit the loop after redirection
    }
    
  }
}
