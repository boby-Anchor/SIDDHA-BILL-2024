<?php
date_default_timezone_set("Asia/Colombo");
session_start();
include('../../config/db.php');
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

if (isset($_POST['category_name'])) {
    $category_name = $_POST['category_name'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category data not received.',
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $medicineCategory = $_POST['category_name'];

    $query = "SELECT `name` FROM p_medicine_category WHERE `name`= '$medicineCategory'";
    $result = $conn->query($query);

    if (!$result) {
        die("Query Failed: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => "Category $medicineCategory already exists."
        ]);
        exit();
    }

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
                header("Location: ../../add-category.php");
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        $updateQuery = "INSERT INTO `p_medicine_category`(`name`,`date`)  VALUES (?,?)";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ss", $medicineCategory, $date);
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => "New category $medicineCategory added"
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $stmt->error
            ]);
        }
    }
}
