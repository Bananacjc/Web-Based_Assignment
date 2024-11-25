<%@page contentType="text/html" pageEncoding="UTF-8"%>
<?php
require '../_base.php';
$_title = 'Cart';
$_css = '../css/cart.css';
include '../_head.php';
?>
<div class="container">
    <h1>Login ERROR</h1>
    <p>Sorry, login failed.</p>
    <p>Please try the following:</p>
    <ul>
        <li>Check your spelling</li>
        <li>Return to the <a href="/index.php">home page</a></li>
    </ul>
    <p>If you believe this is an error, please <a href="contact.php">contact support</a>.</p>
</div>
<?php include '../_foot.php'; ?>