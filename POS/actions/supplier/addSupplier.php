<?php
session_start();
require_once '../../config/db.php';

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $shop_id = $userLoginData['shop_id'];
    $user_id = $userLoginData['id'];
} else {
    echo json_encode([
        'status' => 'session_expired',
        'message' => 'Session expired! Wait to login again.'
    ]);
    exit();
}

    $chk = 0;
    $name = $conn->real_escape_string($_POST['name']);   //required
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $addr1 = $conn->real_escape_string($_POST['address']);
  
    // -------------- Empty input field check
    if (empty($name)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Enter supplier name";
        echo "<script>window.history.back();</script>";
        exit();
    }

    // -------------- Empty input field check end

    $check = mysqli_num_rows($conn->query("SELECT `name`, `store` FROM `p_supplier` where `name`='$name' AND `created_by`='$user_id'"));
    if ($check > 0) {
        $chk = 1;
        $_SESSION['e-msg'] = "This name already exist";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if ($chk == 0) {
        $conn->query("INSERT INTO `p_supplier`(`store`, `name`, `email`, `phone`, `address`, `date`) VALUES ('$_SESSION[store_id]','$name','$email','$phone','$addr1', '$date')");
        $_SESSION['msg'] = "Information submit successfully";
        // echo "<script>window.history.back();</script>";
        exit();
    } else {
        $_SESSION['e-msg'] = "Something went wrong. Try later !!!";
        echo "<script>window.history.back();</script>";
        exit();
    }
