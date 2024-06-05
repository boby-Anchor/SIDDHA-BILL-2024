<?php

session_start();
include('../config/db.php');

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];
        $user_id = $userData['id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $productName = $_POST['po_product_name'];
            $productCode = $_POST['po_product_code'];
            $productId = $_POST['po_product_id'];
            // $cost = $_POST['po_cost'];
            // $price = $_POST['po_price'];

            // Check if file is uploaded
            if (isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0) {
                $targetDir = "../dist/img/product/";
                $fileName = basename($_FILES["uploadfile"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $targetFilePath)) {
                    // File uploaded successfully, update database with new image
                    $updateQuery = "UPDATE p_medicine SET `name` = '$productName' , img = '$fileName' , `code` = '$productCode' WHERE id = '$productId'";
                    if ($conn->query($updateQuery) === TRUE) {
                        // Redirect to manage-products.php after successful update
                        header("Location: ../manage-products.php");
                        exit(); // Ensure script stops execution after redirection
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error uploading file.";
                }
            } else {
                // File not uploaded, retain existing image path
                $updateQuery = "UPDATE p_medicine SET `name` = '$productName' , `code` = '$productCode' WHERE id = '$productId'";
                if ($conn->query($updateQuery) === TRUE) {
                    // Redirect to manage-products.php after successful update
                    header("Location: ../manage-products.php");
                    exit(); // Ensure script stops execution after redirection
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
        }
    }
}
?>
