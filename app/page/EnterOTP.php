<?php
$_title = 'Cart';
$_css = '../css/home.css';
require '../_base.php';
include '../_head.php';
?>
<form action="ValidateOTP" method="POST">
    <label>Enter your OTP number:</label>
    <input type="text" name="OTP" required>
    <button type="submit">Submit</button>
</form>
<?php include '../_foot.php'; ?>