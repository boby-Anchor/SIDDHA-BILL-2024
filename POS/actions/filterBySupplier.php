<?php
include('../config/db.php');
session_start();
if (isset($_GET["sup_id"])) {
    $sup_id = $_GET["sup_id"];
    if ($sup_id != "0") {
        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            foreach ($userLoginData as $userData) {
                $shop_id = $userData['shop_id'];

                $p_medicine_rs = $conn->query("SELECT p_medicine.id AS product_id , p_medicine.name AS product_name ,
                 p_medicine.code AS product_code , p_medicine.img AS product_img , stock2.item_s_price AS item_price , stock2.stock_item_cost AS product_cost   FROM p_medicine 
                INNER JOIN p_supplier ON p_supplier.brand_id = p_medicine.brand 
                INNER JOIN producttoshop ON producttoshop.medicinId = p_medicine.id
                INNER JOIN stock2 ON p_medicine.code = stock2.stock_item_code
                WHERE p_supplier.id = '$sup_id' AND producttoshop.shop_id='$shop_id' AND producttoshop.productToShopStatus = 'added'");

                $tableRowCount = 1;
                while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
?>
                    <tr>
                        <th id="product_code" class="d-none"><?= $p_medicine_data['product_code'] ?></th>
                        <th scope="row"><?= $tableRowCount ?></th>
                        <td>
                            <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['product_img'] ?>');"></div>
                        </td>
                        <td id="product_name"><?= $p_medicine_data['product_name'] ?></td>
                        <td id="product_cost"><?= $p_medicine_data['product_cost'] ?></td>
                        <td id="product_s_price"><?= $p_medicine_data['item_price'] ?></td>
                        <td><button class="btn btn-outline-success add-btn">Add</button></td>
                    </tr>
        <?php
                    $tableRowCount++;
                }
            }
        }

        ?>
    <?php
    } else {
    ?>
        <?php
        if (isset($_SESSION['store_id'])) {

            $userLoginData = $_SESSION['store_id'];

            foreach ($userLoginData as $userData) {
                $shop_id = $userData['shop_id'];

                $p_medicine_rs = $conn->query("SELECT * FROM producttoshop
                INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
                INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
                WHERE shop_id = '$shop_id' AND productToShopStatus = 'added'");
                $tableRowCount = 1;
                while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
        ?>
                    <tr>
                        <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                        <th scope="row"><?= $tableRowCount ?></th>
                        <td>
                            <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                        </td>
                        <td id="product_name"><?= $p_medicine_data['name'] ?></td>
                        <td id="product_cost"><?= $p_medicine_data['cost'] ?></td>
                        <td><button class="btn btn-outline-success add-btn">Add</button></td>
                    </tr>
        <?php
                    $tableRowCount++;
                }
            }
        }

        ?>
<?php
    }
}
