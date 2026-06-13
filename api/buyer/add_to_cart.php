<?php

// =====================================
// ERROR REPORTING
// =====================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =====================================
// CORS HEADERS
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// =====================================
// HANDLE OPTIONS
// =====================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(200);
    exit();
}

// =====================================
// CONNECTION
// =====================================
require_once "../../config/connection.php";

// =====================================
// GET JSON INPUT
// =====================================
$rawData =
    file_get_contents(
        "php://input"
    );

$data =
    json_decode(
        $rawData,
        true
    );

// =====================================
// DEBUG LOG
// =====================================
file_put_contents(

    "debug_cart.txt",

    "\n\n========== NEW REQUEST ==========\n",

    FILE_APPEND
);

file_put_contents(

    "debug_cart.txt",

    "RAW JSON:\n" .
    $rawData . "\n",

    FILE_APPEND
);

// =====================================
// GET DATA
// =====================================
$userId =
    isset($data['user_id'])
        ? intval($data['user_id'])
        : 0;

$productId =
    isset($data['product_id'])
        ? intval($data['product_id'])
        : 0;

$quantity =
    isset($data['quantity'])
        ? intval($data['quantity'])
        : 1;

// =====================================
// SAVE DEBUG
// =====================================
file_put_contents(

    "debug_cart.txt",

    "USER ID: $userId\nPRODUCT ID: $productId\nQTY: $quantity\n",

    FILE_APPEND
);

// =====================================
// VALIDATION
// =====================================
if (
    $userId <= 0 ||
    $productId <= 0 ||
    $quantity <= 0
) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Data cart tidak valid"
    ]);

    exit;
}

// =====================================
// CHECK PRODUCT
// =====================================
$productQuery =
    "SELECT * FROM products WHERE id=? LIMIT 1";

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
// PRODUCT NOT FOUND
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
// STOCK CHECK
// =====================================
if (
    intval($product['stock']) <= 0
) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Stock produk habis"
    ]);

    exit;
}

// =====================================
// CHECK EXISTING CART
// =====================================
$checkQuery = "
    SELECT *
    FROM cart
    WHERE user_id=?
    AND product_id=?
    LIMIT 1
";

$checkStmt =
    mysqli_prepare(
        $conn,
        $checkQuery
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
// UPDATE CART
// =====================================
if ($existingCart) {

    $newQty =
        intval(
            $existingCart['quantity']
        ) + $quantity;

    // =====================================
    // STOCK LIMIT
    // =====================================
    if (
        $newQty >
        intval($product['stock'])
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
    // NEW CART
    // =====================================
    if (
        $quantity >
        intval($product['stock'])
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
// MYSQL ERROR DEBUG
// =====================================
if (!$execute) {

    file_put_contents(

        "debug_cart.txt",

        "MYSQL ERROR:\n" .
        mysqli_error($conn) . "\n",

        FILE_APPEND
    );
}

// =====================================
// SUCCESS RESPONSE
// =====================================
if ($execute) {

    echo json_encode([

        "status" => "success",

        "message" =>
            "Produk berhasil ditambahkan ke cart",

        "data" => [

            "user_id" =>
                $userId,

            "product_id" =>
                $productId,

            "quantity" =>
                $quantity
        ]
    ]);

} else {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Gagal menambahkan ke cart",

        "mysql_error" =>
            mysqli_error($conn)
    ]);
}
?>
