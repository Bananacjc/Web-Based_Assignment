<?php
require '../_base.php';
$_title = 'Promotion';
$_css = '../css/header.css';
include '../_head.php';
?>
<header id="header">
    <div class="navbar d-flex">
        <a href="index.php" class="logo">
            <img src="../images/logo.png" alt="Logo" width="60" height="60" />
            <p class="text-yellow-light">BANANA</p>
            <p class="text-green-light">SIS</p>
        </a>
        <a href="index.php" class="navlink hover-underline-anim">Home</a>
        <a href="RetrieveProduct" class="navlink hover-underline-anim">Shop</a>
        <a href="PromotionServlet?url=promotion" class="navlink hover-underline-anim">Promotion</a>
        <a href="about-us.php" class="navlink hover-underline-anim">About</a>
        <a href="contact.php" class="navlink hover-underline-anim">Contact</a>
    </div>
    <div id="user-features">
        <a href="OrderServlet?url=cart"><i class="ti ti-shopping-cart-filled"></i>Cart</a>
        <a href="ProfileDetail"><i class="ti ti-user-filled"></i>Profile</a>
    </div>
    <script src="js/headerAnimation.js"></script>
</header>