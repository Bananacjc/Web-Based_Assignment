<?php
$_title = 'Payment';
$_css = '../css/payment.css';
require '../_base.php';
include '../_head.php';

require_login();
reset_user();

// Initialize cart
$cart = get_cart();

if (!$cart) {
    temp('popup-msg', ['msg' => 'Cart is Empty', 'isSuccess' => false]);
    redirect('cart.php');
}



?>
<h1 class="h1 header-banner">Payment</h1>
<div class="d-flex justify-content-space-evenly">
    <div id="billing-details-container">
        <h3>Billing Details</h3>
        <?php
        $uName = $_user->username;
        $uEmail = $_user->email;
        $uPhone = $_user->contact_num;

        $paymentMethod = [];
        $addressOption = [];

        if ($_user->banks) {
            $banks = json_decode($_user->banks, true);

            foreach ($banks as $bank) {
                $paymentMethod[$bank['accNum']] = $bank['accNum'];
            }
        }

        if ($_user->addresses) {
            $addresses = json_decode($_user->addresses, true);

            foreach ($addresses as $index => $address) {
                $addressStr = $address['line_1'] . ', ' .
                $address['village'] . ', ' .
                $address['postal_code'] . ' ' .
                $address['city'] . ', ' .
                $address['state'];

                $addressOption[$addressStr] = $addressStr;
                    
            }
        }
        ?>

        <form id="payment-method-form" class="billing-detail-container">
            <label for="uPaymentMethod" class="normal-label">Payment Method</label>
            <?= html_select('selectPayment', $paymentMethod, '- Decide Later -') ?>

            <div id="bank-detail-container" class="d-none">
                <div id="bank-detail-subcontainer" class="d-flex flex-direction-row">
                    <div class="billing-detail-container" style="width: 40%; padding-right: 10px; ">
                        <label for="accNum" class="normal-label">Account Number</label>
                        <?= html_text('accNum', "class='sm-input-box' spellcheck='false' readonly ") ?>
                    </div>
                    <div class="billing-detail-container" style="width: 20%; padding-right: 10px;">
                        <label for="cvvNum" class="normal-label">CVV</label>
                        <?= html_text('cvvNum', "class='sm-input-box' spellcheck='false' readonly ") ?>
                    </div>
                    <div class="billing-detail-container" style="width: 20%; user-select: none;">
                        <label for="exDate" class="normal-label">Expiry Date</label>
                        <?= html_text('exDate', "class='sm-input-box' spellcheck='false' readonly ") ?>
                    </div>
                </div>
            </div>
        </form>

        <div class="billing-detail-container">
            <label for="uName" class="normal-label">Name</label>
            <?php $GLOBALS['uName'] = $uName ?>
            <?= html_text('uName', "class='sm-input-box w-50' spellcheck='false' readonly") ?>
        </div>
        <div class="billing-detail-container">
            <label for="uPhone" class="normal-label">Phone</label>
            <?php $GLOBALS['uPhone'] = $uPhone ?>
            <?= html_text('uPhone', "class='sm-input-box w-50' spellcheck='false' readonly") ?>
        </div>
        <form class="billing-detail-container">
            <label for="uAddress" class="normal-label">Address</label>
            <?php if ($addressOption) {
                html_select('selectAddress', $addressOption, '- Please choose an address -');
            } ?>
            <br>
            <label for="uAddress" class="normal-label">or<br><br>Enter Manually:&nbsp;<span class="text-red">*</span></label>
            <div>
                <div class="address-subcontainer">
                    <label for="line-1" class='address-label'>Address Line 1</label><br>
                    <input id="line-1" type="text" name="line_1" class="sm-input-box w-100" placeholder=" " required />
                </div>
                <div class="address-subcontainer">
                    <label for="village" class='address-label'>Village</label><br>
                    <input id="village" type="text" name="village" class="sm-input-box w-100" placeholder=" " />

                </div>
                <div class="d-flex flex-direction-row">
                    <div class="address-subcontainer">
                        <label for="postal-code" class='address-label'>Postal Code</label><br>
                        <input id="postal-code" type="text" name="postal_code" class="sm-input-box w-100" placeholder=" " required />

                    </div>
                    <div class="address-subcontainer">
                        <label for="city" class='address-label'>City</label><br>
                        <input id="city" type="text" name="city" class="sm-input-box w-100" placeholder=" " required />

                    </div>
                </div>
                <div class="address-subcontainer" style="position: relative;">
                    <label for="state" class='address-label'>State</label><br>
                    <input id="state" type="text" name="state" class="sm-input-box w-50" placeholder=" " required />

                </div>
            </div>
            <div id="map-container">
                <div id="map" style="width: 100%; height: 300px; margin-top: 20px;"></div>
                <button class="btn" id="use-my-location-btn">Use My Location</button>
                <div id="coordinates" class="d-none">
                    <p>Latitude: <span id="latitude">0</span></p>
                    <p>Longitude: <span id="longitude">0</span></p>
                </div>
            </div>
        </form>
    </div>

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
                $shippingFee = 0; //TODO
                $stmt = $_db->prepare('SELECT * FROM products WHERE product_id = ?');
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
                        <td class="number-figure"><?= priceFormat($pPrice); ?></td>
                        <td><?= $quantity ?></td>
                        <td class="number-figure"><?= priceFormat($pSubtotal); ?></td>
                    </tr>
                <?php endforeach ?>
                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">Products Subtotal (RM):</td>
                    <td>
                        <span id="pTotal" class="number-figure"><?= priceFormat($pTotal) ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">Shipping Fee (RM):</td>
                    <td>
                        <span id="pShippingFee" class="number-figure"><?= priceFormat($shippingFee) ?></span>
                    </td>
                </tr>
                <tr>
                    <?php
                    $promotionsOption = [];
                    $promotions = [];
                    $uPromotions = [];

                    $stmt = $_db->prepare('SELECT end_date, promo_code, requirement FROM promotions WHERE promo_id = ?');

                    if ($_user->promotion_records) {
                        $uPromotions = json_decode($_user->promotion_records, true);

                        foreach ($uPromotions as $promotionID => $promotionLimit) {
                            $stmt->execute([$promotionID]);
                            $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($promotionLimit > 0 && $promotion['end_date'] < new DateTime() && $pTotal > $promotion['requirement']) {
                                $promotionsOption[$promotionID] = $promotion['promo_code'];
                            }
                        }
                    }
                    ?>
                    <td colspan="3">
                        <?php if ($promotionsOption): ?>
                            <?= html_select('selectPromo', $promotionsOption, 'Don\'t use promo code', 'data-select-onchange') ?> :
                        <?php else: ?>
                            No promotion code available :
                        <?php endif ?>
                    </td>
                    <td class="text-green-darker">
                        -RM <span id="uPromo" class="number-figure">0.00</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">TOTAL PAYMENT (RM):</td>
                    <td><span id="total-payment" class="number-figure"><?= priceFormat($pTotal + $shippingFee) ?></span></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">
                        <form id="checkout-form" action="payment_checkout.php" target="_blank" method="post">
                            <?= html_hidden('uName'); ?>
                            <?= html_hidden('uEmail'); ?>
                            <?= html_hidden('uPhone'); ?>
                            <?php $cart = json_encode($cart); ?>
                            <?= html_hidden('cart') ?>
                            <!-- Hidden bank field -->
                            <?= html_hidden('hiddenAccNum'); ?>
                            <?= html_hidden('hideenCvvNum'); ?>
                            <?= html_hidden('hidenExDate'); ?>
                            <!-- Hidden address field -->
                            <?= html_hidden('hiddenLine_1'); ?>
                            <?= html_hidden('hiddenVillage'); ?>
                            <?= html_hidden('hiddenPostal_code'); ?>
                            <?= html_hidden('hiddenCity'); ?>
                            <?= html_hidden('hiddenState'); ?>
                            <!-- Additional fee -->
                            <?= html_hidden('hiddenShippingFee'); ?>
                            <?= html_hidden('hiddenPromoID'); ?>
                            <?= html_hidden('hiddenPromoAmount'); ?>
                            <?= html_hidden('hiddenSubtotal'); ?>
                            <?= html_hidden('hiddenTotal'); ?>
                            <button id="pay-button">Pay</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script src="../js/payment.js"></script>
<script src="../js/googleMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFPpOlKxMJuu6PxnVrwxNd1G6vERpptro&libraries=places"></script>

<?php include '../_foot.php'; ?>