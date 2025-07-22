<?php
session_start();
require "config/db.php";



if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];

    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {
            $shop_id = $userData['shop_id'];

            $barcodeResult = $conn->query("SELECT * FROM stock2
            INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
            WHERE stock_shop_id = '$shop_id'
            AND ( p_medicine.code = '$barcode'  OR stock2.stock_minimum_unit_barcode = '$barcode' )
            AND stock2.stock_item_qty > '0'");

            if ($barcodeResult->num_rows > 0) {

                $displayedProducts = array();
                $productsAllTotal = 0;
?>
                <option value="">select price</option>
                <?php
                while ($barcodeData = $barcodeResult->fetch_assoc()) {

                    // if ($barcode == $barcodeData['code']) {
                ?>
                    echo '<option value="<?= $barcodeData['stock_id']  . "-" . $barcodeData['item_s_price'] . "_" . "ip"  ?>"><?= $barcodeData['item_s_price'] ?></option>';

                    <?php
                    // } else {
                    ?>
                    echo '<option value="<?= $barcodeData['stock_id']  . "-" . $barcodeData['unit_s_price'] . "_" . "up" ?>"><?= $barcodeData['unit_s_price'] ?></option>';
<?php
                    // }
                }
            }
        }
    }
}

?>