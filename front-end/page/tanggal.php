<?php
session_start();
require '../../back-end/db_connect.php';

// Keamanan: Pengguna harus login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil parameter dari URL
$ruangan_id = isset($_GET['ruangan_id']) ? (int) $_GET['ruangan_id'] : 0;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$waktu_mulai = isset($_GET['waktu_mulai']) ? $_GET['waktu_mulai'] : '';
$waktu_selesai = isset($_GET['waktu_selesai']) ? $_GET['waktu_selesai'] : '';

// Jika ID ruangan tidak ada, booking tidak bisa dilanjutkan
if ($ruangan_id === 0) {
    die("Error: ID Ruangan tidak ditemukan.");
}
// Di atas file tanggal.php
$custom_ruangan_nama = isset($_GET['custom_ruangan_nama']) ? $_GET['custom_ruangan_nama'] : '';
// Ambil nama ruangan untuk ditampilkan di judul
$stmt = $conn->prepare("SELECT nama_ruangan FROM ruangan WHERE id = ?");
$stmt->bind_param("i", $ruangan_id);
$stmt->execute();
$result = $stmt->get_result();
$ruangan = $result->fetch_assoc();
$nama_ruangan = $ruangan['nama_ruangan'];
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style/pilih-tanggal.css" />
    <title>Pilih Tanggal & Waktu | FILKOMreserV</title>
</head>

<body>
    <nav>
        <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="window.location.href='index.php'"
            style="cursor: pointer;" />

        <ul class="nav__links">
            <li class="link"><a href="index.php">Beranda</a></li>
            <li class="link"><a href="index.php#booklet">Buku Panduan</a></li>
            <li class="link"><a href="#footer">Kontak Kami</a></li>

            <?php if (isset($_SESSION['user_id'])): // Cek ini sebenarnya redundant karena sudah ada proteksi di atas, tapi baik untuk konsistensi template ?>
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
            <?php endif; ?>
        </ul>
    </nav>

    <main class="section__container">
        <div class="tanggal__container">
            <h1>Form Peminjaman Ruangan: <?php echo htmlspecialchars($nama_ruangan); ?></h1>
            <p>Pastikan semua data terisi dengan benar, terutama berkas pendukung.</p>

            <form id="tanggalForm" class="tanggal__form" action="../../back-end/booking_process.php" method="POST"
                enctype="multipart/form-data">

                <input type="hidden" name="ruangan_id" value="<?php echo $ruangan_id; ?>">

                <input type="hidden" name="custom_ruangan_nama"
                    value="<?php echo htmlspecialchars($custom_ruangan_nama); ?>">

                <div class="form-group">
                    <label for="tanggal">Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control"
                        value="<?php echo htmlspecialchars($tanggal); ?>" required />
                </div>

                <div class="form-group">
                    <label for="checkin">Waktu Check-in:</label>
                    <input type="time" id="checkin" name="waktu_mulai" class="form-control"
                        value="<?php echo htmlspecialchars($waktu_mulai); ?>" required />
                </div>

                <div class="form-group">
                    <label for="checkout">Waktu Check-out:</label>
                    <input type="time" id="checkout" name="waktu_selesai" class="form-control"
                        value="<?php echo htmlspecialchars($waktu_selesai); ?>" required />
                </div>

                <div class="form-group">
                    <label for="fileUpload">Unggah Berkas Pendukung (KTM/Surat Tugas, .pdf, .jpg, .png):</label>
                    <input type="file" id="fileUpload" name="fileUpload" class="form-control-file" required />
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Singkat Kegiatan:</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control"
                        placeholder="Contoh: Rapat Himpunan Mahasiswa..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            </form>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <p id="loading-text">Memproses, harap tunggu...</p>
    </div>

</body>

</html>