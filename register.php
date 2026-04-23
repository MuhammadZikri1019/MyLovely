<?php
session_start();
include 'koneksi.php';

$pesan_error = "";
$pesan_sukses = "";

// Jika sudah login, redirect ke index
if(isset($_SESSION['login'])){
    header("Location: index.php");
    exit;
}

// Proses form register
if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_konfirm = $_POST['password_konfirm'];
    
    // Validasi
    if(empty($username)){
        $pesan_error = "Username tidak boleh kosong!";
    } else if(strlen($username) < 3){
        $pesan_error = "Username minimal 3 karakter!";
    } else if(empty($password)){
        $pesan_error = "Password tidak boleh kosong!";
    } else if(strlen($password) < 6){
        $pesan_error = "Password minimal 6 karakter!";
    } else if($password !== $password_konfirm){
        $pesan_error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek username sudah ada atau belum
        $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $hasil = $cek->get_result();
        
        if($hasil->num_rows > 0){
            $pesan_error = "Username sudah terdaftar! Pilih username lain.";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password_hash);
            
            if($stmt->execute()){
                $pesan_sukses = "✅ Akun berhasil dibuat! Silakan <a href='login.php'>login di sini</a>";
            } else {
                $pesan_error = "❌ Gagal membuat akun: " . $stmt->error;
            }
            $stmt->close();
        }
        $cek->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Ruang Cinta Kita</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
            background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
            font-family: 'Poppins', sans-serif; 
            overflow: hidden;
            padding: 20px;
        }
        .register-box {
            background: rgba(255, 255, 255, 0.3); 
            backdrop-filter: blur(10px);
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            text-align: center; 
            width: 90%; 
            max-width: 350px; 
            border: 1px solid rgba(255,255,255,0.5);
        }
        h2 { 
            font-family: 'Dancing Script', cursive; 
            font-size: 2.5em; 
            color: #d63384; 
            margin-bottom: 10px;
            margin-top: 0;
        }
        .subtitle {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
        }
        input {
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: none; 
            border-radius: 10px;
            background: rgba(255,255,255,0.6); 
            outline: none; 
            box-sizing: border-box; 
            font-family: 'Poppins';
            font-size: 14px;
        }
        input::placeholder {
            color: #999;
        }
        input:focus {
            background: rgba(255,255,255,0.8);
            box-shadow: 0 0 10px rgba(214, 51, 132, 0.3);
        }
        button {
            width: 100%; 
            padding: 12px; 
            border: none; 
            border-radius: 10px; 
            background: #d63384;
            color: white; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s; 
            font-family: 'Poppins'; 
            margin-top: 15px;
            font-size: 15px;
        }
        button:hover { 
            background: #b02a6c; 
            transform: scale(1.05);
        }
        .error { 
            color: #d32f2f;
            background: rgba(211, 47, 47, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9em;
            border-left: 4px solid #d32f2f;
        }
        .sukses { 
            color: #388e3c;
            background: rgba(56, 142, 60, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9em;
            border-left: 4px solid #388e3c;
        }
        .sukses a {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
        }
        .sukses a:hover {
            text-decoration: underline;
        }
        .login-link {
            margin-top: 15px;
            font-size: 0.9em;
            color: #666;
        }
        .login-link a {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .password-info {
            font-size: 0.8em;
            color: #999;
            margin-top: 5px;
            text-align: left;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>💖 Daftar</h2>
        <p class="subtitle">Buat akun baru untuk menyimpan momen cinta Anda</p>
        
        <?php if(!empty($pesan_error)): ?>
            <div class="error">❌ <?php echo $pesan_error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($pesan_sukses)): ?>
            <div class="sukses"><?php echo $pesan_sukses; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input 
                type="text" 
                name="username" 
                placeholder="Username" 
                required
                value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
            >
            <div class="password-info">💡 Minimal 3 karakter, gunakan huruf dan angka</div>
            
            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
            >
            <div class="password-info">🔒 Minimal 6 karakter</div>
            
            <input 
                type="password" 
                name="password_konfirm" 
                placeholder="Konfirmasi Password" 
                required
            >
            
            <button type="submit" name="register">Daftar Sekarang</button>
        </form>
        
        <div class="login-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>
