document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById("price-slider");
    const priceValue = document.getElementById('price-value');
    const productCards = document.querySelectorAll('.product-card');
    slider.oninput = function() {
        
        const maxPrice = parseFloat(this.value);
        priceValue.textContent = `RM 0 - RM ${maxPrice.toFixed(2)}`;

        productCards.forEach(card => {
            const priceText = card.querySelector('.price-tag').textContent;
            const productPrice = parseFloat(priceText.replace(/[^0-9.]/g, ''));

            card.style.display = productPrice <= maxPrice ? '' : 'none';
        });

        const value = (this.value - this.min) / (this.max - this.min) * 100;
        this.style.background = `linear-gradient(to right, #34A853 0%, #34A853 ${value}%, #fff ${value}%, #fff 100%)`;
    };

    updateSliderBackground(slider);

    function updateSliderBackground(elem) {
        const value = (elem.value - elem.min) / (elem.max - elem.min) * 100;
        elem.style.background = `linear-gradient(to right, #34A853 0%, #34A853 ${value}%, #fff ${value}%, #fff 100%)`;
    }
});
