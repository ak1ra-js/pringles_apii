<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
?>
<?php
// Memanggil file koneksi database
require_once '../../config/connection.php';

// Menangkap data JSON yang dikirim dari aplikasi Flutter
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Memastikan email dan password tidak kosong
if (isset($input['email']) && isset($input['password'])) {
    
    // Mencegah SQL Injection sederhana
    $email = $conn->real_escape_string($input['email']);
    $password = $input['password'];
    
    // Mengenkripsi password inputan menjadi MD5 agar cocok dengan database
    $hashed_password = md5($password);
    
    // Mencari user di database
    $sql = "SELECT id, name, email, role FROM users WHERE email = '$email' AND password = '$hashed_password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Jika data ditemukan (Login Sukses)
        $user_data = $result->fetch_assoc();
        
        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil",
            "data" => $user_data // Mengirim data (termasuk role) kembali ke Flutter
        ]);
    } else {
        // Jika email atau password salah
        echo json_encode([
            "status" => "error",
            "message" => "Email atau password salah!"
        ]);
    }
} else {
    // Jika Flutter tidak mengirimkan data email/password
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak lengkap!"
    ]);
}

$conn->close();
?>