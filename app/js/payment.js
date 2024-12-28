

$(() => {

    /**@type {HTMLSelectElement} */
    $('#selectPayment').on({
        change: function () {
            const paymentMethod = $(this).val();
            updateBankDetails(paymentMethod);
        }
    });

    /**@type {HTMLSelectElement} */
    $('#selectAddress').on({
        change: function () {
            const address = $(this).val();
            updateAddress(address);
        }
    });

    /**@type {HTMLSelectElement} */
    $('#selectPromo').on({
        change: function () {
            const promotion = $(this).val();
            updatePromotion(promotion);
        }
    })

});

function updateBankDetails(paymentMethod) {
    if (paymentMethod) {
        $.ajax({
            url: 'payment_update.php',
            type: 'POST',
            data: {selectPayment: paymentMethod, action: 'changeBankDetails'},
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#bank-detail-container').removeClass('d-none');
                    $('#accNum').val(data.paymentMethod.accNum);
                    $('#cvvNum').val(data.paymentMethod.cvvNum);
                    $('#exDate').val(data.paymentMethod.exDate);
                } else {
                    showAlertPopup(data.message, false);
                }
            }
        })
    } else {
        $('#bank-detail-container').addClass('d-none');
    }
}

function updateAddress(address){
    if (address) {
        $.ajax({
            url: 'payment_update.php',
            type: 'POST',
            data: {selectAddress: address, action: 'changeAddress'},
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#uAddress').val(data.address);
                } else {
                    showAlertPopup(data.message, false);
                }
            }
        })
    } else {
        $('#uAddress').val('');
    }
}

function updatePromotion(promotionID) {
    if (promotionID) {
        $.ajax({
            url: 'payment_update.php',
            type: 'POST',
            data: {selectPromo: promotionID, action: 'changePromo'},
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    promotionAmount = data.promoAmount ;
                    $('#uPromo').text(promotionAmount.toFixed(2));

                    total = parseFloat($('#pTotal').text());
                    shippingFee = parseFloat($('#pShippingFee').text());

                    $('#total-payment').text(parseFloat(total + shippingFee - promotionAmount).toFixed(2));
                }
            }
        });
    } else {
        $('#uPromo').text((0).toFixed(2));
    }
}