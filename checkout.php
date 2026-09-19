<?php
include "config.php";

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="cart.php">Cart</a></li>
        </ul>
    </div>
</nav>

<div class="container checkout-page">
    <h1>Checkout</h1>
    <p>Order total: <strong>$<?php echo number_format($total, 2); ?> AUD</strong></p>

    <div class="row">
        <div class="col-md-6">
            <form id="checkoutForm" method="post" action="stripe.php" onsubmit="return goPay();">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <h3>Payment method</h3>
                <p>Stripe and PayWay work. PayPal and Google Pay are for Parth (search Parth).</p>

                <div class="payment-option"><label><input type="radio" name="payment_method" value="stripe" checked> Stripe (working)</label></div>
                <div class="payment-option"><label><input type="radio" name="payment_method" value="payway"> Westpac PayWay (working)</label></div>
                <!-- Parth: PayPal - copy the Buy Now form from tutorial5-paypal/index.php -->
                <div class="payment-option"><label><input type="radio" name="payment_method" value="paypal"> PayPal (todo)</label></div>
                <!-- Parth: Google Pay - copy tutorial6-gpay/index.js into gpay.js -->
                <div class="payment-option"><label><input type="radio" name="payment_method" value="gpay"> Google Pay (todo)</label></div>

                <input type="hidden" id="cart_total" value="<?php echo $total; ?>">
                <p style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Continue to payment</button>
                    <a href="cart.php" class="btn btn-default">Back to cart</a>
                </p>
            </form>
        </div>
        <div class="col-md-6">
            <h3>Order summary</h3>
            <ul class="list-group">
                <?php foreach ($_SESSION['cart'] as $item) { ?>
                <li class="list-group-item">
                    <?php echo $item['name']; ?> x <?php echo $item['quantity']; ?>
                    <span class="pull-right">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </li>
                <?php } ?>
                <li class="list-group-item"><strong>Total</strong>
                    <span class="pull-right"><strong>$<?php echo number_format($total, 2); ?></strong></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Parth Google Pay: add
<script src="gpay.js"></script>
<script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>
-->
<script>
function goPay() {
    var method = document.querySelector('input[name="payment_method"]:checked').value;
    var form = document.getElementById("checkoutForm");

    if (method == "stripe") {
        form.action = "stripe.php";
        return true;
    }
    if (method == "payway") {
        form.action = "payway.php";
        return true;
    }

    // Parth PayPal: set form.action = "https://www.sandbox.paypal.com/cgi-bin/webscr"
    // and add hidden fields: cmd=_xclick, business, item_name, amount, currency_code, return, notify_url
    if (method == "paypal") {
        alert("PayPal not done yet. See tutorial5-paypal.");
        return false;
    }

    // Parth Google Pay: call onGooglePaymentButtonClicked() from tutorial6-gpay
    // use document.getElementById('cart_total').value as the price (AUD)
    if (method == "gpay") {
        alert("Google Pay not done yet. See tutorial6-gpay.");
        return false;
    }
    return false;
}
</script>
</body>
</html>
