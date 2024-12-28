<?php
$_title = 'Checkout';
require_once '../_base.php';
require '../../vendor/autoload.php';

$stripe_key = 'sk_test_51QZoxyFSpaYwncq5xNMB1r3lsJ9zBDMwDI5yEYGf37HpW7eIBBlaORB01sAHolsWQfuxcua1TVkxspqhYi5t8alQ00gRNQG0AS';
header('Content-Type: application/json');
$domain = 'http://localhost:8000';
$_currency = 'myr';

$uName = post('uName');
$uEmail = post('uEmail');
$uPhone = post('uPhone');
$cartDetails = json_decode(post('cart'));




if (!$uName || !$uEmail || !$uPhone || !$cartDetails) {
    temp('popup-msg', ['msg' => 'All fields are required.', 'isSuccess' => false]);
    redirect('payment.php');
}

$line_items = [];
$stmt = $_db->prepare('SELECT product_name, price FROM products WHERE product_id = ?');
foreach ($cartDetails as $productID => $quantity) {

    $stmt->execute([$productID]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $line_items[] = [
        'quantity' => $quantity,
        'price_data' => [
            'currency' => $_currency,
            'unit_amount' => $product['price'] * 100,
            'product_data' => [
                'name' => $product['product_name']
            ]
        ]
    ];
}



\Stripe\Stripe::setApiKey($stripe_key);
$stripe = new \Stripe\StripeClient($stripe_key);

$customer = \Stripe\Customer::create([
    'email' => $uEmail,
    'phone' => $uPhone
]);



$session = $stripe->checkout->sessions->create([
    'customer' => $customer->id,
    'mode' => "payment",
    'success_url' => $domain . "/page/payment_success.php",
    'cancel_url' => $domain . "/page/cart.php",
    'payment_method_types' => ["card", "fpx", "grabpay", "alipay", "link"],
    "line_items" => $line_items,
    'phone_number_collection' => [
        'enabled' => true
    ],
    'billing_address_collection' => 'required',
    'shipping_address_collection' => ['allowed_countries' => ['MY']],
    'billing_address_collection' => 'required',
    'shipping_address_collection' => ['allowed_countries' => ['US', 'CA']],


]);

http_response_code(303);
redirect($session->url);

exit;
