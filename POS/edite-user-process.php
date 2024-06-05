<?php
include('config/db.php');
session_start();
if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $uid = $_POST['uid'];
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user_role = $_POST['user_role'];
            $user_added_shop = $_POST['user_added_shop'];

            $conn->query("UPDATE users SET user_name = '$username' , user_pass = '$password' , user_role_id = '$user_role' , shop_id = '$user_added_shop' WHERE id = '$uid'");
            echo "Record updated successfully";
        } else {
            echo "Invalid request";
        }
    }
}
