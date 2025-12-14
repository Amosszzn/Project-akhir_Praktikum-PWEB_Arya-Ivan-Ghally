<?php
require_once 'config/koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = bersihkan_input($_GET['id']);

// Cek apakah ada pesanan yang terkait dengan sepatu ini
$check_query = "SELECT COUNT(*) as total FROM pesanan WHERE id_sepatu = '$id'";
$check_result = $koneksi->query($check_query);
$check_data = $check_result->fetch_assoc();

if ($check_data['total'] > 0) {
    $_SESSION['notifikasi'] = [
        'type' => 'error',
        'message' => 'Tidak bisa menghapus sepatu karena ada pesanan terkait!'
    ];
    header('Location: admin.php');
    exit();
}

// Hapus sepatu
$query = "DELETE FROM sepatu WHERE id = '$id'";

if ($koneksi->query($query)) {
    $_SESSION['notifikasi'] = [
        'type' => 'success',
        'message' => 'Sepatu berhasil dihapus!'
    ];
} else {
    $_SESSION['notifikasi'] = [
        'type' => 'error',
        'message' => 'Gagal menghapus sepatu: ' . $koneksi->error
    ];
}

header('Location: admin.php');
exit();
?>