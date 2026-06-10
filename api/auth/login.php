<?php

// =======================
// ERROR REPORTING
// =======================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// =======================
// HEADER
// =======================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// =======================
// HANDLE OPTIONS
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =======================
// KONEKSI DATABASE
// =======================
require_once '../../config/connection.php';

// =======================
// AMBIL JSON INPUT
// =======================
$data = json_decode(file_get_contents("php://input"), true);

// cek json valid
if (!$data) {

    echo json_encode([
        "status" => "error",
        "message" => "JSON tidak valid"
    ]);

    exit();
}

// =======================
// VALIDASI INPUT
// =======================
$email = isset($data['email'])
    ? trim($data['email'])
    : '';

$password = isset($data['password'])
    ? trim($data['password'])
    : '';

if (empty($email) || empty($password)) {

    echo json_encode([
        "status" => "error",
        "message" => "Email dan password wajib diisi"
    ]);

    exit();
}

// =======================
// HASH PASSWORD
// =======================
$hashedPassword = md5($password);

// =======================
// QUERY LOGIN
// =======================
try {

    $sql = "SELECT id, name, email, role
            FROM users
            WHERE email = :email
            AND password = :password";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // =======================
    // LOGIN SUCCESS
    // =======================
    if ($user) {

        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil",
            "data" => $user
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Email atau password salah"
        ]);
    }

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Terjadi kesalahan database",
        "error" => $e->getMessage()
    ]);
}

?>
