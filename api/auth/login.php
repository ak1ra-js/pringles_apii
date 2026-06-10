<?php

// =======================
// ERROR REPORTING
// =======================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =======================
// HEADER
// =======================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =======================
// KONEKSI DATABASE
// =======================
require_once '../../config/connection.php';

// cek koneksi
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database gagal terkoneksi",
        "error" => $conn->connect_error
    ]);
    exit();
}

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
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

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
$sql = "SELECT id, name, email, role 
        FROM users 
        WHERE email = ? AND password = ?";

$stmt = $conn->prepare($sql);

// cek prepare gagal
if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Query gagal",
        "error" => $conn->error
    ]);
    exit();
}

$stmt->bind_param("ss", $email, $hashedPassword);

$stmt->execute();

$result = $stmt->get_result();

// =======================
// LOGIN SUCCESS
// =======================
if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

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

$stmt->close();
$conn->close();

?>
