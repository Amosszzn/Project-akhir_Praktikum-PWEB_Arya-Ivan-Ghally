<?php
require_once 'config/koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = bersihkan_input($_GET['id']);
$query = "SELECT * FROM sepatu WHERE id = '$id'";
$result = $koneksi->query($query);
$sepatu = $result->fetch_assoc();

if (!$sepatu) {
    header('Location: index.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pembeli = bersihkan_input($_POST['nama_pembeli']);
    $email = bersihkan_input($_POST['email']);
    $telepon = bersihkan_input($_POST['telepon']);
    $jumlah = bersihkan_input($_POST['jumlah']);
    
    // Validasi
    if (empty($nama_pembeli)) $errors[] = "Nama pembeli harus diisi";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";
    if (empty($telepon)) $errors[] = "Telepon harus diisi";
    if (empty($jumlah) || $jumlah <= 0) $errors[] = "Jumlah harus diisi dan lebih dari 0";
    if ($jumlah > $sepatu['stok']) $errors[] = "Stok tidak cukup, stok tersedia: " . $sepatu['stok'];
    
    if (empty($errors)) {
        $total_harga = $sepatu['harga'] * $jumlah;
        $status = 'pending';
        
        // Insert pesanan
        $query_pesanan = "INSERT INTO pesanan (id_sepatu, nama_pembeli, email, telepon, jumlah, total_harga, status) 
                         VALUES ('$id', '$nama_pembeli', '$email', '$telepon', '$jumlah', '$total_harga', '$status')";
        
        if ($koneksi->query($query_pesanan)) {
            // Update stok sepatu
            $stok_baru = $sepatu['stok'] - $jumlah;
            $query_update = "UPDATE sepatu SET stok = '$stok_baru', diubah_kapan = CURRENT_TIMESTAMP WHERE id = '$id'";
            $koneksi->query($query_update);
            
            $success = true;
        } else {
            $errors[] = "Gagal membuat pesanan: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Sepatu - Toko Sepatu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shoe-prints"></i>
                    <h1>Beli Sepatu</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Toko</a></li>
                        <li><a href="admin.php"><i class="fas fa-cog"></i> Admin</a></li>
                        <li><a href="#form"><i class="fas fa-shopping-cart"></i> Beli</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if ($success): ?>
        <div class="form-container" style="text-align: center;">
            <div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin-bottom: 1.5rem;">
                <h3><i class="fas fa-check-circle"></i> Pesanan Berhasil!</h3>
                <p>Terima kasih <?php echo htmlspecialchars($nama_pembeli); ?> telah berbelanja di toko kami.</p>
                <p>Total yang harus dibayar: <strong><?php echo format_rupiah($total_harga); ?></strong></p>
                <p>Status pesanan: <span style="background: #f39c12; color: white; padding: 5px 10px; border-radius: 3px;">Pending</span></p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="index.php" class="btn" style="flex: 1; text-align: center;">
                    <i class="fas fa-home"></i> Kembali ke Toko
                </a>
                <a href="#" onclick="window.print()" class="btn" style="background: #3498db; flex: 1; text-align: center;">
                    <i class="fas fa-print"></i> Cetak Pesanan
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="form-container">
            <h2 style="margin-bottom: 2rem; text-align: center;">
                <i class="fas fa-shopping-cart"></i> Beli Sepatu
            </h2>
            
            <?php if (!empty($errors)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 1.5rem;">
                <h4 style="margin-top: 0;">Terjadi kesalahan:</h4>
                <ul style="margin-bottom: 0;">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 2rem;">
                <h3 style="margin-top: 0;">Detail Sepatu</h3>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 15px;">
                    <div><strong>Nama:</strong></div>
                    <div><?php echo htmlspecialchars($sepatu['nama']); ?></div>
                    
                    <div><strong>Merk:</strong></div>
                    <div><?php echo htmlspecialchars($sepatu['merk']); ?></div>
                    
                    <div><strong>Warna:</strong></div>
                    <div><?php echo htmlspecialchars($sepatu['warna']); ?></div>
                    
                    <div><strong>Harga Satuan:</strong></div>
                    <div><?php echo format_rupiah($sepatu['harga']); ?></div>
                    
                    <div><strong>Stok Tersedia:</strong></div>
                    <div><?php echo $sepatu['stok']; ?></div>
                </div>
            </div>
            
            <form method="POST" action="" id="form">
                <div class="form-group">
                    <label for="nama_pembeli">Nama Pembeli *</label>
                    <input type="text" id="nama_pembeli" name="nama_pembeli" class="form-control" required
                           value="<?php echo isset($_POST['nama_pembeli']) ? htmlspecialchars($_POST['nama_pembeli']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="telepon">No. Telepon *</label>
                    <input type="text" id="telepon" name="telepon" class="form-control" required
                           value="<?php echo isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="jumlah">Jumlah *</label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" 
                           max="<?php echo $sepatu['stok']; ?>" required
                           value="<?php echo isset($_POST['jumlah']) ? htmlspecialchars($_POST['jumlah']) : '1'; ?>"
                           oninput="hitungTotal()">
                    <small>Stok tersedia: <?php echo $sepatu['stok']; ?></small>
                </div>
                
                <div class="form-group">
                    <label>Total Harga</label>
                    <div id="total_harga" style="font-size: 1.5rem; font-weight: bold; color: #e74c3c;">
                        <?php echo format_rupiah($sepatu['harga']); ?>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 2rem;">
                    <a href="index.php" class="btn" style="background: #95a5a6; flex: 1; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn" style="background: #3498db; flex: 1;">
                        <i class="fas fa-check"></i> Konfirmasi Pembelian
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <p>Toko SepatuKu &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <script>
    function hitungTotal() {
        const harga = <?php echo $sepatu['harga']; ?>;
        const jumlah = document.getElementById('jumlah').value;
        const total = harga * jumlah;
        document.getElementById('total_harga').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    </script>
    <script src="js/script.js"></script>
</body>
</html>