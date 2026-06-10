<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$response = [];

try {

    // =====================================
    // CEK FILE IMAGE
    // =====================================

    if (!isset($_FILES['image'])) {

        echo json_encode([
            "status" => "error",
            "message" => "File gambar tidak ditemukan"
        ]);

        exit;
    }

    // =====================================
    // FOLDER UPLOAD
    // =====================================

    $uploadDir = "../../uploads/";

    // =====================================
    // BUAT FOLDER JIKA BELUM ADA
    // =====================================

    if (!file_exists($uploadDir)) {

        mkdir($uploadDir, 0777, true);
    }

    // =====================================
    // AMBIL FILE
    // =====================================

    $file = $_FILES['image'];

    // =====================================
    // EXTENSION
    // =====================================

    $extension = pathinfo(
        $file['name'],
        PATHINFO_EXTENSION
    );

    // =====================================
    // RENAME FILE
    // =====================================

    $newFileName =
        time() .
        "_" .
        rand(1000, 9999) .
        "." .
        $extension;

    // =====================================
    // PATH FINAL
    // =====================================

    $destination =
        $uploadDir . $newFileName;

    // =====================================
    // MOVE FILE
    // =====================================

    if (
        move_uploaded_file(
            $file['tmp_name'],
            $destination
        )
    ) {

        echo json_encode([

            "status" => "success",

            "message" =>
                "Upload berhasil",

            "filename" =>
                $newFileName
        ]);

    } else {

        echo json_encode([

            "status" => "error",

            "message" =>
                "Gagal upload gambar"
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