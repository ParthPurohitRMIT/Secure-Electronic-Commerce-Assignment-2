<?php
// payway.php - Westpac PayWay hosted page (card details stay on PayWay)
// NOTE: we ask PayWay for a token first so the customer cannot edit the amount
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
    'gateway' => 'payway',
    'items' => implode(", ", $names)
);

$email = isset($_POST['email']) ? $_POST['email'] : '';
$amount = number_format($total, 2, '.', '');

$fields = array(
    'biller_code' => PAYWAY_BILLER_CODE,
    'username' => PAYWAY_USERNAME,
    'password' => PAYWAY_PASSWORD,
    'merchant_id' => PAYWAY_MERCHANT_ID,
    'payment_amount' => $amount,
    'receipt_address' => $email,
    'return_link_url' => SITE_URL . '/success.php?gateway=payway',
    'return_link_text' => 'Return to shop'
);

$ch = curl_init("https://www.payway.com.au/RequestToken");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
$reply = trim(curl_exec($ch));
curl_close($ch);

$token = '';
if (strpos($reply, 'token=') === 0) {
    $token = substr($reply, 6);
}

if ($token == '') {
    echo "<h2>PayWay error</h2>";
    echo "<p>Put your biller code / username / password in config.php</p>";
    echo "<pre>" . htmlspecialchars($reply) . "</pre>";
    echo "<p><a href='checkout.php'>Back</a></p>";
    exit();
}
?>
<form id="paywayForm" action="https://www.payway.com.au/MakePayment" method="post">
    <input type="hidden" name="biller_code" value="<?php echo PAYWAY_BILLER_CODE; ?>">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
</form>
<script>document.getElementById("paywayForm").submit();</script>
<p>Redirecting to PayWay...</p>
