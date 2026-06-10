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
// START TRANSACTION
// =====================================

mysqli_begin_transaction(
    $conn
);

try {

    // =====================================
    // AMBIL CART USER
    // =====================================

    $cartQuery = "

    SELECT

        cart.*,

        products.name,

        products.price,

        products.stock

    FROM cart

    INNER JOIN products
    ON cart.product_id = products.id

    WHERE cart.user_id = ?

    ";

    $cartStmt =
        mysqli_prepare(
            $conn,
            $cartQuery
        );

    mysqli_stmt_bind_param(
        $cartStmt,
        "i",
        $userId
    );

    mysqli_stmt_execute(
        $cartStmt
    );

    $cartResult =
        mysqli_stmt_get_result(
            $cartStmt
        );

    $cartItems = [];

    $totalAmount = 0;

    // =====================================
    // VALIDASI CART
    // =====================================

    while (
        $item =
            mysqli_fetch_assoc(
                $cartResult
            )
    ) {

        // =====================================
        // CEK STOCK
        // =====================================

        if (
            $item['quantity']
            >
            $item['stock']
        ) {

            throw new Exception(
                "Stock produk {$item['name']} tidak mencukupi"
            );
        }

        $subtotal =
            $item['price']
            * $item['quantity'];

        $totalAmount +=
            $subtotal;

        $cartItems[] =
            $item;
    }

    // =====================================
    // CART KOSONG
    // =====================================

    if (count($cartItems) <= 0) {

        throw new Exception(
            "Cart masih kosong"
        );
    }

    // =====================================
    // INSERT TRANSACTIONS
    // =====================================

    $transactionQuery = "

    INSERT INTO transactions
    (
        user_id,
        total_amount,
        status
    )
    VALUES
    (
        ?,
        ?,
        'pending'
    )

    ";

    $transactionStmt =
        mysqli_prepare(
            $conn,
            $transactionQuery
        );

    mysqli_stmt_bind_param(

        $transactionStmt,

        "ii",

        $userId,

        $totalAmount
    );

    mysqli_stmt_execute(
        $transactionStmt
    );

    $transactionId =
        mysqli_insert_id(
            $conn
        );

    // =====================================
    // INSERT ITEMS
    // =====================================

    foreach (
        $cartItems as $item
    ) {

        // =====================================
        // INSERT TRANSACTION ITEMS
        // =====================================

        $itemQuery = "

        INSERT INTO transaction_items
        (
            transaction_id,
            product_id,
            quantity,
            price
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )

        ";

        $itemStmt =
            mysqli_prepare(
                $conn,
                $itemQuery
            );

        mysqli_stmt_bind_param(

            $itemStmt,

            "iiii",

            $transactionId,

            $item['product_id'],

            $item['quantity'],

            $item['price']
        );

        mysqli_stmt_execute(
            $itemStmt
        );

        // =====================================
        // REDUCE STOCK
        // =====================================

        $newStock =
            $item['stock']
            - $item['quantity'];

        $stockQuery = "

        UPDATE products
        SET stock=?
        WHERE id=?

        ";

        $stockStmt =
            mysqli_prepare(
                $conn,
                $stockQuery
            );

        mysqli_stmt_bind_param(

            $stockStmt,

            "ii",

            $newStock,

            $item['product_id']
        );

        mysqli_stmt_execute(
            $stockStmt
        );
    }

    // =====================================
    // CLEAR CART
    // =====================================

    $clearCartQuery = "
        DELETE FROM cart
        WHERE user_id=?
    ";

    $clearStmt =
        mysqli_prepare(
            $conn,
            $clearCartQuery
        );

    mysqli_stmt_bind_param(
        $clearStmt,
        "i",
        $userId
    );

    mysqli_stmt_execute(
        $clearStmt
    );

    // =====================================
    // COMMIT
    // =====================================

    mysqli_commit($conn);

    echo json_encode([

        "status" => "success",

        "message" =>
            "Checkout berhasil",

        "transaction_id" =>
            $transactionId,

        "total_amount" =>
            $totalAmount
    ]);

} catch (Exception $e) {

    // =====================================
    // ROLLBACK
    // =====================================

    mysqli_rollback($conn);

    echo json_encode([

        "status" => "error",

        "message" =>
            $e->getMessage()
    ]);
}
?>