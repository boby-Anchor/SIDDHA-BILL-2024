<script>
    $(document).ready(function() {
        var ucv_name;
        var product_unit;

        // Function to adjust visibility based on product_unit
        function adjustVisibility() {
            if (product_unit === 'pack') {
                $(".auto-generate-m-unit").addClass("d-none");
                $(".manual-enter-m-unit").removeClass("d-none");
            } else {
                $(".auto-generate-m-unit").removeClass("d-none");
                $(".manual-enter-m-unit").addClass("d-none");
            }
        }

        $(document).on("click", ".add-btn", function() {
            // Fetch necessary data
            var product_code = $(this).closest("tr").find("#product_code").text();
            var product_name = $(this).closest("tr").find("#product_name").text();
            ucv_name = parseInt($(this).closest("tr").find("#ucv_name").text());
            product_unit = $(this).closest("tr").find("#product_unit").text();
            var product_qty = 1;

            var exists = false;
            $(".addedProTable tbody tr").each(function() {
                if ($(this).find("#addproduct_name").text() === product_name) {
                    exists = true;
                    return false;
                }
            });

            if (!exists) {
                // Append new row to the table
                var markup =
                    "<tr>" +
                    "<th scope='row' id='product_code'>" + product_code + "</th>" +
                    "<td id='addproduct_name'>" + product_name + "</td>" +
                    "<td>" +
                    "<input id='qty_input' type='text' class='bg-dark form-control text-center qty-input' value=''>" +
                    "<input id='free_qty' placeholder='free of qty..' type='text' class='bg-dark form-control text-center free-qty-input' value=''>" +
                    "</td>" +
                    "<td class='text-center auto-generate-m-unit '>" +
                    "<label id='minimum_qty'><i class='fa fa-solid fa-circle-notch fa-spin'></i></label><br>" +
                    "<label id='free_minimum_qty'><i class='fa fa-solid fa-circle-notch fa-spin'></i></label>" +
                    "</td>" +
                    "<td class='text-center manual-enter-m-unit d-none'>" +
                    "<label id='minimum_qty'><i class='fa fa-solid fa-circle-notch fa-spin'></i></label><br>" +
                    "<label id='free_minimum_qty'><i class='fa fa-solid fa-circle-notch fa-spin'></i></label>" +
                    "</td>" +
                    "<td>" + "<input type='text' id='cost_input' class='bg-dark form-control text-center cost-input' value=''></td>" +
                    "<td>" + "<label id='cost_per_unit'></label>" + "</td>" +
                    "<td>" + "<input id='unit_s_price' type='text' class='bg-dark form-control text-center unitsell-price-input' value=''></td>" +
                    "<td>" + "<input id='item_discount' type='text' class='bg-dark form-control text-center itemdicount' value=''>" + "</td>" +
                    "<td>" + "<label id='item_sale_price'></label>" + "</td>" +
                    "<td><i class='fa fa-trash-o cus-delete'></i></td>" +
                    "</tr>";

                $(".addedProTable tbody").append(markup);

                $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
                $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);

                // Update visibility based on product_unit
                adjustVisibility();
            } else {
                alert("Product already exists in the list!");
            }
        });

        // Event listener for clicking the delete button
        $(document).on("click", ".cus-delete", function() {
            $(this).closest("tr").remove();
            $("#proceedGrnBtn").removeAttr("data-toggle data-target");
            $(".po_btn").toggleClass("d-none", $(".addedProTable tbody tr").length === 0);
            $(".po_btn").toggleClass("d-flex", $(".addedProTable tbody tr").length > 0);
        });

        // Convert to minimum unit based on product_unit
        $(document).on("input", ".qty-input", function() {
            if (product_unit === 'l') {
                var liters = parseFloat($(this).val());
                var milliliters = ucv_name * liters * 1000;
                $(this).closest("tr").find("#minimum_qty").text(milliliters + "ml");

            }
            if (product_unit === 'kg') {
                var kilo = parseFloat($(this).val());
                var grams = ucv_name * kilo * 1000;
                $(this).closest("tr").find("#minimum_qty").text(grams + "g");
            }
            if (product_unit === 'm') {
                var meter = parseFloat($(this).val());
                var centimete = ucv_name * meter * 100;
                $(this).closest("tr").find("#minimum_qty").text(centimete + "cm");

            }
            if (product_unit === 'ml') {
                var ml = parseFloat($(this).val());
                var mililiters = ucv_name * ml;
                $(this).closest("tr").find("#minimum_qty").text(mililiters + "ml");
            }
            if (product_unit === 'g') {
                var g = parseFloat($(this).val());
                var grams = ucv_name * g;
                $(this).closest("tr").find("#minimum_qty").text(grams + "g");
            }
            if (product_unit === 'cm') {
                var cm = parseFloat($(this).val());
                var centimeters = ucv_name * cm;
                $(this).closest("tr").find("#minimum_qty").text(centimeters + "cm");
            }
        });

        $(document).on("input", ".free-qty-input", function() {
            if (product_unit === 'l') {
                var liters = parseFloat($(this).val());
                var milliliters = ucv_name * liters * 1000;
                $(this).closest("tr").find("#free_minimum_qty").text(milliliters + "ml");

            }
            if (product_unit === 'kg') {
                var kilo = parseFloat($(this).val());
                var grams = ucv_name * kilo * 1000;
                $(this).closest("tr").find("#free_minimum_qty").text(grams + "g");
            }
            if (product_unit === 'm') {
                var meter = parseFloat($(this).val());
                var centimete = ucv_name * meter * 100;
                $(this).closest("tr").find("#free_minimum_qty").text(centimete + "cm");

            }
            if (product_unit === 'ml') {
                var ml = parseFloat($(this).val());
                var mililiters = ucv_name * ml;
                $(this).closest("tr").find("#free_minimum_qty").text(mililiters + "ml");
            }
            if (product_unit === 'g') {
                var g = parseFloat($(this).val());
                var grams = ucv_name * g;
                $(this).closest("tr").find("#free_minimum_qty").text(grams + "g");
            }
            if (product_unit === 'cm') {
                var cm = parseFloat($(this).val());
                var centimeters = ucv_name * cm;
                $(this).closest("tr").find("#free_minimum_qty").text(centimeters + "cm");
            }
        });

        // Calculate cost per unit based on product_unit
        $(document).on("input", ".cost-input", function() {
            if (product_unit === "l") {
                var cost = parseFloat($(this).val());
                var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 1000;
                var cost_per_unit = cost / milliliters;
                $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            }

            if (product_unit === "kg") {
                var cost = parseFloat($(this).val());
                var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 1000;
                var cost_per_unit = cost / milliliters;
                $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            }

            if (product_unit === "m") {
                var cost = parseFloat($(this).val());
                var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name * 100;
                var cost_per_unit = cost / milliliters;
                $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            }

            if (product_unit === "ml") {
                var cost = parseFloat($(this).val());
                var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                var cost_per_unit = cost / milliliters;
                $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            }

            if (product_unit === "g") {
                var cost = parseFloat($(this).val());
                var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                var cost_per_unit = cost / milliliters;
                $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            }

            if (product_unit === "cm") {
                var cost = parseFloat($(this).val());
                var milliliters = parseFloat($(this).closest("tr").find(".qty-input").val()) * ucv_name;
                var cost_per_unit = cost / milliliters;
                $(this).closest("tr").find("#cost_per_unit").text(cost_per_unit.toFixed(2));
            }
        });

        // Calculate discounted price
        $(document).on("input", ".itemdicount", function() {
            var add_discount = parseFloat($(this).val());

            var discount = add_discount + 100;

            var qty = parseFloat($(this).closest("tr").find(".qty-input").val());

            var cost_input = parseFloat($(this).closest("tr").find(".cost-input").val());
            var item_cost = cost_input / qty;

            var item_sell_price = item_cost / 100 * discount;

            $(this).closest("tr").find("#item_sale_price").text(item_sell_price.toFixed(2));
        });

        // Initial adjustment of visibility
        adjustVisibility();

    });
</script>