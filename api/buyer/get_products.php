<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
?>
<?php
require_once '../../config/connection.php';

// Mengambil semua data produk, diurutkan dari yang terbaru
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Belum ada produk."
    ]);
}

$conn->close();
?>