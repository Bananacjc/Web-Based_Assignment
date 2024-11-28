<?php
$_title = 'Cart';
$_css = '../css/cart.css';
require '../_base.php';
include '../_head.php';
?>
<h1 id="cart-title">Cart</h1>
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
        <tr class="unavailable-product">
            <td>
                <!-- Image and name of the product -->
                <div class="text-left d-flex align-items-center">
                    <img src="img/c-img-1.webp" alt="Product Image" />
                    <span class="product-name">Apple</span>
                </div>
            </td>
            <td class="price">10</td>
            <td class="quantity">
                <!-- Quantity control -->
                <div class="d-flex align-items-center justify-content-space-around">
                    <form id="minusForm<%= orderItem.getOrderItemId()%>" action="OrderServlet" method="POST">
                        <input name="url" value="cart" type="hidden">
                        <input name="action" value="update" type="hidden">
                        <input name="orderItemId" value="<%= orderItem.getOrderItemId()%>" type="hidden">
                        <input name="m" value="minus" type="hidden">
                        <i class="ti ti-minus cursor-pointer" onclick="submitForm('minusForm<%= orderItem.getOrderItemId()%>')"></i>
                    </form>
                    <span class="quantity-value">12</span>
                    <form id="plusForm<%= orderItem.getOrderItemId()%>" action="OrderServlet" method="POST">
                        <input name="url" value="cart" type="hidden">
                        <input name="action" value="update" type="hidden">
                        <input name="orderItemId" value="<%= orderItem.getOrderItemId()%>" type="hidden">
                        <input name="m" value="plus" type="hidden">
                        <i class="ti ti-plus cursor-pointer" onclick="submitForm('plusForm<%= orderItem.getOrderItemId()%>')"></i>
                    </form>
                </div>
            </td>
            <td class="total-price">12.00</td>
            <td class="action">
                <!-- Remove icon for action -->
                <form id="removeForm<%= orderItem.getOrderItemId()%>" action="OrderServlet" method="POST">
                    <input name="url" value="cart" type="hidden">
                    <input name="action" value="delete" type="hidden">
                    <input name="orderItemId" value="<%= orderItem.getOrderItemId()%>" type="hidden">
                    <i class="ti ti-x cursor-pointer" onclick="submitForm('removeForm<%= orderItem.getOrderItemId()%>')"></i>
                </form>
            </td>
        </tr>
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