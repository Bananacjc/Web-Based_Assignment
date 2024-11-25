document.addEventListener("DOMContentLoaded", function() {

    var paymentMethodSelect = document.querySelector('.payment-method-select');

    paymentMethodSelect.addEventListener('change', function() {
        
        var selectedPaymentMethod = this.value;

        document.getElementById('bank-detail').style.display = 'none';
        document.getElementById('ewallet-detail').style.display = 'none';

        if (selectedPaymentMethod === 'bank') {
            document.getElementById('bank-detail').style.display = 'block';
        } else if (selectedPaymentMethod === 'ewallet') {
            document.getElementById('ewallet-detail').style.display = 'block';
        }
    });
});
