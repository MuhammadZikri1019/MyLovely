<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $cek = $stmt->get_result();
    
    if($cek->num_rows > 0){
        $row = $cek->fetch_assoc();
        // Gunakan password_verify untuk keamanan
        if(password_verify($pass, $row['password'])){
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Sayang, username atau passwordnya salah ih :(";
        }
    } else {
        $error = "Sayang, username atau passwordnya salah ih :(";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ruang Cinta Kita</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh;
            background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
            font-family: 'Poppins', sans-serif; overflow: hidden;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 20px; box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            text-align: center; width: 90%; max-width: 350px; border: 1px solid rgba(255,255,255,0.5);
        }
        h2 { font-family: 'Dancing Script', cursive; font-size: 3em; color: #d63384; margin-bottom: 10px; }
        input {
            width: 100%; padding: 12px; margin: 10px 0; border: none; border-radius: 10px;
            background: rgba(255,255,255,0.6); outline: none; box-sizing: border-box; font-family: 'Poppins';
        }
        button {
            width: 100%; padding: 12px; border: none; border-radius: 10px; background: #d63384;
            color: white; font-weight: bold; cursor: pointer; transition: 0.3s; font-family: 'Poppins'; margin-top: 10px;
        }
        button:hover { background: #b02a6c; transform: scale(1.05); }
        .error { color: red; font-size: 0.8em; margin-bottom: 10px; }
        .register-link {
            margin-top: 15px;
            font-size: 0.9em;
            color: #6c757d;
        }
        .register-link a {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Ruang Kita</h2>
        <p style="color: #6c757d; font-size: 0.9em;">Masuk untuk melihat kenangan kita ❤️</p>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username (sayang)" required>
            <input type="password" name="password" placeholder="Password (cinta)" required>
            <button type="submit" name="login">Buka Kenangan</button>
        </form>
        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>