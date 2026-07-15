<?php
include('../../config/db.php');
session_start();

$shop_id;
$user_id;

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $shop_id = $userLoginData['shop_id'];
    $user_id = $userLoginData['id'];
} else {
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
        "UPDATE stock4
         SET transferred_qty = transferred_qty + ?
         WHERE stock_shop_id = ?
         AND stock_item_code = ?
         AND item_s_price = ?"
    );
    $stmtStock->bind_param("disd", $transferringQty, $shop_id, $barcode, $price);
    $stmtStock->execute();
    if ($stmtStock->affected_rows === 0) {
        throw new Exception("Stock update failed");
    }
    $stmtStock->close();

    $stmtStockTransfer = $conn->prepare(
        "INSERT INTO stock3_transfers (barcode, qty, price, shop_id, user_id)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmtStockTransfer->bind_param("sddii", $barcode, $transferringQty, $price, $shop_id, $user_id);
    $stmtStockTransfer->execute();
    $stmtStockTransfer->close();

    $stmtStock2 = $conn->prepare(
        "UPDATE stock2
         SET stock_item_qty = stock_item_qty + ?,
         stock_mu_qty = ?
         WHERE stock_shop_id = ?
         AND stock_item_code = ?
         AND item_s_price = ?"
    );
    if (!$stmtStock2) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmtStock2->bind_param("ddisd", $transferringQty, $minimum_qty, $shop_id, $barcode, $price);
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
