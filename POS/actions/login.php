<?php

if (!isset($_POST['btnLogin'])) {
    echo "<script>window.history.back();</script>";
    exit();
} else {

    session_start();
    include('../config/db.php');

    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $chk = 0;

    if (empty($username)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Enter your username";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if (empty($password)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Enter your password";
        echo "<script>window.history.back();</script>";
        exit();
    }

    $userLoginData = array();
    $result = $conn->query("SELECT * FROM users WHERE user_name = '$username' AND user_pass = '$password'");
    $userCheck = mysqli_num_rows($result);

    if ($userCheck > 0 && $chk == 0) {
        $userLoginData[] = mysqli_fetch_assoc($result);
        $_SESSION['store_id'] = $userLoginData;
        header("location:../index.php");
        exit();
    } else {
        $_SESSION['e-msg'] = "Invalid user input";
        echo "<script>window.history.back();</script>";
        exit();
    }
}
