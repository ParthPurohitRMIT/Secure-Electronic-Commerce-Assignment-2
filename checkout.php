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
                <h3>Billing address</h3>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Last name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" value="alice">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" value="1234 Main St">
                </div>
                <div class="form-group">
                    <label>Address 2 (optional)</label>
                    <input type="text" class="form-control" name="address2" value="Apartment 1">
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Country</label>
                            <select class="form-control" name="country">
                                <option value="AU" selected>Australia</option>
                                <option value="NZ">New Zealand</option>
                                <option value="GB">United Kingdom</option>
                                <option value="US">United States</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>State</label>
                            <select class="form-control" name="state">
                                <option value="NSW" selected>NSW</option>
                                <option value="VIC">VIC</option>
                                <option value="QLD">QLD</option>
                                <option value="SA">SA</option>
                                <option value="WA">WA</option>
                                <option value="TAS">TAS</option>
                                <option value="NT">NT</option>
                                <option value="ACT">ACT</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Zip</label>
                            <input type="text" class="form-control" name="zip" value="2000">
                        </div>
                    </div>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="shipping_same" value="1" checked> Shipping address is the same as my billing address</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="save_info" value="1" checked> Save this information for next time</label>
                </div>

                <h3>Payment method</h3>

                <div class="payment-option"><label><input type="radio" name="payment_method" value="stripe" checked> Stripe (working)</label></div>
                <div class="payment-option"><label><input type="radio" name="payment_method" value="square"> Square (sandbox)</label></div>
                <div class="payment-option"><label><input type="radio" name="payment_method" value="paypal"> PayPal (working)</label></div>
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

<!-- Calls the GooglePay javascript file to allow for integration of GPay  -->
<script src="gpay.js"></script>
<script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>

<script>
function goPay() {
    var method = document.querySelector('input[name="payment_method"]:checked').value;
    var form = document.getElementById("checkoutForm");

    if (method == "stripe") {
        form.action = "stripe.php";
        return true;
    }
    if (method == "square") {
        form.action = "square.php";
        return true;
    }
    if (method == "paypal") {
        form.action = "paypal.php";
        return true;
    }

    if (method == "gpay") {
        onGooglePaymentButtonClicked();
        return false;
    }
}
</script>
</body>
</html>
