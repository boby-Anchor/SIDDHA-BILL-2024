<?php

if (!isset($_POST['submit'])) {
    echo "<script>window.history.back();</script>";
    exit();
} else {

    session_start();
    include('../config/db.php');

    $chk = 0;
    $shopname = $conn->real_escape_string($_POST['shopname']);   //required
    $shopemail = $conn->real_escape_string($_POST['shopemail']);
    $shopphone = $conn->real_escape_string($_POST['shopphone']);
    $shopwhatsapp = $conn->real_escape_string($_POST['shopwhatsapp']);
    $shopaddr1 = $conn->real_escape_string($_POST['shopaddress']);

    $shopmanagername = $conn->real_escape_string($_POST['shopmanagername']);   //required
    $shopmanageremail = $conn->real_escape_string($_POST['shopmanageremail']);
    $shopmanagerphone = $conn->real_escape_string($_POST['shopmanagerphone']);
    $shopmanagerwhatsapp = $conn->real_escape_string($_POST['shopmanagerwhatsapp']);
    $shopmanageraddr1 = $conn->real_escape_string($_POST['shopmanageraddress']);


    // image 
    $uploadFileRename = "not-available.png";
    $filename = $_FILES["uploadfile"]["name"]; //file name
    if (!empty($filename)) {
        $tempname = $_FILES["uploadfile"]["tmp_name"]; //file
        $uploadFileRename = time() . rand(00, 999) . $_SESSION['store_id'] . $filename; //rename file
        $folder = "../dist/img/shop/" . $uploadFileRename; //root folder destination

        $allowTypes = array('jpg', 'png', 'jpeg');
        $fileType = pathinfo($filename, PATHINFO_EXTENSION);

        // checking file type for image
        if (!in_array($fileType, $allowTypes)) {
            $chk = 1;
            $_SESSION['e-msg'] = "Only 'jpg','png','jpeg' support.";
            echo "<script>window.history.back();</script>";
            exit();
        }
    }

    // -------------- Empty input field check 
    if (empty($shopname)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Enter shop name";
        echo "<script>window.history.back();</script>";
        exit();
    }
    // -------------- Empty input field check end

    $check = mysqli_num_rows($conn->query("SELECT * FROM `shop` WHERE `shopName` = '$shopname' AND `shopManagerName` = '$shopmanagername'"));
    if ($check > 0) {
        $chk = 1;
        $_SESSION['e-msg'] = "This shop already exist";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if ($chk == 0) {
        $shopInsert = $conn->query("INSERT INTO `shop` (`shopName`,`shopEmail`,`shopAddress`,`shopTel`,`shopWhatsApp`,`shopManagerName`,`shopManagerEmail`,`shopManagerAddress`,`shopManagerTel`,`shopManagerWhatsApp`,`shopImg`) VALUES ('$shopname','$shopemail','$shopaddr1','$shopphone','$shopwhatsapp','$shopmanagername','$shopmanageremail','$shopmanageraddr1','$shopmanagerphone','$shopmanagerwhatsapp','$uploadFileRename')");

        if ($shopInsert) {
            // Get the inserted ID
            $shopinserted_id = $conn->insert_id;
            $p_medicin_data = $conn->query("SELECT * FROM `p_medicine`");
            while ($p_medicin_row = mysqli_fetch_assoc($p_medicin_data)) {
                $conn->query("INSERT INTO producttoshop (medicinId,shop_id,productToShopStatus) VALUES ('" . $p_medicin_row["id"] . "','$shopinserted_id','remove')");
            }
            $_SESSION['msg'] = "Information submit successfully";
            if (!empty($filename)) {
                move_uploaded_file($tempname, $folder);
            }

            echo "<script>
                window.history.back();
                document.getElementById('addShopForm').reset();
            </script>";
            exit();
        }
    } else {
        $_SESSION['e-msg'] = "Something went wrong. Try later !!!";
        echo "<script>window.history.back();</script>";
        exit();
    }
}
