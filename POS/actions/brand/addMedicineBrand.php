<?php
date_default_timezone_set("Asia/Colombo");
session_start();
require_once '../config/db.php';
$date = date("Y-m-d");

if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to log in again.'
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $medicineBrand = $_POST['medicineBrand'];
    $detailsText = $_POST['detailsText'];

    // Check if file is uploaded
    // if (isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0) {
    //     $targetDir = "../dist/img/product/";
    //     $fileName = basename($_FILES["uploadfile"]["name"]);
    //     $targetFilePath = $targetDir . $fileName;
    //     $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    //     if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $targetFilePath)) {
    //         // File uploaded successfully, update database with new image
    //         $updateQuery = "INSERT INTO `p_brand` (`name`, `details`, `img`, `date`) VALUES (?, ?, ?, ?)";
    //         $stmt = $conn->prepare($updateQuery);
    //         $stmt->bind_param("ssss", $medicineBrand, $detailsText, $fileType, $date);
    //         if ($stmt->execute()) {
    //             // Send success message after completion
    //             echo json_encode([
    //                 'status' => 'success',
    //                 'message' => "$medicineBrand Added successfully",
    //             ]);
    //         } else {
    //             echo json_encode([
    //                 'status' => 'error',
    //                 'message' => "Error updating record: $stmt->error"
    //             ]);
    //         }
    //     } else {
    //         echo json_encode([
    //             'status' => 'error',
    //             'message' => 'Error uploading file.'
    //         ]);
    //     }
    // } else {
    // File not uploaded, retain existing image path
    $updateQuery = "INSERT INTO `p_brand` (`name`, `details`, `date`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sss", $medicineBrand, $detailsText, $date);
    if ($stmt->execute()) {
        // Send success message after completion
        echo json_encode([
            'status' => 'success',
            'message' => "$medicineBrand Added successfully",
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "Error updating record: $stmt->error"
        ]);
    }
    // }
}
