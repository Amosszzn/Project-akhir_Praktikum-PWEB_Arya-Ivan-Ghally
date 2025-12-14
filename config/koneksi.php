<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "tokosepatu_ghally";

$koneksi = new mysqli($host, $username, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

function bersihkan_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $koneksi->real_escape_string($data);
}

function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>