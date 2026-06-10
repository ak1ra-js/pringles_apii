<?php

// =======================
// ERROR REPORTING
// =======================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
// md5 digunakan jika saat register kamu juga memakai md5
$hashedPassword = md5($password);

// =======================
// QUERY LOGIN (SUDAH DIUBAH KE MYSQLI)
// =======================
try {

    // Ubah :email dan :password menjadi tanda tanya (?) untuk MySQLi
    $sql = "SELECT id, name, email, role 
            FROM users 
            WHERE email = ? 
            AND password = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        throw new Exception("Gagal menyiapkan query database");
    }

    // Bind parameter "ss" (String, String) untuk email dan password
    mysqli_stmt_bind_param($stmt, "ss", $email, $hashedPassword);

    mysqli_stmt_execute($stmt);

    // Ambil hasil query
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

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

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Terjadi kesalahan database",
        "error" => $e->getMessage()
    ]);
}

?>
