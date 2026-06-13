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
// GET USER ID
// =====================================
$userId = 0;

// GET METHOD
if (isset($_GET['user_id'])) {

    $userId =
        intval($_GET['user_id']);
}

// POST JSON METHOD
if ($userId <= 0) {

    $data =
        json_decode(
            file_get_contents(
                "php://input"
            ),
            true
        );

    $userId =
        isset($data['user_id'])
            ? intval($data['user_id'])
            : 0;
}

// =====================================
// VALIDATION
// =====================================
if ($userId <= 0) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "User ID tidak valid"
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

    products.stock,

    products.image_path

FROM cart

INNER JOIN products
ON cart.product_id = products.id

WHERE cart.user_id = ?

ORDER BY cart.id DESC

";

// =====================================
// PREPARE
// =====================================
$stmt =
    mysqli_prepare(
        $conn,
        $query
    );

if (!$stmt) {

    echo json_encode([

        "status" => "error",

        "message" =>
            mysqli_error($conn)
    ]);

    exit;
}

// =====================================
// BIND
// =====================================
mysqli_stmt_bind_param(

    $stmt,

    "i",

    $userId
);

// =====================================
// EXECUTE
// =====================================
mysqli_stmt_execute(
    $stmt
);

$result =
    mysqli_stmt_get_result(
        $stmt
    );

// =====================================
// DATA
// =====================================
$cartItems = [];

$totalPrice = 0;

// =====================================
// FETCH
// =====================================
while (
    $row =
        mysqli_fetch_assoc(
            $result
        )
) {

    $subtotal =
        intval($row['price']) *
        intval($row['quantity']);

    $totalPrice +=
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
            intval($row['price']),

        "quantity" =>
            intval($row['quantity']),

        "stock" =>
            intval($row['stock']),

        "subtotal" =>
            $subtotal,

        "image_path" =>
            $row['image_path']
    ];
}

// =====================================
// RESPONSE
// =====================================
echo json_encode([

    "status" => "success",

    "message" =>
        "Data cart berhasil diambil",

    "total_price" =>
        $totalPrice,

    "total_items" =>
        count($cartItems),

    "data" =>
        $cartItems
]);
?>
