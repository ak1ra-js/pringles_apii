<?php

header("Content-Type: application/json");

require_once("../config/connection.php");

$data = json_decode(file_get_contents("php://input"), true);

$cart_id = $data['cart_id'] ?? 0;

if (!$cart_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Cart ID kosong"
    ]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM cart
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $cart_id
);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Item berhasil dihapus"
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Gagal menghapus item"
    ]);
}

$stmt->close();
$conn->close();
