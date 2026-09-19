<?php
include "config.php";

// update qty
if (isset($_POST['update']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = max(1, intval($qty));
        }
    }
}

// remove ticked items
if (isset($_POST['remove']) && isset($_POST['remove_id'])) {
    foreach ($_POST['remove_id'] as $id) {
        unset($_SESSION['cart'][$id]);
    }
}

$total = 0;
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shopping Cart</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
            <li><a href="checkout.php">Checkout</a></li>
        </ul>
    </div>
</nav>

<div class="container cart-page">
    <h1>Shopping Cart</h1>
    <?php if (empty($_SESSION['cart'])) { ?>
        <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
    <?php } else { ?>
    <form method="post">
        <div class="cart-container">
            <div class="cart-header">
                <div>Remove</div>
                <div>Image</div>
                <div class="description">Product</div>
                <div class="price">Price</div>
                <div class="qty">Qty</div>
                <div class="total">Total</div>
            </div>
            <?php foreach ($_SESSION['cart'] as $id => $item) { ?>
            <div class="cart-item">
                <div><input type="checkbox" name="remove_id[]" value="<?php echo $id; ?>"></div>
                <div><img src="<?php echo $item['image']; ?>" alt=""></div>
                <div class="description"><p><strong><?php echo $item['name']; ?></strong></p></div>
                <div class="price">$<?php echo number_format($item['price'], 2); ?></div>
                <div class="qty"><input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1"></div>
                <div class="total">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
            </div>
            <?php } ?>
        </div>
        <p class="cart-grand-total"><strong>Cart Total: $<?php echo number_format($total, 2); ?> AUD</strong></p>
        <button class="update-btn" type="submit" name="update" value="1">UPDATE QTY</button>
        <button class="remove-btn" type="submit" name="remove" value="1">REMOVE</button>
        <a href="checkout.php" class="btn btn-success">Continue to Checkout</a>
        <a href="index.php" class="btn btn-default">Keep Shopping</a>
    </form>
    <?php } ?>
</div>
</body>
</html>
