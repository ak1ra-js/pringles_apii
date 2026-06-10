<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once "../../config/connection.php";

try {

    // =====================================
    // AMBIL DATA
    // =====================================

    $name =
        $_POST['name'] ?? '';

    $flavor =
        $_POST['flavor'] ?? '';

    $price =
        $_POST['price'] ?? 0;

    $imagePath =
        $_POST['image_path'] ?? '';

    $stock =
        $_POST['stock'] ?? 0;

    // =====================================
    // VALIDASI
    // =====================================

    if (
        empty($name) ||
        empty($flavor) ||
        empty($price) ||
        empty($imagePath)
    ) {

        echo json_encode([

            "status" => "error",

            "message" =>
                "Data produk tidak lengkap"
        ]);

        exit;
    }

    // =====================================
    // INSERT DATABASE
    // =====================================

    $query = "
        INSERT INTO products
        (
            name,
            flavor,
            price,
            image_path,
            stock
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmt =
        mysqli_prepare(
            $conn,
            $query
        );

    mysqli_stmt_bind_param(

        $stmt,

        "ssisi",

        $name,

        $flavor,

        $price,

        $imagePath,

        $stock
    );

    $execute =
        mysqli_stmt_execute(
            $stmt
        );

    if ($execute) {

        echo json_encode([

            "status" => "success",

            "message" =>
                "Produk berhasil ditambahkan"
        ]);

    } else {

        echo json_encode([

            "status" => "error",

            "message" =>
                "Gagal menambahkan produk"
        ]);
    }

} catch (Exception $e) {

    echo json_encode([

        "status" => "error",

        "message" =>
            $e->getMessage()
    ]);
}
?>