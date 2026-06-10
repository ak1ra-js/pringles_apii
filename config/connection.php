<?php

<<<<<<< HEAD
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
=======
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$password = getenv("MYSQLPASSWORD");
$database = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");
>>>>>>> 21485ca0f257f3873f7effa4f31de6270d0c2b25

$conn = new mysqli(
    $host,
    $user,
<<<<<<< HEAD
    $pass,
    $db,
    $port
);

// =====================================
// ERROR KONEKSI
// =====================================

=======
    $password,
    $database,
    $port
);

>>>>>>> 21485ca0f257f3873f7effa4f31de6270d0c2b25
if ($conn->connect_error) {

    die(json_encode([
        "status" => "error",
<<<<<<< HEAD
        "message" => "Koneksi database gagal",
        "error" => $conn->connect_error
    ]));
}

// =====================================
// UTF8
// =====================================

$conn->set_charset("utf8");

?>
=======
        "message" => "Koneksi gagal: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");

?>
>>>>>>> 21485ca0f257f3873f7effa4f31de6270d0c2b25
