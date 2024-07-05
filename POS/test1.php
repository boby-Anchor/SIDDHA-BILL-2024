<?php
include('../config/db.php');
session_start();

?>
<style>
    .labInvo {
        font-weight: bold;
        color: #3E8F0C;
    }
</style>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($_POST['sd'], true);
    $start_date = $data['STDATE'];
    $end_date = $data['ENDDATE'];
    // Ensure user_id is set in the session  
    $user_id;
    $shop_id;
    $output = '';
    $currentDate = date('Y-m-d');

    if (isset($_SESSION['store_id'])) {
        $userLoginData = $_SESSION['store_id'];
        foreach ($userLoginData as $userData) {
            $user_id = $userData['id'];
            $shop_id = $userData['shop_id'];
        }
    }
    $sellAmountResult = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount)
        AS total_amount FROM invoices
        WHERE DATE(`created`) = '.$currentDate.' AND user_id = '.$user_id.'"));

    $cashPaymentResult = mysqli_fetch_assoc($conn->query("SELECT SUM(paidAmount) AS cash_amount
        FROM invoices
        WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'"));

    $cardPaymentResult = mysqli_fetch_assoc($conn->query("SELECT SUM(cardPaidAmount) AS cardPaidAmount
        FROM invoices
        WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'"));

    $cashoutResult = mysqli_fetch_assoc($conn->query("SELECT ROUND(SUM(balance), 2) AS cashout
        FROM invoices
        WHERE DATE(`created`) = '$currentDate' AND user_id = '$user_id'"));


    $output .= '<div class="col-12">
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-body bg-success">
                    <h2 class="text-white text-uppercase">Sell Amount</h2>
                    <p class="totalAmount">';
    $result['total_amount'];
    'LKR</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body bg-info">
                    <h2 class="text-white text-uppercase">Cash Payments</h2>
                    <p class="totalAmount">';
    $result['cash_amount'];
    'LKR</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body bg-primary">
                    <h2 class="text-white text-uppercase">Card Payments</h2>
                    <p class="totalAmount">';
    $result['cardPaidAmount'];
    'LKR</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body bg-danger">
                    <h2 class="text-white text-uppercase">Cash Out</h2>
                    <p class="totalAmount">-';
    $result['cashout'];
    'LKR</p>
                </div>
            </div>


        </div>
    </div>
</div>
</div>

<div class="col-12">
<div class="card">

    <div class="card-body">
        <button class="no-print btn btn-primary" onclick="window.print()">Print Table</button>

        <table id="mytable" class="table table-bordered table-hover">
            <thead>
                <tr class="bg-info">
                    <th>Invoice Number</th>
                    <th>Patient Name</th>
                    <th>Tell</th>
                    <th>Doctor Name</th>
                    <th>REG Number</th>
                    <th>Total Amount</th>
                    <th>Payment Type</th>
                    <th>Bill Type</th>
                    <th>Chachier</th>
                    <th>SHOP</th>
                </tr>
            </thead>
            <tbody id="cash-sale">';

    $currentDate = date("Y-m-d");
    $sql = $conn->query("SELECT invoices.*, payment_type.payment_type, bill_type.bill_type_name ,users.name ,shop.shopName
                FROM invoices 
                INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method 
                INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id 
                INNER JOIN users ON users.id = invoices.user_id 
                INNER JOIN shop ON shop.shopId = invoices.shop_id 
                WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' 
                AND invoices.shop_id = '$shop_id' 
                ORDER BY invoices.created
                ");

    $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount 
                       FROM invoices 
                       WHERE DATE(`created`) = '$currentDate' 
                       AND user_id = '$user_id'"));
    while ($row = mysqli_fetch_assoc($sql)) {

        $output .= '
                    <tr>
                        <td><label class="labInvo">' . $row['invoice_id'] . '</label> <br>' . $row['created'] . '</td>
                        <td>' . $row['p_name'] . '</td>
                        <td>' . $row['contact_no'] . '</td>
                        <td>' . $row['d_name'] . '</td>
                        <td>' . $row['reg'] . '</td>
                        <td>' . $row['total_amount'] . '</td>
                        <td>' . $row['payment_type'] . '</td>
                        <td>' . $row['bill_type_name'] . '</td>
                        <td>' . $row['name'] . '</td>
                        <td>' . $row['shopName'] . '</td>
                    </tr>';
    }
    $output .= '
                <tr class="bg-dark">
                    <td></td>
                    <td class="fw-bold" style="font-size:larger;">Total Sales</td>
                    <td class="fw-bold" style="font-size:larger;">' . $result['total_amount'] . 'LKR</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>';

    echo $output;
}
?>