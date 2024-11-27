<?php
$_title = 'Payment';
$_css = '../css/payment.css';
require '../_base.php';
include '../_head.php';
?>
<h1 id="payment-title">Payment</h1>
<div id="payment-container">
    <div id="billing-details-container">
        <h3>Billing Details</h3>
        <select class="flex-item payment-method-select" name="selectPayment">
            <option value="">Choose a payment method</option>
            <option value="bank">Bank</option>
            <option value="ewallet">E Wallet</option>
            <option value="cash">Cash on delivery</option>
        </select>
        <div class="billing-detail-container">
            <label for="name" class="normal-label">Name</label>
            <input type="text" name="name" value="<%= customer.getCustomerName()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
        </div>
        <div class="billing-detail-container">
            <label for="phone" class="normal-label">Phone</label>
            <input type="text" name="phone" value="<%= customer.getContactNumber()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
        </div>
        <div class="billing-detail-container">
            <label for="address" class="normal-label">Address</label>
            <textarea name="address" class="bg-input-box" rows="4" cols="50" placeholder="<%= customer.getAddress()%>"></textarea>
        </div>
        <div class="payment-method-container" id="bank-detail" style="display: none;">
            <div class="billing-detail-container">
                <label for="name-in-bank" class="normal-label">Name in Bank</label>
                <input type="text" name="name-in-bank" value="<%= bank.getName()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
            </div>
            <div class="billing-detail-container">
                <label for="bank-acc-num" class="normal-label">Bank Account Number</label>
                <input type="text" name="bank-acc-num" value="<%= bank.getAccNum()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
            </div>
            <div class="billing-detail-container">
                <label for="card-type" class="normal-label">Card Type</label>
                <input type="text" name="card-type" value="<%= bank.getCardType()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
            </div>
        </div>
        <div class="payment-method-container" id="ewallet-detail" style="display: none;">
            <div class="billing-detail-container">
                <label for="name-in-ewallet" class="normal-label">Name in E-Wallet</label>
                <input type="text" name="name-in-ewallet" value="<%= ewallet.getName()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
            </div>
            <div class="billing-detail-container">
                <label for="ewallet-phone-num" class="normal-label">E-Wallet Phone Number</label>
                <input type="text" name="ewallet-phone-num" value="<%= ewallet.getPhone()%>" class="sm-input-box" spellcheck="false" readonly tabindex="-1" />
            </div>
        </div>
    </div>

    <div id="order-summary-container">
        <h3>Order Summary</h3>
        <div id="flex-container">
            <h5 class="flex-item order-header">PRODUCT</h5>
            <h5 class="flex-item order-header">TOTAL (RM)</h5>
            <!-- <%
                    List<Orderitems> orderItems = (List<Orderitems>) request.getAttribute("orderItemList");
                    List<Product> products = (List<Product>) request.getAttribute("products");
                    if (orderItems != null && products != null) {
                        for (Orderitems orderItem : orderItems) {
                            for (Product product : products) {
                                if (orderItem.getProductId().equals(product.getProductId())) {
                    %> -->
            <p class="flex-item product-name"><%= product.getProductName()%> x <%= orderItem.getQuantity()%></p>
            <p class="flex-item product-price"><%= orderItem.getTotalPrice(product.getPrice(), orderItem.getQuantity())%></p>
            <!-- <%
                                }
                            }
                        }
                    }
                    %> -->
            <h5 class="flex-item subtotal">SUBTOTAL</h5>
            <h5 class="flex-item subtotal">${subTotal}</h5>
            <h5 class="flex-item">Shipping Fee</h5>
            <h5 class="flex-item">+ ${shippingFee}</h5>
            <select class="flex-item promo-code-select" onchange="fetchPromoDetails()" name="promoCode">
                <option value="-1">Choose a promo code...</option>
                <%
                        List<Promotionrecord> records = (List<Promotionrecord>) request.getAttribute("promotionRecordList");
                        List<Promotion> promotions = (List<Promotion>) request.getAttribute("promotions");
                        if (records != null && promotions != null) {
                            for (Promotionrecord record : records) {
                                for (Promotion promotion : promotions) {
                                    if (record.getPromotionrecordPK().getPromoId()== promotion.getPromoId()) {
                        %>
                <option value="<%= promotion.getPromoId()%>"><%= promotion.getPromoName()%></option>
                <%
                                    }
                                }
                            }
                        }
                        %>
            </select>
            <p class="flex-item" id="promo-amount">- RM ${promoAmount}</p>
            <h5 class="flex-item total-price">TOTAL</h5>
            <h5 id="total" class="flex-item total-price">RM ${total}</h5>
        </div>
    </div>
</div>
<form action="PromotionServlet" method="POST" id="selectForm">
    <input type="hidden" name="url" value="payment">
    <input type="hidden" name="action" value="select">
    <input type="hidden" name="promoId" value="">
    <input type="hidden" name="subTotal" value="${subTotal}">
</form>
<div>
    <form id="payForm" action="PaymentServlet" method="POST">
        <input type="hidden" name="paymentMethod" value="">
        <button class="paybtn" onclick="pay()">Pay</button>
    </form>
</div>
<script src="js/paymentMethod.js"></script>
<script>
    function pay() {
        var inputElements = document.getElementsByName('paymentMethod');
        var selectElements = document.getElementsByName('selectPayment');
        if (selectElements[0].value === "bank") {
            inputElements[0].value = "0";
        }
        if (selectElements[0].value === "ewallet") {
            inputElements[0].value = "1";
        }
        if (selectElements[0].value === "cash") {
            inputElements[0].value = "2";
        }
        var form = document.getElementById("payForm");
        form.submit();
    }

    function fetchPromoDetails() {
        var inputElements = document.getElementsByName('promoId');
        var selectElements = document.getElementsByName('promoCode');
        inputElements[0].value = selectElements[0].value;
        if (selectElements[0].value === "-1") {
            return;
        }
        var form = document.getElementById("selectForm");
        form.submit();
    }

    var selectedPromoId = '<%= session.getAttribute("promoId") %>';

    document.addEventListener('DOMContentLoaded', function() {
        selectPromoCode(selectedPromoId);
    });

    function selectPromoCode(promoId) {
        if (promoId !== null && promoId !== "-1") {
            var selectElement = document.querySelector('.promo-code-select');
            selectElement.value = promoId;
        } else {
            var selectElement = document.querySelector('.promo-code-select');
            selectElement.value = "-1";
        }
    }
</script>
<?php include '../_foot.php'; ?>