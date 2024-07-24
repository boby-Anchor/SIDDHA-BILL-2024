<?php
session_start();
require "../config/db.php";



if (isset($_GET['barcode']) && isset($_GET['stock_s_price'])) {
    $barcode = $_GET['barcode'];
    $stock_s_price = $_GET['stock_s_price'];

    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {
            $shop_id = $userData['shop_id'];

            $barcodeResult = $conn->query("SELECT * FROM stock2
            INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
            INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
            WHERE stock_shop_id = '$shop_id' AND p_medicine.code = '$barcode' AND stock2.item_s_price = '$stock_s_price' ");
            if ($barcodeResult->num_rows > 0) {

                $displayedProducts = array();
                $productsAllTotal = 0;

                while ($barcodeData = $barcodeResult->fetch_assoc()) {

                    // $totalPrice = number_format($barcodeData['stock_s_price'], 2);
                    $totalPrice = $barcodeData['item_s_price'];

                    $productsAllTotal += $barcodeData['stock_item_cost'];

                    if (in_array($barcodeData['id'], $displayedProducts)) {

                        continue;
                    }

                    $displayedProducts[] = $barcodeData['id'];

?>
                    <tr data-barcode="<?= $barcodeData['code'] ?><?= $barcodeData['item_s_price'] ?>">
                        <th scope="row">#</th>
                        <td id="code" class="d-none"><?= $barcodeData['code'] ?></td>
                        <td id="product_name"><?= $barcodeData['name'] ?></td>
                        <td id="product_price"><?= $barcodeData['item_s_price'] ?></td>
                        <td>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-2 d-flex justify-content-center">
                                        <button class="btn btn-secondary minusQty" onclick="decreaseQuantity(this)">-</button>
                                    </div>
                                    <div class="col-4">
                                        <input class="form-control text-center" id="qty" type="number" min="1" value="1" onchange="updateTotal(this)" data-price="<?= $barcodeData['item_s_price'] ?>">
                                    </div>
                                    <div class="col-2 d-flex justify-content-center">
                                        <button class="btn btn-primary plusQty" onclick="increaseQuantity(this)">+</button>
                                    </div>
                                    <div class="col-2">
                                        <label for="" id="unit"><?= $barcodeData['unit'] ?></label>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="total" id="totalprice"><?= $totalPrice ?></td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16" onclick="removeRow(this)" style="cursor: pointer;">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"></path>
                            </svg></td>
                    </tr>
<?php
                }
            }
        }
    }
}

?>