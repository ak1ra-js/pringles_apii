<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once "../../config/connection.php";

try {

    // =====================================
    // DATA
    // =====================================

    $id =
        $_POST['id'] ?? '';

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

    if (empty($id)) {

        echo json_encode([

            "status" => "error",

            "message" =>
                "ID produk kosong"
        ]);

        exit;
    }

    // =====================================
    // UPDATE
    // =====================================

    $query = "
        UPDATE products
        SET
            name = ?,
            flavor = ?,
            price = ?,
            image_path = ?,
            stock = ?
        WHERE id = ?
    ";

    $stmt =
        mysqli_prepare(
            $conn,
            $query
        );

    mysqli_stmt_bind_param(

        $stmt,

        "ssisii",

        $name,

        $flavor,

        $price,

        $imagePath,

        $stock,

        $id
    );

    $execute =
        mysqli_stmt_execute(
            $stmt
        );

    if ($execute) {

        echo json_encode([

            "status" => "success",

            "message" =>
                "Produk berhasil diupdate"
        ]);

    } else {

        echo json_encode([

            "status" => "error",

            "message" =>
                "Gagal update produk"
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