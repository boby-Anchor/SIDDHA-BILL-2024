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
            $oldBarcode = $_POST['original_barcode'];
            $newBarcode = $_POST['po_product_code'];
            $productId = $_POST['po_product_id'];
            // $cost = $_POST['po_cost'];
            // $price = $_POST['po_price'];

            // Check if file is uploaded
            // if (isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0) {
            //     $targetDir = "../dist/img/product/";
            //     $fileName = basename($_FILES["uploadfile"]["name"]);
            //     $targetFilePath = $targetDir . $fileName;
            //     $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            //     // if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $targetFilePath)) {
            //     //     // File uploaded successfully, update database with new image
            //     //     $updateQuery = "UPDATE p_medicine SET `name` = '$productName' , img = '$fileName' , `code` = '$productCode' WHERE id = '$productId'";
            //     //     if ($conn->query($updateQuery) === TRUE) {
            //     //         // Redirect to manage-products.php after successful update
            //     //         header("Location: ../manage-products.php");
            //     //         exit(); // Ensure script stops execution after redirection
            //     //     } else {
            //     //         echo "Error updating record: " . $conn->error;
            //     //     }
            //     // } else {
            //     //     echo "Error uploading file.";
            //     // }
            // } else {
            // File not uploaded, retain existing image path
            // $updateQuery = "UPDATE p_medicine SET `name` = '$productName' , `code` = '$productCode' WHERE id = '$productId'";
            // if ($conn->query($updateQuery) === TRUE) {
            //     // Redirect to manage-products.php after successful update
            //     header("Location: ../manage-products.php");
            //     exit(); // Ensure script stops execution after redirection
            // } else {
            //     echo "Error updating record: " . $conn->error;
            // }
            // }
            // Start transaction

            $conn->begin_transaction();

            try {
                // Update p_medicine
                $updateQuery = "UPDATE p_medicine SET `code` = '$newBarcode' WHERE code = '$oldBarcode'";

                if ($conn->query($updateQuery) !== TRUE) {
                    error_log($conn->error);
                    throw new Exception("Error updating p_medicine: " . $conn->error);
                }

                // update GRN items
                $updateGrn = "UPDATE grn_item SET grn_p_id = '$newBarcode' WHERE grn_p_id = '$oldBarcode'";
                if ($conn->query($updateGrn) !== TRUE) {
                    error_log($conn->error);
                    throw new Exception("Error updating grn_item: " . $conn->error);
                }
                // update Invoice items
                $updateInvoiceItem = "UPDATE invoiceitems SET barcode = '$newBarcode' WHERE barcode = '$oldBarcode'";
                if ($conn->query($updateInvoiceItem) !== TRUE) {
                    error_log($conn->error);
                    throw new Exception("Error updating invoice_item: " . $conn->error);
                }

                // update PO items
                $updatePO = "UPDATE poinvoiceitems SET item_code = '$newBarcode' WHERE item_code = '$oldBarcode'";
                if ($conn->query($updatePO) !== TRUE) {
                    error_log($conn->error);
                    throw new Exception("Error updating grn_item: " . $conn->error);
                }

                // update Stock
                $updateStock = "UPDATE stock2 SET stock_item_code  = '$newBarcode' WHERE stock_item_code  = '$oldBarcode'";
                if ($conn->query($updateStock) !== TRUE) {
                    error_log($conn->error);
                    throw new Exception("Error updating grn_item: " . $conn->error);
                }

                // Commit transaction if all queries succeeded
                $conn->commit();

                // Redirect after success
                header("Location: ../manage-products.php");
                exit();
            } catch (Exception $e) {
                // Rollback transaction if any query failed
                $conn->rollback();
                echo $e->getMessage();
            }
        }
    }
}
