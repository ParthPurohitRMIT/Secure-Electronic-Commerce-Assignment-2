<?php
// square_charge.php - charge the Square token. Amount comes from the session cart.
include "config.php";

header("Content-Type: application/json");

if (empty($_SESSION['cart'])) {
    http_response_code(400);
    echo json_encode(array("ok" => false, "message" => "Your cart is empty."));
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
$sourceId = is_array($input) && isset($input["sourceId"]) ? trim($input["sourceId"]) : "";
if ($sourceId === "") {
    http_response_code(400);
    echo json_encode(array("ok" => false, "message" => "Missing card token."));
    exit();
}

$total = 0;
$names = array();
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
    $names[] = $item['name'] . " x " . $item['quantity'];
}

$body = array(
    "source_id" => $sourceId,
    "idempotency_key" => "ALICE-" . time() . "-" . mt_rand(1000, 9999),
    "amount_money" => array(
        "amount" => intval($total * 100),
        "currency" => "AUD"
    ),
    "location_id" => SQUARE_LOCATION_ID,
    "note" => implode(", ", $names)
);

$ch = curl_init(SQUARE_PAYMENTS_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . SQUARE_ACCESS_TOKEN,
    "Content-Type: application/json",
    "Square-Version: " . SQUARE_VERSION
));
$raw = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);
$result = json_decode($raw, true);

$status = "";
if (isset($result["payment"]["status"])) {
    $status = $result["payment"]["status"];
}

if ($status === "COMPLETED" || $status === "APPROVED") {
    $_SESSION["last_order"] = array(
        "amount" => $total,
        "gateway" => "square",
        "items" => implode(", ", $names)
    );
    echo json_encode(array(
        "ok" => true,
        "redirect" => "success.php?gateway=square"
    ));
    exit();
}

http_response_code(400);
$message = "Square declined the payment.";
if ($curl_err !== "") {
    $message = $curl_err;
} else if (isset($result["errors"][0]["detail"])) {
    $message = $result["errors"][0]["detail"];
}
echo json_encode(array("ok" => false, "message" => $message));
