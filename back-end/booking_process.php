<?php
session_start();
require 'db_connect.php';
$custom_ruangan_nama = !empty($_POST['custom_ruangan_nama']) ? $_POST['custom_ruangan_nama'] : NULL;
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Cek apakah metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $user_id = $_SESSION['user_id'];
    $ruangan_id = $_POST['ruangan_id'];
    $tanggal = $_POST['tanggal'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $deskripsi = $_POST['deskripsi'];
    $custom_ruangan_nama = !empty($_POST['custom_ruangan_nama']) ? $_POST['custom_ruangan_nama'] : NULL;
    try {
        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta')); // Waktu server saat ini
        $requested_time = new DateTime($tanggal . ' ' . $waktu_mulai, new DateTimeZone('Asia/Jakarta'));

        if ($requested_time < $now) {
            die("Error: Anda tidak dapat melakukan peminjaman untuk waktu yang sudah berlalu. Silakan kembali dan pilih jadwal lain.");
        }
    } catch (Exception $e) {
        die("Error: Format tanggal atau waktu tidak valid.");
    }
    $check_sql = "SELECT id FROM peminjaman
              WHERE ruangan_id = ?
              AND tanggal_pinjam = ?
              AND status = 'approved'
              AND (
                  (waktu_mulai < ? AND waktu_selesai > ?) OR
                  (waktu_mulai >= ? AND waktu_mulai < ?)
              )";

    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("isssss", $ruangan_id, $tanggal, $waktu_selesai, $waktu_mulai, $waktu_mulai, $waktu_selesai);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        die("Ruangan tidak tersedia atau telah dipesan. Silakan kembali dan pilih jadwal lain.");
    }
    $check_stmt->close();
    // 2. Proses Unggahan File
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == 0) {
        $upload_dir = 'uploads/'; // Buat folder 'uploads' di dalam folder 'back-end/'
        // Buat nama file yang unik untuk menghindari tumpang tindih
        $file_name = time() . '_' . basename($_FILES['fileUpload']['name']);
        $target_file = $upload_dir . $file_name;
        $file_path_for_db = 'uploads/' . $file_name; // Path yang akan disimpan ke DB

        // Pindahkan file dari lokasi sementara ke folder uploads
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $target_file)) {
            // 3. Jika file berhasil diunggah, masukkan data ke database
            $sql = "INSERT INTO peminjaman (user_id, ruangan_id, custom_ruangan_nama, tanggal_pinjam, waktu_mulai, waktu_selesai, file_path, deskripsi_kegiatan, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iissssss", $user_id, $ruangan_id, $custom_ruangan_nama, $tanggal, $waktu_mulai, $waktu_selesai, $file_path_for_db, $deskripsi);
            if ($stmt->execute()) {
                // 4. Jika berhasil, arahkan ke halaman notifikasi
                header("Location: ../Front-end/page/notifikasi.php");
                exit();
            } else {
                echo "Error: Gagal menyimpan data peminjaman. " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: Gagal mengunggah file.";
        }
    } else {
        echo "Error: Berkas pendukung wajib diunggah.";
    }
    $conn->close();
}
?>