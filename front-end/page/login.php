<?php
session_start();

// Jika pengguna sudah memiliki sesi (sudah login)
if (isset($_SESSION['user_id'])) {
    // Arahkan berdasarkan peran (role)
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard-admin.php");
    } else {
        header("Location: index.php");
    }
    exit(); // Hentikan eksekusi skrip agar tidak menampilkan sisa halaman
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style/login.css" />
    <title>Login | FILKOMreserV</title>
</head>

<body>
    <nav>
        <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="window.location.href='index.php'"
            style="cursor: pointer;" />
        <ul class="nav__links">
            <li class="link"><a href="index.php">Beranda</a></li>
            <li class="link"><a href="index.php#booklet">Buku Panduan</a></li>
            <li class="link"><a href="#footer">Kontak Kami</a></li>
        </ul>
    </nav>

    <main class="section__container">
        <div class="login-container" id="loginContainer">
            <div class="login-card">
                <div class="login-header">
                    <h1>Login</h1>
                    <p>Silakan login untuk melanjutkan</p>
                </div>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php endif; ?>
                <form id="loginForm" action="../../back-end/login_process.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username atau Email</label>
                        <input type="username" id="username" name="username" class="form-control"
                            placeholder="Masukkan username atau email Anda" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Masukkan password Anda" required />
                    </div>
                    <button id="loginButton" type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <div class="login-footer">
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                    <p>Lupa Password?</p>
                </div>
            </div>
        </div>
        <p></p>
    </main>


    <footer class="footer" id="footer">
        <div class="section__container footer__container">
            <div class="footer__col">
                <h3>FILKOMreserV</h3>
                <p>
                    Sistem yang memudahkan mahasiswa dan dosen untuk memesan ruangan di Fakultas Ilmu Komputer.
                </p>
                <p>
                    Kemudahan akses, efisiensi waktu, dan transparansi adalah fokus utama kami.
                </p>
            </div>
            <div class="footer__col">
                <h4>Informasi Kontak</h4>
                <p>
                    Jl. Veteran, Ketawanggede, Lowokwaru, Kota Malang, Jawa Timur, Indonesia - 65145
                </p>
                <p>filkom@ub.ac.id</p>
                <p>+ 01 234 567 88</p>
                <p>+ 01 234 567 89</p>
            </div>
            <div class="footer__col">
                <h4>Legal</h4>
                <p>FAQ</p>
                <p>Syarat & Ketentuan</p>
                <p>Kebijakan Privasi</p>
            </div>
        </div>
        <div class="footer__bar">Copyright © 2024 FILKOMreserV.</div>
    </footer>

</body>

</html>