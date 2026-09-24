<?php
include "config.php";

if (empty($_SESSION['cart'])) {
    header("Location: checkout.php");
    exit();
}

$total = 0;
$names = array();

// Collects the order information
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
    $names[] = $item['name'] . " x " . $item['quantity'];
}

// Saves it and presents it in the success.php page
$_SESSION['last_order'] = array(
    'amount' => $total,
    'gateway' => 'Google Pay',
    'items' => implode(", ", $names)
);

// Redirects to the success.php page that contains all the information
header("Location: success.php");
exit();
?>
