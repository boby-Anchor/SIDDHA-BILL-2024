<?php
date_default_timezone_set("Asia/Colombo");
session_start();
require_once '../../config/db.php';
$date = date("Y-m-d");

if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];
    $user_id = $userData['id'];
    $shop_id = $userData['shop_id'];
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session Expired! Wait to login again.',
    ]);
    exit();
}

if (isset($_GET['id']) && $_POST['medicineCategory']) {
    $category_name = $_POST['medicineCategory'];
    $category_id = $_GET['id'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category data not received.',
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $medicineCategory = $_POST['medicineCategory'];

    // Check if file is uploaded
    if (isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0) {
        $targetDir = "../dist/img/product/";
        $fileName = basename($_FILES["uploadfile"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $targetFilePath)) {
            // File uploaded successfully, update database with new image
            $updateQuery = "
                    INSERT INTO `p_medicine_category`( `store`, `name`, `img`, `date`)   VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("isss", $shop_id, $medicineCategory, $fileType, $date);
            if ($stmt->execute()) {
                $_SESSION['msg'] = "ADD The Catogery :-" . $medicineCategory;
                // Redirect to manage-products.php after successful update
                header("Location: ../add-category.php");
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        // File not uploaded, retain existing image path
        $updateQuery = "UPDATE `p_medicine_category` SET `name`=? WHERE `id`=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $medicineCategory, $category_id);
        if ($stmt->execute()) {
            // Redirect to manage-products.php after successful update
            $_SESSION['msg'] = "Category :- . $medicineCategory updated";
            header("Location: ../../manage-category.php");
            exit();
        } else {
            echo "Error updating record: $stmt->error";
        }
    }
}
