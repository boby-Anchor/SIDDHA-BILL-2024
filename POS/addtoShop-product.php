<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pharmacy</title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>

    <!-- All CSS end -->

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Product Add To Shop</h3>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="p-2 col-4">
                                            <select id="selectProductCategory" class="form-control" onchange="searchByCategory(this.value);">
                                                <option value="0">All Categories</option>
                                                <?php
                                                $sql = $conn->query("SELECT * FROM `p_medicine_category` ");
                                                while ($row = mysqli_fetch_assoc($sql)) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-8 p-2 d-flex justify-content-end">
                                            <div class="col-6">
                                                <input type="text" class="form-control" placeholder="Item name" onkeyup="searchByName(this.value);">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control" placeholder="Barcode" onkeyup="searchByCode(this.value);">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="productTable">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="bg-info">
                                                    <th scope="col">
                                                        <div class="row">
                                                            <div class="col-1 d-flex align-items-center">
                                                                #
                                                            </div>
                                                            <div class="col-8">
                                                                <select name="records_per_page" id="records_per_page" class="record_per_page_sele form-control">
                                                                    <option value="0">Filter Row Count</option>
                                                                    <option value="30">30</option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">Brand</th>
                                                    <th scope="col">Selected Shop</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody">
                                                <?php
                                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                                                $records_per_page = isset($_SESSION['records_per_page']) ? $_SESSION['records_per_page'] : 20;
                                                $start_from = ($page - 1) * $records_per_page;

                                                //PRODUCT NAME, BRAND, SHOP     p_medicine.name AS pName,  selectedShops

                                                $record_per_page_sql = "SELECT p_medicine.*, p_brand.name AS bName , medicine_unit.unit AS unit , unit_category_variation.ucv_name
                                                FROM p_medicine
                                                JOIN p_brand ON p_brand.id = p_medicine.brand
                                                INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
                                                INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation ORDER BY p_medicine.name
                                                LIMIT $start_from, $records_per_page  ";

                                                $record_per_page_result = $conn->query($record_per_page_sql);
                                                if ($record_per_page_result->num_rows > 0) {
                                                    while ($record_per_page_row = $record_per_page_result->fetch_assoc()) {
                                                        $medicine_id = $record_per_page_row["id"];

                                                ?>
                                                        <tr>
                                                            <th scope="row"><?= $record_per_page_row["id"] ?></th>
                                                            <td><?= $record_per_page_row["name"] ?>
                                                                (<?= $record_per_page_row['ucv_name'] ?><?php echo $record_per_page_row['unit']; ?>)
                                                                <br>
                                                                <?= $record_per_page_row["code"] ?>
                                                            </td>
                                                            <td><?= $record_per_page_row["bName"] ?></td>
                                                            <td>
                                                                <div class="" id="mydiv<?= $record_per_page_row["id"] ?>">
                                                                    <div class="col-12">
                                                                        <div class="row">
                                                                            <div class="m-2">
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
                                                                                    <div class="d-flex justify-content-between m-2">
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
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center">
                                        <?php
                                        $pagination_sql = "SELECT COUNT(*) AS total FROM p_medicine";
                                        $pagination_result = $conn->query($pagination_sql);
                                        $pagination_row = $pagination_result->fetch_assoc();
                                        $total_records = $pagination_row['total'];
                                        $total_pages = ceil($total_records / $records_per_page);

                                        echo "<ul class='pagination'>";
                                        for ($i = 1; $i <= $total_pages; $i++) {
                                            $active_class = $page == $i ? 'active' : '';
                                            echo "<li class='page-item $active_class'><a class='page-link' href='?page=$i'>$i</a></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="saveDetails();">Save Changes</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->
    </div>

</body>

<!-- Alert -->
<?php include("part/alert.php"); ?>
<!-- Alert end -->

<!-- All JS -->
<?php include("part/all-js.php"); ?>
<!-- All JS end -->

<!-- Data Table JS -->
<?php include("part/data-table-js.php"); ?>
<!-- Data Table JS end -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="dist/js/addToShop-product.js"></script>

<script>
    function handleCheckboxStyle() {
        var checkboxes = document.querySelectorAll(".shop-checkbox");

        checkboxes.forEach(function(checkbox) {
            var label = checkbox.nextElementSibling;
            if (checkbox.checked) {
                label.classList.add("selected-shop");
                label.classList.remove("not-selected-shop");
            } else {
                label.classList.add("not-selected-shop");
                label.classList.remove("selected-shop");
            }
        });
    }
    var checkboxes = document.querySelectorAll(".shop-checkbox");
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener("change", handleCheckboxStyle);
    });
    handleCheckboxStyle();

    function attachEventListeners() {
        var checkboxes = document.querySelectorAll(".shop-checkbox");
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener("change", handleCheckboxStyle);
        });
        handleCheckboxStyle();
    }

    attachEventListeners();

    document.addEventListener("DOMContentLoaded", function() {

        var selectElement = document.getElementById('records_per_page');
        selectElement.addEventListener('change', function() {
            var selectedValue = selectElement.value;
            updateRecordsPerPage(selectedValue);
        });

        function updateRecordsPerPage(value) {
            var form = new FormData();
            form.append("records_per_page", value);

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = xhr.responseText;
                    document.getElementById("tableBody").innerHTML = response;
                    attachEventListeners();
                }
            };
            xhr.open("POST", "update_records_per_page.php", true);
            xhr.send(form);
        }
    });
</script>

</html>