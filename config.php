<?php

session_start();


define('SITE_URL', 'http://localhost/Secure%20Electric%20Commerce%20Assigment%202/Secure-Electronic-Commerce-Assignment-2');
define('CURRENCY', 'AUD');

// Stripe
// https://dashboard.stripe.com/test/apikeys
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE');

// Westpac PayWay (working) - but I need to change the password
// merchant_id TEST = no real money
define('PAYWAY_BILLER_CODE', '000000');
define('PAYWAY_USERNAME', 'YOUR_PAYWAY_USERNAME');
define('PAYWAY_PASSWORD', '');
define('PAYWAY_MERCHANT_ID', 'TEST');

// Parth: PayPal - fill these in from tutorial5-paypal/config.php
define('PAYPAL_ID', 'PlaceYourSellerEmail');
define('PAYPAL_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');

$products = array(
    1 => array('id' => 1, 'name' => 'Bronton', 'price' => 3000, 'image' => 'assets/img/bronton.jpg'),
    2 => array('id' => 2, 'name' => 'E-BMX', 'price' => 2000, 'image' => 'assets/img/dummyimg.jpg'),
    3 => array('id' => 3, 'name' => 'F-65', 'price' => 700, 'image' => 'assets/img/f65.jpg')
);
?>
