<?php
include('config/db.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['selectedShopId'])) {
        $selectedShopId = $_POST['selectedShopId'];
?>

        <?php
        $billpreview_rs = $conn->query("SELECT * FROM customize_bills WHERE `customize_bill_shop-id` = '$selectedShopId'");
        $billPreviewNum = $billpreview_rs->num_rows;
        // echo $billPreviewNum;
        $billpreview_data = $billpreview_rs->fetch_assoc();

        ?>


        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center p-2">
                    <div class="billpreviewlogo<?= $billpreview_data['print_paper_size'] ?>" style="background-image: url('<?= $billpreview_data['customize_bills_logo'] ?>');"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="col-12 d-flex justify-content-center">
                    <label class="" id="contactNumberPreview"><?= $billpreview_data['customize_bills_mobile'] ?></label>
                </div>
                <div class="col-12 d-flex justify-content-center ">
                    <label id="addresspreview" class="address<?= $billpreview_data['print_paper_size'] ?>"><?= $billpreview_data['customize_bills_address'] ?></label>
                </div>
            </td>
        </tr>
        <tr style="border-bottom: lightgray 0.2rem solid;">
            <td>
                <div class="col-12 d-flex justify-content-center ">
                    <label class="datetime<?= $billpreview_data['print_paper_size'] ?>">2024-03-15 15:57:35</label>
                </div>
                <div class="col-12 d-flex justify-content-center">
                    <label class="agent<?= $billpreview_data['print_paper_size'] ?>">Hub Admin No-00011162</label>
                </div>
            </td>
        </tr>
        <tr style="font-weight: 600;">
            <td class="pt-2">
                <?php
                for ($i = 0; $i < 5; $i++) {
                ?>
                    <div class="col-12 offset-1 productTable<?= $billpreview_data['print_paper_size'] ?>">
                        <div class="row">
                            <div class="col-12">
                                <span class="product_name">Example Product</span>
                            </div>
                            <div class="col-4">
                                <span class="product_cost">1500</span>
                            </div>
                            <div class="col-2">
                                <span class="product_qty">5kg</span>
                            </div>
                            <div class="col-4" style="text-align: end;">
                                <span class="productTotal">7500.00</span>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </td>
        </tr>
        <tr style="font-weight: 600;">
            <td>

                <div class="col-12">
                    <div class="row">
                        <div class="col-11 d-flex justify-content-end pt-2">
                            <span class="productsAllTotal">Total : 7500.00</span>
                        </div>

                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                            <span class="enterAmountFiled">Paid :8000 .00</span>
                        </div>

                        <div class="col-11 d-flex justify-content-end pt-2" style="border-bottom: lightgray 0.2rem solid;">
                            <span class="balance">Balance : 500.00</span>
                        </div>
                    </div>
                </div>


            </td>
        </tr>

        <tr style="font-weight: 600;">
            <td>
                <div class="col-12 pt-2">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center text-center">
                            <span id="billnotepreview" style="font-size:9px;"><?= $billpreview_data['bill_note'] ?></span>
                        </div>
                        <div class="col-12 d-flex justify-content-center">
                            <span>Thank You !</span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <script>
            // upload logo js start=====
            $(document).ready(function() {
                $(".logoImg").click(function() {
                    $("#fileInput").click();
                });

                $("#fileInput").change(function() {
                    var file = this.files[0];
                    var selectedShopId = $("#selectedShopNumber").val();
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $(".logoImg").css("background-image", "url(" + e.target.result + ")");
                            // Send file to server via AJAX
                            var formData = new FormData();
                            formData.append("file", file);
                            formData.append("selectedShopId", selectedShopId);
                            $.ajax({
                                url: "updateCustomizeBill.php",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    console.log(response);
                                    // Handle response from server if needed
                                },
                                error: function(xhr, status, error) {
                                    console.error(xhr.responseText);
                                    // Handle error if any
                                },
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
            // upload logo js end=====

            // update contact number js start======
            document.getElementById("contactNoInput").addEventListener("input", function() {
                var inputValue = this.value;
                var selectedShopId = $("#selectedShopNumber").val();

                inputValue = inputValue.replace(/\D/g, "");
                inputValue = inputValue.slice(0, 10);
                this.value = inputValue;

                if (inputValue.length === 10 && !isNaN(inputValue)) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "updateCustomizeBill.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log(xhr.responseText);
                        }
                    };

                    // Prepare data to send
                    var data = "contactNo=" + inputValue + "&selectedShopId=" + selectedShopId;

                    // Send the request with the prepared data
                    xhr.send(data);
                }
            });


            // update contact number js end========

            // update address js start======
            document
                .getElementById("addressNoInput")
                .addEventListener("input", function() {
                    var inputValue = this.value;
                    var selectedShopId = $("#selectedShopNumber").val();

                    this.value = inputValue;

                    if (inputValue.length != 0) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "updateCustomizeBill.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                console.log(xhr.responseText);
                            }
                        };
                        xhr.send("address=" + inputValue + "&selectedShopId=" + selectedShopId);
                    }
                });

            // update address js end========

            // update note js start======
            document.getElementById("noteInput").addEventListener("input", function() {
                var inputValue = this.value;
                var selectedShopId = $("#selectedShopNumber").val();

                this.value = inputValue;

                if (inputValue.length != 0) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "updateCustomizeBill.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log(xhr.responseText);
                        }
                    };
                    xhr.send("noteInput=" + inputValue + "&selectedShopId=" + selectedShopId);
                }
            });

            // update note js end========

            // real time update bill contact preview

            const contactNoInput = document.getElementById("contactNoInput");
            const contactNumberPreview = document.getElementById("contactNumberPreview");
            contactNoInput.addEventListener("input", function() {
                contactNumberPreview.textContent = contactNoInput.value;
            });

            // real time update bill address preview
            const addressNoInput = document.getElementById("addressNoInput");
            const addresspreview = document.getElementById("addresspreview");
            addressNoInput.addEventListener("input", function() {
                addresspreview.textContent = addressNoInput.value;
            });

            // real time update bill note preview
            const noteInput = document.getElementById("noteInput");
            const billnotepreview = document.getElementById("billnotepreview");
            noteInput.addEventListener("input", function() {
                billnotepreview.textContent = noteInput.value;
            });
        </script>

<?php
    } else {
        echo "no data";
    }
}
?>