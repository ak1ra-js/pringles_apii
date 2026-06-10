<?php

// =====================================
// CORS HEADERS (WAJIB UNTUK API)
// =====================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tangani Preflight Request dari Browser (atau Flutter)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require_once '../../config/connection.php';

// =====================================
// AMBIL DATA JSON
// =====================================
$input = json_decode(file_get_contents('php://input'), TRUE);

if (isset($input['name']) && isset($input['email']) && isset($input['password'])) {
    
    // Trim untuk menghilangkan spasi berlebih di awal/akhir input
    $name = trim($input['name']);
    $email = trim($input['email']);
    $password = $input['password'];
    
    // Hash password menggunakan MD5 (disamakan dengan format login)
    $hashed_password = md5($password);
    
    // =====================================
    // 1. CEK EMAIL SUDAH TERDAFTAR/BELUM
    // =====================================
    $check_email_query = "SELECT id FROM users WHERE email = ?";
    $stmt_check = mysqli_prepare($conn, $check_email_query);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    
    $check_result = mysqli_stmt_get_result($stmt_check);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Jika email sudah ada di database
        echo json_encode([
            "status" => "error",
            "message" => "Email ini sudah terdaftar. Silakan gunakan email lain atau langsung Login."
        ]);
    } else {
        // =====================================
        // 2. INSERT SEBAGAI BUYER BARU
        // =====================================
        $role = 'buyer';
        $insert_query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        // Bind parameter "ssss" (String, String, String, String)
        mysqli_stmt_bind_param($stmt_insert, "ssss", $name, $email, $hashed_password, $role);
        
        $execute_insert = mysqli_stmt_execute($stmt_insert);
        
        if ($execute_insert) {
            echo json_encode([
                "status" => "success",
                "message" => "Akun berhasil dibuat! Silakan login."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal mendaftar: " . mysqli_error($conn)
            ]);
        }
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Data pendaftaran tidak lengkap!"
    ]);
}

?>
