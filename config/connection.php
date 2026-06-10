<?php

// =====================================
// DEBUGGING (Matikan saat Production nanti)
// =====================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =====================================
// CORS HEADERS (BACKUP)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

// =====================================
// KREDENSIAL DATABASE RAILWAY
// =====================================
$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$port = getenv("MYSQLPORT");

// Mengambil nama database dari Railway Environment (Otomatis)
// Jika variabel di Railway kosong, fallback ke 'pringles_store'
$db   = getenv("MYSQLDATABASE");
if (empty($db)) {
    $db = "pringles_store"; 
}

// =====================================
// KONEKSI MYSQL
// =====================================
// Menggunakan @ agar error bawaan PHP tidak merusak format JSON jika server down
$conn = @new mysqli($host, $user, $pass, $db, $port);

// =====================================
// ERROR KONEKSI
// =====================================
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal",
        "error" => $conn->connect_error
    ]));
}

// =====================================
// CHAR SET
// =====================================
// utf8mb4 direkomendasikan karena mendukung karakter penuh
$conn->set_charset("utf8mb4");

?>
