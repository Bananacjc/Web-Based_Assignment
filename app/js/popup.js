
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


function showPopup(msg, isSuccess) {

    const bg = isSuccess ? 'var(--bg-success)' : 'var(--bg-failed)';
    const color = isSuccess ? 'var(--color-success)' : 'var(--color-failed)';

    const popup = $('#popup');
    const popupContent = $('#popup-content');
    const popupTitle = $('#popup-title');
    const popupMsg = $('#popup-msg');
    const popupBtn = $('#popup-btn');

    // Change color
    popupContent.css('background-color', bg);
    popupContent.css('color', color);
    popupBtn.css('background-color', bg);
    popupBtn.css('color', color);

    popupBtn.on('hover', function() {
        popupBtn.css('background-color', bg);
    })

    popupTitle.text(isSuccess ? 'Success' : 'Failed');
    popupMsg.text(msg);


    // Show popup
    popup.removeClass('hide');
    popup.addClass('show');

}
