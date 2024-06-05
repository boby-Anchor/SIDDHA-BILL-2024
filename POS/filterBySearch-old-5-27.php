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
            $p_medicine_rs = $conn->query("SELECT * FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added' AND p_medicine.code LIKE '%$bnInput%'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
?>
                <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                <th scope="row"><?= $tableRowCount ?></th>
                <td>
                    <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                </td>
                <td id="product_name"><?= $p_medicine_data['name'] ?></td>
                <td id="product_cost">
                    <label for=""><?= $p_medicine_data['cost'] ?></label>
                </td>
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
            $p_medicine_rs = $conn->query("SELECT * FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added' AND p_medicine.code LIKE '%$pcInput%'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
            ?>
                <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                <th scope="row"><?= $tableRowCount ?></th>
                <td>
                    <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                </td>
                <td id="product_name"><?= $p_medicine_data['name'] ?></td>
                <td id="product_cost">
                    <label for=""><?= $p_medicine_data['cost'] ?></label>
                </td>
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
            $p_medicine_rs = $conn->query("SELECT * FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added' AND p_medicine.name LIKE  '%$pnInput%'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
            ?>
                <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                <th scope="row"><?= $tableRowCount ?></th>
                <td>
                    <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                </td>
                <td id="product_name"><?= $p_medicine_data['name'] ?></td>
                <td id="product_cost">
                    <label for=""><?= $p_medicine_data['cost'] ?></label>
                </td>
                <td><button class="btn btn-outline-success add-btn">Add</button></td>
                </tr>
            <?php
                $tableRowCount++;
            }
        }

        if (empty($searchBy)) {
            $searchBy = "all";
            $tableRowCount = 1;
            $p_medicine_rs = $conn->query("SELECT * FROM producttoshop
            INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
            INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
            WHERE shop_id = '$shop_id' AND productToShopStatus = 'added'");
            while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
            ?>
                <th id="product_code" class="d-none"><?= $p_medicine_data['code'] ?></th>
                <th scope="row"><?= $tableRowCount ?></th>
                <td>
                    <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['img'] ?>');"></div>
                </td>
                <td id="product_name"><?= $p_medicine_data['name'] ?></td>
                <td id="product_cost">
                    <label for=""><?= $p_medicine_data['category'] ?></label>
                </td>
                <td><button class="btn btn-outline-success add-btn">Add</button></td>
                </tr>
<?php
                $tableRowCount++;
            }
        }
    }
}
