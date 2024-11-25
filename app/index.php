<?php
require 'base.php';
$_title = 'BananaSIS';
$_css = 'css/home.css';
include 'head.php';
?>
<div id="content-container">
    <div id="info-container">
        <h1 id="info-title">FRESH GROCERY</h1>
        <h2 id="info-content">Here you can Buy all of your Grocery Products.</h2>
        <a href="RetrieveProduct" id="shopbtn">Shop Now</a>
    </div>
    <div id="img-container">
        <img src="images/products.webp" alt="Products" width="100%" height="100%" />
    </div>
</div>
<div id="service-container">
    <div class="service-sub-container">
        <div class="service-icon">
            <i class="ti ti-truck-loading"></i>
        </div>
        <div>
            <h3 class="service-title">Free Shipping</h3>
            <span class="service-info">When ordering over RM50</span>
        </div>
    </div>
    <div class="service-sub-container">
        <div class="service-icon">
            <i class="ti ti-reload"></i>
        </div>
        <div>
            <h3 class="service-title">Free Return</h3>
            <span class="service-info">Get Return within 30 days</span>
        </div>
    </div>
    <div class="service-sub-container">
        <div class="service-icon">
            <i class="ti ti-shield-lock"></i>
        </div>
        <div>
            <h3 class="service-title">Secure Payment</h3>
            <span class="service-info">100% Secure Online Payment</span>
        </div>
    </div>
    <div class="service-sub-container">
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
    <a href="RetrieveProduct" id="view-all">View All</a>
</div>
<div id="category-container">
    <a href="RetrieveProduct#fruits" class="category-link">
        <div class="category-subcontainer" id="fruits">
            <div class="category-img-container">
                <img src="images/c-img-1.webp" alt="Fruits" />
            </div>
            <p class="category-name">Fruits</p>
        </div>
    </a>
    <a href="RetrieveProduct#vegetables" class="category-link">
        <div class="category-subcontainer" id="vegetables">
            <div class="category-img-container">
                <img src="images/c-img-2.webp" alt="Vegetables" />
            </div>
            <p class="category-name">Vegetables</p>
        </div>
    </a>
    <a href="RetrieveProduct#juices" class="category-link">
        <div class="category-subcontainer" id="juices">
            <div class="category-img-container">
                <img src="images/c-img-3.webp" alt="Juices" />
            </div>
            <p class="category-name">Juices</p>
        </div>
    </a>
    <a href="RetrieveProduct#meat" class="category-link">
        <div class="category-subcontainer" id="meat">
            <div class="category-img-container">
                <img src="images/c-img-4.webp" alt="Meat" />
            </div>
            <p class="category-name">Meat</p>
        </div>
    </a>
    <a href="RetrieveProduct#cold-drinks" class="category-link">
        <div class="category-subcontainer" id="cold-drinks">
            <div class="category-img-container">
                <img src="images/c-img-5.webp" alt="Cold Drinks" />
            </div>
            <p class="category-name">Cold Drinks</p>
        </div>
    </a>
    <a href="RetrieveProduct#breads" class="category-link">
        <div class="category-subcontainer" id="breads">
            <div class="category-img-container">
                <img src="images/c-img-6.webp" alt="Breads" />
            </div>
            <p class="category-name">Breads</p>
        </div>
    </a>
</div>
<script src="js/categoryLink.js"></script>
<?php include 'foot.php'; ?>