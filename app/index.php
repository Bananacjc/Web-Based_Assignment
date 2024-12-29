<?php
$_title = 'BananaSIS';
$_css = 'css/home.css';
require '_base.php';
include '_head.php';

// Fetch categories from the database
$stmt = $_db->query("SELECT category_name, category_image FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="content-container">
    <div id="info-container" class="w-50">
        <h1 class="h1">FRESH GROCERY</h1>
        <h2 id="info-content">Here you can Buy all of your Grocery Products.</h2>
        <a id="shopbtn" href="RetrieveProduct">Shop Now</a>
    </div>
    <div id="img-container" class="w-50">
        <img src="images/products.webp" alt="Products" width="100%" height="100%" />
    </div>
</div>
<div id="service-container">
    <div>
        <div class="service-icon">
            <i class="ti ti-truck-loading"></i>
        </div>
        <div>
            <h3 class="service-title">Free Shipping</h3>
            <span class="service-info">When ordering over RM50</span>
        </div>
    </div>
    <div>
        <div class="service-icon">
            <i class="ti ti-reload"></i>
        </div>
        <div>
            <h3 class="service-title">Free Return</h3>
            <span class="service-info">Get Return within 30 days</span>
        </div>
    </div>
    <div>
        <div class="service-icon">
            <i class="ti ti-shield-lock"></i>
        </div>
        <div>
            <h3 class="service-title">Secure Payment</h3>
            <span class="service-info">100% Secure Online Payment</span>
        </div>
    </div>
    <div>
        <div class="service-icon">
            <i class="ti ti-trophy"></i>
        </div>
        <div>
            <h3 class="service-title">Best Quality</h3>
            <span class="service-info">Original Product Guaranteed</span>
        </div>
    </div>
</div>
<div id="category-title-container">
    <h3 id="category-title">Product Category</h3>
    <a href="/page/shop.php" id="view-all" class="hover-underline-anim">View All</a>
</div>
<div id="category-slider">
    <i class="ti ti-chevron-compact-left" id="slider-left"></i>
    <div id="category-container">
        <?php foreach ($categories as $category): ?>
            <a href="/page/shop.php#<?= strtolower(str_replace(' ', '-', $category['category_name'])) ?>" class="category-link hover-translate-y">
                <div id="<?= strtolower(str_replace(' ', '-', $category['category_name'])) ?>" class="category-img-container">
                    <img src="uploads/category_images/<?= $category['category_image'] ?>" alt="<?= $category['category_name'] ?> " width="70" height="70" />
                </div>
                <p class="category-name"><?= $category['category_name'] ?></p>
            </a>
        <?php endforeach; ?>
    </div>
    <i class="ti ti-chevron-compact-right" id="slider-right"></i>
</div>

<script src="js/categoryLink.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const categoryContainer = document.getElementById("category-container");
        const sliderLeft = document.getElementById("slider-left");
        const sliderRight = document.getElementById("slider-right");

        // Adjust the scroll amount (category width + gap)
        const scrollAmount = 160;

        sliderLeft.addEventListener("click", function() {
            categoryContainer.scrollBy({
                left: -scrollAmount,
                behavior: "smooth",
            });
        });

        sliderRight.addEventListener("click", function() {
            categoryContainer.scrollBy({
                left: scrollAmount,
                behavior: "smooth",
            });
        });

        // Show/hide slider buttons based on scroll position
        function updateSliderButtons() {
            sliderLeft.style.display = categoryContainer.scrollLeft > 0 ? "block" : "none";
            sliderRight.style.display =
                categoryContainer.scrollLeft + categoryContainer.clientWidth < categoryContainer.scrollWidth ?
                "block" :
                "none";
        }

        categoryContainer.addEventListener("scroll", updateSliderButtons);
        window.addEventListener("resize", updateSliderButtons);

        // Initialize button visibility
        updateSliderButtons();
    });
</script>
<?php include '_foot.php'; ?>