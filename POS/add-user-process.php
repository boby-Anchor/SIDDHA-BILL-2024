<?php
include('config/db.php');
session_start();
if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user_role = $_POST['user_role'];
            $user_added_shop = $_POST['user_added_shop'];
            $check_username_query = "SELECT * FROM users WHERE user_name = '$username'";
            $result = $conn->query($check_username_query);
            if ($result->num_rows > 0) {
                echo "Username already exists";
            } else {
                $current_datetime = date("Y-m-d H:i:s");
                $insert_query = "INSERT INTO users (`name`, user_name, user_pass,date_registered, user_role_id,shop_id) VALUES ('$name', '$username', '$password','$current_datetime', '$user_role','$user_added_shop')";
                if ($conn->query($insert_query) === TRUE) {
                    echo "New record created successfully";
                } else {
                    echo "Error: " . $insert_query . "<br>" . $conn->error;
                }
            }
        } else {
            echo "Invalid request";
        }
    }
}
