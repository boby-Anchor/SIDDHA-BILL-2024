<?php

session_start();
include('../config/db.php');

if (isset($_POST['submit'])) {
    $currentDate = date('Y-m-d');
    $chk = 0;
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $product_code = $conn->real_escape_string($_POST['product_code']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $category_product = $conn->real_escape_string($_POST['category_product']);
    $unit_variation = $conn->real_escape_string($_POST['unit_variation']);
    $brand_product = $conn->real_escape_string($_POST['brand_product']);
    $details = $conn->real_escape_string($_POST['details']);


    // image 
    $uploadFileRename = "not-available.png";
    $filename = $_FILES["uploadfile"]["name"]; //file name

    if (!empty($filename)) {
        $tempname = $_FILES["uploadfile"]["tmp_name"]; //file
        $uploadFileRename = time() . rand(00, 999) . $filename; //rename file
        $folder = "../dist/img/product/" . $uploadFileRename; //root folder destination

        // Allowed file types
        define('ALLOWED_TYPES', ['jpg', 'png', 'jpeg']);

        $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // checking file type for image
        if (!in_array($fileType, ALLOWED_TYPES)) {
            $_SESSION['e-msg'] = "Only 'jpg', 'png', 'jpeg' formats are supported.";
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        }
    }

    // Empty input field check 
    if (empty($product_name) || ctype_space($product_name)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Product name can not be empty";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if (empty($category_product) || ctype_space($category_product)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Category field can not be empty";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if (empty($brand_product) || ctype_space($brand_product)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Brand field can not be empty";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if (empty($unit_variation) || ctype_space($unit_variation)) {
        $chk = 1;
        $_SESSION['e-msg'] = "Unit Variation field can not be empty";
        echo "<script>window.history.back();</script>";
        exit();
    }

    // Empty input field check end


    // checking product 
    $check = numRows("SELECT * FROM p_medicine WHERE `name`= '$product_name' AND `category` = '$category_product' AND `brand` = '$brand_product' AND `medicine_unit_id` = '$unit' AND `code` = '$product_code' AND unit_variation = '$unit_variation'");
    if ($check > 0) {
        $chk = 1;
        $_SESSION['e-msg'] = "This product already exist";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if ($chk == 0) {

        $insert = $conn->query("INSERT INTO p_medicine (`name`,`details`,`category`,`brand`,`medicine_unit_id`,`code`,`img`,`date`,`unit_variation`) VALUES ('$product_name','$details','$category_product','$brand_product','$unit','$product_code','$uploadFileRename','$currentDate','$unit_variation')");
        $product_id = $conn->insert_id;
        $shop_result = $conn->query("SELECT * FROM shop");
        while ($shop_data = $shop_result->fetch_assoc()) {
            $conn->query("INSERT INTO producttoshop (medicinId,shop_id,productToShopStatus) VALUES ('$product_id','" . $shop_data['shopId'] . "','remove')");
        }

        $conn->query("UPDATE producttoshop  SET productToShopStatus = 'added'  WHERE shop_id = '1' AND medicinId ='$product_id'");

        // $conn->query("INSERT INTO stock2 (stock_item_id, stock_qty, stock_item_cost, stock_s_price, stock_shop_id, stock_item_unit) VALUES ('$product_code', '$new_stock', '$cost', '$product_price', '1', '$unit')");
        $_SESSION['msg'] = "Information submit successfully";
        if (!empty($filename)) {
            move_uploaded_file($tempname, $folder);
        }
        header("location:../add-product.php");
        exit();
    } else {
        $_SESSION['msg'] = "Something went wrong. Try again !!!";
        header("location:../add-product.php");
        exit();
    }
}

// Default return
echo "<script>window.history.back();</script>";
exit();
