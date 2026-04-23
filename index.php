<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// Proses Hapus Foto
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    $get_foto = $conn->query("SELECT image_name FROM photos WHERE id='$id'")->fetch_assoc();
    if(file_exists("uploads/".$get_foto['image_name'])) {
        unlink("uploads/".$get_foto['image_name']);
    }
    $conn->query("DELETE FROM photos WHERE id='$id'");
    header("Location: index.php");
}

// Proses Upload Foto
if(isset($_POST['upload'])){
    $title = $_POST['title'];
    $desc = $_POST['description'];
    
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $fotobaru = date('YmdHis').$foto;
    $path = "uploads/".$fotobaru;

    if(move_uploaded_file($tmp, $path)){
        $conn->query("INSERT INTO photos (title, description, image_name) VALUES ('$title', '$desc', '$fotobaru')");
        header("Location: index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Cinta Kita</title>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; padding: 0; background: #fff0f5; font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        .header {
            text-align: center; padding: 40px 20px; background: linear-gradient(to bottom, #ffd1dc, #fff0f5);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: relative;
        }
        .header h1 { font-family: 'Caveat', cursive; font-size: 4em; color: #d63384; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); }
        .header p { color: #888; font-size: 1.1em; }
        
        /* Tombol & Form Upload */
        .btn-upload {
            background: #ff6b81; color: white; border: none; padding: 10px 25px; border-radius: 20px;
            font-size: 1em; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(255, 107, 129, 0.4);
            font-family: 'Poppins'; margin-top: 10px;
        }
        .btn-upload:hover { background: #ff4757; transform: translateY(-2px); }
        
        .upload-modal {
            display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: white; padding: 30px; border-radius: 15px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            z-index: 1000; width: 90%; max-width: 400px;
        }
        .upload-modal input, .upload-modal textarea {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Poppins';
        }
        .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; backdrop-filter: blur(3px); }

        /* Polaroid Gallery */
        .gallery {
            display: flex; flex-wrap: wrap; justify-content: center; padding: 40px 20px; gap: 30px;
        }
        .polaroid {
            background: white; padding: 15px 15px 30px 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            width: 250px; text-align: center; transition: 0.4s ease; position: relative; border-radius: 5px;
        }
        /* Efek Miring Random untuk Polaroid */
        .polaroid:nth-child(even) { transform: rotate(3deg); }
        .polaroid:nth-child(odd) { transform: rotate(-3deg); }
        .polaroid:hover { transform: scale(1.08) rotate(0deg); z-index: 10; box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        
        .polaroid img { width: 100%; height: 250px; object-fit: cover; border-radius: 3px; }
        .polaroid h3 { font-family: 'Caveat', cursive; font-size: 2em; margin: 15px 0 5px 0; color: #333; }
        .polaroid p { font-size: 0.85em; color: #666; margin: 0; padding: 0 10px; line-height: 1.4; }
        .polaroid .time { font-size: 0.7em; color: #aaa; margin-top: 10px; display: block; }
        
        .btn-delete {
            position: absolute; top: -10px; right: -10px; background: #ff4757; color: white;
            width: 30px; height: 30px; border-radius: 50%; text-decoration: none; display: flex;
            align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            opacity: 0; transition: 0.3s;
        }
        .polaroid:hover .btn-delete { opacity: 1; }

        /* Efek Hati Jatuh */
        .heart { position: fixed; color: #ff6b81; font-size: 20px; animation: fall linear forwards; pointer-events: none; z-index: 1; }
        @keyframes fall {
            to { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .polaroid { width: 90%; }
            .header h1 { font-size: 3em; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Perjalanan Cinta Kita ❤️</h1>
        <p>Setiap foto adalah detik di mana aku jatuh cinta padamu, lagi dan lagi.</p>
        <button class="btn-upload" onclick="toggleModal()">+ Tambah Kenangan</button>
    </div>

    <div class="overlay" id="overlay" onclick="toggleModal()"></div>
    <div class="upload-modal" id="modal">
        <h3 style="text-align: center; color: #d63384; font-family: 'Caveat'; font-size: 2em; margin-top: 0;">Simpan Momen Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Nama Momen (ex: Kencan Pertama)" required>
            <textarea name="description" rows="3" placeholder="Ceritakan sedikit tentang foto ini sayang..." required></textarea>
            <input type="file" name="foto" accept="image/*" required>
            <button type="submit" name="upload" class="btn-upload" style="width: 100%;">Upload Momen</button>
        </form>
    </div>

    <div class="gallery">
        <?php
        $data = $conn->query("SELECT * FROM photos ORDER BY id DESC");
        while($row = $data->fetch_assoc()){
            // Format waktu menjadi lebih cantik
            $waktu = date("d F Y, H:i", strtotime($row['upload_time']));
        ?>
        <div class="polaroid">
            <a href="?hapus=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Yakin mau hapus kenangan ini sayang?')">X</a>
            <img src="uploads/<?= $row['image_name'] ?>" alt="Foto Kita">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <span class="time">Disimpan pada: <?= $waktu ?></span>
        </div>
        <?php } ?>
    </div>

    <script>
        // JS untuk Pop-up Modal Upload
        function toggleModal() {
            var modal = document.getElementById('modal');
            var overlay = document.getElementById('overlay');
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
                overlay.style.display = 'none';
            } else {
                modal.style.display = 'block';
                overlay.style.display = 'block';
            }
        }

        // JS untuk Efek Hati Jatuh (Bikin Meleleh)
        function createHeart() {
            const heart = document.createElement('div');
            heart.classList.add('heart');
            heart.innerHTML = '❤️';
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = Math.random() * 3 + 2 + 's'; // Waktu jatuh random (2-5 detik)
            heart.style.fontSize = Math.random() * 15 + 10 + 'px'; // Ukuran random
            
            document.body.appendChild(heart);
            
            // Hapus hati dari DOM setelah selesai animasi agar tidak berat
            setTimeout(() => {
                heart.remove();
            }, 5000);
        }
        // Munculkan hati setiap 300 ms
        setInterval(createHeart, 300);
    </script>
</body>
</html>