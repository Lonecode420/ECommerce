<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = md5($data['password']);

$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT id, name, email FROM users WHERE email=? AND password=?");
$stmt->execute([$email, $password]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(["user" => $user]);
} else {
    echo json_encode(["error" => "Invalid credentials"]);
}
