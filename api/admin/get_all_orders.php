<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require_once "../../config/connection.php";

$query = "

SELECT

    t.id,
    t.user_id,
    t.total_amount,
    t.status,
    t.created_at,

    u.name AS customer_name,
    u.email AS customer_email

FROM transactions t

LEFT JOIN users u
ON t.user_id = u.id

ORDER BY t.id DESC

";

$result = mysqli_query(
    $conn,
    $query
);

$orders = [];

while ($transaction = mysqli_fetch_assoc($result)) {

    $transactionId =
        (int)$transaction['id'];

    $itemsQuery = "

    SELECT

        ti.quantity,
        ti.price,

        p.name,
        p.flavor,
        p.image_path

    FROM transaction_items ti

    LEFT JOIN products p
    ON ti.product_id = p.id

    WHERE ti.transaction_id = ?

    ";

    $stmt = mysqli_prepare(
        $conn,
        $itemsQuery
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $transactionId
    );

    mysqli_stmt_execute($stmt);

    $itemsResult =
        mysqli_stmt_get_result($stmt);

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

    $orders[] = [

        "id" =>
            $transactionId,

        "user_id" =>
            (int)$transaction['user_id'],

        "buyer_name" =>
            $transaction['customer_name'],

        "email" =>
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

echo json_encode([
    "status" => "success",
    "data" => $orders
]);
