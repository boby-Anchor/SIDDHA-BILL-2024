<?php
session_start();
include('config/db.php');

$poArray = $_POST['products'];
$productsAllTotal = $_POST['total'] ?? 0;

if (is_array($poArray) && !empty($poArray)) {
    foreach ($poArray as $product) {

        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            foreach ($userLoginData as $userData) {
                $userId = $userData['id'];
                $shop_id = $userData['shop_id'];

                $code = isset($product['code']) ? $product['code'] : '';
                $product_name = isset($product['product_name']) ? $product['product_name'] : '';
                $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
                $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
                $product_unit = isset($product['product_unit']) ? $product['product_unit'] : '';

                $productTotal = floatval($product_cost) * intval($product_qty);

                $productsAllTotal += $productTotal;

                $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = 'ceylriea_ts' AND table_name = 'invoices'");
                $invoiceId_row = $invoiceId_rs->fetch_assoc();
                $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
                $invoiceNumber = "000" . $userId . $shop_id .  $invoiceId;
            }
        } else {
            echo "Session Expired !";
        }
    }

?>
    
    <?= floatval($productsAllTotal); ?>
<?php
} else {
    echo "No products found or invalid data received.";
}
