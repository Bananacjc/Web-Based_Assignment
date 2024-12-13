<?php
$_title = 'Cart';
$_css = '../css/cart.css';
require '../_base.php';
include '../_head.php';
?>
<h1 class="h1 header-banner">Cart</h1>
<table class="rounded-table cart-table">
    <thead>
        <tr>
            <th class="text-left">PRODUCT</th>
            <th>PRICE (RM)</th>
            <th>QUANTITY</th>
            <th>TOTAL (RM)</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $orderItems = [
            [
                'orderItemID' => 1, 
                'productName' => 'Apple',
                'productImage' => 'img/Apple.webp',
                'price' => 10, 
                'quantity' => 12,
                'totalPrice' => 0
            ],
            [
                'orderItemID' => 2, 
                'productName' => 'Banana',
                'productImage' => 'img/Banana.webp',
                'price' => 8, 
                'quantity' => 5,
                'totalPrice' => 0 
            ]


        ]
        ?>
        <?php foreach ($orderItems as $orderItem): ?>
        <tr class='unavailable-product'>
            <td>
                <!-- Image and name of the product -->
                <div class='text-left d-flex align-items-center'>
                    <img src='<?= $orderItem[' productImage']?>' alt='Product Image' />
                    <span class='product-name'><?= $orderItem['productName'] ?></span>
                </div>
            </td>
            <td class='price'><?= $orderItem['price'] ?></td>
            <td class='quantity'>
                <!-- Quantity control -->
                <div class='d-flex align-items-center justify-content-space-around'>
                    <form id='minusForm<?= $orderItem['orderItemId'] ?>' action='OrderServlet' method='POST'>
                        <input name='url' value='cart' type='hidden'>
                        <input name='action' value='update' type='hidden'>
                        <input name='orderItemId' value='<?= $orderItem[' orderItemId'] ?>' type='hidden'>
                        <input name='m' value='minus' type='hidden'>
                        <i class='ti ti-minus cursor-pointer' onclick='submitForm("minusForm<?= $orderItem["orderItemId"]?>")'></i>
                    </form>
                    <span class='quantity-value'><?= $orderItem['quantity'] ?></span>
                    <form id='plusForm<?= $orderItem['orderItemId'] ?>' action='OrderServlet' method='POST'>
                        <input name='url' value='cart' type='hidden'>
                        <input name='action' value='update' type='hidden'>
                        <input name='orderItemId' value='<?= $orderItem[' orderItemId'] ?>' type='hidden'>
                        <input name='m' value='plus' type='hidden'>
                        <i class='ti ti-plus cursor-pointer' onclick='submitForm("minusForm<?= $orderItem["orderItemId"]?>")'></i>
                    </form>
                </div>
            </td>
            <td class='total-price'><?= $orderItem['price'] * $orderItem['quantity'] ?></td>
            <td class='action'>
                <!-- Remove icon for action -->
                <form id='removeForm<?= $orderItem['orderItemId'] ?>' action='OrderServlet' method='POST'>
                    <input name='url' value='cart' type='hidden'>
                    <input name='action' value='delete' type='hidden'>
                    <input name='orderItemId' value='<?= $orderItem[' orderItemId'] ?>' type='hidden'>
                    <i class='ti ti-x cursor-pointer' onclick='submitForm("minusForm<?= $orderItem["orderItemId"]?>")'></i>
                </form>
            </td>
        </tr>
        <?php endforeach?>

    </tbody>

</table>
<div class="button-container">
    <form action="OrderServlet" method="POST">
        <input name="url" value="cart" type="hidden">
        <input name="action" value="clear" type="hidden">
        <button id="clearbtn" type="submit">Clear Cart</button>
    </form>
    <button id="paymentbtn" onclick="goToPayment()">Proceed to Payment</button>
</div>
<?php include '../_foot.php'; ?>