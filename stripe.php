<?php
// stripe.php - send the customer to Stripe Checkout
// NOTE: Stripe amounts are in CENTS. $10.00 = 1000
include "config.php";

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$total = 0;
$names = array();
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
    $names[] = $item['name'] . " x " . $item['quantity'];
}

$_SESSION['last_order'] = array(
    'amount' => $total,
    'gateway' => 'stripe',
    'items' => implode(", ", $names)
);

$fields = array(
    'payment_method_types[0]' => 'card',
    'mode' => 'payment',
    'success_url' => SITE_URL . '/success.php?gateway=stripe',
    'cancel_url' => SITE_URL . '/success.php?cancel=1',
    'line_items[0][price_data][currency]' => 'aud',
    'line_items[0][price_data][product_data][name]' => implode(", ", $names),
    'line_items[0][price_data][unit_amount]' => intval($total * 100),
    'line_items[0][quantity]' => 1
);

// NOTE: Stripe auth = secret key as username, blank password
$ch = curl_init("https://api.stripe.com/v1/checkout/sessions");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ":");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
$result = json_decode(curl_exec($ch), true);
curl_close($ch);

if (isset($result['url'])) {
    header("Location: " . $result['url']);
    exit();
}

echo "<h2>Stripe error</h2>";
echo "<p>Put your test key in config.php</p>";
if (isset($result['error']['message'])) {
    echo "<p>" . htmlspecialchars($result['error']['message']) . "</p>";
}
echo "<p><a href='checkout.php'>Back</a></p>";
?>
