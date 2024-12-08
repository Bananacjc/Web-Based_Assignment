<?php
$_title = 'BananaSIS';
$_css = 'css/home.css';
require '_base.php';
include '_head.php';
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
<div id="category-container">
    <a href="/page/shop.php#fruits" class="category-link hover-translate-y">
        <div id="fruits" class="category-img-container">
            <img src="images/c-img-1.webp" alt="Fruits" />
        </div>
        <p class="category-name">Fruits</p>
    </a>
    <a href="/page/shop.php#vegetables" class="category-link hover-translate-y">
        <div id="vegetables" class="category-img-container">
            <img src="images/c-img-2.webp" alt="Vegetables" />
        </div>
        <p class="category-name">Vegetables</p>
    </a>
    <a href="/page/shop.php#juices" class="category-link hover-translate-y">
        <div id="juices" class="category-img-container">
            <img src="images/c-img-3.webp" alt="Juices" />
        </div>
        <p class="category-name">Juices</p>
    </a>
    <a href="/page/shop.php#meat" class="category-link hover-translate-y">
        <div id="meat" class="category-img-container">
            <img src="images/c-img-4.webp" alt="Meat" />
        </div>
        <p class="category-name">Meat</p>
    </a>
    <a href="/page/shop.php#cold-drinks" class="category-link hover-translate-y">
        <div id="cold-drinks" class="category-img-container">
            <img src="images/c-img-5.webp" alt="Cold Drinks" />
        </div>
        <p class="category-name">Cold Drinks</p>
    </a>
    <a href="/page/shop.php#breads" class="category-link hover-translate-y">
        <div id="breads" class="category-img-container">
            <img src="images/c-img-6.webp" alt="Breads" />
        </div>
        <p class="category-name">Breads</p>
    </a>

</div>
<script src="js/categoryLink.js"></script>
<?php include '_foot.php'; ?>