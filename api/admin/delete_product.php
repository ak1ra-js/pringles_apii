<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once "../../config/connection.php";

$data =
    json_decode(
        file_get_contents(
            "php://input"
        ),
        true
    );

$id =
    $data['id'] ?? '';

if (empty($id)) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "ID produk kosong"
    ]);

    exit;
}

$query =
    "DELETE FROM products WHERE id=?";

$stmt =
    mysqli_prepare(
        $conn,
        $query
    );

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

$execute =
    mysqli_stmt_execute(
        $stmt
    );

if ($execute) {

    echo json_encode([

        "status" => "success",

        "message" =>
            "Produk berhasil dihapus"
    ]);

} else {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Gagal menghapus produk"
    ]);
}
?>