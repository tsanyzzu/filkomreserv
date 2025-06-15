<?php
session_start();
require '../../back-end/db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn .jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
  <link rel="stylesheet" href="../style/notifikasi.css" />
  <title>Dashboard Admin | FILKOMreserV</title>
</head>

<body>
  <nav>
    <img class="logo" src="../assets/LOGO.png" alt="Logo FILKOMreserV" onclick="location.reload();" />

    <ul class="nav__links">
      <li class="link"><a href="dashboard-admin.php">Dashboard</a></li>
      <li class="link">
        <span style="color: black; margin-right: 15px; font-weight: bold;">Admin:
          <?php echo htmlspecialchars($_SESSION['username']); ?></span>
      </li>
      <a href="../../back-end/logout.php" class="btn"
        style="color: var(--white); background-color: var(--primary-color);">Logout</a>
    </ul>
  </nav>

  <main class="section__container">
    <h1 class="notif-header">Dashboard Admin - Verifikasi Peminjaman</h1>
    <div id="notif-container"></div>
  </main>
  <div class="table-container px-5">
    <table class="table table-striped table-hover">
      <thead class="table-primary">
        <tr style="background-color: #2c3855; color: #ffff">
          <th>No</th>
          <th>Nama Peminjam</th>
          <th>Ruangan</th>
          <th>Tanggal</th>
          <th>Waktu</th>
          <th>Status</th>
          <th>Aksi</th>
          <th>Download Berkas</th>
        </tr>
      </thead>
      <tbody id="loanTableBody">
        <?php
        // mengambil semua data peminjaman dan diurutkan dari yang terbaru
        $sql = "SELECT p.id, u.nama_lengkap AS nama_peminjam, r.nama_ruangan, p.tanggal_pinjam, 
                   p.waktu_mulai, p.waktu_selesai, p.status, p.file_path
            FROM peminjaman p
            JOIN users u ON p.user_id = u.id
            JOIN ruangan r ON p.ruangan_id = r.id
            ORDER BY p.tanggal_pengajuan DESC";

        $result = $conn->query($sql);
        $no = 1;

        if ($result->num_rows > 0) {
          // looping untuk setiap baris data peminjaman
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_peminjam']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_ruangan']) . "</td>";
            echo "<td>" . date("d M Y", strtotime($row['tanggal_pinjam'])) . "</td>";
            echo "<td>" . substr($row['waktu_mulai'], 0, 5) . " - " . substr($row['waktu_selesai'], 0, 5) . "</td>";

            // warna status
            $status_class = '';
            if ($row['status'] == 'approved')
              $status_class = 'text-success font-weight-bold';
            if ($row['status'] == 'rejected')
              $status_class = 'text-danger font-weight-bold';
            if ($row['status'] == 'pending')
              $status_class = 'text-warning font-weight-bold';
            echo "<td><span class='" . $status_class . "'>" . htmlspecialchars(ucfirst($row['status'])) . "</span></td>";

            // menampilkan tombol Aksi hanya jika status masih 'pending'
            echo "<td>";
            if ($row['status'] == 'pending') {
              echo "<a href='../../back-end/update_status.php?id=" . $row['id'] . "&status=approved' class='btn btn-success btn-sm mb-1'>Approve</a> ";
              echo "<a href='../../back-end/update_status.php?id=" . $row['id'] . "&status=rejected' class='btn btn-danger btn-sm'>Reject</a>";
            } else {
              echo "-"; 
            }
            echo "</td>";

            // download berkas
            echo "<td><a href='../../back-end/" . htmlspecialchars($row['file_path']) . "' class='btn btn-info btn-sm' target='_blank'>Download</a></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='8' class='text-center'>Belum ada data peminjaman yang masuk.</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>

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