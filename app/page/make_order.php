<?php
require_once '../_base.php';
// Get the order details from the request

reset_user();
$order_id = get('order_id');
$order_items = get('order_items');
$promo_id = get('promo_id');
$promo_amount = get('promo_amount');
$subtotal = get('subtotal');
$shipping_fee = get('shipping_fee');
$total = get('total');
$paymentMethod = get('payment_method');

set_cart();

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
// Handle promotion usage, if applicable
if ($promo_id) {
    $get_promo_stmt = $_db->prepare("
        SELECT promotion_records 
        FROM customers 
        WHERE customer_id = ?
    ");
    $get_promo_stmt->execute([$customer_id]);
    $promo_record = $get_promo_stmt->fetch(PDO::FETCH_ASSOC);

    if ($promo_record && isset($promo_record['promotion_records'])) {
        // Decode the JSON string into an associative array
        $promo_records = json_decode($promo_record['promotion_records'], true);

        // Check if the promo_id exists in the decoded array
        if (isset($promo_records[$promo_id])) {
            // Decrement the promoLimit, ensuring it does not go below 0
            $currentLimit = (int)$promo_records[$promo_id]['promoLimit'];
            $promo_records[$promo_id]['promoLimit'] = max(0, $currentLimit - 1);

            // Update the database with the modified JSON data
            $promo_update_stmt = $_db->prepare("
                UPDATE customers 
                SET promotion_records = ? 
                WHERE customer_id = ?
            ");

            if ($promo_update_stmt->execute([json_encode($promo_records), $customer_id])) {
                echo 'Promotion usage updated successfully.';
            } else {
                echo 'Failed to update promotion usage.';
            }
        } else {
            echo 'Promotion ID not found in the records.';
        }
    } else {
        echo 'No promotion records found for this customer.';
    }
}

redirect("payment_success.php?order_id=$order_id");
?>