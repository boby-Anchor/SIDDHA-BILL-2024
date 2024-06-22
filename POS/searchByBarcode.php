<?php
include('config/db.php');
if (isset($_POST["productCode"])) {
    $productCode = $_POST["productCode"];
?>
    <!-- <div id="productTable"> -->
    <table class="table table-bordered">
        <thead>
            <tr class="bg-info">
                <th scope="col">#</th>
                <th scope="col">Product Name</th>
                <th scope="col">Brand</th>
                <th scope="col">Selected Shop</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //  $sql = $conn->query("SELECT * FROM p_medicine WHERE `name` LIKE '%$productName%'");
            $sql = $conn->query("SELECT p_medicine.*, p_brand.name AS bName FROM p_medicine
            JOIN p_brand ON p_brand.id = p_medicine.brand
            WHERE p_medicine.code LIKE '%$productCode%'");

            while ($row = mysqli_fetch_assoc($sql)) {
                $medicine_id = $row["id"];
            ?>
                <tr>
                    <th scope="row"><?= $row["id"] ?></th>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["bName"] ?></td>
                    <td>
                        <div class="" id="mydiv<?= $row["id"] ?>">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-2 d-flex justify-content-center">
                                        <?php
                                        if ($row["selectedShops"] == "all") {
                                        ?>
                                            <input type="checkbox" id="allShop<?= $row['id'] ?>" class="shop-checkbox" checked>
                                        <?php
                                        } else {
                                        ?>
                                            <input type="checkbox" id="allShop<?= $row['id'] ?>" class="shop-checkbox">
                                        <?php
                                        }
                                        ?>
                                        <label for="allShop<?= $row['id'] ?>" class="shop-label" onclick="shopSelected('<?= $row['id'] ?>','');">All</label>
                                    </div>
                                    <?php
                                    $shop_sql = $conn->query("SELECT * FROM `shop` WHERE `shopStatus` = '1'");
                                    while ($shop_row = mysqli_fetch_assoc($shop_sql)) {
                                        $selected_shop = $conn->query("SELECT * FROM `producttoshop` WHERE medicinId = '" . $row["id"] . "' AND shop_id = '" . $shop_row['shopId'] . "'");
                                        while ($selected_shop_row = mysqli_fetch_assoc($selected_shop)) {
                                    ?>
                                            <div class="col-2 d-flex justify-content-center">
                                                <?php
                                                if ($selected_shop_row["productToShopStatus"] == "added") {
                                                ?>
                                                    <input type="checkbox" id="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-checkbox" checked>
                                                <?php
                                                } else if ($selected_shop_row["productToShopStatus"] == "remove") {
                                                ?>
                                                    <input type="checkbox" id="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-checkbox">
                                                <?php
                                                } else {
                                                ?>
                                                    <input type="checkbox" id="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-checkbox" checked>
                                                <?php
                                                }
                                                ?>
                                                <label for="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-label" onclick="oneByoneSelect('<?= $shop_row['shopId'] ?><?= $medicine_id ?>','<?= $medicine_id ?>','<?= $shop_row['shopId'] ?>');" id="lable<?= $shop_row['shopId'] ?><?= $medicine_id ?>"><?= $shop_row["shopName"] ?></label>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>



                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <!-- </div> -->

<?php
}
?>