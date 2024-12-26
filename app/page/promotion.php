<!DOCTYPE html>
<html>
<?php
$_title = 'Promotion';
$_css = '../css/promotion.css';
require '../_base.php';
include '../_head.php';

?>

<h1 class="h1 header-banner">Promotions</h1>
<div id="promo-container">
    <div class="promo-card">
        <div class="promo-image">
            <img src="../images/login-products.png" alt="Promotion Image">
        </div>
        <div class="promo-details">
            <h2 class="promo-name">New Year Bonanza</h2>
            <p class="promo-code">Code: NYBONANZA2024</p>
            <p class="promo-desc">Kickstart the new year with amazing savings!</p>
            <p class="promo-req">Requirement: Minimum purchase of RM50.</p>
            <p class="promo-discount">Discount: RM10 Off</p>
            <p class="promo-limit">Limit: Can be used up to 3 times per user.</p>
            <p class="promo-duration">Expiry Date: 31st January 2024</p>
            <form action="PromotionServlet" method="POST">
                <input type="hidden" name="url" value="promotion">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="promoId" value="NYBONANZA2024">
                <button class="promo-btn" type="submit">Get Voucher Code</button>
            </form>
        </div>
    </div>
    <div class="promo-card">
    <div class="promo-image">
        <img src="../images/c-img-3.webp" alt="Promotion Image">
    </div>
    <div class="promo-details">
        <h2 class="promo-name">Festive Frenzy</h2>
        <p class="promo-code">Code: FESTIVE20</p>
        <p class="promo-desc">Celebrate the holidays with a special discount.</p>
        <p class="promo-req">Requirement: Minimum purchase of RM100.</p>
        <p class="promo-discount">Discount: 20% Off</p>
        <p class="promo-limit">Limit: Can be used once per user.</p>
        <p class="promo-duration">Expiry Date: 25th December 2024</p>
        <form action="PromotionServlet" method="POST">
            <input type="hidden" name="url" value="promotion">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="promoId" value="FESTIVE20">
            <button class="promo-btn" type="submit">Get Voucher Code</button>
        </form>
    </div>
</div>
<div class="promo-card">
    <div class="promo-image">
        <img src="../images/logo.png" alt="Promotion Image">
    </div>
    <div class="promo-details">
        <h2 class="promo-name">BananaSIS Special</h2>
        <p class="promo-code">Code: BANANA5</p>
        <p class="promo-desc">Enjoy exclusive savings, just for you!</p>
        <p class="promo-req">Requirement: No minimum purchase required.</p>
        <p class="promo-discount">Discount: RM5 Off</p>
        <p class="promo-limit">Limit: Unlimited usage per user.</p>
        <p class="promo-duration">Expiry Date: 30th June 2024</p>
        <form action="PromotionServlet" method="POST">
            <input type="hidden" name="url" value="promotion">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="promoId" value="BANANA5">
            <button class="promo-btn" type="submit">Get Voucher Code</button>
        </form>
    </div>
</div>


</div>
<?php include '../_foot.php'; ?>
</body>

</html>