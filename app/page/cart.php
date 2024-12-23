<?php
$_title = 'Cart';
$_css = '../css/cart.css';
require '../_base.php';
include '../_head.php';

$orderItems = [];
if (is_post()) {
    $btn = req('btn');

    if ($btn == 'clear') {
        set_cart();
        temp('popup-msg', ['msg' => 'Cart Cleared Successfully', 'isSuccess' => true]);
        redirect('?');
    } else if (count(get_cart())) {
        redirect('?');
    } else {
        temp('popup-msg', ['msg' => 'Cart is Empty', 'isSuccess' => false]);
        redirect('?');
    }
}

?>

<h1 class="h1 header-banner">Cart</h1>
<div class="d-flex flex-direction-row justify-content-center">
    <button id="paymentbtn" data-post="payment.php">Proceed to Payment</button>
    <button id="clearbtn" data-post="?btn=clear">Clear Cart</button>
</div>
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
        $count = 0;
        $total = 0;

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

            $subtotal = $p->price * $quantity;
            $count += $quantity;
            $total += $subtotal;
            ?>
            <tr class="available-product">
                <!-- Product Image + Name -->
                <td>
                    <div class="text-left d-flex align-items-center">
                        <img class="product-img" src="../uploads/product_images/<?= $pImage ?>" alt="<?= $pName ?>">
                        <span class="product-name"><?= $pName ?></span>
                    </div>
                </td>
                <!-- Product Price -->
                <td class="price">
                    <?= number_format($pPrice, 2) ?>
                </td>
                <!-- Product Qty + Modify Qty -->
                <td class="quantity">
                    <div class='d-flex align-items-center justify-content-space-around'>
                        <i class="ti ti-minus cursor-pointer" data-product-id="<?= $pID ?>" data-action="decrease"></i>
                        <span class="quantity-value" id="quantity-<?= $pID?>"><?= $quantity ?></span>
                        <i class="ti ti-plus cursor-pointer" data-product-id="<?= $pID ?>" data-action="increase"></i>
                    </div>
                </td>
                <!-- Sub-total -->
                <td class='total-price' id="subtotal-<?= $pID ?>">
                    <?= number_format($subtotal, 2) ?>
                </td>
                <!-- Remove -->
                <td class="action">
                    <form id='removeForm<?= $pID ?>' action='OrderServlet' method='POST'>
                        <input name='url' value='cart' type='hidden'>
                        <input name='action' value='delete' type='hidden'>
                        <input name='orderItemId' value='<?= $pID ?>' type='hidden'>
                        <button class="cart-remove-btn" type="button">
                            Remove from Cart
                        </button>

                    </form>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<script src="../js/cart.js"></script>
<?php include '../_foot.php'; ?>