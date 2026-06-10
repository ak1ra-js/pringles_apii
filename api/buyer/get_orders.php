<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once "../../config/connection.php";

// =====================================
// USER ID
// =====================================

$userId =
    $_GET['user_id'] ?? '';

// =====================================
// VALIDASI
// =====================================

if (empty($userId)) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "User ID kosong"
    ]);

    exit;
}

// =====================================
// QUERY TRANSACTIONS
// =====================================

$query = "

SELECT

    transactions.id,

    transactions.total_amount,

    transactions.status,

    transactions.created_at

FROM transactions

WHERE transactions.user_id = ?

ORDER BY transactions.id DESC

";

$stmt =
    mysqli_prepare(
        $conn,
        $query
    );

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $userId
);

mysqli_stmt_execute(
    $stmt
);

$result =
    mysqli_stmt_get_result(
        $stmt
    );

$orders = [];

// =====================================
// LOOP TRANSAKSI
// =====================================

while (
    $transaction =
        mysqli_fetch_assoc(
            $result
        )
) {

    $transactionId =
        $transaction['id'];

    // =====================================
    // QUERY ITEMS
    // =====================================

    $itemsQuery = "

    SELECT

        transaction_items.quantity,

        transaction_items.price,

        products.name,

        products.flavor,

        products.image_path

    FROM transaction_items

    INNER JOIN products
    ON transaction_items.product_id = products.id

    WHERE transaction_items.transaction_id = ?

    ";

    $itemsStmt =
        mysqli_prepare(
            $conn,
            $itemsQuery
        );

    mysqli_stmt_bind_param(
        $itemsStmt,
        "i",
        $transactionId
    );

    mysqli_stmt_execute(
        $itemsStmt
    );

    $itemsResult =
        mysqli_stmt_get_result(
            $itemsStmt
        );

    $items = [];

    while (
        $item =
            mysqli_fetch_assoc(
                $itemsResult
            )
    ) {

        $items[] = [

            "name" =>
                $item['name'],

            "flavor" =>
                $item['flavor'],

            "price" =>
                (int)$item['price'],

            "quantity" =>
                (int)$item['quantity'],

            "image_path" =>
                $item['image_path']
        ];
    }

    // =====================================
    // FINAL ARRAY
    // =====================================

    $orders[] = [

        "transaction_id" =>
            $transaction['id'],

        "total_amount" =>
            (int)$transaction['total_amount'],

        "status" =>
            $transaction['status'],

        "created_at" =>
            $transaction['created_at'],

        "items" =>
            $items
    ];
}

// =====================================
// RESPONSE
// =====================================

echo json_encode([

    "status" => "success",

    "data" => $orders
]);
?>