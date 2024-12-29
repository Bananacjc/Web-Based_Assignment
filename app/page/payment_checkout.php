<?php

ob_start();
$_title = 'Checkout';
require_once '../_base.php';
require '../../vendor/autoload.php';
ob_end_clean();

$stripe_key = 'sk_test_51QZoxyFSpaYwncq5xNMB1r3lsJ9zBDMwDI5yEYGf37HpW7eIBBlaORB01sAHolsWQfuxcua1TVkxspqhYi5t8alQ00gRNQG0AS';
$googleMapsApiKey = "AIzaSyBFPpOlKxMJuu6PxnVrwxNd1G6vERpptro";
header('Content-Type: application/json');
$domain = 'http://localhost:8000';
$_currency = 'myr';

// None editable field || no validation
$uName = post('uName');
$uEmail = post('uEmail');
$uPhone = post('uPhone');
//// Payment method
$accNum = post('hiddenAccNum');
$cvvNum = post('hiddenCvvNum');
$exDate = post('hiddenExDate');
//// Additional Fee || Discount
$shippingFee = post('hiddenShippingFee');
$promoAmount = post('hiddenPromoAmount');

// Editable field, validation required
$cartDetails = json_decode(post('cart'));
//// Address
$line_1 = post('hiddenLine_1');
$village = post('hiddenVillage');
$postal_code = post('hiddenPostal_code');
$city = post('hiddenCity');
$state = post('hiddenState');

$full_address = [
    'line_1' => $line_1,
    'village' => $village,
    'postal_code' => $postal_code,
    'city' => $city,
    'state' => $state
];

if (!$cartDetails) {
    temp('popup-msg', ['msg' => 'Cart is Empty', 'isSuccess' => false]);
    redirect('cart.php');
}

if (!$line_1) {
    temp('popup-msg', ['msg' => 'Please enter an address', 'isSuccess' => false]);
    redirect('payment.php');
} else if (!validate_address_with_google($full_address, $googleMapsApiKey)) {
    temp('popup-msg', ['msg' => 'Please enter a valid address', 'isSuccess' => false]);
    redirect('payment.php');
}

if (!$shippingFee) {
    redirect('payment.php');
}

$line_items = [];

if ($shippingFee) {
    $line_items[] = [
        'quantity' => 1,
        'price_data' => [
            'currency' => $_currency,
            'unit_amount' => $shippingFee * 100,
            'product_data' => [
                'name' => 'Shipping Fee'
            ]
        ]
    ];
}

if ($promoAmount) {
    $line_items[] = [
        'quantity' => 1,
        'price_data' => [
            'currency' => $_currency,
            'unit_amount' => $promoAmount * -100,
            'product_data' => [
                'name' => 'Promotion Discount'
            ]
        ]
    ];
}

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
    'name'    => $uName,
    'email'   => $uEmail,
    'phone'   => $uPhone,
    'address' => [
        'line1'       => $line_1,
        'line2'       => $village,
        'city'        => $city,
        'state'       => $state,
        'postal_code' => $postal_code,
        'country'     => 'MY',
    ],
    'metadata' => [
        'contact_num' => $uPhone
    ]
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
    'payment_intent_data' => [
        'shipping' => [
            'name'    => $uName,
            'address' => [
                'line1'       => $line_1,
                'line2'       => $village,
                'city'        => $city,
                'state'       => $state,
                'country'     => 'MY',
                'postal_code' => $postal_code,
            ],
        ],
    ]

]);

http_response_code(303);
redirect($session->url);

exit;
