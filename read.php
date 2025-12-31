<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../config/database.php";

$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM products");
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["records" => $products]);
