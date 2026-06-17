<?php

// =====================================
// CORS HEADERS (WAJIB UNTUK API)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tangani Preflight Request dari Browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require_once "../../config/connection.php";

// Tangkap data JSON dari Flutter
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';

if (empty($id)) {
    echo json_encode([
        "status" => "error",
        "message" => "ID produk kosong"
    ]);
    exit;
}

// =====================================
// 1. HAPUS GAMBAR FISIK DARI SERVER
// =====================================
// Sesuaikan "image_path" dengan nama kolom tempat kamu menyimpan nama gambar di database
$query_img = "SELECT image_path FROM products WHERE id=?";
$stmt_img = mysqli_prepare($conn, $query_img);
mysqli_stmt_bind_param($stmt_img, "i", $id);
mysqli_stmt_execute($stmt_img);
$result = mysqli_stmt_get_result($stmt_img);
$row = mysqli_fetch_assoc($result);

if ($row && !empty($row['image_path'])) {
    // Sesuaikan path ini agar mengarah ke folder tempat kamu menyimpan gambar upload
    // Misalnya jika folder "uploads" sejajar dengan folder "api", gunakan "../../uploads/"
    $image_file = "../../uploads/" . $row['image_path'];
    
    // Cek apakah file gambar benar-benar ada di dalam folder
    if (file_exists($image_file)) {
        unlink($image_file); // Perintah sakti untuk menghapus file permanen
    }
}

// =====================================
// 2. HAPUS DATA DARI DATABASE MYSQL
// =====================================
$query = "DELETE FROM products WHERE id=?";
$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $id);
$execute = mysqli_stmt_execute($stmt);

if ($execute) {
    echo json_encode([
        "status" => "success",
        "message" => "Produk dan gambar berhasil dihapus"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menghapus produk dari database"
    ]);
}

?>
