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
// QUERY CART
// =====================================

$query = "

SELECT

    cart.id AS cart_id,

    cart.quantity,

    products.id AS product_id,

    products.name,

    products.flavor,

    products.price,

    products.image_path,

    products.stock

FROM cart

INNER JOIN products
ON cart.product_id = products.id

WHERE cart.user_id = ?

ORDER BY cart.id DESC

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

$cartItems = [];

$totalAmount = 0;

// =====================================
// LOOP DATA
// =====================================

while (
    $row =
        mysqli_fetch_assoc(
            $result
        )
) {

    $subtotal =
        $row['price']
        * $row['quantity'];

    $totalAmount +=
        $subtotal;

    $cartItems[] = [

        "cart_id" =>
            $row['cart_id'],

        "product_id" =>
            $row['product_id'],

        "name" =>
            $row['name'],

        "flavor" =>
            $row['flavor'],

        "price" =>
            (int)$row['price'],

        "quantity" =>
            (int)$row['quantity'],

        "subtotal" =>
            $subtotal,

        "stock" =>
            (int)$row['stock'],

        "image_path" =>
            $row['image_path']
    ];
}

// =====================================
// RESPONSE
// =====================================

echo json_encode([

    "status" => "success",

    "total_amount" =>
        $totalAmount,

    "total_items" =>
        count($cartItems),

    "data" =>
        $cartItems
]);
?>