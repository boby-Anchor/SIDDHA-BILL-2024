<?php
session_start();
include('config/db.php');

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $userId = $userData['id'];
        $shop_id = $userData['shop_id'];

        $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'invoices'");
        $invoiceId_row = $invoiceId_rs->fetch_assoc();
        $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
        $invoiceNumber = "000" . $userId . $shop_id .  $invoiceId;

        $_SESSION["invoiceNumber"] = $invoiceNumber;
    }
} else {
    echo "Session Expired !";
}
?>    
    <?= trim($invoiceNumber); ?>
