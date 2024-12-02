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
            <img src="img/logo.png" alt="Promotion Image">
        </div>
        <div class="promo-details">
            <h2 class="promo-name"></h2>
            <p class="promo-code"></p>
            <p class="promo-desc"></p>
            <p class="promo-req"></p>
            <p class="promo-discount"></p>
            <p class="promo-limit">Limit: Can be used up to times per user.</p>
            <p class="promo-duration">Expiry Date: </p>
            <form action="PromotionServlet" method="POST">
                <input type="hidden" name="url" value="promotion">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="promoId" value="<%= promotion.getPromoId()%>">
                <button class="promo-btn" type="submit">Get Voucher Code</button>
            </form>
        </div>
    </div>

</div>
<?php include '../_foot.php'; ?>
</body>

</html>