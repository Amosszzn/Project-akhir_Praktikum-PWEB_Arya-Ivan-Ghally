<?php
require_once 'config/koneksi.php';
session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = bersihkan_input($_POST['nama']);
    $merk = bersihkan_input($_POST['merk']);
    $harga = bersihkan_input($_POST['harga']);
    $ukuran = bersihkan_input($_POST['ukuran']);
    $warna = bersihkan_input($_POST['warna']);
    $stok = bersihkan_input($_POST['stok']);
    $gambar = bersihkan_input($_POST['gambar']);
    $deskripsi = bersihkan_input($_POST['deskripsi']);
    
    // Validasi
    if (empty($nama)) $errors[] = "Nama sepatu harus diisi";
    if (empty($merk)) $errors[] = "Merk harus diisi";
    if (empty($harga) || $harga <= 0) $errors[] = "Harga harus diisi dan lebih dari 0";
    if (empty($stok) || $stok < 0) $errors[] = "Stok harus diisi dan tidak boleh negatif";
    
    if (empty($errors)) {
        $query = "INSERT INTO sepatu (nama, merk, harga, ukuran, warna, stok, gambar, deskripsi) 
                  VALUES ('$nama', '$merk', '$harga', '$ukuran', '$warna', '$stok', '$gambar', '$deskripsi')";
        
        if ($koneksi->query($query)) {
            $_SESSION['notifikasi'] = [
                'type' => 'success',
                'message' => 'Sepatu berhasil ditambahkan!'
            ];
            header('Location: admin.php');
            exit();
        } else {
            $errors[] = "Gagal menambahkan sepatu: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Sepatu - Toko Sepatu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shoe-prints"></i>
                    <h1>Tambah Sepatu</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Toko</a></li>
                        <li><a href="admin.php"><i class="fas fa-cog"></i> Admin</a></li>
                        <li><a href="tambah.php" class="active"><i class="fas fa-plus"></i> Tambah Sepatu</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 style="margin-bottom: 2rem; text-align: center;">
                <i class="fas fa-plus-circle"></i> Tambah Sepatu Baru
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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama Sepatu *</label>
                    <input type="text" id="nama" name="nama" class="form-control" required 
                           value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="merk">Merk *</label>
                    <input type="text" id="merk" name="merk" class="form-control" required
                           value="<?php echo isset($_POST['merk']) ? htmlspecialchars($_POST['merk']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga (Rp) *</label>
                    <input type="number" id="harga" name="harga" class="form-control" min="1" required
                           value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="ukuran">Ukuran (pisahkan dengan koma)</label>
                    <input type="text" id="ukuran" name="ukuran" class="form-control" 
                           placeholder="Contoh: 40,41,42,43"
                           value="<?php echo isset($_POST['ukuran']) ? htmlspecialchars($_POST['ukuran']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="warna">Warna</label>
                    <input type="text" id="warna" name="warna" class="form-control"
                           value="<?php echo isset($_POST['warna']) ? htmlspecialchars($_POST['warna']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="stok">Stok *</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required
                           value="<?php echo isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="gambar">URL Gambar</label>
                    <input type="text" id="gambar" name="gambar" class="form-control" 
                           placeholder="https://example.com/gambar.jpg"
                           value="<?php echo isset($_POST['gambar']) ? htmlspecialchars($_POST['gambar']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 2rem;">
                    <a href="admin.php" class="btn" style="background: #95a5a6; flex: 1; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn" style="background: #2ecc71; flex: 1;">
                        <i class="fas fa-save"></i> Simpan Sepatu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>Toko SepatuKu &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>