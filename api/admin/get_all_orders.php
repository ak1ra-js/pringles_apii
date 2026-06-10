<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once "../../config/connection.php";

// =====================================
// QUERY ALL TRANSACTIONS
// =====================================

$query = "

SELECT

    transactions.id,

    transactions.user_id,

    transactions.total_amount,

    transactions.status,

    transactions.created_at,

    users.name AS customer_name,

    users.email AS customer_email

FROM transactions

INNER JOIN users
ON transactions.user_id = users.id

ORDER BY transactions.id DESC

";

$result =
    mysqli_query(
        $conn,
        $query
    );

$orders = [];

// =====================================
// LOOP TRANSACTION
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
    // GET ITEMS
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

        "user_id" =>
            $transaction['user_id'],

        "customer_name" =>
            $transaction['customer_name'],

        "customer_email" =>
            $transaction['customer_email'],

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