<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi 
    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password)) {
        header("Location: ../Front-end/page/register.php?error=Semua kolom wajib diisi");
        exit();
    }
    if ($password !== $confirm_password) {
        header("Location: ../Front-end/page/register.php?error=Password dan konfirmasi password tidak cocok");
        exit();
    }
    if (strlen($password) < 8) {
        header("Location: ../Front-end/page/register.php?error=Password minimal harus 8 karakter");
        exit();
    }

    // Cek apakah username atau email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../Front-end/page/register.php?error=Username atau Email sudah terdaftar");
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan pengguna baru ke database
    $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, email, password, role) VALUES (?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssss", $nama_lengkap, $username, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: ../Front-end/page/login.php?success=Registrasi berhasil! Silakan login.");
        exit();
    } else {
        header("Location: ../Front-end/page/register.php?error=Terjadi kesalahan. Coba lagi nanti.");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>