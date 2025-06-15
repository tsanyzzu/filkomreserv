<?php
session_start();
session_unset();
session_destroy();

// === PERIKSA DAN PASTIKAN BAGIAN INI BENAR ===
// Arahkan kembali ke halaman login di Front-end/page/
header("Location: ../Front-end/page/");
exit(); // Penting untuk menghentikan eksekusi setelah redirect
?>