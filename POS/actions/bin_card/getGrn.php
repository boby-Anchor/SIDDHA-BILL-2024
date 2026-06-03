<?php
session_start();

if (!isset($_SESSION['store_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $barcode  = trim($_POST['barcode'] ?? '');
    $startDate = trim($_POST['startDate'] ?? '');
    $endDate   = trim($_POST['endDate'] ?? '');

    if ($startDate xor $endDate) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Both start and end dates are required.'
        ]);
        exit();
    }

    if ($barcode === '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Barcode is required.'
        ]);
        exit();
    }

    $sql = "
        SELECT
            grn.grn_number,
            grn.invoice_number,
            p_supplier.name AS supplier,
            grn.grn_date AS date,
            grn_item.grn_p_qty AS qty,
            grn_item.grn_p_price AS price,
            grn_item.grn_item_cost AS cost
        FROM grn_item
        INNER JOIN grn
            ON grn_item.grn_number = grn.grn_number
        LEFT JOIN p_supplier
            ON p_supplier.id = grn.supplier_id
        WHERE grn_item.grn_p_id = ?
    ";

    $params = [$barcode];
    $types = "s";

    if ($startDate !== '' && $endDate !== '') {
        $sql .= " AND grn.grn_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ss";
    } else {
        $sql .= " LIMIT 10";
    }
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No data available.'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'details' => $result->fetch_all(MYSQLI_ASSOC)
    ]);

    $stmt->close();
} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
