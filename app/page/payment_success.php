<?php
$_title = 'Payment Successful';
require '../_base.php';

require_login();

$order_id = get('order_id');

if (!$order_id || !$_user->email) {
    die("Invalid request");
}

$order_id = htmlspecialchars($order_id);
$customer_id = $_user->customer_id;
$order_items = json_encode(get('order_items'));
$promo_id = get('promo_id');
$promo_amount = get('promo_amount');
$subtotal = get('subtotal');
$shipping_fee = get('shipping_fee');
$total = get('total');
$paymentMethod = get('payment_method');


$stmt = $_db->prepare('INSERT INTO orders 
(order_id, customer_id, order_items, 
promo_amount, subtotal, shipping_fee, 
total, payment_method, order_time, `status`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

if ($stmt->execute([$order_id, $customer_id, $order_items, 
    $promo_amount, $subtotal, $shipping_fee,
    $total, $paymentMethod, new Datetime(), 'PAID'])) {

        // Update product
        $update_stmt = $_db->prepare('UPDATE products VALUE amount_sold = ?, current_stock = ? WHERE product_id = ?');
        $get_stock_stmt = $_db->prepare('SELECT current_stock FROM products WHERE product_id = ?');

        foreach(json_decode($order_items, true) as $pID => $quantity) {

            $get_stock_stmt->execute([$pID]);
            $current_stock = $get_stock_stmt->fetch();

            $update_stmt->execute([$quantity, $current_stock - $quantity, $pID]);
        }

        // Reduce promotion uses
        $promo_limit_stmt = $_db->prepare('UPDATE customers VALUE promotion_records = ? WHERE customer_id = ?');
        $get_promo_stmt = $_db->prepare('SELECT promotion_records FROM customers WHERE customer_id = ?');

        $get_promo_stmt->execute([$customer_id]);
        $uPromotions = $get_promo_stmt->fetchAll();

        foreach($uPromotions as $promoID => $promoLimit) {
            if ($promoID == $promo_id) {
                $newPromoRecord = [
                    $promoID => $promoLimit - 1
                ];
                $promo_limit_stmt->execute([json_encode($newPromoRecord), $customer_id]);
            }
        }


    }



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Purchasing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        #thank-you {
            font-size: 24px;
            margin-bottom: 20px;
        }

        #redirect-message {
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <p id="thank-you">THANK YOU FOR PURCHASING!</p>
    <p id="redirect-message">You will be redirected to the home page in <span id="countdown">5</span> seconds.</p>
    <script>
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = "profile.php?activeTab=order-history-btn";
            }
        }, 1000);
    </script>
</body>
<?php


$order_id = htmlspecialchars($_GET['order_id']);
$user_email = $_user->email;

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


// Generate the receipt HTML with inline CSS
$receipt_html = "
<div style='width: 500px; margin: 20px auto; padding: 20px; border-radius: 10px; border: solid 1px rgba(0, 0, 0, 0.1); font-family: Arial, sans-serif; color: #333;'>
    <h3 style='text-align: center; color: #333;'>Order Summary</h3>
    <table style='width: 100%; border-collapse: collapse;'>
        <tr>
            <th style='text-align: left; padding: 8px; border-bottom: 1px solid #ddd;'>PRODUCT</th>
            <th style='text-align: right; padding: 8px; border-bottom: 1px solid #ddd;'>PRICE (RM)</th>
            <th style='text-align: right; padding: 8px; border-bottom: 1px solid #ddd;'>QUANTITY</th>
            <th style='text-align: right; padding: 8px; border-bottom: 1px solid #ddd;'>SUBTOTAL (RM)</th>
        </tr>";

$pTotal = 0;

// Fetch product details and append rows
foreach ($order_items as $product_id => $quantity) {
    $stmt = $_db->prepare("SELECT product_name, price FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $pName = htmlspecialchars($product['product_name']);
        $pPrice = number_format($product['price'], 2);
        $pSubtotal = number_format($product['price'] * $quantity, 2);
        $pTotal += $product['price'] * $quantity;

        $receipt_html .= "
        <tr>
            <td style='padding: 8px;'>$pName</td>
            <td style='text-align: right; padding: 8px; font-family: monospace;'>$pPrice</td>
            <td style='text-align: right; padding: 8px; font-family: monospace;'>$quantity</td>
            <td style='text-align: right; padding: 8px; font-family: monospace;'>$pSubtotal</td>
        </tr>";
    }
}

// Add subtotals, shipping fee, and promo amount
$shipping_fee = number_format($order['shipping_fee'], 2);
$promo_discount = number_format($order['promo_amount'] ?? 0, 2);
$total_payment = number_format($order['total'], 2);

$receipt_html .= "
        <tr>
            <td colspan='4' style='padding: 8px; border-bottom: 1px solid #ddd;'></td>
        </tr>
        <tr>
            <td colspan='3' style='text-align: right; padding: 8px;'>Products Subtotal (RM):</td>
            <td style='text-align: right; padding: 8px; font-family: monospace;'>".number_format($pTotal, 2)."</td>
        </tr>
        <tr>
            <td colspan='3' style='text-align: right; padding: 8px;'>Shipping Fee (RM):</td>
            <td style='text-align: right; padding: 8px; font-family: monospace;'>$shipping_fee</td>
        </tr>
        <tr>
            <td colspan='3' style='text-align: right; padding: 8px;'>Promotion Discount (RM):</td>
            <td style='text-align: right; padding: 8px; font-family: monospace; color: #0F9B58;'>-$promo_discount</td>
        </tr>
        <tr>
            <td colspan='4' style='padding: 8px; border-bottom: 1px solid #ddd;'></td>
        </tr>
        <tr>
            <td colspan='3' style='text-align: right; font-weight: bold; padding: 8px;'>TOTAL PAYMENT (RM):</td>
            <td style='text-align: right; font-weight: bold; padding: 8px; font-family: monospace;'>$total_payment</td>
        </tr>
    </table>
</div>";

// Send the email
try {
    $m = get_mail();
    $m->addAddress($user_email);
    $m->isHTML(true);
    $m->Subject = "Your Order Receipt: $order_id";
    $m->Body = "
        <h3 style='text-align: center; font-family: Arial, sans-serif;'>Thank you for your purchase!</h3>
        $receipt_html
    ";

    if ($m->send()) {
        $email_status = 'Success';
    } else {
        $email_status = 'Failed to send receipt.';
    }
} catch (Exception $e) {
    $email_status = 'Unexpected server error occurred.';
}

?>

</html>