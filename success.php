<?php
include "config.php";

$cancel = isset($_GET['cancel']);
$ok = isset($_SESSION['last_order']) && !$cancel;

if ($ok) {
    $order = $_SESSION['last_order'];
    unset($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
    </div>
</nav>
<div class="container">
<?php if ($cancel) { ?>
    <h1>Payment cancelled</h1>
    <p>Nothing was charged. Your cart is still there.</p>
    <p><a href="checkout.php" class="btn btn-primary">Try again</a></p>
<?php } else if ($ok) { ?>
    <h1>Your Payment has been Successful</h1>
    <p><b>Gateway:</b> <?php echo htmlspecialchars($order['gateway']); ?></p>
    <p><b>Amount:</b> $<?php echo htmlspecialchars($order['amount']); ?> AUD</p>
    <p><b>Items:</b> <?php echo htmlspecialchars($order['items']); ?></p>
    <p><a href="index.php" class="btn btn-primary">Back to Products</a></p>
<?php } else { ?>
    <h1>Your Payment has Failed</h1>
    <p><a href="index.php" class="btn btn-primary">Back to Products</a></p>
<?php } ?>
</div>
</body>
</html>
