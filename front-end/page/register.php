<?php
session_start();
// Jika user sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Akun | FILKOMreserV</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../style/login.css" /> </head>
<body>
    <nav>
        <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="window.location.href='index.php'" style="cursor: pointer;" />
        <ul class="nav__links">
            <li class="link"><a href="index.php">Beranda</a></li>
        </ul>
    </nav>
    <main class="section__container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Buat Akun Baru</h1>
                    <p>Silakan isi data diri Anda</p>
                </div>
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                <form id="registerForm" action="../../back-end/register_process.php" method="POST">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap Anda" required />
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Pilih username unik" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Gunakan email institusi" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required />
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ketik ulang password Anda" required />
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
                <div class="login-footer mt-3">
                    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>