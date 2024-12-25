<?php
$_title = 'Checkout';
require_once '../_base.php';
require '../../vendor/autoload.php';

$stripe_key = 'sk_test_51QZoxyFSpaYwncq5xNMB1r3lsJ9zBDMwDI5yEYGf37HpW7eIBBlaORB01sAHolsWQfuxcua1TVkxspqhYi5t8alQ00gRNQG0AS';
header('Content-Type: application/json');
$domain = 'http://localhost:8000';

\Stripe\Stripe::setApiKey($stripe_key);
$stripe = new \Stripe\StripeClient($stripe_key);


$checkout_SESSION = \Stripe\Checkout\Session::create([
    "customer_email" => null,
    "mode" => "payment",
    "success_url" => $domain . "/page/payment_success.php",
    "cancel_url" => $domain . "/page/cart.php",
    "payment_method_types" => ["card", "fpx", "grabpay", "alipay", "link"],
    'billing_address_collection' => 'required',
    'shipping_address_collection' => [
        'allowed_countries' => ['MY'],
    ],
    'billing_address_collection' => 'required',
    'phone_number_collection' => ['enabled' => true],
    "line_items" => [
        [
            "quantity" => 1,
            "price_data" => [
                "currency" => "myr",
                "unit_amount" => 2000,
                "product_data" => [
                    "name" => "Banana"
                ]
            ]
        ]
    ]
]);

$paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => 5000, // Amount in cents
    'currency' => 'usd',
    'payment_method' => 'pm_card_visa', // Replace with actual payment method ID
    'receipt_email' => 'cjccheong@gmail.com', // Ensure email is valid
    'description' => 'Thanks for the helkp',
    'confirm' => true,
    'return_url' => $domain."/page/payment_success"
]);


http_response_code(303);
header("Location: " . $checkout_SESSION->url);
