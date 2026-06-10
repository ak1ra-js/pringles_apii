<?php

// =====================================
// CORS HEADERS (WAJIB UNTUK API)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tangani Preflight Request dari Browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
