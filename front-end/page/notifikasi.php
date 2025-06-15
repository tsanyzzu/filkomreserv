<?php
session_start();
require '../../back-end/db_connect.php';

// 1. Lindungi halaman ini: pengguna harus login untuk melihat notifikasi
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// 2. Ambil ID pengguna yang sedang login dari sesi
$user_id = $_SESSION['user_id'];

// 3. Siapkan query untuk mengambil semua riwayat peminjaman milik pengguna ini
// Kita JOIN dengan tabel ruangan untuk mendapatkan nama ruangannya
$sql = "SELECT p.tanggal_pinjam, p.waktu_mulai, p.waktu_selesai, p.status, p.tanggal_pengajuan, r.nama_ruangan
        FROM peminjaman p
        JOIN ruangan r ON p.ruangan_id = r.id
        WHERE p.user_id = ?
        ORDER BY p.tanggal_pengajuan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
  <link rel="stylesheet" href="../style/notifikasi.css" />
  <title>Riwayat | FILKOMreserV</title>
</head>

<body>
  <nav>
    <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="window.location.href='index.php'" style="cursor: pointer;" />
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
    <h1 class="notif-header">Riwayat Peminjaman</h1>
    <div id="notif-container">
      <?php
      if ($result->num_rows > 0) {
        // Jika ada notifikasi, tampilkan satu per satu
        while ($row = $result->fetch_assoc()) {
          $status = $row['status'];
          $alert_class = '';
          $status_message = '';
          $icon_class = '';

          // Tentukan style dan pesan berdasarkan status peminjaman
          if ($status == 'approved') {
            $alert_class = 'alert-success';
            $icon_class = 'ri-checkbox-circle-fill';
            $status_message = 'Peminjaman Anda telah <strong>Disetujui</strong>.';
          } elseif ($status == 'rejected') {
            $alert_class = 'alert-danger';
            $icon_class = 'ri-close-circle-fill';
            $status_message = 'Peminjaman Anda telah <strong>Ditolak</strong>.';
          } else { // 'pending'
            $alert_class = 'alert-warning';
            $icon_class = 'ri-time-fill';
            $status_message = 'Peminjaman Anda sedang <strong>Menunggu Verifikasi</strong> dari admin.';
          }
          ?>
          <div class="alert <?php echo $alert_class; ?>" role="alert">
            <h4 class="alert-heading"><i class="<?php echo $icon_class; ?>"></i>
              <?php echo htmlspecialchars($row['nama_ruangan']); ?></h4>
            <p><?php echo $status_message; ?></p>
            <hr>
            <p class="mb-0">
              Detail Jadwal: <strong><?php echo date("d F Y", strtotime($row['tanggal_pinjam'])); ?></strong>,
              pukul <strong><?php echo substr($row['waktu_mulai'], 0, 5); ?> -
                <?php echo substr($row['waktu_selesai'], 0, 5); ?></strong>.
            </p>
            <small class="text-muted">Diajukan pada:
              <?php echo date("d M Y, H:i", strtotime($row['tanggal_pengajuan'])); ?></small>
          </div>
          <?php
        } // Akhir dari while loop
      } else {
        // Tampilan jika tidak ada notifikasi sama sekali
        echo "<div class='alert alert-info text-center'>Anda belum memiliki Riwayat peminjaman.</div>";
      }
      $stmt->close();
      $conn->close();
      ?>
    </div>
  </main>

  <footer class="footer" id="footer">
    <div class="section__container footer__container">
      <div class="footer__col">
        <h3>FILKOMreserV</h3>
        <p>
          Sistem yang memudahkan mahasiswa dan dosen untuk memesan ruangan di
          Fakultas Ilmu Komputer.
        </p>
        <p>
          Kemudahan akses, efisiensi waktu, dan transparansi adalah fokus
          utama kami.
        </p>
      </div>
      <div class="footer__col">
        <h4>Informasi Kontak</h4>
        <p>
          Jl. Veteran, Ketawanggede, Lowokwaru, Kota Malang, Jawa Timur,
          Indonesia - 65145
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