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

// =====================================
// AMBIL JSON
// =====================================

$data =
    json_decode(
        file_get_contents(
            "php://input"
        ),
        true
    );

// =====================================
// DATA
// =====================================

$transactionId =
    $data['transaction_id']
    ?? '';

$status =
    $data['status']
    ?? '';

// =====================================
// VALIDASI
// =====================================

if (
    empty($transactionId)
    ||
    empty($status)
) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Data tidak lengkap"
    ]);

    exit;
}

// =====================================
// VALID STATUS
// =====================================

$allowedStatus = [

    "pending",

    "processing",

    "completed",

    "cancelled"
];

if (
    !in_array(
        $status,
        $allowedStatus
    )
) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Status tidak valid"
    ]);

    exit;
}

// =====================================
// UPDATE STATUS
// =====================================

$query = "

UPDATE transactions
SET status=?
WHERE id=?

";

$stmt =
    mysqli_prepare(
        $conn,
        $query
    );

mysqli_stmt_bind_param(

    $stmt,

    "si",

    $status,

    $transactionId
);

$execute =
    mysqli_stmt_execute(
        $stmt
    );

// =====================================
// RESPONSE
// =====================================

if ($execute) {

    echo json_encode([

        "status" => "success",

        "message" =>
            "Status pesanan berhasil diupdate"
    ]);

} else {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Gagal update status"
    ]);
}
?>
