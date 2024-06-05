	<!-- product's detals popup JS -->
<?php include("part/edit_product-popup-js.php");?>
	<!-- product's detals popup JS end -->


<script>
    $(document).ready(function() {
      // Function to handle edit button click
      $('.dropdown-item.edit').click(function(e) {
        e.preventDefault(); // Prevent default link behavior

        // Get the product details from the table row
        var productId = $(this).closest('tr').find('td:first').text();
        var productName = $(this).closest('tr').find('td:nth-child(3)').text();
        // Populate the modal with the product details
        $('#editModal .modal-body').html(`
          <form id="editForm">
            <div class="form-group">
              <label for="productName">Product Name</label>
              <input type="text" class="form-control" id="productName" value="${productName}">
            </div>
            <!-- Add more fields as needed -->
            <button type="submit" class="btn btn-primary">Update</button>
          </form>
        `);

        // Show the modal
        $('#editModal').modal('show');
      });

      // Function to handle form submission
      $('#editModal').on('submit', '#editForm', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Get the updated product details from the form
        var updatedProductName = $('#productName').val();
        // Add more fields as needed

        // Perform AJAX request to update the database
        $.ajax({
          url: 'update_product.php',
          method: 'POST',
          data: {
            productId: productId,
            updatedProductName: updatedProductName
            // Add more data fields if necessary
          },
          success: function(response) {
            // Handle success response
            console.log('Product updated successfully');
            // Close the modal after updating
            $('#editModal').modal('hide');
            // Reload the table or update the relevant row with new data
          },
          error: function(xhr, status, error) {
            // Handle error
            console.error('Error updating product:', error);
          }
        });
      });
    });
  </script>