<?php
include "config.php";

// add to cart
if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    if (isset($products[$id])) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = $products[$id];
            $_SESSION['cart'][$id]['quantity'] = 1;
        }
        header("Location: cart.php");
        exit();
    }
}

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alice's Electronic Bike Shop</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
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

<div class="container">
    <ol class="breadcrumb"><li class="active">Electric Bikes</li></ol>
    <div class="row">
        <?php foreach ($products as $p) { ?>
        <div class="col-md-4 text-center col-sm-6">
            <div class="thumbnail product-box">
                <img src="<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" />
                <div class="caption">
                    <h3><?php echo $p['name']; ?></h3>
                    <p>Price : <strong>$ <?php echo $p['price']; ?></strong></p>
                    <form action="index.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="btn btn-success">Add To Cart</button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
