<?php
	date_default_timezone_set("Asia/Colombo");
session_start();
include('../config/db.php');
	$date=date("Y-m-d");
if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'];
    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];
        $user_id = $userData['id'];

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
                    $stmt->bind_param("isss", $shop_id, $medicineCategory, $fileType,$date);
                    if ($stmt->execute()) {
                        $_SESSION['msg'] = "ADD The Catogery :-" . $medicineCategory;
                        // Redirect to manage-products.php after successful update
                        header("Location: ../add-category.php");
                        exit(); // Ensure script stops execution after redirection
                    } else {
                        echo "Error updating record: " . $stmt->error;
                    }
                } else {
                    echo "Error uploading file.";
                }
            } else {
                // File not uploaded, retain existing image path
                $updateQuery = "INSERT INTO `p_medicine_category`( `store`, `name`,`date`)  VALUES (?, ?, ?)";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("iss", $shop_id, $medicineCategory,$date);
                if ($stmt->execute()) {
                    // Redirect to manage-products.php after successful update
                    $_SESSION['msg'] = "ADD The Catogery :-".$medicineCategory;
                    header("Location: ../add-category.php");
                    exit(); // Ensure script stops execution after redirection
                } else {
                    echo "Error updating record: " . $stmt->error;
                }
            }
        }
    }
}
?>
