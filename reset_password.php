<?php
header('Content-Type: text/html; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "my_lovely";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error);
}

// Jika form di-submit
if(isset($_POST['update_password'])){
    $username = trim($_POST['username']);
    $password_baru = $_POST['password_baru'];
    
    if(empty($username) || empty($password_baru)){
        $pesan_error = "Username dan password tidak boleh kosong!";
    } else {
        // Hash password dengan bcrypt (aman)
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        
        // Update password di database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $password_hash, $username);
        
        if($stmt->execute()){
            $pesan_sukses = "✅ Password untuk user '<strong>$username</strong>' berhasil diperbarui!<br>Silakan login dengan password baru.";
        } else {
            $pesan_error = "❌ Gagal update password: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
        }
        h2 {
            color: #d63384;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #d63384;
            box-shadow: 0 0 5px rgba(214, 51, 132, 0.3);
        }
        button {
            width: 100%;
            padding: 10px;
            background: #d63384;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        button:hover {
            background: #b02a6c;
        }
        .pesan {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .sukses {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
        }
        a {
            color: #d63384;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Update Password</h2>
        
        <?php if(isset($pesan_sukses)): ?>
            <div class="pesan sukses"><?php echo $pesan_sukses; ?></div>
        <?php endif; ?>
        
        <?php if(isset($pesan_error)): ?>
            <div class="pesan error"><?php echo $pesan_error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            
            <div class="form-group">
                <label for="password_baru">Password Baru:</label>
                <input type="password" id="password_baru" name="password_baru" placeholder="Masukkan password baru" required>
            </div>
            
            <button type="submit" name="update_password">Update Password</button>
        </form>
        
        <div class="info">
            💡 <strong>Catatan:</strong> Gunakan tool ini untuk update password ke format yang aman (bcrypt). Setelah selesai, silakan login di <a href="login.php">halaman login</a>.
        </div>
    </div>
</body>
</html>
