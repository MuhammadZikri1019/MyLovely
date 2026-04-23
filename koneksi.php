<?php
header('Content-Type: text/html; charset=utf-8');

$host = "db.fr-pari1.bengt.wasmernet.com";
$port = 10272;
$user = "a0ca7d1578e58000f353bcfc84ab";
$pass = "069ea0ca-7d15-79e7-8000-396c4103dd3c";
$db   = "dboSUaE6DmNSaLj6JUVYBthp";

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("<h3>❌ Koneksi Database Gagal!</h3><p>Error: " . $conn->connect_error . "</p><p>Silakan cek kembali kredensial Wasmer Anda.</p>");
}

$conn->set_charset("utf8mb4");
?>