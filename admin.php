<?php
require_once 'config/koneksi.php';
session_start();

// Cek jika ada notifikasi
if (isset($_SESSION['notifikasi'])) {
    $notifikasi = $_SESSION['notifikasi'];
    unset($_SESSION['notifikasi']);
}

// Ambil data sepatu
$query_sepatu = "SELECT * FROM sepatu ORDER BY dibuat_kapan DESC";
$result_sepatu = $koneksi->query($query_sepatu);

// Ambil data pesanan
$query_pesanan = "SELECT p.*, s.nama as nama_sepatu FROM pesanan p 
                  JOIN sepatu s ON p.id_sepatu = s.id 
                  ORDER BY p.dibuat_kapan DESC";
$result_pesanan = $koneksi->query($query_pesanan);

// Hitung statistik
$total_sepatu = $koneksi->query("SELECT COUNT(*) as total FROM sepatu")->fetch_assoc()['total'];
$total_stok = $koneksi->query("SELECT SUM(stok) as total FROM sepatu")->fetch_assoc()['total'];
$total_pesanan = $koneksi->query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Toko Sepatu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shoe-prints"></i>
                    <h1>Admin SepatuKu</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Toko</a></li>
                        <li><a href="admin.php" class="active"><i class="fas fa-cog"></i> Admin</a></li>
                        <li><a href="#data-sepatu"><i class="fas fa-shoe-prints"></i> Data Sepatu</a></li>
                        <li><a href="#pesanan"><i class="fas fa-clipboard-list"></i> Pesanan</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (isset($notifikasi)): ?>
        <div class="notification <?php echo $notifikasi['type']; ?>">
            <?php echo $notifikasi['message']; ?>
        </div>
        <?php endif; ?>

        <section style="margin: 2rem 0; text-align: center;">
            <h2>Dashboard Admin</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 2rem 0;">
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                    <h3>Total Sepatu</h3>
                    <div style="font-size: 2.5rem; color: #3498db; font-weight: bold;"><?php echo $total_sepatu; ?></div>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                    <h3>Total Stok</h3>
                    <div style="font-size: 2.5rem; color: #2ecc71; font-weight: bold;"><?php echo $total_stok ?: 0; ?></div>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                    <h3>Total Pesanan</h3>
                    <div style="font-size: 2.5rem; color: #e74c3c; font-weight: bold;"><?php echo $total_pesanan; ?></div>
                </div>
            </div>
        </section>

        <section id="data-sepatu" style="margin: 3rem 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="section-title">Data Sepatu</h2>
                <a href="tambah.php" class="btn" style="background: #2ecc71;">
                    <i class="fas fa-plus"></i> Tambah Sepatu
                </a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Merk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Warna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_sepatu->num_rows > 0): ?>
                            <?php $no = 1; while ($sepatu = $result_sepatu->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($sepatu['nama']); ?></td>
                                <td><?php echo htmlspecialchars($sepatu['merk']); ?></td>
                                <td><?php echo format_rupiah($sepatu['harga']); ?></td>
                                <td><?php echo $sepatu['stok']; ?></td>
                                <td><?php echo htmlspecialchars($sepatu['warna']); ?></td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="edit.php?id=<?php echo $sepatu['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="hapus.php?id=<?php echo $sepatu['id']; ?>" 
                                           onclick="return confirm('Hapus sepatu <?php echo addslashes($sepatu['nama']); ?>?')"
                                           class="btn-hapus">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada data sepatu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="pesanan" style="margin: 3rem 0;">
            <h2 class="section-title">Data Pesanan</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pembeli</th>
                            <th>Sepatu</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pesanan->num_rows > 0): ?>
                            <?php $no = 1; while ($pesanan = $result_pesanan->fetch_assoc()): 
                                $tanggal = date('d/m/Y', strtotime($pesanan['dibuat_kapan']));
                                $status_colors = [
                                    'pending' => '#f39c12',
                                    'diproses' => '#3498db',
                                    'dikirim' => '#9b59b6',
                                    'selesai' => '#2ecc71'
                                ];
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($pesanan['nama_pembeli']); ?></td>
                                <td><?php echo htmlspecialchars($pesanan['nama_sepatu']); ?></td>
                                <td><?php echo $pesanan['jumlah']; ?></td>
                                <td><?php echo format_rupiah($pesanan['total_harga']); ?></td>
                                <td>
                                    <span class="status-badge" style="background: <?php echo $status_colors[$pesanan['status']]; ?>; color: white; padding: 5px 10px; border-radius: 3px;">
                                        <?php echo ucfirst($pesanan['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $tanggal; ?></td>
                                <td>
                                    <div style="display: flex; gap: 5px; flex-direction: column;">
                                        <form method="POST" action="ubah_status.php" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $pesanan['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 5px; border-radius: 3px; width: 100%;">
                                                <option value="pending" <?php echo $pesanan['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="diproses" <?php echo $pesanan['status'] == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                                <option value="dikirim" <?php echo $pesanan['status'] == 'dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                                <option value="selesai" <?php echo $pesanan['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                            </select>
                                        </form>
                                        <?php if ($pesanan['status'] == 'selesai'): ?>
                                        <a href="hapus_pesanan.php?id=<?php echo $pesanan['id']; ?>" 
                                           onclick="return confirm('Hapus pesanan ini?')"
                                           style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; text-align: center; font-size: 0.9rem;">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem;">Belum ada data pesanan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <p>Admin Panel Toko SepatuKu &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>