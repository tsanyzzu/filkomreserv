<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username']; // Bisa email atau username
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // === PERIKSA DAN PASTIKAN BAGIAN INI BENAR ===
            // Arahkan ke file di dalam Front-end/page/
            if ($user['role'] === 'admin') {
                header("Location: ../Front-end/page/dashboard-admin.php");
            } else {
                header("Location: ../Front-end/page/index.php");
            }
            exit(); // Penting untuk menghentikan eksekusi setelah redirect
        } else {
            echo "Password salah. <a href='../Front-end/page/login.php'>Coba lagi</a>";
        }
    } else {
        echo "Username atau email tidak ditemukan. <a href='../Front-end/page/login.php'>Coba lagi</a>";
    }

    $stmt->close();
    $conn->close();
}
?>