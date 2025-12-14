<?php
require_once 'config/koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = bersihkan_input($_POST['id']);
    $status = bersihkan_input($_POST['status']);
    
    $allowed_status = ['pending', 'diproses', 'dikirim', 'selesai'];
    
    if (!in_array($status, $allowed_status)) {
        $_SESSION['notifikasi'] = [
            'type' => 'error',
            'message' => 'Status tidak valid!'
        ];
        header('Location: admin.php');
        exit();
    }
    
    $query = "UPDATE pesanan SET status = '$status' WHERE id = '$id'";
    
    if ($koneksi->query($query)) {
        $_SESSION['notifikasi'] = [
            'type' => 'success',
            'message' => 'Status pesanan berhasil diubah!'
        ];
    } else {
        $_SESSION['notifikasi'] = [
            'type' => 'error',
            'message' => 'Gagal mengubah status: ' . $koneksi->error
        ];
    }
}

header('Location: admin.php');
exit();
?>