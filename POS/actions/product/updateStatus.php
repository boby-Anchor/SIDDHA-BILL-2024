<?php
session_start();
require_once '../../config/db.php';
$response_message = 'Product Deactivated Successfully';

if (isset($_SESSION['store_id'])) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $barcode = $_POST['barcode'];
        $status = $_POST['status'];

        try {
            // Update p_medicine
            $updateQuery = "UPDATE p_medicine SET `status` = '$status' WHERE code = '$barcode'";

            if ($conn->query($updateQuery) !== TRUE) {
                error_log($conn->error);
                throw new Exception(`Error updating p_medicine: $conn->error`);
            }

            if ($status == 1) {
                $response_message = 'Product Activated Successfully';
            }
            echo json_encode([
                'status' => 'success',
                'message' => $response_message
            ]);
            exit();
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to login again.'
    ]);
}
