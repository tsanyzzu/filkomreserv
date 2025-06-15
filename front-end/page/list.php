<?php
session_start();
require '../../back-end/db_connect.php';

// Ambil parameter pencarian awal
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$waktu_mulai = isset($_GET['waktu_mulai']) ? $_GET['waktu_mulai'] : '';
$waktu_selesai = isset($_GET['waktu_selesai']) ? $_GET['waktu_selesai'] : '';
$kapasitas = isset($_GET['kapasitas']) && is_numeric($_GET['kapasitas']) ? (int) $_GET['kapasitas'] : 0;

if (empty($tanggal) || empty($waktu_mulai) || empty($waktu_selesai) || $kapasitas <= 0) {
  die("Parameter pencarian tidak lengkap. Silakan kembali.");
}

// Query utama: hanya mengambil gedung induk (parent_id IS NULL) atau ruangan mandiri.
// Logika ketersediaan jadwalnya kita pindahkan ke query sub-ruangan nanti.
$main_sql = "SELECT * FROM ruangan WHERE parent_id IS NULL AND kapasitas >= ?";
$main_stmt = $conn->prepare($main_sql);
$main_stmt->bind_param("i", $kapasitas);
$main_stmt->execute();
$main_result = $main_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>List Ruangan yang Tersedia</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../style/list.css" />
</head>

<body>
  <nav>
    <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="window.location.href='index.php'"
      style="cursor: pointer;" />
    <ul class="nav__links">
      <li class="link"><a href="index.php">Beranda</a></li>
      <li class="link"><a href="#booklet">Buku Panduan</a></li>
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
        <a href="login.php" class="btn" style="color: var(--white); background-color: var(--primary-color);">Login</a>
      <?php endif; ?>
    </ul>
  </nav>

  <div class="container mt-5">
    <h1 class="text-center mb-4">List Ruangan yang Tersedia</h1>
    <p class="text-center text-muted">
      Hasil pencarian untuk <strong><?php echo date("d F Y", strtotime($tanggal)); ?></strong>,
      jam <strong><?php echo $waktu_mulai; ?> - <?php echo $waktu_selesai; ?></strong>
    </p>

    <div id="room-list" class="row">
      <?php
      if ($main_result->num_rows > 0) {
        while ($ruangan = $main_result->fetch_assoc()) {
          ?>
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
              <img src="<?php echo htmlspecialchars($ruangan['gambar']); ?>" class="card-img-top"
                alt="<?php echo htmlspecialchars($ruangan['nama_ruangan']); ?>">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($ruangan['nama_ruangan']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($ruangan['deskripsi']); ?></p>
              </div>
              <div class="card-footer bg-white">
                <a href="informasi.php?id=<?php echo $ruangan['id']; ?>" class="btn btn-secondary btn-block mb-2">Lihat
                  Detail Gedung</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                  <?php if ($ruangan['is_bookable']): ?>
                    <a href="tanggal.php?ruangan_id=<?php echo $ruangan['id']; ?>&tanggal=<?php echo $tanggal; ?>&waktu_mulai=<?php echo $waktu_mulai; ?>&waktu_selesai=<?php echo $waktu_selesai; ?>"
                      class="btn btn-primary btn-block">Pesan Sekarang</a>
                  <?php else: ?>
                    <form action="tanggal.php" method="GET">
                      <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                      <input type="hidden" name="waktu_mulai" value="<?php echo htmlspecialchars($waktu_mulai); ?>">
                      <input type="hidden" name="waktu_selesai" value="<?php echo htmlspecialchars($waktu_selesai); ?>">

                      <div class="form-group">
                        <label for="sub_ruangan_<?php echo $ruangan['id']; ?>">Pilih Ruangan Tersedia:</label>
                        <select name="ruangan_id" id="sub_ruangan_<?php echo $ruangan['id']; ?>" class="form-control" required>
                          <option value="" disabled selected>-- Pilih Opsi --</option>
                          <?php
                          // Query untuk mencari sub-ruangan yang TERSEDIA
                          $sub_sql = "SELECT * FROM ruangan WHERE parent_id = ? AND is_bookable = 1 AND id NOT IN (SELECT ruangan_id FROM peminjaman WHERE tanggal_pinjam = ? AND status = 'approved' AND (waktu_mulai < ? AND waktu_selesai > ?))";
                          $sub_stmt = $conn->prepare($sub_sql);
                          $sub_stmt->bind_param("isss", $ruangan['id'], $tanggal, $waktu_selesai, $waktu_mulai);
                          $sub_stmt->execute();
                          $sub_result = $sub_stmt->get_result();
                          while ($sub = $sub_result->fetch_assoc()) {
                            echo '<option value="' . $sub['id'] . '">' . $sub['nama_ruangan'] . ' (Kapasitas: ' . $sub['kapasitas'] . ')</option>';
                          }
                          // Tambahkan opsi "Lainnya"
                          $other_option_id = $conn->query("SELECT id FROM ruangan WHERE parent_id = " . $ruangan['id'] . " AND nama_ruangan LIKE 'Lainnya%'")->fetch_assoc()['id'];
                          echo '<option value="' . $other_option_id . '">Lainnya (Isi Sendiri)</option>';
                          $sub_stmt->close();
                          ?>
                        </select>
                      </div>
                      <button type="submit" class="btn btn-info btn-block">Pesan Ruangan Ini</button>
                    </form>
                  <?php endif; ?>
                <?php else: ?>
                  <a href="login.php" class="btn btn-primary btn-block">Login untuk Memesan</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php
        } // Akhir while loop
      } else {
        echo "<div class='col-12'><div class='alert alert-warning text-center'>Tidak ada gedung/ruangan yang cocok dengan kriteria kapasitas Anda.</div></div>";
      }
      $main_stmt->close();
      $conn->close();
      ?>
    </div>
  </div>
</body>

</html>