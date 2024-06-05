<?php
include('../config/db.php');
if (isset($_GET["sup_id"])) {
    $sup_id = $_GET["sup_id"];
    if ($sup_id != "0") {


?>
        <?php
        $p_medicine_rs = $conn->query("SELECT p_medicine.id AS product_id , p_medicine.name AS product_name , p_medicine.cost AS product_cost , p_medicine.code AS product_code , p_medicine.img AS product_img FROM p_medicine INNER JOIN p_supplier ON p_supplier.brand_id = p_medicine.brand WHERE p_supplier.id = '$sup_id'");
        $tableRowCount = 1;
        while ($p_medicine_data = $p_medicine_rs->fetch_assoc()) {
        ?>
            <tr class="ptr">
                <th id="product_code" class="d-none"><?= $p_medicine_data['product_code'] ?></th>
                <th scope="row"><?= $tableRowCount ?></th>
                <td>
                    <div class="product-img" style="background-image: url('dist/img/product/<?= $p_medicine_data['product_img'] ?>');"></div>
                </td>
                <td id="product_name"><?= $p_medicine_data['product_name'] ?></td>
                <td id="product_cost"><?= $p_medicine_data['product_cost'] ?></td>
                <td><button class="btn btn-outline-success add-btn">Add</button></td>
            </tr>
        <?php
            $tableRowCount++;
        }
        ?>
<?php
    } else {
        echo "select-supplier";
    }
}
