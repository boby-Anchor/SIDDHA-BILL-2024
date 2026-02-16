<?php
session_start();
include('../config/db.php');

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'];
    // AND stock2.stock_item_qty > 0 ORDER BY p_medicine.name ASC
    foreach ($userLoginData as $userData) {
        $shop_id = $userData['shop_id'];
        $searchName = !empty($_POST['searchName']) ? $_POST['searchName'] : null;

        $query = "SELECT stock2.*, p_brand.name AS bName, p_medicine.code AS code, p_medicine.name AS name,
        medicine_unit.unit AS unit2 , unit_category_variation.ucv_name AS ucv_name2 
        FROM stock2
        INNER JOIN p_medicine ON p_medicine.code = stock2.stock_item_code
        INNER JOIN p_brand ON p_brand.id = p_medicine.brand
        INNER JOIN medicine_unit ON medicine_unit.id = p_medicine.medicine_unit_id
        INNER JOIN unit_category_variation ON unit_category_variation.ucv_id = p_medicine.unit_variation
        WHERE stock2.stock_shop_id = '$shop_id'
        AND stock2.stock_item_qty >= 0
        AND p_medicine.name LIKE '%$searchName%'
        ORDER BY bName ASC
        ";

        $cm = runQuery($query);

        // Generate HTML for products
        if (!empty($cm)) {
            foreach ($cm as $v) {
                echo '<div class="col-md-4 col-sm-6 mt-3" onclick="getBarcode2(\'' . $v['code'] . '\')">
                        <div class="product-grid h-100">
                            
                            <div class="product-content">
                                <div class="name" style="color: #fff;">' . $v['name'] . '<br>' . $v['code'] . '</div>
                                <div class="name" style="color: #f67019; font-size:20px;">' . $v['bName'] . '</div>
                                <div class="price" style="color: #3dce12;">I:- RS ' . $v['item_s_price'] . '</div>
                                <div class="price" style="color: #d8f13b;">U:- RS ' . $v['unit_s_price'] . '</div>
                               <div class="title" style="color: #fff;">' . $v['ucv_name2'] . ' -' . $v['unit2'] . '</div>                               
                              </div>
                        </div>
                    </div>';
            }
        } else {
            // No results found
            echo "
            <h1 style='color: white;'>No products found.</h1>";
        }
    }
}

?>

<!-- <div class="product-image">
    <a href="#" class="image">
        <img src="dist/img/product/' . $v['img'] . '" width="50" alt="Image">
    </a>
</div> -->

<!--<h4 class="title"><a href="#" style="color: #fff;">' . $v['name'] . '<br> -->
<!--                                 '. $v['code'] .'-->
<!--                             </a></h4>-->