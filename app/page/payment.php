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

require_login();

?>
<h1 class="h1 header-banner">Payment</h1>
<div class="d-flex justify-content-space-evenly">
    <div id="billing-details-container">
        <h3>Billing Details</h3>
        <?php
        $stmt = $_db->prepare('SELECT * FROM customers WHERE customer_id = ?');
        $stmt->execute([$_user->customer_id]);
        $currentUser = $stmt->fetch();

        $uName = $currentUser->username;
        $uEmail = $currentUser->email;
        $uPhone = $currentUser->contact_num;

        $paymentMethod = [];
        $addressOption = [];

        if ($currentUser->banks) {
            $banks = json_decode($currentUser->banks, true);

            foreach ($banks as $index => $bank) {
                $paymentMethod[$bank['accNum']] = $bank['accNum'];
            }
        }

        if ($currentUser->addresses) {
            $addresses = json_decode($currentUser->addresses, true);

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
            <label for="uAddress" class="normal-label">or Manually:</label>
            <?= html_textarea('uAddress', "class='bg-input-box w-100' rows='4' cols='50'") ?>

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
                    <td>RM&nbsp;<?= priceFormat($pTotal + $shippingTotal) ?></td>
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
<script src="../js/payment.js"></script>

<?php include '../_foot.php'; ?>