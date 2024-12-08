$(document).ready(function () {
    const $searchBar = $('#searchbar'); // Search bar
    const $categorySections = $('.category-section'); // Category sections

    $searchBar.on('input', function () {
        const searchText = $(this).val().toLowerCase();

        $categorySections.each(function () {
            const $section = $(this);
            const $productCards = $section.find('.product-card'); // Products in this category
            let hasVisibleProduct = false;

            $productCards.each(function () {
                const $card = $(this);
                const name = $card.find('.product-name').text().toLowerCase();

                if (name.includes(searchText)) {
                    $card.show(); // Show matching product
                    hasVisibleProduct = true; // Mark this category as having visible products
                } else {
                    $card.hide(); // Hide non-matching product
                }
            });

            const $categoryTitle = $section.find('.category-title');

            if (hasVisibleProduct) {
                $categoryTitle.show(); // Show category title
                $section.show(); // Show the whole section
            } else {
                $categoryTitle.hide(); // Hide category title
                $section.hide(); // Hide the whole section
            }
        });
    });
});
