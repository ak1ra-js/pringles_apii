<?php
// ==========================
// HEADER CORS & JSON
// ==========================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==========================
// KONEKSI DATABASE
// ==========================
require_once '../../config/connection.php';

// Cek koneksi database
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal"
    ]);
    exit();
}

// ==========================
// AMBIL DATA JSON
// ==========================
$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

// Cek JSON valid
if (!$input) {
    echo json_encode([
        "status" => "error",
        "message" => "Format JSON tidak valid"
    ]);
    exit();
}

// ==========================
// VALIDASI INPUT
// ==========================
if (
    !isset($input['email']) ||
    !isset($input['password']) ||
    empty(trim($input['email'])) ||
    empty(trim($input['password']))
) {
    echo json_encode([
        "status" => "error",
        "message" => "Email dan password wajib diisi"
    ]);
    exit();
}

$email = trim($input['email']);
$password = trim($input['password']);

// ==========================
// HASH PASSWORD
// ==========================
$hashed_password = md5($password);

// ==========================
// QUERY LOGIN
// ==========================
$sql = "SELECT id, name, email, role 
        FROM users 
        WHERE email = ? AND password = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Prepare statement gagal"
    ]);
    exit();
}

$stmt->bind_param("ss", $email, $hashed_password);
$stmt->execute();

$result = $stmt->get_result();

// ==========================
// CEK LOGIN
// ==========================
if ($result->num_rows > 0) {

    $user_data = $result->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "message" => "Login berhasil",
        "data" => $user_data
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Email atau password salah"
    ]);
}

// ==========================
// CLOSE
// ==========================
$stmt->close();
$conn->close();
?>
