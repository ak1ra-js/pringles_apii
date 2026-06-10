<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Menampilkan pesan bahwa server aktif
echo json_encode([
    "status" => "success",
    "message" => "Welcome to Pringles Store API! 🚀 Server is running normally.",
    "version" => "1.0.0",
    "time" => date("Y-m-d H:i:s")
]);

?>
