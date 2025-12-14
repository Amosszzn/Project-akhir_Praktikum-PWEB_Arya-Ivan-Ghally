<?php
require_once 'config/koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = bersihkan_input($_GET['id']);
$errors = [];

// Ambil data sepatu yang akan diedit
$query = "SELECT * FROM sepatu WHERE id = '$id'";
$result = $koneksi->query($query);
$sepatu = $result->fetch_assoc();

if (!$sepatu) {
    header('Location: admin.php');
    exit();
}

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
        $query = "UPDATE sepatu SET 
                  nama = '$nama',
                  merk = '$merk',
                  harga = '$harga',
                  ukuran = '$ukuran',
                  warna = '$warna',
                  stok = '$stok',
                  gambar = '$gambar',
                  deskripsi = '$deskripsi',
                  diubah_kapan = CURRENT_TIMESTAMP
                  WHERE id = '$id'";
        
        if ($koneksi->query($query)) {
            $_SESSION['notifikasi'] = [
                'type' => 'success',
                'message' => 'Sepatu berhasil diperbarui!'
            ];
            header('Location: admin.php');
            exit();
        } else {
            $errors[] = "Gagal memperbarui sepatu: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sepatu - Toko Sepatu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shoe-prints"></i>
                    <h1>Edit Sepatu</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Toko</a></li>
                        <li><a href="admin.php"><i class="fas fa-cog"></i> Admin</a></li>
                        <li><a href="edit.php?id=<?php echo $id; ?>" class="active"><i class="fas fa-edit"></i> Edit Sepatu</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 style="margin-bottom: 2rem; text-align: center;">
                <i class="fas fa-edit"></i> Edit Sepatu
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
                           value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : htmlspecialchars($sepatu['nama']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="merk">Merk *</label>
                    <input type="text" id="merk" name="merk" class="form-control" required
                           value="<?php echo isset($_POST['merk']) ? htmlspecialchars($_POST['merk']) : htmlspecialchars($sepatu['merk']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga (Rp) *</label>
                    <input type="number" id="harga" name="harga" class="form-control" min="1" required
                           value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : $sepatu['harga']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="ukuran">Ukuran (pisahkan dengan koma)</label>
                    <input type="text" id="ukuran" name="ukuran" class="form-control" 
                           placeholder="Contoh: 40,41,42,43"
                           value="<?php echo isset($_POST['ukuran']) ? htmlspecialchars($_POST['ukuran']) : htmlspecialchars($sepatu['ukuran']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="warna">Warna</label>
                    <input type="text" id="warna" name="warna" class="form-control"
                           value="<?php echo isset($_POST['warna']) ? htmlspecialchars($_POST['warna']) : htmlspecialchars($sepatu['warna']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="stok">Stok *</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required
                           value="<?php echo isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : $sepatu['stok']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="gambar">URL Gambar</label>
                    <input type="text" id="gambar" name="gambar" class="form-control" 
                           placeholder="https://example.com/gambar.jpg"
                           value="<?php echo isset($_POST['gambar']) ? htmlspecialchars($_POST['gambar']) : htmlspecialchars($sepatu['gambar']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : htmlspecialchars($sepatu['deskripsi']); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 2rem;">
                    <a href="admin.php" class="btn" style="background: #95a5a6; flex: 1; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn" style="background: #f39c12; flex: 1;">
                        <i class="fas fa-save"></i> Update Sepatu
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