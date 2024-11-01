<?php

if (!isset($_POST['btnLogin'])) {
    echo "<script>window.history.back();</script>";
    exit();
} else {

    session_start();
    include('../config/db.php');

    $userid = $conn->real_escape_string($_POST['userid']);
    $pwd = $conn->real_escape_string($_POST['pwd']);
    $chk = 0;

    if (empty($userid)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Enter your user ID";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if (empty($pwd)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Enter your password";
        echo "<script>window.history.back();</script>";
        exit();
    }

    $userLoginData = array();
    $result = $conn->query("SELECT * FROM users WHERE user_name = '$userid' AND user_pass = '$pwd'");
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
