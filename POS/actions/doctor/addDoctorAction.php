<?php
include('../../config/db.php');

if (!isset($_POST['doctor_name']) || empty(trim($_POST['doctor_name']))) {
    errorThrow("No doctor name received.");
    exit();
}

$doctor_name = trim($_POST['doctor_name']);

function errorThrow($message)
{
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO doctors (`name`) VALUES (?)");
$stmt->bind_param("s", $doctor_name);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'New doctor added successfully'
    ]);
} else {
    errorThrow("Failed to add doctor. Please try again.");
}

$stmt->close();
$conn->close();
