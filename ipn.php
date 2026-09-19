<?php
// Parth: copy tutorial5-paypal/ipn.php here
// PayPal posts to this file in the background.
// Unusual: read php://input (not $_POST), then send it back with cmd=_notify-validate
// and wait for VERIFIED.
?>
