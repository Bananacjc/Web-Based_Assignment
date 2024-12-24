
$(() => {

    $('.ti-minus').on({
        click: function () {
            const productID = $(this).data('product-id');
            updateCartQty(productID, 'decrease');
        }
    });

    $('.ti-plus').on({
        click: function () {
            const productID = $(this).data('product-id');
            updateCartQty(productID, 'increase');
        }
    })

    $('.cart-remove-btn').on({
        click: function () {
            const productID = $(this).data('product-id');
            updateCartQty(productID, 'remove');
        }
    })

    $('.quantity-value').on({
        change: function() {
            console.log('hello')
            const productID = $(this).data('product-id');
            updateCartQty(productID, 'change');
        }
    })

})

function updateCartQty(productID, action) {
    const quantity = parseInt($(`#quantity-${productID}`).val());

    const formData = {
        product_id: productID,
        action: action,
        quantity: quantity
    };

    $.ajax({
        url: 'cart_update.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                if (data.action === 'increase' || data.action === 'decrease' || data.action === 'change') {
                    /**@type {HTMLInputElement} */
                    $(`#quantity-${productID}`).val(data.newQuantity);
                    $(`#subtotal-${productID}`).text(data.newSubtotal.toFixed(2));
                } else if (data.action === 'remove') {
                    $(`#product-${productID}`).remove();
                } 

                if (data.cartCount > 0 && data.newTotal > 0) {
                    $('#cart-count').text("(" + data.cartCount + ")");
                    $('#cart-total').text(data.newTotal.toFixed(2));
                } else {
                    $('#cart-count').text('');
                    $('#cart-total').parent().remove();
                }

                if (data.cartCount == 0) {
                    $('#nothing-to-show').after(
                        "<td colspan='5'>\
                        <h3>Nothing is here, Try add \
                        <a href='../page/shop.php' \
                        class='text-decoration-none text-green-darker hover-underline-anim'>something!\
                        </a></h3>\
                        </td>"
                    );
                }

            } else {
                showAlertPopup(data.message, false);
                if (data.action === 'change') {
                    $(`#quantity-${productID}`).val(data.quantity);
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ', error);
            showAlertPopup('Error Updating Cart', false);
        }
    });


}