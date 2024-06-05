<?php
include('config/db.php');
session_start();
$poArray = $_POST['products'];
$productsAllTotal = 0;

if (is_array($poArray) && !empty($poArray)) {
    foreach ($poArray as $product) {
        // echo $product;
        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            foreach ($userLoginData as $userData) {
                $userId = $userData['id'];
                $shop_id = $userData['shop_id'];

                // $product_code = isset($product['product_code']) ? $product['product_code'] : '';
                $product_name = isset($product['product_name']) ? $product['product_name'] : '';
                $product_cost = isset($product['product_cost']) ? $product['product_cost'] : '';
                $product_qty = isset($product['product_qty']) ? $product['product_qty'] : '';
                $product_unit = isset($product['product_unit']) ? $product['product_unit'] : '';

                $productTotal = intval($product_cost) * intval($product_qty);

                $productsAllTotal += $productTotal;

                $invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = 'siddhahub' AND table_name = 'invoices'");
                $invoiceId_row = $invoiceId_rs->fetch_assoc();
                $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
                $invoiceNumber = "000" . $userId . $shop_id .  $invoiceId;

?>
                <tr class="service">
                    <td class="tableitem">
                        <p class="product_name"><?= $product_name ?></p>
                    </td>
                    <td class="tableitem">
                        <p class="product_cost"><?= $product_cost ?></p>
                    </td>
                    <td class="tableitem">
                        <p class="product_qty"><?= $product_qty ?></p>
                    </td>
                    <td class="tableitem">
                        <p class="product_unit"><?= $product_unit ?></p>
                    </td>
                    <td class="tableitem">
                        <p class="productTotal"><?= $productTotal ?></p>
                    </td>
                </tr>


    <?php
            }
        } else {
            echo "Session Expired !";
        }
    }

    ?>
    <tr class="tabletitle">
        <td></td>
        <td></td>
        <td></td>
        <td class="Rate">
            <h2>Sub Total</h2>
        </td>
        <td class="payment">
            <h2 class="productsAllTotal"><?= $productsAllTotal ?></h2>
        </td>
    </tr>

<?php
} else {
    echo "No products found or invalid data received.";
}
