<?php
require_once '../_base.php';
// Get the order details from the request
$order_id = get('order_id');
$order_items = get('order_items');
$promo_id = get('promo_id');
$promo_amount = get('promo_amount');
$subtotal = get('subtotal');
$shipping_fee = get('shipping_fee');
$total = get('total');
$paymentMethod = get('payment_method');

if (!$order_id || !$order_items || !$_user->email) {
    die("Invalid request");
}

$order_id = htmlspecialchars($order_id);
$customer_id = $_user->customer_id;

// Decode `order_items` JSON into an associative array
$order_items_decoded = json_decode($order_items, true);
if (!is_array($order_items_decoded)) {
    die("Invalid order items format.");
}

// Convert DateTime to string
$dateString = (new DateTime())->format('Y-m-d H:i:s');

// Insert the order into the `orders` table
$stmt = $_db->prepare("
    INSERT INTO orders 
    (order_id, customer_id, order_items, 
     promo_amount, subtotal, shipping_fee, 
     total, payment_method, order_time, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt->execute([
    $order_id,
    $customer_id,
    json_encode($order_items_decoded), // Save `order_items` as JSON
    $promo_amount,
    $subtotal,
    $shipping_fee,
    $total,
    json_encode($paymentMethod), // Save `paymentMethod` as JSON if needed
    $dateString,
    'PAID'
])) {
    die("Error saving order.");
}

// Update stock and sales count for products
$update_stmt = $_db->prepare("
    UPDATE products 
    SET amount_sold = amount_sold + ?, 
        current_stock = current_stock - ? 
    WHERE product_id = ?
");

foreach ($order_items_decoded as $product_id => $quantity) {
    $update_stmt->execute([$quantity, $quantity, $product_id]);
}

// Handle promotion usage, if applicable
if ($promo_id) {
    $get_promo_stmt = $_db->prepare("
        SELECT promotion_records 
        FROM customers 
        WHERE customer_id = ?
    ");
    $get_promo_stmt->execute([$customer_id]);
    $row = $get_promo_stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['promotion_records']) {
        $promo_records = json_decode($row['promotion_records'], true);
        if (is_array($promo_records) && isset($promo_records[$promo_id]) && is_int($promo_records[$promo_id])) {
            $promo_records[$promo_id] = max(0, $promo_records[$promo_id] - 1);
            $promo_limit_stmt = $_db->prepare("
                UPDATE customers 
                SET promotion_records = ? 
                WHERE customer_id = ?
            ");
            $promo_limit_stmt->execute([
                json_encode($promo_records),
                $customer_id
            ]);
        }
    }
}
redirect("payment_success.php?order_id=$order_id");
?>