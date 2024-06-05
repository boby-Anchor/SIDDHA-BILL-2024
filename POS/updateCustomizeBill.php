<?php
require "./config/db.php";

// logo update
if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
    $tempFile = $_FILES["file"]["tmp_name"];
    $targetFile = "dist/img/billLogoes/" . $_FILES["file"]["name"];
    $selectedShopId = isset($_POST['selectedShopId']) ? $_POST['selectedShopId'] : '';

    if (move_uploaded_file($tempFile, $targetFile)) {
        $filePath = $targetFile;

        $conn->query("UPDATE customize_bills SET customize_bills_logo = '$filePath' WHERE `customize_bill_shop-id` = '$selectedShopId'");

        echo "File uploaded successfully.";
    } else {
        echo "Error moving file.";
    }
} else {
    echo "Error uploading file.";
}

// contact number update

if (isset($_POST['contactNo'])) {
    $contact_number = isset($_POST['contactNo']) ? $_POST['contactNo'] : '';
    $selectedShopId = isset($_POST['selectedShopId']) ? $_POST['selectedShopId'] : '';
    $conn->query("UPDATE customize_bills SET customize_bills_mobile = '$contact_number' WHERE `customize_bill_shop-id` = '$selectedShopId'");
    echo "Data inserted successfully.";
} else {
    echo "Error: Contact No is not set.";
}


// address update

if (isset($_POST['address'])) {
    $address = $_POST['address'];
    $selectedShopId = isset($_POST['selectedShopId']) ? $_POST['selectedShopId'] : '';
    $conn->query("UPDATE customize_bills SET customize_bills_address = '$address' WHERE `customize_bill_shop-id` = '$selectedShopId'");
    echo "Data inserted successfully.";
} else {
    echo "Error: Address No is not set.";
}


// note update

if (isset($_POST['noteInput'])) {
    $noteInput = $_POST['noteInput'];
    $selectedShopId = isset($_POST['selectedShopId']) ? $_POST['selectedShopId'] : '';
    $conn->query("UPDATE customize_bills SET bill_note = '$noteInput' WHERE `customize_bill_shop-id` = '$selectedShopId'");
    echo "Data inserted successfully.";
} else {
    echo "Error: Note is not set.";
}
