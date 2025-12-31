<?php
header("Content-Type: application/json");

$consumerKey = "YOUR_CONSUMER_KEY";
$consumerSecret = "YOUR_CONSUMER_SECRET";
$shortcode = "174379";
$passkey = "YOUR_PASSKEY";

$data = json_decode(file_get_contents("php://input"), true);

$phone = $data['phone'];
$amount = $data['amount'];
$order_id = $data['order_id'];

$timestamp = date("YmdHis");
$password = base64_encode($shortcode . $passkey . $timestamp);

$credentials = base64_encode("$consumerKey:$consumerSecret");

$token = json_decode(
    file_get_contents("https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials", false,
    stream_context_create([
        "http" => [
            "header" => "Authorization: Basic $credentials"
        ]
    ]))
)->access_token;

$payload = [
    "BusinessShortCode" => $shortcode,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => $amount,
    "PartyA" => $phone,
    "PartyB" => $shortcode,
    "PhoneNumber" => $phone,
    "CallBackURL" => "https://yourdomain.com/api/mpesa/callback.php",
    "AccountReference" => "Order_$order_id",
    "TransactionDesc" => "ShopHub Order Payment"
];

$ch = curl_init("https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest");
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
