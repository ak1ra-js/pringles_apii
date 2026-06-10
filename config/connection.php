<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// =====================================
// DATABASE RAILWAY
// =====================================

$host = getenv("mysql.railway.internal");
$user = getenv("root");
$pass = getenv("cuLnucwlAmIbcVakpbasMFDSVyKclyMz");
$db   = getenv("railway");
$port = getenv("3306");

// =====================================
// KONEKSI MYSQL
// =====================================

$conn = new mysqli(
    $host,
    $user,
    $pass,
    $db,
    $port
);

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
// UTF8
// =====================================

$conn->set_charset("utf8");

?>