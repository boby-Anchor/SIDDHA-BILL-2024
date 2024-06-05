<?php
session_start();
include('config/db.php');

$rb = json_decode(file_get_contents('php://input'), true);

$sd = $rb['v1'];

if (isset($_SESSION['store_id'])) {

    $userLoginData = $_SESSION['store_id'];

    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];

        $p_medicine_rs = $conn->query("SELECT stock2.stock_id, p_brand.name AS bName, p_medicine.img AS p_img , p_medicine.name AS p_name , stock2.stock_item_cost AS p_cost , stock2.stock_item_code AS p_code , stock2.stock_item_qty AS p_a_stock , stock2.item_s_price AS p_s_price , p_medicine_category.name AS p_category
                                              FROM stock2 
                                              INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
                                              INNER JOIN p_brand ON p_brand.id = p_medicine.brand
                                              INNER JOIN p_medicine_category ON p_medicine_category.id = p_medicine.category
                                              WHERE stock2.stock_shop_id = '$shop_id' AND (p_medicine.name LIKE '%$sd%' OR p_medicine.code LIKE '%$sd%') ORDER BY `stock2`.`stock_id` ASC");

        $tableRowCount = 1;
        while ($row = $p_medicine_rs->fetch_assoc()) {
?>
            <tr>
                <td style="padding:5px" class="text-center">
                    <img src="dist/img/product/<?php echo $row['p_img']; ?>" width="50" alt="Image">
                    <?php echo $row['stock_id']; ?>
                </td>
                <td> <?php echo $row['p_name']; ?></td>
                <td> <?php echo $row['bName']; ?></td>
                <td> <?php echo $row['p_category']; ?></td>
                <td> <?php echo $row['p_cost']; ?>.00</td>
                <td class="text-center"> <label for="" class="product-selling-price"><?php echo $row['p_s_price']; ?></label> </td>
                <td> <?php echo $row['p_a_stock']; ?> </td>
            </tr>
<?php
            $tableRowCount++;
        }
    }
}
?>
