<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Password Hash Generator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .container { max-width: 600px; margin-top: 50px; }
        .hash-result { 
            word-wrap: break-word; 
            background-color: #e9ecef; 
            padding: 15px; 
            border-radius: 5px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Password Hash Generator</h2>
                <p>Gunakan form ini untuk membuat hash password yang aman untuk database Anda.</p>
                <form method="POST" action="buat_hash.php">
                    <div class="form-group">
                        <label for="password">Masukkan Password:</label>
                        <input type="text" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Hash</button>
                </form>

                <?php
                // Cek jika form sudah disubmit
                if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['password'])) {
                    $password = $_POST['password'];
                    // Buat hash menggunakan algoritma default yang aman
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    echo '<hr>';
                    echo '<h4>Hasil Hash:</h4>';
                    echo '<p>Password Anda: <strong>' . htmlspecialchars($password) . '</strong></p>';
                    echo '<p>Copy dan paste hash di bawah ini ke kolom password di database:</p>';
                    echo '<div class="hash-result">' . $hash . '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>