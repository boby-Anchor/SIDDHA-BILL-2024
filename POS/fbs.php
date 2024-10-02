<?php
session_start();
include('config/db.php');
$bnInput = $_POST["bnInput"];
$pcInput = $_POST["pcInput"];
$pnInput = $_POST["pnInput"];
$searchBy = "";

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];
    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];

        if (!empty($bnInput)) {
            $searchBy .= "barcode";
            $tableRowCount = 1;
            $p_medicine_rs = $conn->query("SELECT p_medicine.id AS pid, p_medicine.name AS p_name, 
            p_medicine.code AS code,
            p_medicine.img AS img,
            p_medicine_category.name AS category, p_brand.name AS brand,
            medicine_unit.unit AS unit, unit_category_variation.ucv_name,
            stock2.stock_id AS stock_id,
            stock2.item_s_price AS itemSprice,
            stock2.unit_s_price AS unitSprice
            FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
            LEFT JOIN `stock2` ON `stock2`.`stock_item_code` = `p_medicine`.`code`
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added' AND p_medicine.code LIKE '$bnInput%'
            GROUP BY itemSprice");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
?>
                <tr>
                    <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                    <th id="stock_id" class="d-none"><?= $p_medicine_data['stock_id'] ?></th>
                    <th id="ucv_name" class="d-none"><?= $p_medicine_data['ucv_name'] ?> </th>
                    <th id="unitSprice" class="d-none"><?= $p_medicine_data['unitSprice'] ?> </th>
                    <th scope="row"><?= $tableRowCount ?></th>
                    <!-- <td>
                        <div class="product-img" style="background-image: url('dist/img/product/<?php //$p_medicine_data['img'] 
                                                                                                ?>');"></div>
                    </td> -->
                    <td>
                        <label id="product_name"><?= $p_medicine_data['p_name'] ?></label>
                        (<?= $p_medicine_data['ucv_name'] ?>
                        <?= $p_medicine_data['unit'] ?>)
                    </td>
                    <td id="product_category"><?= $p_medicine_data['category'] ?></td>
                    <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
                    <td id="itemSprice"><?= $p_medicine_data['itemSprice'] ?></td>
                    <td id="product_unit"><?= $p_medicine_data['unit'] ?></td>
                    <td><button class="btn btn-outline-success add-btn">Add</button></td>
                </tr>
            <?php
                $tableRowCount++;
            }
        }

        if (!empty($pcInput)) {
            if (!empty($searchBy)) {
                $searchBy .= " & ";
            }
            $searchBy .= "product code";
            $tableRowCount = 1;
            $p_medicine_rs = $conn->query("SELECT p_medicine.id AS pid, p_medicine.name AS p_name, 
            p_medicine.code AS code,
            p_medicine.img AS img,
            p_medicine_category.name AS category, p_brand.name AS brand,
            medicine_unit.unit AS unit, unit_category_variation.ucv_name,
            stock2.stock_id AS stock_id,
            stock2.item_s_price AS itemSprice,
            stock2.unit_s_price AS unitSprice
            FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
            LEFT JOIN `stock2` ON `stock2`.`stock_item_code` = `p_medicine`.`code`
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added' AND p_medicine.name LIKE '%$pcInput%'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
            ?>
                <tr>
                    <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                    <th id="stock_id" class="d-none"><?= $p_medicine_data['stock_id'] ?></th>
                    <th id="ucv_name" class="d-none"><?= $p_medicine_data['ucv_name'] ?> </th>
                    <th id="unitSprice" class="d-none"><?= $p_medicine_data['unitSprice'] ?> </th>
                    <th scope="row"><?= $tableRowCount ?></th>
                    <!-- <td>
                        <div class="product-img" style="background-image: url('dist/img/product/<?php //$p_medicine_data['img'] 
                                                                                                ?>');"></div>
                    </td> -->
                    <td>
                        <label id="product_name"><?= $p_medicine_data['p_name'] ?></label>
                        (<?= $p_medicine_data['ucv_name'] ?>
                        <?= $p_medicine_data['unit'] ?>)
                    </td>
                    <td id="product_category"><?= $p_medicine_data['category'] ?></td>
                    <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
                    <td id="itemSprice"><?= $p_medicine_data['itemSprice'] ?></td>
                    <td id="product_unit"><?= $p_medicine_data['unit'] ?></td>
                    <td><button class="btn btn-outline-success add-btn">Add</button></td>
                </tr>
            <?php
                $tableRowCount++;
            }
        }

        if (!empty($pnInput)) {
            if (!empty($searchBy)) {
                $searchBy .= " & ";
            }
            $searchBy .= "product name";
            $tableRowCount = 1;
            $p_medicine_rs = $conn->query("SELECT p_medicine.id AS pid, p_medicine.name AS p_name, 
            p_medicine.code AS code,
            p_medicine.img AS img,
            p_medicine_category.name AS category, p_brand.name AS brand,
            medicine_unit.unit AS unit, unit_category_variation.ucv_name,
            stock2.stock_id AS stock_id,
            stock2.item_s_price AS itemSprice,
            stock2.unit_s_price AS unitSprice
            FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
            LEFT JOIN `stock2` ON `stock2`.`stock_item_code` = `p_medicine`.`code`
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added' AND p_medicine.name LIKE  '%$pnInput%'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
            ?>
                <tr>
                    <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                    <th id="stock_id" class="d-none"><?= $p_medicine_data['stock_id'] ?></th>
                    <th id="ucv_name" class="d-none"><?= $p_medicine_data['ucv_name'] ?> </th>
                    <th id="unitSprice" class="d-none"><?= $p_medicine_data['unitSprice'] ?> </th>
                    <th scope="row"><?= $tableRowCount ?></th>
                    <!-- <td>
                        <div class="product-img" style="background-image: url('dist/img/product/<?php //$p_medicine_data['img'] 
                                                                                                ?>');"></div>
                    </td> -->
                    <td>
                        <label id="product_name"><?= $p_medicine_data['p_name'] ?></label>
                        (<?= $p_medicine_data['ucv_name'] ?>
                        <?= $p_medicine_data['unit'] ?>)
                    </td>
                    <td id="product_category"><?= $p_medicine_data['category'] ?></td>
                    <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
                    <td id="itemSprice"><?= $p_medicine_data['itemSprice'] ?></td>
                    <td id="product_unit"><?= $p_medicine_data['unit'] ?></td>
                    <td><button class="btn btn-outline-success add-btn">Add</button></td>
                </tr>
            <?php
                $tableRowCount++;
            }
        }

        if (empty($searchBy)) {
            $searchBy = "all";
            $tableRowCount = 1;
            $p_medicine_rs = $conn->query("SELECT p_medicine.id AS pid, p_medicine.name AS p_name, 
            p_medicine.code AS code,
            p_medicine.img AS img,
            p_medicine_category.name AS category, p_brand.name AS brand,
            medicine_unit.unit AS unit, unit_category_variation.ucv_name,
            stock2.stock_id AS stock_id,
            stock2.item_s_price AS itemSprice,
            stock2.unit_s_price AS unitSprice
            FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN p_brand ON p_brand.id = p_medicine.brand
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
            INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
            LEFT JOIN `stock2` ON `stock2`.`stock_item_code` = `p_medicine`.`code`
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
            ?>
                <tr>
                    <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                    <th id="stock_id" class="d-none"><?= $p_medicine_data['stock_id'] ?></th>
                    <th id="ucv_name" class="d-none"><?= $p_medicine_data['ucv_name'] ?> </th>
                    <th id="unitSprice" class="d-none"><?= $p_medicine_data['unitSprice'] ?> </th>
                    <th scope="row"><?= $tableRowCount ?></th>
                    <!-- <td>
                        <div class="product-img" style="background-image: url('dist/img/product/<?php //$p_medicine_data['img'] 
                                                                                                ?>');"></div>
                    </td> -->
                    <td>
                        <label id="product_name"><?= $p_medicine_data['p_name'] ?></label>
                        (<?= $p_medicine_data['ucv_name'] ?>
                        <?= $p_medicine_data['unit'] ?>)
                    </td>
                    <td id="product_category"><?= $p_medicine_data['category'] ?></td>
                    <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
                    <td id="itemSprice"><?= $p_medicine_data['itemSprice'] ?></td>
                    <td id="product_unit"><?= $p_medicine_data['unit'] ?></td>
                    <td><button class="btn btn-outline-success add-btn">Add</button></td>
                </tr>
<?php
                $tableRowCount++;
            }
        }
    }
}
