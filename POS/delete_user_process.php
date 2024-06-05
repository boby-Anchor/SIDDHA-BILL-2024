<?php
include('config/db.php');
session_start();
if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $uid = $_POST['user_id'];
            

            $conn->query("DELETE FROM users WHERE id = '$uid'");
            echo "Record deleted successfully";
        } else {
            echo "Invalid request";
        }
    }
}
