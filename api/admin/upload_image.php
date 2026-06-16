<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

try {

    // =====================================
    // CEK FILE
    // =====================================

    if (!isset($_FILES['image'])) {

        echo json_encode([
            "status" => "error",
            "message" => "File gambar tidak ditemukan"
        ]);

        exit;
    }

    $file = $_FILES['image'];

    // =====================================
    // CEK ERROR UPLOAD
    // =====================================

    if ($file['error'] !== UPLOAD_ERR_OK) {

        echo json_encode([
            "status" => "error",
            "message" => "Upload error",
            "error_code" => $file['error']
        ]);

        exit;
    }

    // =====================================
    // FOLDER UPLOAD
    // =====================================

    $uploadDir = dirname(__DIR__, 2) . "/uploads/";

    // =====================================
    // BUAT FOLDER JIKA BELUM ADA
    // =====================================

    if (!is_dir($uploadDir)) {

        mkdir($uploadDir, 0777, true);
    }

    // =====================================
    // VALIDASI EXTENSION
    // =====================================

    $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    $extension = strtolower(
        pathinfo(
            $file['name'],
            PATHINFO_EXTENSION
        )
    );

    if (!in_array(
        $extension,
        $allowedExtensions
    )) {

        echo json_encode([
            "status" => "error",
            "message" => "Format gambar tidak didukung"
        ]);

        exit;
    }

    // =====================================
    // GENERATE NAMA FILE BARU
    // =====================================

    $newFileName =
        time() .
        "_" .
        uniqid() .
        "." .
        $extension;

    $destination =
        $uploadDir .
        $newFileName;

    // =====================================
    // SIMPAN FILE
    // =====================================

    $uploaded = move_uploaded_file(
        $file['tmp_name'],
        $destination
    );

    if (!$uploaded) {

        echo json_encode([
            "status" => "error",
            "message" => "move_uploaded_file gagal",
            "tmp_name" => $file['tmp_name'],
            "destination" => $destination
        ]);

        exit;
    }

    // =====================================
    // CEK FILE BENAR-BENAR TERSIMPAN
    // =====================================

    if (!file_exists($destination)) {

        echo json_encode([
            "status" => "error",
            "message" => "File tidak ditemukan setelah upload"
        ]);

        exit;
    }

    // =====================================
    // SUCCESS
    // =====================================

    echo json_encode([

        "status" => "success",

        "message" => "Upload berhasil",

        "filename" => $newFileName,

        "path" => $destination,

        "size" => filesize($destination)
    ]);

} catch (Throwable $e) {

    echo json_encode([

        "status" => "error",

        "message" => $e->getMessage()
    ]);
}
