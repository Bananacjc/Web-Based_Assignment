
$(() => {

    /**
     * Remove popup when button clicked
     */
    $('#popup-btn').on({
        click: function() {
            $('#popup').removeClass('show');
            $('#popup').addClass('hide');
        }
    })

   
});


function showAlertPopup(msg, isSuccess) {

    const status = isSuccess ? 'success' : 'failed';

    const popup = $('#popup');
    const popupContent = $('#popup-content');
    const popupTitle = $('#popup-title');
    const popupMsg = $('#popup-msg');
    const popupBtn = $('#popup-btn');

    // Change color
    popupContent.addClass(status);
    popupBtn.addClass(status);

    popupTitle.text(isSuccess ? 'Success' : 'Failed');
    popupMsg.text(msg);

    // Show popup
    popup.removeClass('hide');
    popup.addClass('show');

}

function showCartPopup(imagePath) {
    
    const popup = $('#cart-popup');
    const popupImg = $('#cart-popup-img');

    // Change img
    popupImg.attr('src', "../uploads/product_images/" + imagePath);

    popup.addClass('show');

    setTimeout(function() {
        popup.removeClass('show');
    }, 300);

}
