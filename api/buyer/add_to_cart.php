<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
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

$userId =
    $data['user_id'] ?? '';

$productId =
    $data['product_id'] ?? '';

$quantity =
    $data['quantity'] ?? 1;

// =====================================
// VALIDASI
// =====================================

if (
    empty($userId) ||
    empty($productId)
) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Data cart tidak lengkap"
    ]);

    exit;
}

// =====================================
// CEK PRODUK
// =====================================

$productQuery =
    "SELECT * FROM products WHERE id=?";

$productStmt =
    mysqli_prepare(
        $conn,
        $productQuery
    );

mysqli_stmt_bind_param(
    $productStmt,
    "i",
    $productId
);

mysqli_stmt_execute(
    $productStmt
);

$productResult =
    mysqli_stmt_get_result(
        $productStmt
    );

$product =
    mysqli_fetch_assoc(
        $productResult
    );

// =====================================
// PRODUK TIDAK ADA
// =====================================

if (!$product) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Produk tidak ditemukan"
    ]);

    exit;
}

// =====================================
// STOCK HABIS
// =====================================

if ($product['stock'] <= 0) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Stock produk habis"
    ]);

    exit;
}

// =====================================
// CEK CART EXIST
// =====================================

$checkCartQuery = "
    SELECT *
    FROM cart
    WHERE user_id=?
    AND product_id=?
";

$checkStmt =
    mysqli_prepare(
        $conn,
        $checkCartQuery
    );

mysqli_stmt_bind_param(

    $checkStmt,

    "ii",

    $userId,

    $productId
);

mysqli_stmt_execute(
    $checkStmt
);

$checkResult =
    mysqli_stmt_get_result(
        $checkStmt
    );

$existingCart =
    mysqli_fetch_assoc(
        $checkResult
    );

// =====================================
// JIKA SUDAH ADA
// =====================================

if ($existingCart) {

    $newQty =
        $existingCart['quantity']
        + $quantity;

    // =====================================
    // CEK STOCK
    // =====================================

    if (
        $newQty >
        $product['stock']
    ) {

        echo json_encode([

            "status" => "error",

            "message" =>
                "Quantity melebihi stock"
        ]);

        exit;
    }

    $updateQuery = "
        UPDATE cart
        SET quantity=?
        WHERE id=?
    ";

    $updateStmt =
        mysqli_prepare(
            $conn,
            $updateQuery
        );

    mysqli_stmt_bind_param(

        $updateStmt,

        "ii",

        $newQty,

        $existingCart['id']
    );

    $execute =
        mysqli_stmt_execute(
            $updateStmt
        );

} else {

    // =====================================
    // INSERT CART BARU
    // =====================================

    if (
        $quantity >
        $product['stock']
    ) {

        echo json_encode([

            "status" => "error",

            "message" =>
                "Quantity melebihi stock"
        ]);

        exit;
    }

    $insertQuery = "
        INSERT INTO cart
        (
            user_id,
            product_id,
            quantity
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ";

    $insertStmt =
        mysqli_prepare(
            $conn,
            $insertQuery
        );

    mysqli_stmt_bind_param(

        $insertStmt,

        "iii",

        $userId,

        $productId,

        $quantity
    );

    $execute =
        mysqli_stmt_execute(
            $insertStmt
        );
}

// =====================================
// RESPONSE
// =====================================

if ($execute) {

    echo json_encode([

        "status" => "success",

        "message" =>
            "Produk berhasil ditambahkan ke cart"
    ]);

} else {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Gagal menambahkan ke cart"
    ]);
}
?>