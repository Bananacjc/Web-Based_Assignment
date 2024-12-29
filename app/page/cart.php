<?php

$_title = 'Cart';
$_css = '../css/cart.css';
require '../_base.php';
include '../_head.php';

if (is_post()) {
    $btn    = req('btn');
    $target = req('target');
    $count  = count(get_cart());


    if ($count > 0) {
        if ($btn == 'clear') {
            set_cart();
            temp('popup-msg', ['msg' => 'Cart Cleared Successfully', 'isSuccess' => true]);
            redirect('?');
        } else if ($btn == 'payment') {
            redirect('payment.php');
        }
    } else {
        temp('popup-msg', ['msg' => 'Cart is Empty', 'isSuccess' => false]);
        redirect('?');
    }
}

require_login();
?>

<h1 class="h1 header-banner">Cart</h1>
<div class="d-flex flex-direction-row justify-content-center">
    <button id="paymentbtn" data-post="?btn=payment">Proceed to Payment</button>
    <button id="clearbtn" data-post="?btn=clear">Clear Cart</button>
</div>
<table class="rounded-table cart-table">
    <thead>
        <tr>
            <th class="text-left">PRODUCT</th>
            <th>PRICE (RM)</th>
            <th>QUANTITY</th>
            <th>SUB-TOTAL (RM)</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $pCount = 0;
        $pTotal = 0;

        $cart = get_cart();
        $stmt = $_db->prepare('SELECT * FROM products WHERE product_id = ?');
        ?>

        <?php foreach ($cart as $id => $quantity): ?>
            <?php
            $stmt->execute([$id]);
            $p = $stmt->fetch();

            $pID    = $p->product_id;
            $pImage = $p->product_image;
            $pName  = $p->product_name;
            $pPrice = $p->price;

            $pSubtotal = $pPrice* $quantity;
            $pCount += $quantity;
            $pTotal += $pSubtotal;
            ?>
            <tr class="available-product" id="product-<?= $pID ?>">
                <!-- Product Image + Name -->
                <td>
                    <div class="text-left d-flex align-items-center">
                        <img class="product-img" src="../uploads/product_images/<?= $pImage ?>" alt="<?= $pName ?>">
                        <span class="product-name"><?= $pName ?></span>
                    </div>
                </td>
                <!-- Product Price -->
                <td class="price">
                    <?= priceFormat($pPrice) ?>
                </td>
                <!-- Product Qty + Modify Qty -->
                <td class="quantity">
                    <div class='d-flex align-items-center justify-content-space-around'>
                        <i class="ti ti-minus cursor-pointer" data-product-id="<?= $pID ?>" data-action="decrease"></i>
                        <?php $GLOBALS['quantity-' . $pID] = $quantity; ?>
                        <?= html_number('quantity-' . $pID, 1, '', 1, "class='quantity-value' data-product-id='$pID' data-action='change'") ?>
                        
                        <i class="ti ti-plus cursor-pointer" data-product-id="<?= $pID ?>" data-action="increase"></i>
                    </div>
                </td>
                <!-- Sub-total -->
                <td class='total-price' id="subtotal-<?= $pID ?>">
                    <?= priceFormat($pSubtotal) ?>
                </td>
                <!-- Remove -->
                <td class="action">
                    <button class="cart-remove-btn" type="button" data-product-id="<?= $pID ?>" data-action="remove">
                        Remove from Cart
                    </button>


                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($pTotal): ?>
            <tr>
                <td colspan="3" class="text-right">TOTAL:</td>
                <td id="cart-total">
                    <?= priceFormat($pTotal) ?>
                </td>
                <td></td>
            </tr>
        <?php endif ?>

        <tr id="nothing-to-show">
        <?php if (!$cart): ?>
            <td colspan="5">
                <h3>Nothing is here, Try add <a href="../page/shop.php" class='text-decoration-none text-green-darker hover-underline-anim'>something</a>!</h3>
            </td>
        <?php endif ?>
        </tr>


    </tbody>
</table>
<script src="../js/cart.js"></script>
<?php include '../_foot.php'; ?>