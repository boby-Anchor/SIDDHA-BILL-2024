<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to login again.'
    ]);
    exit;
}

$supplier_id   = null;
$supplier_name = null;
$email         = null;
$contact       = null;
$address       = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id   = trim($_POST['supplier_id'] ?? '') ?: null;
    $supplier_name = trim($_POST['supplier_name'] ?? '') ?: null;
    $email         = trim($_POST['email'] ?? '') ?: null;
    $contact       = trim($_POST['contact'] ?? '') ?: null;
    $address       = trim($_POST['address'] ?? '') ?: null;
}

try {
    $stmt = $conn->prepare("SELECT * FROM p_supplier WHERE id = ?");
    if (!$stmt) {
        error_log($conn->error);
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        throw new Exception("Supplier not found");
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

try {
    if (
        (($product['name'] ?? null) === $supplier_name) &&
        (($product['email'] ?? null) === $email) &&
        (($product['phone'] ?? null) === $contact) &&
        (($product['address'] ?? null) === $address)
    ) {
        echo json_encode([
            "status"  => "error",
            "message" => "No changes detected"
        ]);
        exit();
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}

// Start transaction
$conn->begin_transaction();

try {

    if ($product['name'] != $supplier_name) {
        $stmt = $conn->prepare("SELECT name FROM p_supplier WHERE name = ?");
        if (!$stmt) {
            error_log($conn->error);
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $supplier_name);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            throw new Exception("Supplier already exists!");
        }
        $stmt->close();

        $updateQuery = "UPDATE p_supplier SET `name` = '$supplier_name' WHERE id = '$supplier_id'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating Supplier name: " . $conn->error);
        }
    }
    if ($product['phone'] != $contact) {
        $updateQuery = "UPDATE p_supplier SET `phone` = '$contact' WHERE id = '$supplier_id'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating Supplier contact: " . $conn->error);
        }
    }
    if ($product['email'] != $email) {
        $updateQuery = "UPDATE p_supplier SET `email` = '$email' WHERE id = '$supplier_id'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating Supplier email: " . $conn->error);
        }
    }
    $conn->commit();

    echo json_encode([
        "status"  => "success",
        "message" => "Details updated successfully"
    ]);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
