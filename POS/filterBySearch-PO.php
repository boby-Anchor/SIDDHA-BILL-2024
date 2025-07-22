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

      $p_medicine_rs = $conn->query("SELECT 
      stock2.stock_item_code AS barcode,
      p_brand.name AS brand,
      p_medicine.name AS medicineName,
      p_medicine_category.name AS category,
      unit_category_variation.ucv_name AS volume,
      medicine_unit.unit AS unit,
      stock2.item_s_price AS item_s_price
      FROM p_medicine
      INNER JOIN stock2 ON p_medicine.code = stock2.stock_item_code
      INNER JOIN p_medicine_category ON p_medicine.category = p_medicine_category.id
      INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
      INNER JOIN p_brand ON p_medicine.brand = p_brand.id
      INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
      WHERE stock2.stock_shop_id = '$shop_id'
      AND p_medicine.code LIKE '%$bnInput%'
      ORDER BY p_medicine.name ASC, volume ASC");

      while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
?>
        <tr>
          <th id="product_code" class="d-none"><?= $p_medicine_data['barcode'] ?></th>
          <th id="item_s_price" class="d-none"><?= $p_medicine_data['item_s_price'] ?></th>

          <th scope="row"><?= $tableRowCount ?></th>

          <td id="product_name"><?= $p_medicine_data['medicineName'] ?> </td>
          <td id="product_category"><?= $p_medicine_data['category'] ?> </td>
          <td id="product_unit">
            <label><?= $p_medicine_data['volume'] ?><?= $p_medicine_data['unit'] ?></label>
          </td>
          <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
          <td>
            <?= number_format($p_medicine_data['item_s_price'], 0) ?>
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
      $p_medicine_rs = $conn->query("SELECT 
      stock2.stock_item_code AS barcode,
      p_brand.name AS brand,
      p_medicine.name AS medicineName,
      p_medicine_category.name AS category,
      unit_category_variation.ucv_name AS volume,
      medicine_unit.unit AS unit,
      stock2.item_s_price AS item_s_price
      FROM p_medicine
      INNER JOIN stock2 ON p_medicine.code = stock2.stock_item_code
      INNER JOIN p_medicine_category ON p_medicine.category = p_medicine_category.id
      INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
      INNER JOIN p_brand ON p_medicine.brand = p_brand.id
      INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
      WHERE stock2.stock_shop_id = '$shop_id'
      AND p_medicine.name LIKE  '%$pnInput%'
      ORDER BY p_medicine.name ASC, volume ASC");

      while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
      ?>
        <tr>
          <th id="product_code" class="d-none"><?= $p_medicine_data['barcode'] ?></th>
          <th id="item_s_price" class="d-none"><?= $p_medicine_data['item_s_price'] ?></th>

          <th scope="row"><?= $tableRowCount ?></th>

          <td id="product_name"><?= $p_medicine_data['medicineName'] ?> </td>
          <td id="product_category"><?= $p_medicine_data['category'] ?> </td>
          <td id="product_unit">
            <label><?= $p_medicine_data['volume'] ?><?= $p_medicine_data['unit'] ?></label>
          </td>
          <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
          <td>
            <?= number_format($p_medicine_data['item_s_price'], 0) ?>
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
      $p_medicine_rs = $conn->query("SELECT 
      stock2.stock_item_code AS barcode,
      p_brand.name AS brand,
      p_medicine.name AS medicineName,
      p_medicine_category.name AS category,
      unit_category_variation.ucv_name AS volume,
      medicine_unit.unit AS unit,
      stock2.item_s_price AS item_s_price
      FROM p_medicine
      INNER JOIN stock2 ON p_medicine.code = stock2.stock_item_code
      INNER JOIN p_medicine_category ON p_medicine.category = p_medicine_category.id
      INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
      INNER JOIN p_brand ON p_medicine.brand = p_brand.id
      INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
      WHERE stock2.stock_shop_id = '$shop_id'
      AND p_medicine.name LIKE  '%$pnInput%'
      ORDER BY p_medicine.name ASC, volume ASC");

      while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
      ?>
        <tr>
          <th id="product_code" class="d-none"><?= $p_medicine_data['barcode'] ?></th>
          <th id="item_s_price" class="d-none"><?= $p_medicine_data['item_s_price'] ?></th>

          <th scope="row"><?= $tableRowCount ?></th>

          <td id="product_name"><?= $p_medicine_data['medicineName'] ?> </td>
          <td id="product_category"><?= $p_medicine_data['category'] ?> </td>
          <td id="product_unit">
            <label><?= $p_medicine_data['volume'] ?><?= $p_medicine_data['unit'] ?></label>
          </td>
          <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>
          <td>
            <?= number_format($p_medicine_data['item_s_price'], 0) ?>
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
//       $p_medicine_rs = $conn->query("SELECT producttoshop.*, p_brand.name AS brand,
//                               p_medicine.name AS medName, medicine_unit.unit AS unit, stock2.stock_item_cost AS cost,
//                               stock2.item_s_price AS item_s_price
//                               FROM producttoshop
//                               INNER JOIN p_medicine ON p_medicine.id = producttoshop.medicinId
//                               INNER JOIN medicine_unit ON p_medicine.medicine_unit_id = medicine_unit.id
//                               INNER JOIN stock2 ON p_medicine.code = stock2.stock_item_code
//                               INNER JOIN p_brand ON p_medicine.brand = p_brand.id
//                               WHERE producttoshop.shop_id = '$shop_id' AND productToShopStatus = 'added' AND  ORDER BY stock2.stock_id ASC");

//       while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
//       ?>
<!-- //         <tr>
//           <th id="product_code" class="d-none"><?= $p_medicine_data['medicinId'] ?></th>

//           <th scope="row"><?= $tableRowCount ?></th>

//           <td id="product_name"><?= $p_medicine_data['medName'] ?></td>
//           <td id="product_brand"><?= $p_medicine_data['brand'] ?></td>

//           <td id="product_unit">
//             <label for=""><?= $p_medicine_data['unit'] ?></label>
//           </td>
//           <td id="product_cost">
//             <label for=""><?= $p_medicine_data['cost'] ?></label>
//           </td>
//           <td id="product_sprice">
//             <label for=""><?= $p_medicine_data['item_s_price'] ?></label>
//           </td>

//           <td><button class="btn btn-outline-success add-btn">Add</button></td>
//         </tr> -->
// <?php
//         $tableRowCount++;
//       }
    }
  }
}
