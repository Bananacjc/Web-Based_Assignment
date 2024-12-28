<?php
$_title = 'Receipt';
$_css = '../css/payment.css';
require '../_base.php';
include '../_head.php';

require_login();

// Fetch the order ID from the URL query parameter
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("<h3>Invalid order ID</h3>");
}

// Fetch order details
$stmt = $_db->prepare("
    SELECT o.*, c.username, c.email 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("<h3>Order not found</h3>");
}

// Decode order items from JSON
$order_items = json_decode($order['order_items'], true);
?>

<h1 class="h1 header-banner">Receipt</h1>
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

            // Fetch product details for each product in the order
            $stmt = $_db->prepare("SELECT product_name, price FROM products WHERE product_id = ?");
            foreach ($order_items as $product_id => $quantity) {
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $pName = $product['product_name'];
                    $pPrice = $product['price'];
                    $pSubtotal = $pPrice * $quantity;
                    $pTotal += $pSubtotal;
            ?>
                    <tr>
                        <td><?= htmlspecialchars($pName); ?></td>
                        <td class="number-figure"><?= number_format($pPrice, 2); ?></td>
                        <td><?= htmlspecialchars($quantity); ?></td>
                        <td class="number-figure"><?= number_format($pSubtotal, 2); ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td colspan="4">
                    <hr>
                </td>
            </tr>
            <tr>
                <td colspan="3">Products Subtotal (RM):</td>
                <td>
                    <span id="pTotal" class="number-figure"><?= number_format($pTotal, 2); ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">Shipping Fee (RM):</td>
                <td>
                    <span id="pShippingFee" class="number-figure"><?= number_format($order['shipping_fee'], 2); ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">Promotion Discount (RM):</td>
                <td class="text-green-darker">
                    RM <span id="uPromo" class="number-figure"><?= number_format($order['promo_amount'] ?? 0, 2); ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr>
                </td>
            </tr>
            <tr>
                <td colspan="3">TOTAL PAYMENT (RM):</td>
                <td><span id="total-payment" class="number-figure"><?= number_format($order['total'], 2); ?></span></td>
            </tr>
        </table>
    </div>
</div>

<?php include '../_foot.php'; ?>
