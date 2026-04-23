<?php
header('Content-Type: text/html; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "my_lovely";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("<h3>❌ Koneksi Database Gagal!</h3><p>Error: " . $conn->connect_error . "</p><p>Silakan jalankan: <a href='setup.php'>http://localhost/MyLovely/setup.php</a></p>");
}

$conn->set_charset("utf8mb4");
?>