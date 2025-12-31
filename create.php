<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$db = (new Database())->getConnection();

$db->beginTransaction();

$stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
$stmt->execute([
    $data['user_id'],
    $data['total_amount'],
    $data['shipping_address']
]);

$order_id = $db->lastInsertId();

$itemStmt = $db->prepare(
    "INSERT INTO order_items (order_id, product_id, quantity, price)
     VALUES (?, ?, ?, ?)"
);

foreach ($data['items'] as $item) {
    $itemStmt->execute([
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['price']
    ]);
}

$db->commit();

echo json_encode(["order_id" => $order_id]);
