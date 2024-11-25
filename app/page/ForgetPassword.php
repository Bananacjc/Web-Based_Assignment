<?php
require '../_base.php';
$_title = 'Cart';
$_css = '../css/cart.css';
include '../_head.php';
?>
<form action="ForgetPassword" method="POST">
    <label for="email" class="label">Email:</label>
    <input type="text" name="email" value="" class="input-box" spellcheck="false" required />
    <button type="submit">Request OTP</button>
</form>
<?php include '../_foot.php'; ?>