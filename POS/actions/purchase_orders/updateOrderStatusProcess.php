<?php
include('../../config/db.php');
try {

    $orderNumber = $_POST['orderNumber'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$orderNumber || !$status) {
        throw new Exception('Missing required parameters.');
    }

    if ($conn->query("UPDATE hub_order_details SET hub_order_status = '$status' WHERE hub_order_number = '$orderNumber'")) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Order status updated!',
        ));
    } else {
        throw new Exception('Status update failed!');
    }
} catch (Exception $exception) {
    echo json_encode(array(
        'status' => 'error',
        'message' => $exception->getMessage(),
    ));
}
