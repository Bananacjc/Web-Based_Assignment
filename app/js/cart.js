
$(() => {

    $('.ti-minus').on({
        click: function() {
            const productID = $(this).data('product-id');
            updateCartQty(productID, 'decrease');
        }
    });

    $('.ti-plus').on({
        click: function() {
            const productID = $(this).data('product-id');
            updateCartQty(productID, 'increase');
        }
    })

})

function updateCartQty(productID, action) {
    const formData = {
        product_id: productID,
        action: action
    };

    $.ajax({
        url: 'cart_update.php',
        type: 'POST',
        data: formData, 
        dataType: 'json', 
        success: function(data) {
            if (data.success) {
                console.log
                $(`#quantity-${productID}`).text(data.newQuantity);
                $(`#subtotal-${productID}`).text(data.newSubtotal.toFixed(2));
                $('#cart-count').text(data.cartCount);
            } else {
                showAlertPopup('Error Updating Cart', false);
            }
        }, 
        error: function(xhr, status, error) {
            console.error('AJAX error: ', error);
            showAlertPopup('Error Updating Cart', false);
        }
    });


}