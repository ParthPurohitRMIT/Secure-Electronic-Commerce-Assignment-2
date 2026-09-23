<?php
// square.php - Square Web Payments card form (sandbox)
// The card form is Square's iframe. We only receive a one-time token.
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

$appId = trim(SQUARE_APPLICATION_ID);
$token = trim(SQUARE_ACCESS_TOKEN);
$location = trim(SQUARE_LOCATION_ID);
if ($appId === '' || $token === '' || $location === '') {
    echo "<h2>Square sandbox is not configured</h2>";
    echo "<p>On <a href='https://developer.squareup.com/apps'>developer.squareup.com/apps</a>, open the app and switch to Sandbox. Copy:</p>";
    echo "<ol>";
    echo "<li>Sandbox application ID into <code>SQUARE_APPLICATION_ID</code>.</li>";
    echo "<li>Sandbox access token into <code>SQUARE_ACCESS_TOKEN</code>.</li>";
    echo "<li>Sandbox location ID into <code>SQUARE_LOCATION_ID</code>.</li>";
    echo "</ol>";
    echo "<p><a href='checkout.php'>Back</a></p>";
    exit();
}

$given = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$family = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
if ($given === '') {
    $given = 'Customer';
}
if ($family === '') {
    $family = 'Customer';
}
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$address2 = isset($_POST['address2']) ? trim($_POST['address2']) : '';
$state = isset($_POST['state']) ? trim($_POST['state']) : '';
$country = isset($_POST['country']) ? trim($_POST['country']) : 'AU';

$amount = number_format($total, 2, '.', '');
$js = json_encode(array(
    'appId' => $appId,
    'locationId' => $location,
    'amount' => $amount,
    'givenName' => $given,
    'familyName' => $family,
    'email' => $email,
    'address' => $address,
    'address2' => $address2,
    'state' => $state,
    'country' => $country
));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pay with Square</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet" />
    <script src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
    </div>
</nav>
<div class="container" style="max-width:520px; margin-top:30px;">
    <h1>Card payment</h1>
    <p><?php echo htmlspecialchars(implode(", ", $names)); ?></p>
    <p>Amount: <strong>$<?php echo htmlspecialchars($amount); ?> AUD</strong></p>
    <p>Visa <code>4111 1111 1111 1111</code>, Mastercard <code>5105 1051 0510 5100</code>, or AmEx <code>3400 0000 0000 009</code>. Any future expiry and any CVC. Square's ZIP field needs <code>12345</code>.</p>
    <form id="payment-form">
        <div id="card-brand" aria-live="polite" style="height:36px; margin-bottom:8px;"></div>
        <div id="card-container" style="min-height:90px; margin:16px 0;"></div>
        <button id="card-button" class="btn btn-primary" type="submit">Pay</button>
        <a href="checkout.php" class="btn btn-default">Back</a>
        <p id="card-error" style="color:#a94442; margin-top:12px;"></p>
    </form>
</div>
<script>
var squareConfig = <?php echo $js; ?>;
var card;

async function startSquare() {
    if (!window.Square) {
        document.getElementById("card-error").textContent = "Square's card form did not load.";
        return;
    }
    var payments = window.Square.payments(squareConfig.appId, squareConfig.locationId);
    card = await payments.card({ postalCode: "12345" });
    await card.attach("#card-container");
    card.addEventListener("cardBrandChanged", function (event) {
        var brand = event.detail && event.detail.cardBrand ? event.detail.cardBrand : "unknown";
        showCardBrand(brand);
    });
    showCardBrand("unknown");
}

function showCardBrand(brand) {
    var key = String(brand).toLowerCase();
    var marks = {
        visa: '<svg width="62" height="28" viewBox="0 0 62 28" role="img" aria-label="Visa"><rect width="62" height="28" rx="4" fill="#1a1f71"/><text x="31" y="19" text-anchor="middle" fill="#fff" font-size="13" font-family="Arial" font-weight="700">VISA</text></svg>',
        mastercard: '<svg width="62" height="28" viewBox="0 0 62 28" role="img" aria-label="Mastercard"><rect width="62" height="28" rx="4" fill="#f7f7f7"/><circle cx="26" cy="14" r="8" fill="#eb001b"/><circle cx="36" cy="14" r="8" fill="#f79e1b" opacity="0.95"/></svg>',
        americanexpress: '<svg width="62" height="28" viewBox="0 0 62 28" role="img" aria-label="American Express"><rect width="62" height="28" rx="4" fill="#2e77bc"/><text x="31" y="18" text-anchor="middle" fill="#fff" font-size="11" font-family="Arial" font-weight="700">AMEX</text></svg>',
        discover: '<svg width="72" height="28" viewBox="0 0 72 28" role="img" aria-label="Discover"><rect width="72" height="28" rx="4" fill="#f76b1c"/><text x="36" y="18" text-anchor="middle" fill="#fff" font-size="10" font-family="Arial" font-weight="700">DISCOVER</text></svg>',
        unknown: '<svg width="62" height="28" viewBox="0 0 62 28" role="img" aria-label="Card"><rect width="62" height="28" rx="4" fill="#ddd"/><text x="31" y="18" text-anchor="middle" fill="#555" font-size="11" font-family="Arial">CARD</text></svg>'
    };
    if (key === "american_express" || key === "american-express") {
        key = "americanexpress";
    }
    document.getElementById("card-brand").innerHTML = marks[key] || marks.unknown;
}

document.getElementById("payment-form").addEventListener("submit", async function (event) {
    event.preventDefault();
    var button = document.getElementById("card-button");
    var errorBox = document.getElementById("card-error");
    errorBox.textContent = "";
    button.disabled = true;
    try {
        var tokenResult = await card.tokenize({
            amount: squareConfig.amount,
            currencyCode: "AUD",
            intent: "CHARGE",
            customerInitiated: true,
            sellerKeyedIn: false,
            billingContact: {
                givenName: squareConfig.givenName,
                familyName: squareConfig.familyName,
                email: squareConfig.email,
                addressLines: [squareConfig.address, squareConfig.address2].filter(function (line) { return line; }),
                state: squareConfig.state,
                countryCode: squareConfig.country || "AU",
                postalCode: "12345"
            }
        });
        if (tokenResult.status !== "OK") {
            throw new Error("Card details were not accepted.");
        }
        var response = await fetch("square_charge.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ sourceId: tokenResult.token })
        });
        var result = await response.json();
        if (!response.ok || !result.ok) {
            throw new Error(result.message || "Payment failed.");
        }
        window.location = result.redirect;
    } catch (err) {
        errorBox.textContent = err.message;
        button.disabled = false;
    }
});

startSquare().catch(function (err) {
    document.getElementById("card-error").textContent = err.message;
});
</script>
</body>
</html>
