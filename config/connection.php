<?php

// =====================================
// DEBUGGING (Matikan saat Production)
// =====================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =====================================
// CORS HEADERS (Untuk akses API)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// =====================================
// KREDENSIAL DATABASE RAILWAY
// =====================================
// Mengambil variabel secara dinamis dari Railway Environment
$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$port = getenv("MYSQLPORT");

// Nama database yang kamu pakai di TablePlus untuk mengimport tabel
$db   = "pringles_store"; 

// =====================================
// KONEKSI MYSQL
// =====================================
$conn = new mysqli($host, $user, $pass, $db, $port);

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
// utf8mb4 direkomendasikan karena mendukung karakter penuh termasuk Emoji
$conn->set_charset("utf8mb4");

?>
