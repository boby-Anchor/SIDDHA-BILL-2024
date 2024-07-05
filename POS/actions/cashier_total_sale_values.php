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
                        <p class="totalAmount">'.$sellAmountResult['total_amount'].' LKR</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-body bg-info">
                        <h2 class="text-white text-uppercase">Cash Payments</h2>
                        <p class="totalAmount">'.$cashPaymentResult['cash_amount'].'LKR</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body bg-primary">
                        <h2 class="text-white text-uppercase">Card Payments</h2>
                       <p class="totalAmount">' .$cardPaymentResult['cardPaidAmount'].'LKR</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body bg-danger">
                        <h2 class="text-white text-uppercase">Cash Out</h2>
                        <p class="totalAmount">-' .$cashoutResult['cashout'].'LKR</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>';

    echo $output;
}
?>