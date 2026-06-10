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
$db   = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

// Jika Anda sedang testing di Localhost dan variabel di atas kosong,
// Anda bisa menggunakan fallback (nilai cadangan) seperti ini:
// $host = getenv("MYSQLHOST") ?: "localhost";
// $user = getenv("MYSQLUSER") ?: "root";
// $pass = getenv("MYSQLPASSWORD") ?: "";
// $db   = getenv("MYSQLDATABASE") ?: "nama_db_lokal";
// $port = getenv("MYSQLPORT") ?: "3306";

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
