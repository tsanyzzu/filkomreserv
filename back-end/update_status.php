<?php
session_start();
require 'db_connect.php';

// hanya admin 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Validasi status
    $allowed_statuses = ['approved', 'rejected'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE peminjaman SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            header("Location: ../front-end/page/dashboard-admin.php");
        } else {
            echo "Gagal mengupdate status.";
        }
        $stmt->close();
    } else {
        echo "Status tidak valid.";
    }
} else {
    echo "Parameter tidak lengkap.";
}
$conn->close();
?>