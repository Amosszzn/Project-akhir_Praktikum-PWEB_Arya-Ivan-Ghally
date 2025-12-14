<?php
require_once 'config/koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = bersihkan_input($_GET['id']);

// Cek status pesanan
$check_query = "SELECT status FROM pesanan WHERE id = '$id'";
$check_result = $koneksi->query($check_query);
$pesanan = $check_result->fetch_assoc();

if (!$pesanan) {
    header('Location: admin.php');
    exit();
}

// Hanya bisa hapus jika status selesai
if ($pesanan['status'] !== 'selesai') {
    $_SESSION['notifikasi'] = [
        'type' => 'error',
        'message' => 'Hanya pesanan dengan status SELESAI yang bisa dihapus!'
    ];
    header('Location: admin.php');
    exit();
}

// Hapus pesanan
$query = "DELETE FROM pesanan WHERE id = '$id'";

if ($koneksi->query($query)) {
    $_SESSION['notifikasi'] = [
        'type' => 'success',
        'message' => 'Pesanan berhasil dihapus!'
    ];
} else {
    $_SESSION['notifikasi'] = [
        'type' => 'error',
        'message' => 'Gagal menghapus pesanan: ' . $koneksi->error
    ];
}

header('Location: admin.php');
exit();
?>