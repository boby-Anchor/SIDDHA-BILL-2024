<?php
session_start();
require_once '../../config/db.php';

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $shop_id = $userLoginData['shop_id'];
    $user_id = $userLoginData['id'];
} else {
    echo json_encode([
        'status' => 'session_expired',
        'message' => 'Session expired! Wait to login again.'
    ]);
    exit();
}

$name = $conn->real_escape_string($_POST['name']);
$email = isset($_POST['email']) && trim($_POST['email']) !== ''
    ? trim($_POST['email'])
    : null;

$phone = isset($_POST['phone']) && trim($_POST['phone']) !== ''
    ? trim($_POST['phone'])
    : null;

$address = isset($_POST['address']) && trim($_POST['address']) !== ''
    ? trim($_POST['address'])
    : null;

if (empty($name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Enter Supplier Name!'
    ]);
    exit();
}

$stmt = $conn->prepare("SELECT 1 FROM p_supplier WHERE name = ? LIMIT 1");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Supplier already exists.'
    ]);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO p_supplier (name, email, phone, address, created_by) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Supplier saved successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Supplier saving failed.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "System error . $stmt->error"
        ]);
    }
    $stmt->close();
} catch (Exception $error) {
    echo json_encode([
        'status' => 'error',
        'message' => $error->getMessage()
    ]);
}
