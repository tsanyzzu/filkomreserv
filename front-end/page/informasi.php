<?php
session_start();
require '../../back-end/db_connect.php';

// Cek apakah ID ruangan ada di URL dan merupakan angka
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID Ruangan tidak valid."); // Tampilkan pesan error dan hentikan skrip
}

$ruangan_id = $_GET['id'];

// Ambil data detail ruangan menggunakan prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT * FROM ruangan WHERE id = ?");
$stmt->bind_param("i", $ruangan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Ruangan tidak ditemukan."); // Tampilkan pesan jika ID tidak ada di DB
}

// Simpan data ruangan ke dalam variabel
$ruangan = $result->fetch_assoc();
$stmt->close();


// Ambil data riwayat penggunaan untuk ruangan ini (yang sudah di-approve)
$history_stmt = $conn->prepare(
    "SELECT p.tanggal_pinjam, u.username 
     FROM peminjaman p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.ruangan_id = ? AND p.status = 'approved' 
     ORDER BY p.tanggal_pinjam DESC"
);
$history_stmt->bind_param("i", $ruangan_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$history_stmt->close();

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style/informasi.css" />
    <title>Informasi Ruangan | FILKOMreserV</title>
</head>

<body>
    <nav>
        <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="window.location.href='index.php'" style="cursor: pointer;" />
        <ul class="nav__links">
            <li class="link"><a href="index.php">Beranda</a></li>
            <li class="link"><a href="index.php#booklet">Buku Panduan</a></li>
            <li class="link"><a href="#footer">Kontak Kami</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="link">
                    <a href="notifikasi.php" style="display: flex; align-items: center;">
                        <i class="ri-notification-3-line" style="font-size: 1.5rem; margin-right: 5px;"></i>
                        Riwayat
                    </a>
                </li>
                <li class="link">
                    <span style="color: black; margin-right: 15px; font-weight: bold;">Halo,
                        <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                </li>
                <a href="../../back-end/logout.php" class="btn"
                    style="color: var(--white); background-color: var(--primary-color);">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn"
                    style="color: var(--white); background-color: var(--primary-color);">Login</a>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="section__container">
        <div class="informasi__ruangan">
            <h1>Informasi Ruangan</h1>
            <!-- Foto Ruangan -->
            <img id="ruangan-image" src="<?php echo htmlspecialchars($ruangan['gambar']); ?>"
                alt="Foto <?php echo htmlspecialchars($ruangan['nama_ruangan']); ?>" class="ruangan__image" />
            <div class="ruangan__detail">
                <!-- Nama Ruangan -->
                <h2 id="ruangan-name"><?php echo htmlspecialchars($ruangan['nama_ruangan']); ?></h2>
                <p>
                    <strong>Kapasitas:</strong>
                    <span id="ruangan-capacity"><?php echo htmlspecialchars($ruangan['kapasitas']); ?> Orang</span>
                </p>
                <p><strong>Fasilitas:</strong></p>
                <ul id="ruangan-facilities">
                    <li>Proyektor</li>
                    <li>Papan Tulis</li>
                    <li>AC</li>
                    <li>Sound System</li>
                </ul>
                <p><strong>Riwayat Penggunaan:</strong></p>
                <ul id="ruangan-usage-history">
                    <?php
                    if ($history_result->num_rows > 0) {
                        while ($history_row = $history_result->fetch_assoc()) {
                            echo "<li>";
                            echo "Digunakan oleh <strong>" . htmlspecialchars($history_row['username']) . "</strong>";
                            echo " pada tanggal <strong>" . date("d F Y", strtotime($history_row['tanggal_pinjam'])) . "</strong>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li>Belum ada riwayat penggunaan yang tercatat.</li>";
                    }
                    $conn->close(); // Tutup koneksi setelah semua query selesai
                    ?>
                </ul>
            </div>
        </div>
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