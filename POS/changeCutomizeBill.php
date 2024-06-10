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
            <td colspan="3" class="">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Logo</h2>
                                </div>
                                <div class="">
                                    <span>Add Your Logo here</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <input type="file" id="fileInput" style="display: none;">
                                <div class="logoImg" style="background-image: url('<?= $billpreview_data['customize_bills_logo'] ?>');"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Print Size</h2>
                                </div>
                                <div class="">
                                    <span>Adjust Your Print Width Size</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div>
                                    <div class="range-slider">
                                        <input class="range-slider__range" type="range" value="<?= $billpreview_data['print_paper_size'] ?>mm" min="48" max="88" step="10">
                                        <span class="range-slider__value">0</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Print Meta</h2>
                                </div>
                                <div class="">
                                    <span>
                                        <input type="checkbox" name="printdate" id="printdate" checked>
                                        <label for="printdate">Date</label>

                                        <input type="checkbox" name="printtime" id="printtime" checked>
                                        <label for="printtime">Time</label>

                                        <input type="checkbox" name="printagent" id="printagent" checked>
                                        <label for="printagent">Agent</label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="">
                                    <label class="btn-onoff">
                                        <input type="checkbox" name="printmeta" data-onoff="toggle" checked><span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Products</h2>
                                </div>
                                <div class="">
                                    <span>Your Products list</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="">
                                    <label class="btn-onoff">
                                        <input type="checkbox" name="products" data-onoff="toggle" checked disabled><span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Discount</h2>
                                </div>
                                <div class="">
                                    <span>Add discount section</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="">
                                    <label class="btn-onoff">
                                        <input type="checkbox" name="discountsection" data-onoff="toggle"><span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Contact No</h2>
                                </div>
                                <div class="">
                                    <span>Add Your Contact No here</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="">
                                    <input type="text" id="contactNoInput" class="bg-dark form-control border-0 text-center" value="<?= $billpreview_data['customize_bills_mobile'] ?>">
                                    <input type="text" value='<?= $selectedShopId ?>' class="d-none" id="selectedShopNumber">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Address</h2>
                                </div>
                                <div class="">
                                    <span>Add Your Address here</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div>
                                    <input type="text" class="bg-dark form-control border-0 text-center" id="addressNoInput" value="<?= $billpreview_data['customize_bills_address'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="col-12 d-flex justify-content-center mb-2">
                    <div class="col-10 p-2 customize-bar">
                        <div class="row">
                            <div class="col-1 d-flex justify-content-end justify-content-md-center align-items-center drag-handle" draggable="true">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <h2>Note</h2>
                                </div>
                                <div class="">
                                    <span>Add note to bill</span>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="">
                                    <input type="text" id="noteInput" class="bg-dark form-control border-0 text-center" value="<?= $billpreview_data['bill_note'] ?>">
                                </div>
                            </div>
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