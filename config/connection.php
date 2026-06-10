<?php

$host = getenv("MYSQLHOST");
$db   = getenv("MYSQLDATABASE");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$port = getenv("MYSQLPORT");

try {

    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$db",
        $user,
        $pass
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die(json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal",
        "error" => $e->getMessage()
    ]));
}
?>
