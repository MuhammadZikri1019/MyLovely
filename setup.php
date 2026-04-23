<?php
$host = "localhost";
$user = "root";
$pass = "";

// Koneksi ke MySQL tanpa database
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database
$sql = "CREATE DATABASE IF NOT EXISTS my_lovely";
if ($conn->query($sql) === TRUE) {
    echo "✓ Database 'my_lovely' berhasil dibuat/sudah ada<br>";
} else {
    echo "✗ Gagal membuat database: " . $conn->error . "<br>";
}

// Pilih database
$conn->select_db("my_lovely");

// Buat tabel users
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "✓ Tabel 'users' berhasil dibuat/sudah ada<br>";
} else {
    echo "✗ Gagal membuat tabel users: " . $conn->error . "<br>";
}

// Buat tabel photos
$sql_photos = "CREATE TABLE IF NOT EXISTS photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_photos) === TRUE) {
    echo "✓ Tabel 'photos' berhasil dibuat/sudah ada<br>";
} else {
    echo "✗ Gagal membuat tabel photos: " . $conn->error . "<br>";
}

// Cek akun default
$cek = $conn->query("SELECT * FROM users WHERE username='admin'");
if ($cek->num_rows == 0) {
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password) VALUES ('admin', '$password')");
    echo "✓ Akun default dibuat (username: admin, password: admin123)<br>";
} else {
    echo "✓ Akun default sudah ada<br>";
}

echo "<br><strong>Setup selesai! Silakan hapus file ini setelah selesai.</strong><br>";
echo "Akses: http://localhost/MyLovely/login.php<br>";
$conn->close();
?>
