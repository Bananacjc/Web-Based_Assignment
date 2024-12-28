


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

    /**@type {HTMLButtonElement}*/
    $('#pay-button').on('submit', e => {
        e.preventDefault;

        accNum = null;
        cvvNum = null;
        exDate = null;

        const paymentMethod = $('#selectPayment').val();
        if (paymentMethod) {
            $('#hiddenAccNum').val($('#accNum').val());
            $('#hiddenCvvNum').val($('#cvvNum').val());
            $('#hiddenExDate').val($('#exDate').val());
        }

        const address = $('#uAddress').val();
        if (address) {

        }


        $('#hiddenAddress').val(address);

        $('#checkout-form').trigger('submit');
    })

});

function updateBankDetails(paymentMethod) {
    if (paymentMethod) {
        $.ajax({
            url: 'payment_update.php',
            type: 'POST',
            data: { selectPayment: paymentMethod, action: 'changeBankDetails' },
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
        $('#accNum').val('');
        $('#cvvNum').val('');
        $('#exDate').val('');
    }
}

function updateAddress(address) {
    if (address) {
        $.ajax({
            url: 'payment_update.php',
            type: 'POST',
            data: { selectAddress: address, action: 'changeAddress' },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#address-input').val(data.address);
                    distance = 0;
                    
                    (async () => {
                        try {
                            distance = await calculateDistance(data.address);
                            updateShippingFee(distance);
                            updateTotal();
                        } catch (error) {
                            console.error(error);
                        }
                    })();

                   
                } else {
                    showAlertPopup(data.message, false);
                }
            }
        })
    } else {
        $('#address-input').val('');
        updateShippingFee(0);
        updateTotal(0);
    }
}

function updateShippingFee(distance) {
    $('#pShippingFee').text((distance * 1.0).toFixed(2));
}

function updatePromotion(promotionID) {
    if (promotionID) {
        $.ajax({
            url: 'payment_update.php',
            type: 'POST',
            data: { selectPromo: promotionID, action: 'changePromo' },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    promotionAmount = data.promoAmount;
                    $('#uPromo').text(promotionAmount.toFixed(2));

                    updateTotal(null, promotionAmount);
                }
            }
        });
    } else {
        $('#uPromo').text((0).toFixed(2));
        updateTotal(null, 0);
    }
}

function updateTotal(shippingFee = null, promoAmount = null) {

    if (!shippingFee) {
        shippingFee = parseFloat($('#pShippingFee').text());
    }

    if (!promoAmount) {
        promoAmount = parseFloat($('#uPromo').text());
    }

    productTotal = parseFloat($('#pTotal').text());
    $('#total-payment').text((productTotal + shippingFee - promoAmount).toFixed(2));
}

/**@returns {number} */
async function calculateDistance(destination) {
    const origin = "Jalan Genting Kelang, 53300 Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia";
    const service = new google.maps.DistanceMatrixService();

    return new Promise((resolve, reject) => {
        service.getDistanceMatrix(
            {
                origins: [origin],
                destinations: [destination],
                travelMode: 'DRIVING',
            },
            (response, status) => {
                if (status === 'OK') {
                    const distance = response.rows[0].elements[0].distance.text;
                    resolve(parseFloat(distance.split(' ')[0]));
                } else {
                    reject(`Error with DistanceMatrixService: ${status}`);
                }
            }
        );
    });
}



