<?php
  include('config/db.php');
$records_per_page = isset($_POST['records_per_page']) ? $_POST['records_per_page'] : 1;

session_start();
$_SESSION['records_per_page'] = $records_per_page;
?>
<?php
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = isset($_SESSION['records_per_page']) ? $_SESSION['records_per_page'] : 1;
$start_from = ($page - 1) * $records_per_page;
$record_per_page_sql = "SELECT * FROM p_medicine LIMIT $start_from, $records_per_page";
$record_per_page_result = $conn->query($record_per_page_sql);
if ($record_per_page_result->num_rows > 0) {
    while ($record_per_page_row = $record_per_page_result->fetch_assoc()) {
        $medicine_id = $record_per_page_row["id"];

?>
        <tr>
            <th scope="row"><?= $record_per_page_row["id"] ?></th>
            <td><?= $record_per_page_row["name"] ?></td>
            <td>
                <div class="" id="mydiv<?= $record_per_page_row["id"] ?>">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-2 d-flex justify-content-center">
                                <?php if ($record_per_page_row["selectedShops"] == "all") { ?>
                                    <input type="checkbox" id="allShop<?= $record_per_page_row['id'] ?>" class="shop-checkbox" checked>
                                <?php } else { ?>
                                    <input type="checkbox" id="allShop<?= $record_per_page_row['id'] ?>" class="shop-checkbox">
                                <?php } ?>
                                <label for="allShop<?= $record_per_page_row['id'] ?>" class="shop-label" onclick="shopSelected('<?= $record_per_page_row['id'] ?>','');">All</label>
                            </div>
                            <?php
                            $shop_sql = $conn->query("SELECT * FROM `shop` WHERE `shopStatus` = '1'");
                            while ($shop_row = mysqli_fetch_assoc($shop_sql)) {
                                $selected_shop = $conn->query("SELECT * FROM `producttoshop` WHERE medicinId = '" . $record_per_page_row["id"] . "' AND shop_id = '" . $shop_row['shopId'] . "'");
                                while ($selected_shop_row = mysqli_fetch_assoc($selected_shop)) {
                            ?>
                                    <div class="col-2 d-flex justify-content-center">
                                        <?php if ($selected_shop_row["productToShopStatus"] == "added") { ?>
                                            <input type="checkbox" id="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-checkbox" checked>
                                        <?php } else if ($selected_shop_row["productToShopStatus"] == "remove") { ?>
                                            <input type="checkbox" id="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-checkbox">
                                        <?php } else { ?>
                                            <input type="checkbox" id="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-checkbox" checked>
                                        <?php } ?>
                                        <label for="shop<?= $shop_row['shopId'] ?><?= $medicine_id ?>" class="shop-label" onclick="oneByoneSelect('<?= $shop_row['shopId'] ?><?= $medicine_id ?>','<?= $medicine_id ?>','<?= $shop_row['shopId'] ?>')" id="lable<?= $shop_row['shopId'] ?><?= $medicine_id ?>"><?= $shop_row["shopName"] ?></label>
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
} else {
    echo "<tr><td colspan='3'>No records found.</td></tr>";
}
?>
<?php

?>