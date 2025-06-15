<?php
session_start();
require '../../back-end/db_connect.php';
?>
<!DOCTYPE html>
<html lang="id" id="top">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
  <link rel="stylesheet" href="../style/styles.css" />
  <link rel="icon" type="image/png" href="../assets/LOGO.png" />
  <script src="../script/script.js"></script>

  <title>FILKOMreserV | Sistem Peminjaman Ruangan</title>
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
  <header class="section__container header__container">
    <div class="header__image__container">
      <div class="header__content">
        <h1>Atur Peminjaman Ruangan dengan Mudah</h1>
        <p>
          Reservasi ruangan di FILKOM UB secara cepat, efisien, dan
          terorganisir dengan FILKOMreserV.
        </p>
      </div>
      <div class="booking__container">
        <form action="list.php" method="GET">
          <div class="form__group">
            <div class="input__group">
              <input type="date" id="date-picker" name="tanggal" required />
              <label>Tanggal Peminjaman</label>
            </div>
            <p>Pilih tanggal peminjaman</p>
          </div>
          <div class="form__group">
            <div class="input__group">
              <input type="time" id="start-time-picker" name="waktu_mulai" required />
              <label>Waktu Mulai</label>
            </div>
            <p>Pilih waktu mulai peminjaman</p>
          </div>
          <div class="form__group">
            <div class="input__group">
              <input type="time" id="end-time-picker" name="waktu_selesai" required />
              <label>Waktu Selesai</label>
            </div>
            <p>Pilih waktu selesai peminjaman</p>
          </div>
          <div class="form__group">
            <div class="input__group">
              <input type="number" id="capacity" name="kapasitas" min="1" required />
              <label>Kapasitas</label>
            </div>
            <p>Tentukan jumlah orang</p>
          </div>
          <button type="submit" class="btn"><i class="ri-search-line"></i></button>
        </form>
      </div>
    </div>
  </header>


  <section class="section__container popular__container">
    <p></p>
    <h2 class="section__header">Ruangan yang tersedia di FILKOM</h2>
    <div class="popular__grid">
      <?php
      $sql = "SELECT * FROM ruangan WHERE parent_id IS NULL ORDER BY nama_ruangan ASC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while ($ruangan = $result->fetch_assoc()) {
          ?>
          <div class="popular__card">
            <img src="<?php echo htmlspecialchars($ruangan['gambar']); ?>"
              alt="<?php echo htmlspecialchars($ruangan['nama_ruangan']); ?>" />
            <div class="popular__content">
              <div class="popular__card__header">
                <h4><?php echo htmlspecialchars($ruangan['nama_ruangan']); ?></h4>
                <h4><?php echo htmlspecialchars($ruangan['kapasitas']); ?> Orang</h4>
              </div>
              <div class="popular__description">
                <p><?php echo htmlspecialchars($ruangan['deskripsi']); ?></p>

                <a href="informasi.php?id=<?php echo $ruangan['id']; ?>">
                  <p class="lebih__lanjut">Detail</p>
                </a>
              </div>
            </div>
          </div>
          <?php
        } // Akhir dari while loop
      } else {
        echo "<p>Tidak ada ruangan yang tersedia saat ini.</p>";
      }
      $conn->close();
      ?>
    </div>
  </section>

  <section class="client">
    <div class="section__container client__container">
      <h2 class="section__header">Testimoni Pengguna</h2>
      <div class="client__grid">
        <div class="client__card">
          <img src="../assets/client-1.jpg" alt="Client 1" />
          <p>
            Proses peminjaman sangat cepat, dan notifikasi diterima instan.
            FILKOMreserV membuat segalanya lebih mudah!
          </p>
        </div>
        <div class="client__card">
          <img src="../assets/client-2.jpg" alt="Client 2" />
          <p>
            Saya dapat melihat ketersediaan ruangan secara detail, termasuk
            fasilitasnya. Sangat membantu untuk kegiatan saya.
          </p>
        </div>
        <div class="client__card">
          <img src="../assets/client-3.jpg" alt="Client 3" />
          <p>
            Peminjaman ruangan selesai dalam beberapa menit. Sistem ini sangat
            efisien!
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="section__container" id="booklet">
    <div class="reward__container">
      <p style="font-size: 30px; font-weight: bold">Buku Panduan</p>
      <h4>Buku panduan untuk memahami alur peminjaman ruangan di FILKOM.</h4>
      <a href="https://drive.google.com/file/d/1p_tvT4LuMu7fshY09A8rpIGc8lM1mUZo/view?usp=sharing"><button
          class="reward__btn">Klik di sini</button></a>
    </div>
  </section>

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
    <a href="#top" class="scroll-to-top" aria-label="Scroll to top">
      <i class="ri-arrow-up-s-line"></i>
    </a>
    <script>
      // Menampilkan tombol ketika halaman di-scroll ke bawah
      const scrollToTopButton = document.querySelector(".scroll-to-top");

      window.addEventListener("scroll", () => {
        if (window.scrollY > 1500) {
          // Jika sudah di-scroll lebih dari 200px
          scrollToTopButton.style.display = "flex";
        } else {
          scrollToTopButton.style.display = "none";
        }
      });
    </script>
  </footer>
  <script>
    // Script untuk membatasi pencarian tanggal dan waktu di masa lalu
    document.addEventListener('DOMContentLoaded', function () {
      const datePicker = document.getElementById('date-picker');
      const startTimePicker = document.getElementById('start-time-picker');

      // Dapatkan tanggal dan waktu hari ini dalam format yang benar
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');

      // Ini adalah sintaks JavaScript TEMPLATE LITERAL yang BENAR
      const today = `${year}-${month}-${day}`;
      const currentTime = `${hours}:${minutes}`;

      // Atur tanggal minimal yang bisa dipilih adalah hari ini
      datePicker.min = today;

      // Fungsi untuk mengatur waktu minimal jika tanggal yang dipilih adalah hari ini
      function setMinTime() {
        if (datePicker.value === today) {
          startTimePicker.min = currentTime;
        } else {
          // Hapus batasan waktu jika memilih hari di masa depan
          startTimePicker.min = null;
        }
      }

      // Panggil fungsi saat halaman dimuat dan setiap kali tanggal diubah
      setMinTime();
      datePicker.addEventListener('change', setMinTime);
    });
  </script>
</body>

</html>