<?php

// =====================================
// CORS HEADERS (WAJIB UNTUK API)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tangani Preflight Request dari Browser atau Frontend
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require_once '../../config/connection.php';

// =====================================
// QUERY SEMUA PRODUK
// =====================================
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$products = [];

// =====================================
// CEK DAN LOOPING DATA
// =====================================
if ($result && mysqli_num_rows($result) > 0) {
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Mengubah tipe data agar presisi saat dibaca oleh Flutter/Frontend
        $row['id'] = (int)$row['id'];
        $row['price'] = (int)$row['price'];
        $row['stock'] = (int)$row['stock'];
        
        $products[] = $row;
    }
    
    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);
    
} else {
    
    echo json_encode([
        "status" => "error",
        "message" => "Belum ada produk."
    ]);
    
}

?>
