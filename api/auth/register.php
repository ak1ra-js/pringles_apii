<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
?>
<?php
require_once '../../config/connection.php';

// Menangkap data JSON dari Flutter
$input = json_decode(file_get_contents('php://input'), TRUE);

if (isset($input['name']) && isset($input['email']) && isset($input['password'])) {
    
    $name = $conn->real_escape_string($input['name']);
    $email = $conn->real_escape_string($input['email']);
    $password = $input['password'];
    
    // Hash password menggunakan MD5 (disamakan dengan format login kita)
    $hashed_password = md5($password);
    
    // 1. Cek dulu apakah email ini sudah pernah didaftarkan
    $check_email = "SELECT id FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_email);
    
    if ($check_result->num_rows > 0) {
        // Jika email sudah ada di database
        echo json_encode([
            "status" => "error",
            "message" => "Email ini sudah terdaftar. Silakan gunakan email lain atau langsung Login."
        ]);
    } else {
        // 2. Jika email belum ada, masukkan data sebagai 'buyer' baru
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', 'buyer')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode([
                "status" => "success",
                "message" => "Akun berhasil dibuat! Silakan login."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal mendaftar: " . $conn->error
            ]);
        }
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Data pendaftaran tidak lengkap!"
    ]);
}

$conn->close();
?>