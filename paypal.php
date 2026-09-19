<?php
// paypal.php - sends the cart to PayPal sandbox (tutorial 5 Buy Now form)
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

$order_id = 'ALICE-' . time();

$_SESSION['last_order'] = array(
    'amount' => $total,
    'gateway' => 'paypal',
    'items' => implode(", ", $names)
);
?>
<p>Redirecting to PayPal...</p>
<form id="paypalForm" action="<?php echo PAYPAL_URL; ?>" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>">
    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars(implode(", ", $names)); ?>">
    <input type="hidden" name="item_number" value="<?php echo $order_id; ?>">
    <input type="hidden" name="amount" value="<?php echo number_format($total, 2, '.', ''); ?>">
    <input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">
    <input type="hidden" name="return" value="<?php echo PAYPAL_RETURN_URL; ?>">
    <input type="hidden" name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>">
    <input type="hidden" name="notify_url" value="<?php echo PAYPAL_NOTIFY_URL; ?>">
</form>
<script>document.getElementById("paypalForm").submit();</script>
