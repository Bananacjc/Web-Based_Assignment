//SEARCH FILTER
const $searchBar = $("#searchbar"); // Search bar
const $categorySections = $(".category-section"); // Category sections

$searchBar.on("input", function () {
  const searchText = $(this).val().toLowerCase();

  $categorySections.each(function () {
    const $section = $(this);
    const $productCards = $section.find(".product-card"); // Products in this category
    let hasVisibleProduct = false;

    $productCards.each(function () {
      const $card = $(this);
      const name = $card.find(".product-name").text().toLowerCase();

      if (name.includes(searchText)) {
        $card.show(); // Show matching product
        hasVisibleProduct = true; // Mark this category as having visible products
      } else {
        $card.hide(); // Hide non-matching product
      }
    });

    const $categoryTitle = $section.find(".category-title");

    if (hasVisibleProduct) {
      $categoryTitle.show(); // Show category title
      $section.show(); // Show the whole section
    } else {
      $categoryTitle.hide(); // Hide category title
      $section.hide(); // Hide the whole section
    }
  });
});

//CATEGORY FILTER
$(".sidebar a").on("click", function () {
  $(".sidebar a").removeClass("active"); // Remove 'active' class from all links
  $(this).addClass("active"); // Add 'active' class to the clicked link
});

//PRICE FILTER
const $slider = $("#price-slider"); // Price slider
const $priceValue = $("#price-value"); // Price value display

// Set slider default to 0 to max on page load
$slider.val($slider.attr("max"));
$priceValue.text(`RM 0 - RM ${parseFloat($slider.val()).toFixed(2)}`);

// Filter products based on slider input
$slider.on("input", function () {
  const maxPrice = parseFloat($(this).val());
  $priceValue.text(`RM 0 - RM ${maxPrice.toFixed(2)}`);

  $categorySections.each(function () {
    const $section = $(this);
    const $productCards = $section.find(".product-card"); // Products in the category
    let hasVisibleProduct = false;

    $productCards.each(function () {
      const $card = $(this);
      const priceText = $card.find(".price-tag").text();
      const productPrice = parseFloat(priceText.replace(/[^0-9.]/g, ""));

      const isVisible = productPrice <= maxPrice;
      $card.toggle(isVisible); // Show or hide the product card
      if (isVisible) {
        hasVisibleProduct = true; // At least one product is visible in this category
      }
    });

    // Show or hide the category title and section based on visible products
    const $categoryTitle = $section.find(".category-title");
    $section.toggle(hasVisibleProduct); // Hide or show the whole category section
    $categoryTitle.toggle(hasVisibleProduct); // Hide or show the category title
  });

  updateSliderBackground($(this));
});

// Initialize the slider background on page load
updateSliderBackground($slider);

function updateSliderBackground($elem) {
  const value =
    (($elem.val() - $elem.attr("min")) /
      ($elem.attr("max") - $elem.attr("min"))) *
    100;
  $elem.css(
    "background",
    `linear-gradient(to right, #34A853 0%, #34A853 ${value}%, #fff ${value}%, #fff 100%)`
  );
}

//RATING FILTER
function setFilterRating(rating) {
  document
    .querySelectorAll("#rating-filter .star")
    .forEach(function (star, index) {
      star.style.transform = "scale(1)";
      if (index < rating) {
        star.classList.add("ti-star-filled");
        star.style.transform = "scale(1.1)";
        setTimeout(function () {
          star.style.transform = "scale(1)"; // Scale back after 0.3s
        }, 300); 
      } else {
        star.classList.remove("ti-star-filled");
      }
    });

  document.getElementById("ratingFilterInput").value = rating;
  filterProductsByRating(rating);
}

function filterProductsByRating(rating) {
  const categorySections = document.querySelectorAll(".category-section");

  categorySections.forEach(function (section) {
    let hasVisibleProduct = false;
    const productCards = section.querySelectorAll(".product-card");

    productCards.forEach(function (card) {
      const productRating =
        card.querySelectorAll(".rating .ti-star-filled").length +
        card.querySelectorAll(".rating .ti-star-half-filled").length * 0.5;

      let starRating = 0;

      if (productRating <= 1.5) starRating = 1;
      else if (productRating <= 2.5) starRating = 2;
      else if (productRating <= 3.5) starRating = 3;
      else if (productRating <= 4.5) starRating = 4;
      else if (productRating === 5) starRating = 5;

      if (starRating === rating) {
        card.style.display = "block";
        hasVisibleProduct = true;
      } else {
        card.style.display = "none";
      }
    });

    section.style.display = hasVisibleProduct ? "block" : "none";
  });
}

//RESET FILTER
function resetFilters() {
  // Clear the search bar
  $("#searchbar").val("");
  $(".product-card").show(); // Show all products
  $(".category-section").show(); // Show all categories
  $(".category-title").show(); // Show all category titles

  //Reset price filter
  const $slider = $("#price-slider");
  $slider.val($slider.attr("max")); // Reset slider to maximum value
  const maxPrice = parseFloat($slider.val());
  $("#price-value").text(`RM 0 - RM ${maxPrice.toFixed(2)}`); // Update the displayed price range
  updateSliderBackground($slider); // Reset slider background

  // Reset rating filter
  document.querySelectorAll("#rating-filter .star").forEach(function (star) {
    star.classList.remove("ti-star-filled");
  });
  document.getElementById("ratingFilterInput").value = "";

  // Reset active category in sidebar
  $(".sidebar a").removeClass("active");
}

$("#reset-filters").on("click", resetFilters);
