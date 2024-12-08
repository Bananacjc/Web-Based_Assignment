$(document).ready(function () {
    const $slider = $("#price-slider"); // Price slider
    const $priceValue = $('#price-value'); // Price value display
    const $categorySections = $('.category-section'); // Category sections

    // Set slider default to 0 to max on page load
    $slider.val($slider.attr('max'));
    $priceValue.text(`RM 0 - RM ${parseFloat($slider.val()).toFixed(2)}`);

    // Filter products based on slider input
    $slider.on('input', function () {
        const maxPrice = parseFloat($(this).val());
        $priceValue.text(`RM 0 - RM ${maxPrice.toFixed(2)}`);

        $categorySections.each(function () {
            const $section = $(this);
            const $productCards = $section.find('.product-card'); // Products in the category
            let hasVisibleProduct = false;

            $productCards.each(function () {
                const $card = $(this);
                const priceText = $card.find('.price-tag').text();
                const productPrice = parseFloat(priceText.replace(/[^0-9.]/g, ''));

                const isVisible = productPrice <= maxPrice;
                $card.toggle(isVisible); // Show or hide the product card
                if (isVisible) {
                    hasVisibleProduct = true; // At least one product is visible in this category
                }
            });

            // Show or hide the category title and section based on visible products
            const $categoryTitle = $section.find('.category-title');
            $section.toggle(hasVisibleProduct); // Hide or show the whole category section
            $categoryTitle.toggle(hasVisibleProduct); // Hide or show the category title
        });

        updateSliderBackground($(this));
    });

    // Initialize the slider background on page load
    updateSliderBackground($slider);

    function updateSliderBackground($elem) {
        const value = (($elem.val() - $elem.attr('min')) / ($elem.attr('max') - $elem.attr('min'))) * 100;
        $elem.css('background', `linear-gradient(to right, #34A853 0%, #34A853 ${value}%, #fff ${value}%, #fff 100%)`);
    }
});
