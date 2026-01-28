<?php
include('../../config/db.php');
session_start();

if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired. Wait to Login again.',
    ]);
    exit;
}

$stock_id = isset($_POST['stockId']) ? (int) $_POST['stockId'] : 0;
$barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : '';
$transferringQty  = isset($_POST['transferringQty']) ? (float) $_POST['transferringQty'] : 0;
$minimum_qty  = isset($_POST['minimum_qty']) ? (float) $_POST['minimum_qty'] : 0;
$price  = isset($_POST['price']) ? (float) $_POST['price'] : 0;
$total_qty = isset($_POST['total_qty']) ? (float) $_POST['total_qty'] : 0;

if (
    $stock_id <= 0 ||
    $barcode === '' ||
    $transferringQty <= 0 ||
    $price <= 0 ||
    $total_qty <= 0
) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid data received.'
    ]);
    exit;
}

try {
    $conn->begin_transaction();

    $stmtStock = $conn->prepare(
        "UPDATE stock3
         SET transferred_qty = transferred_qty + ?
         WHERE stock_id = ?"
    );
    $stmtStock->bind_param("di", $transferringQty, $stock_id);
    $stmtStock->execute();
    if ($stmtStock->affected_rows === 0) {
        throw new Exception("Stock update failed");
    }
    $stmtStock->close();

    $stmtStockTransfer = $conn->prepare(
        "INSERT INTO stock3_transfers (barcode, qty, price)
         VALUES (?, ?, ?)"
    );
    $stmtStockTransfer->bind_param("sdd", $barcode, $transferringQty, $price);
    $stmtStockTransfer->execute();
    $stmtStockTransfer->close();

    $stmtStock2 = $conn->prepare(
        "UPDATE stock2
         SET stock_item_qty = stock_item_qty + ?,
         stock_mu_qty = ?
         WHERE stock_id = ?"
    );
    if (!$stmtStock2) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmtStock2->bind_param("ddi", $transferringQty, $minimum_qty, $stock_id);
    $stmtStock2->execute();
    if ($stmtStock2->affected_rows === 0) {
        throw new Exception("Stock update failed");
    }
    $stmtStock2->close();

    $conn->commit();

    echo json_encode([
        'status'  => 'success',
        'message' => 'All stock operations completed successfully.'
    ]);
    exit;
} catch (Exception $e) {
    $conn->rollback();

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
