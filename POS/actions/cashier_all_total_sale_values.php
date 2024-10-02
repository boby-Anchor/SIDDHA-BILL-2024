<?php
include('../config/db.php');
session_start();

$user_id;
$shop_id;

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];

    $user_id = $userLoginData['id'];
    $shop_id = $userLoginData['shop_id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['sortDates'])) {

        $sortDates = json_decode($_POST['sortDates'], true);

        $start_date = $sortDates['startDate'];
        $end_date = $sortDates['endDate'];

        $sellAmountResult = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount)
        AS total_amount FROM invoices
        WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' "));

        $cashPaymentResult = mysqli_fetch_assoc($conn->query("SELECT SUM(paidAmount) AS cash_amount
        FROM invoices
        WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' "));

        $cardPaymentResult = mysqli_fetch_assoc($conn->query("SELECT SUM(cardPaidAmount) AS cardPaidAmount
        FROM invoices
        WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' "));

        $cashoutResult = mysqli_fetch_assoc($conn->query("SELECT ROUND(SUM(balance), 2) AS cashout
        FROM invoices
        WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' "));

        $tableData = $conn->query("SELECT invoices.*, payment_type.payment_type, bill_type.bill_type_name ,users.name ,shop.shopName
        FROM invoices
        INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method
        INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id
        INNER JOIN users ON users.id = invoices.user_id
        INNER JOIN shop ON shop.shopId = invoices.shop_id
        WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date'
        ORDER BY invoices.created
        ");
        $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount
        FROM invoices
        WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date'
    "));

        if ($tableData) {
            $tableDataArray = array();
            while ($row = $tableData->fetch_assoc()) {
                $tableDataArray[] = $row;
            }
        }

        echo json_encode(
            array(
                'status' => 'success',
                'sellAmount' => number_format($sellAmountResult['total_amount'], 0),
                'cashpayment' => number_format($cashPaymentResult['cash_amount'], 0),
                'cardPayment' => number_format($cardPaymentResult['cardPaidAmount'], 0),
                'cashOut' => number_format($cashoutResult['cashout'], 0),
                'tableData' => $tableDataArray,
            )
        );
    } else {
        echo json_encode(
            array(
                'status' => 'error',
                'message' => 'No data received'
            )
        );
    }
}

