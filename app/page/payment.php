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
                $addressOption[$address] = $address;
            }
        }
        ?>
        <div class="billing-detail-container">
            <label for="uPaymentMethod" class="normal-label">Payment Method</label>
            <?= html_select('selectPayment', $paymentMethod, '- Decide Later -') ?>
        </div>
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

        <div class="billing-detail-container">
            <label for="uName" class="normal-label">Name</label>
            <?php $GLOBALS['uName'] = $uName ?>
            <?= html_text('uName', "class='sm-input-box w-50' spellcheck='false'") ?>
        </div>
        <div class="billing-detail-container">
            <label for="uPhone" class="normal-label">Phone</label>
            <?php $GLOBALS['uPhone'] = $uPhone ?>
            <?= html_text('uPhone', "class='sm-input-box w-50' spellcheck='false'") ?>
        </div>
        <div class="billing-detail-container">
            <label for="uAddress" class="normal-label">Address</label>
            <?php if ($addressOption) {
                html_select('selectAddress', $addressOption, '- Decide Later -');
            } ?>
            <br>
            <label for="uAddress" class="normal-label">or Enter Manually:</label>
            <div style="position: relative;">
                <input
                    id="address-input"
                    type="text"
                    placeholder="Enter your address"
                    autocomplete="off"
                    name="address"
                    class="input-box" />
            </div>
        </div>
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
                    <td>RM&nbsp;<span id="pTotal"><?= priceFormat($pTotal) ?></span></td>
                </tr>
                <tr>
                    <td colspan="3">Shipping Fee :</td>
                    <td>RM&nbsp;<span id="pShippingFee"><?= priceFormat($shippingFee) ?></span></td>
                </tr>
                <tr>
                    <?php
                    $promotionsOption = [];
                    $promotions = [];
                    $uPromotions = [];

                    $stmt = $_db->prepare('SELECT end_date, promo_code, requirement FROM promotions WHERE promo_id = ?');

                    if ($_user->promotion_records) {
                        $uPromotions = json_decode($_user->promotion_records, true);

                        foreach($uPromotions as $promotionID => $promotionLimit) {
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
                        RM <span id="uPromo">0.00</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">TOTAL PAYMENT :</td>
                    <td>RM&nbsp;<span id="total-payment"><?= priceFormat($pTotal + $shippingFee) ?></span></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">
                        <form action="payment_checkout.php" target="_blank">
                            <button class="pay-button">Pay</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script src="../js/payment.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFPpOlKxMJuu6PxnVrwxNd1G6vERpptro&libraries=places"></script>

<?php include '../_foot.php'; ?>