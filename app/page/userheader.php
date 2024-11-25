<?php
require '../_base.php';
$_title = 'Promotion';
$_css = '../css/header.css';
include '../_head.php';
?>
<header id="header">
    <div id="navbar">
        <a href="index.php" id="logo">
            <img src="${logo}" alt="Logo" width="60" height="60" />
            <p id="banana"></p>
            <p id="sis"></p>
        </a>
        <a href="index.php" class="navlink">Home</a>
        <a href="RetrieveProduct" class="navlink">Shop</a>
        <a href="PromotionServlet?url=promotion" class="navlink">Promotion</a>
        <a href="about-us.php" class="navlink">About</a>
        <a href="contact.php" class="navlink">Contact</a>
    </div>
    <div id="user-features">
        <a href="OrderServlet?url=cart"><i class="ti ti-shopping-cart-filled"></i>Cart</a>
        <a href="ProfileDetail"><i class="ti ti-user-filled"></i>Profile</a>
    </div>
    <script src="js/headerAnimation.js"></script>
</header>