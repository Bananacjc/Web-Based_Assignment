


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
    $('#pay-button').on('click', e => {
        e.preventDefault;

        accNum = null;
        cvvNum = null;
        exDate = null;

        const paymentMethod = $('#selectPayment').val();
        if (paymentMethod) {
            $('#hiddenAccNum').val($('#accNum').text());
            $('#hideenCvvNum').val($('#cvvNum').text());
            $('#hiddenExDate').val($('#exDate').text());
        }

        const address = $('#selectAddress').val();
        if (address) {
            $('#hiddenLine_1').val($('#line-1').val());
            $('#hiddenVillage').val($('#village').val());
            $('#hiddenPostal_code').val($('#postal-code').val());
            $('#hiddenCity').val($('#city').val());
            $('#hiddenState').val($('#state').val());
        }

        const shippingFee = $('#pShippingFee').text();
        if (parseFloat(shippingFee) >= 0) {
            $('#hiddenShippingFee').val(parseFloat(shippingFee));
        }

        const promoID = $('#selectPromo').val();
        if (promoID) {
            $('#hiddenPromoID').val(promoID);
            const promoAmount = $('#uPromo').text();
            if (parseFloat(promoAmount) >= 0) {
                $('#hiddenPromoAmount').val(parseFloat(promoAmount));
            }
        }

        const subtotal = $('#pTotal').text();
        if (parseFloat(subtotal) >= 0) {
            $('#hiddenSubtotal').val(parseFloat(subtotal));
        }
        console.log(subtotal);

        const total = $('#total-payment').text();
        if (parseFloat(total) >= 0) {
            $('#hiddenTotal').val(parseFloat(total));
        }
        console.log(total);

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
                    $('#line-1').val(data.line_1);
                    $('#village').val(data.village);
                    $('#postal-code').val(data.postal_code);
                    $('#city').val(data.city);
                    $('#state').val(data.state);

                    distance = 0;

                    (async () => {
                        try {
                            distance = await calculateDistance(data.full_address);
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
        $('#line-1').val('');
        $('#village').val('');
        $('#postal-code').val('');
        $('#city').val('');
        $('#state').val('');
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



