<?php
$_title = 'Payment';
$_css = '../css/payment.css';
require '../_base.php';
include '../_head.php';

if (is_post()) {
}

// Initialize cart
$cart = get_cart();

// Prepare Stmt
$get_product_stmt = 'SELECT * FROM products WHERE product_id = ?';

if (!$cart) {
    temp('popup-msg', ['msg' => 'Cart is Empty', 'isSuccess' => false]);
    redirect('cart.php');
}

?>
<h1 class="h1 header-banner">Payment</h1>
<div class="d-flex justify-content-space-evenly">
    <!-- <div id="billing-details-container">
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
    </div> -->

    <div id='order-summary-container'>
        <h3 class="text-center">Order Summary</h3>
        <div class="d-flex flex-wrap w-100 justify-content-space-between">
            <table id="summary-table" class="w-100">
                <tr>
                    <th>PRODUCT</th>
                    <th>PRICE (RM)</th>
                    <th>QUANTITY</th>
                    <th>SUBTOTAL (RM)</th>
                </tr>
                <tr>
                    <td colspan="5">
                        <hr>
                    </td>
                </tr>
                <?php
                $pTotal = 0;
                $shippingTotal = 0; //TODO
                $stmt = $_db->prepare($get_product_stmt);
                ?>
                <?php foreach ($cart as $id => $quantity): ?>
                    <?php
                    $stmt->execute([$id]);
                    $p = $stmt->fetch();

                    $pName = $p->product_name;
                    $pPrice = $p->price;
                    $pSubtotal = $pPrice * $quantity;
                    $pTotal += $pSubtotal;
                    ?>
                    <tr>
                        <td><?= $pName; ?></td>
                        <td><?= priceFormat($pPrice); ?></td>
                        <td><?= $quantity ?></td>
                        <td><?= priceFormat($pSubtotal); ?></td>
                    </tr>
                <?php endforeach ?>
                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">Products Subtotal :</td>
                    <td>RM&nbsp;<?= priceFormat($pTotal) ?></td>
                </tr>
                <tr>
                    <td colspan="3">Shipping Subtotal :</td>
                    <td>RM&nbsp;<?= priceFormat($shippingTotal) ?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <?php
                        $promo_code = [
                            'promoID_1' => 'promoNamae_1',
                            'promoID_2' => 'promoNamae_2',
                            'promoID_3' => 'promoNamae_3'
                        ]
                        ?>
                        <?= html_select('promo', $promo_code, '- Choose a promo code -', 'data-select-onchange') ?>
                    </td>
                    <td>AJAX</td>
                </tr>
                <tr>
                    <td colspan="3">TOTAL PAYMENT :</td>
                    <td>RM&nbsp;<?= priceFormat($pTotal + $shippingTotal)?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">
                        <form action="payment_checkout.php" target="_blank">
                            <button>Pay</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>




<!-- <form action="PromotionServlet" method="POST" id="selectForm">
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
</script> -->
<?php include '../_foot.php'; ?>